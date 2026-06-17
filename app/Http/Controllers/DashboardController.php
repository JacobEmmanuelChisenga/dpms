<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Driver;
use App\Models\Permit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user->usesManagementLayout()) {
            return $this->managementDashboard($user);
        }

        if ($user->usesFleetLayout()) {
            return $this->fleetDashboard($user);
        }

        return $this->adminDashboard($user);
    }

    protected function adminDashboard(User $user): View
    {
        $stats = [
            'drivers' => Driver::query()->active()->count(),
            'permits_active' => Permit::query()->where('status', Permit::STATUS_VALID)->count(),
            'permits_expired' => Permit::query()->where('status', Permit::STATUS_EXPIRED)->count(),
            'permits_revoked' => Permit::query()->where('status', Permit::STATUS_REVOKED)->count(),
            'users' => User::query()->count(),
        ];

        $permitTotal = max(1, $stats['permits_active'] + $stats['permits_expired'] + $stats['permits_revoked']);

        $permitChart = [
            'active' => $stats['permits_active'],
            'expired' => $stats['permits_expired'],
            'revoked' => $stats['permits_revoked'],
            'pending' => Permit::query()
                ->where('status', Permit::STATUS_VALID)
                ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
                ->count(),
        ];

        $year = (int) request('year', now()->year);
        $monthlyIssuance = Permit::query()
            ->whereYear('issue_date', $year)
            ->get()
            ->groupBy(fn (Permit $p) => $p->issue_date->month)
            ->map->count();

        $monthlyLabels = [];
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyLabels[] = now()->month($m)->format('M');
            $monthlyData[] = (int) ($monthlyIssuance[$m] ?? 0);
        }

        $recentActivities = AuditLog::query()
            ->with('actor')
            ->latest('logged_at')
            ->limit(8)
            ->get();

        $upcomingExpiry = $this->upcomingExpiryPermits(6);

        return view('dashboard', [
            'user' => $user,
            'stats' => $stats,
            'permitTotal' => $permitTotal,
            'permitChart' => $permitChart,
            'monthlyLabels' => $monthlyLabels,
            'monthlyData' => $monthlyData,
            'chartYear' => $year,
            'recentActivities' => $recentActivities,
            'upcomingExpiry' => $upcomingExpiry,
        ]);
    }

    protected function managementDashboard(User $user): View
    {
        $stats = [
            'drivers' => Driver::query()->active()->count(),
            'permits_active' => Permit::query()->where('status', Permit::STATUS_VALID)->count(),
            'expiring_soon' => Permit::query()
                ->where('status', Permit::STATUS_VALID)
                ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
                ->count(),
            'permits_expired' => Permit::query()->where('status', Permit::STATUS_EXPIRED)->count(),
        ];

        $recentIssuance = Permit::query()
            ->with('driver')
            ->latest('issue_date')
            ->limit(6)
            ->get();

        return view('dashboard-management', [
            'user' => $user,
            'stats' => $stats,
            'recentIssuance' => $recentIssuance,
        ]);
    }

    protected function fleetDashboard(User $user): View
    {
        $stats = [
            'drivers' => Driver::query()->active()->count(),
            'permits_active' => Permit::query()->where('status', Permit::STATUS_VALID)->count(),
            'expiring_soon' => Permit::query()
                ->where('status', Permit::STATUS_VALID)
                ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
                ->count(),
            'permits_expired' => Permit::query()->where('status', Permit::STATUS_EXPIRED)->count(),
        ];

        $recentIssuance = Permit::query()
            ->with('driver')
            ->latest('issue_date')
            ->latest('id')
            ->limit(8)
            ->get();

        $upcomingExpiry = $this->upcomingExpiryPermits(5);

        return view('dashboard-fleet', [
            'user' => $user,
            'stats' => $stats,
            'recentIssuance' => $recentIssuance,
            'upcomingExpiry' => $upcomingExpiry,
        ]);
    }

    /**
     * @return Collection<int, Permit>
     */
    protected function upcomingExpiryPermits(int $limit)
    {
        return Permit::query()
            ->with('driver')
            ->where('status', Permit::STATUS_VALID)
            ->whereDate('expiry_date', '>=', now())
            ->whereDate('expiry_date', '<=', now()->addDays(60))
            ->orderBy('expiry_date')
            ->limit($limit)
            ->get()
            ->map(function (Permit $permit) {
                $permit->days_left = (int) now()->startOfDay()->diffInDays($permit->expiry_date, false);

                return $permit;
            });
    }
}
