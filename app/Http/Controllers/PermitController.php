<?php

namespace App\Http\Controllers;

use App\Models\CertificateSetting;
use App\Models\Driver;
use App\Models\Permit;
use App\Services\PermitNumberGenerator;
use App\Services\PermitQrService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PermitController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Permit::class, 'permit');
    }

    public function index(Request $request): View
    {
        $permits = Permit::query()
            ->with(['driver', 'issuer'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search')->trim();
                $query->where(function ($q) use ($term) {
                    $q->where('permit_number', 'like', '%'.$term.'%')
                        ->orWhereHas('driver', function ($dq) use ($term) {
                            $dq->where('full_name', 'like', '%'.$term.'%')
                                ->orWhere('employee_id', 'like', '%'.$term.'%');
                        });
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('permits.index', compact('permits'));
    }

    public function create(Request $request): RedirectResponse
    {
        if ($request->filled('driver_id')) {
            $driver = Driver::query()->active()->find($request->integer('driver_id'));

            if ($driver === null) {
                abort(404);
            }

            if ($driver->hasActivePermit()) {
                return redirect()->route('drivers.show', $driver)
                    ->withErrors([
                        'driver_id' => __('This driver already has an active permit. Revoke or wait until expiry before issuing another.'),
                    ]);
            }

            session()->put(PermitIssuanceController::SESSION_KEY, [
                'driver_id' => $driver->id,
                'step' => 2,
            ]);

            return redirect()->route('permits.issue.validity');
        }

        return redirect()->route('permits.issue');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'driver_id' => ['required', 'exists:drivers,id'],
            'issue_date' => ['required', 'date'],
            'expiry_date' => ['required', 'date', 'after_or_equal:issue_date'],
        ]);

        $driver = Driver::query()
            ->active()
            ->eligibleForIssuance()
            ->find($validated['driver_id']);

        if ($driver === null) {
            throw ValidationException::withMessages([
                'driver_id' => __('This driver already has an active permit. Revoke or wait until expiry before issuing another.'),
            ]);
        }

        $permitNumber = PermitNumberGenerator::generate();

        $status = now()->startOfDay()->gt($request->date('expiry_date'))
            ? Permit::STATUS_EXPIRED
            : Permit::STATUS_VALID;

        $payload = [
            'driver_id' => $driver->id,
            'permit_number' => $permitNumber,
            'issue_date' => $validated['issue_date'],
            'expiry_date' => $validated['expiry_date'],
            'status' => $status,
            'issued_by' => $request->user()->id,
            'qr_code' => null,
        ];

        $permit = Permit::create($payload);

        PermitQrService::ensure($permit);

        return redirect()->route('permits.show', $permit)
            ->with('status', __('Permit issued. QR and PDF certificate are ready.'));
    }

    public function certificate(Permit $permit)
    {
        $this->authorize('view', $permit);

        PermitQrService::ensure($permit);
        $permit->refresh();
        $permit->load(['driver', 'issuer']);

        return Pdf::loadView('permits.pdf-certificate', [
            'permit' => $permit,
            'qrDataUri' => PermitQrService::qrDataUriForDomPdf($permit),
            'logoDataUri' => self::certificateLogoDataUri(),
            'signatureDataUri' => PermitQrService::imageDataUriFromStorage(
                CertificateSetting::singleton()->official_signature_path
            ),
            'verificationUrl' => PermitQrService::verificationUrl($permit),
            'certificateTitle' => __('Driver Authorization Certificate'),
            'issuedByRole' => __('Transport Officer'),
        ])
            ->setPaper('a4', 'landscape')
            ->stream('permit-'.Str::slug($permit->permit_number).'.pdf');
    }

    public function show(Permit $permit): View
    {
        PermitQrService::ensure($permit);
        $permit->refresh();
        $permit->load(['driver', 'issuer']);

        return view('permits.show', compact('permit'));
    }

    public function edit(Permit $permit): View
    {
        return view('permits.edit', compact('permit'));
    }

    public function update(Request $request, Permit $permit): RedirectResponse
    {
        $validated = $request->validate([
            'issue_date' => ['required', 'date'],
            'expiry_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'status' => ['required', 'in:valid,expired,revoked'],
        ]);

        $permit->fill($validated);

        if ($permit->status !== Permit::STATUS_REVOKED) {
            if ($permit->expiry_date->lt(now()->startOfDay())) {
                $permit->status = Permit::STATUS_EXPIRED;
            } elseif ($permit->status === Permit::STATUS_EXPIRED) {
                $permit->status = Permit::STATUS_VALID;
            }
        }

        $permit->save();

        return redirect()->route('permits.show', $permit)
            ->with('status', 'Permit updated.');
    }

    /**
     * Mark permit as revoked.
     */
    public function revoke(Permit $permit): RedirectResponse
    {
        $this->authorize('revoke', $permit);

        $permit->update(['status' => Permit::STATUS_REVOKED]);

        return redirect()->route('permits.show', $permit)
            ->with('status', 'Permit revoked.');
    }

    public function destroy(Permit $permit): RedirectResponse
    {
        $permit->delete();

        return redirect()->route('permits.index')
            ->with('status', 'Permit record deleted.');
    }

    private static function certificateLogoDataUri(): ?string
    {
        $path = public_path('images/zafficologo.jpg');
        if (! is_readable($path)) {
            return null;
        }

        return 'data:image/jpeg;base64,'.base64_encode((string) file_get_contents($path));
    }
}
