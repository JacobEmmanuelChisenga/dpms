<?php

namespace App\Http\Controllers;

use App\Models\Permit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermitVerificationController extends Controller
{
    /**
     * Public permit verification (QR payload may point here with permit_number or id).
     */
    public function show(Request $request, ?string $code = null): View
    {
        $code = trim((string) ($code ?? $request->input('code', '')));

        if ($code === '') {
            return view('permits.verify', [
                'permit' => null,
                'code' => $code,
            ]);
        }

        $permit = Permit::query()
            ->with(['driver', 'issuer'])
            ->where(function ($query) use ($code) {
                $query->where('permit_number', $code);
                if (ctype_digit($code)) {
                    $query->orWhere('id', (int) $code);
                }
            })
            ->first();

        if ($permit !== null) {
            $permit->syncStatusFromDates();
            $permit->refresh();
        }

        return view('permits.verify', [
            'permit' => $permit,
            'code' => $code,
        ]);
    }
}
