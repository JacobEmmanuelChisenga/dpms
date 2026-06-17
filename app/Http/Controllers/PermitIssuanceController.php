<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Permit;
use App\Services\PermitNumberGenerator;
use App\Services\PermitQrService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PermitIssuanceController extends Controller
{
    public const SESSION_KEY = 'permit_issue_wizard';

    public function showDriver(): View|RedirectResponse
    {
        $this->authorize('create', Permit::class);

        $drivers = Driver::query()
            ->active()
            ->eligibleForIssuance()
            ->orderBy('full_name')
            ->get();
        $wizard = session(self::SESSION_KEY, []);

        return view('permits.issue.driver', [
            'drivers' => $drivers,
            'selectedDriverId' => old('driver_id', $wizard['driver_id'] ?? null),
        ]);
    }

    public function storeDriver(Request $request): RedirectResponse
    {
        $this->authorize('create', Permit::class);

        $validated = $request->validate([
            'driver_id' => ['required', 'exists:drivers,id'],
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

        session([
            self::SESSION_KEY => [
                'driver_id' => $driver->id,
                'step' => 2,
            ],
        ]);

        return redirect()->route('permits.issue.validity');
    }

    public function showValidity(): View|RedirectResponse
    {
        $this->authorize('create', Permit::class);

        $wizard = $this->wizardAtStep(2);
        if ($wizard instanceof RedirectResponse) {
            return $wizard;
        }

        $driver = Driver::active()->findOrFail($wizard['driver_id']);

        return view('permits.issue.validity', [
            'driver' => $driver,
            'issueDate' => old('issue_date', $wizard['issue_date'] ?? now()->format('Y-m-d')),
            'expiryDate' => old('expiry_date', $wizard['expiry_date'] ?? now()->addYear()->format('Y-m-d')),
            'notes' => old('notes', $wizard['notes'] ?? ''),
        ]);
    }

    public function storeValidity(Request $request): RedirectResponse
    {
        $this->authorize('create', Permit::class);

        $wizard = $this->wizardAtStep(2);
        if ($wizard instanceof RedirectResponse) {
            return $wizard;
        }

        $validated = $request->validate([
            'issue_date' => ['required', 'date'],
            'expiry_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        session([
            self::SESSION_KEY => array_merge($wizard, [
                'issue_date' => $validated['issue_date'],
                'expiry_date' => $validated['expiry_date'],
                'notes' => $validated['notes'] ?? null,
                'step' => 3,
            ]),
        ]);

        return redirect()->route('permits.issue.review');
    }

    public function showReview(): View|RedirectResponse
    {
        $this->authorize('create', Permit::class);

        $wizard = $this->wizardAtStep(3);
        if ($wizard instanceof RedirectResponse) {
            return $wizard;
        }

        $driver = Driver::active()->with('permits')->findOrFail($wizard['driver_id']);

        return view('permits.issue.review', [
            'driver' => $driver,
            'wizard' => $wizard,
        ]);
    }

    public function storeReview(Request $request): RedirectResponse
    {
        $this->authorize('create', Permit::class);

        $wizard = $this->wizardAtStep(3);
        if ($wizard instanceof RedirectResponse) {
            return $wizard;
        }

        $request->validate([
            'approved' => ['accepted'],
        ]);

        session([
            self::SESSION_KEY => array_merge($wizard, [
                'approved' => true,
                'approved_by' => $request->user()->id,
                'approved_at' => now()->toIso8601String(),
                'step' => 4,
            ]),
        ]);

        return redirect()->route('permits.issue.generate');
    }

    public function showGenerate(): View|RedirectResponse
    {
        $this->authorize('create', Permit::class);

        $wizard = $this->wizardAtStep(4);
        if ($wizard instanceof RedirectResponse) {
            return $wizard;
        }

        if (empty($wizard['approved'])) {
            return redirect()->route('permits.issue.review')
                ->withErrors(['approved' => __('You must approve the permit before generation.')]);
        }

        $driver = Driver::active()->findOrFail($wizard['driver_id']);

        return view('permits.issue.generate', [
            'driver' => $driver,
            'wizard' => $wizard,
        ]);
    }

    public function storeGenerate(Request $request): RedirectResponse
    {
        $this->authorize('create', Permit::class);

        $wizard = $this->wizardAtStep(4);
        if ($wizard instanceof RedirectResponse) {
            return $wizard;
        }

        if (empty($wizard['approved'])) {
            return redirect()->route('permits.issue.review');
        }

        $driver = Driver::active()->findOrFail($wizard['driver_id']);

        if ($driver->hasActivePermit()) {
            session()->forget(self::SESSION_KEY);

            return redirect()->route('permits.issue')
                ->withErrors([
                    'driver_id' => __('This driver already has an active permit. Revoke or wait until expiry before issuing another.'),
                ]);
        }

        $issueDate = $wizard['issue_date'];
        $expiryDate = $wizard['expiry_date'];

        $status = now()->startOfDay()->gt($expiryDate)
            ? Permit::STATUS_EXPIRED
            : Permit::STATUS_VALID;

        $permit = Permit::create([
            'driver_id' => $driver->id,
            'permit_number' => PermitNumberGenerator::generate(),
            'issue_date' => $issueDate,
            'expiry_date' => $expiryDate,
            'status' => $status,
            'issued_by' => $request->user()->id,
            'qr_code' => null,
        ]);

        PermitQrService::ensure($permit);

        session()->forget(self::SESSION_KEY);

        return redirect()
            ->route('permits.issue.complete', $permit)
            ->with('status', __('Permit :number has been generated successfully.', ['number' => $permit->permit_number]));
    }

    public function complete(Permit $permit): View
    {
        $this->authorize('view', $permit);

        PermitQrService::ensure($permit);
        $permit->refresh();
        $permit->load(['driver', 'issuer']);

        return view('permits.issue.complete', compact('permit'));
    }

    public function cancel(): RedirectResponse
    {
        session()->forget(self::SESSION_KEY);

        return redirect()->route('permits.index')
            ->with('status', __('Permit issuance cancelled.'));
    }

    /**
     * @return array<string, mixed>|RedirectResponse
     */
    protected function wizardAtStep(int $minimumStep): array|RedirectResponse
    {
        $wizard = session(self::SESSION_KEY);

        if (! is_array($wizard) || empty($wizard['driver_id'])) {
            return redirect()->route('permits.issue')
                ->with('status', __('Start by selecting a driver.'));
        }

        $current = (int) ($wizard['step'] ?? 1);
        if ($current < $minimumStep) {
            return match ($current) {
                1 => redirect()->route('permits.issue'),
                2 => redirect()->route('permits.issue.validity'),
                default => redirect()->route('permits.issue.review'),
            };
        }

        return $wizard;
    }
}
