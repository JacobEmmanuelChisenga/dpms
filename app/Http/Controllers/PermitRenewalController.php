<?php

namespace App\Http\Controllers;

use App\Models\Permit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermitRenewalController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Permit::class);

        $days = (int) $request->integer('days', 60);
        $days = max(7, min(180, $days));

        $permits = Permit::query()
            ->with(['driver', 'issuer'])
            ->where('status', Permit::STATUS_VALID)
            ->whereDate('expiry_date', '>=', now()->toDateString())
            ->whereDate('expiry_date', '<=', now()->addDays($days)->toDateString())
            ->orderBy('expiry_date')
            ->paginate(20)
            ->withQueryString();

        $permits->getCollection()->transform(function (Permit $permit) {
            $permit->days_left = (int) now()->startOfDay()->diffInDays($permit->expiry_date, false);

            return $permit;
        });

        $expiringWithin30 = Permit::query()
            ->where('status', Permit::STATUS_VALID)
            ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->count();

        return view('permits.renewals', [
            'permits' => $permits,
            'days' => $days,
            'expiringWithin30' => $expiringWithin30,
        ]);
    }
}
