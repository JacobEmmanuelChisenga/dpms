<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Permit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewReports');

        $type = $request->string('type', 'active_permits')->toString();

        $data = match ($type) {
            'active_permits' => [
                'title' => 'Active permits',
                'rows' => Permit::query()
                    ->with('driver')
                    ->where('status', Permit::STATUS_VALID)
                    ->whereDate('expiry_date', '>=', now())
                    ->orderBy('expiry_date')
                    ->paginate(50)
                    ->withQueryString(),
            ],
            'expired_permits' => [
                'title' => 'Expired permits',
                'rows' => Permit::query()
                    ->with('driver')
                    ->where(function ($q) {
                        $q->where('status', Permit::STATUS_EXPIRED)
                            ->orWhereDate('expiry_date', '<', now());
                    })
                    ->orderByDesc('expiry_date')
                    ->paginate(50)
                    ->withQueryString(),
            ],
            'drivers' => [
                'title' => 'Driver list',
                'rows' => Driver::query()
                    ->active()
                    ->orderBy('full_name')
                    ->paginate(50)
                    ->withQueryString(),
            ],
            'issuance' => [
                'title' => 'Permit issuance log',
                'rows' => Permit::query()
                    ->with(['driver', 'issuer'])
                    ->latest()
                    ->paginate(50)
                    ->withQueryString(),
            ],
            default => [
                'title' => 'Active permits',
                'rows' => Permit::query()
                    ->with('driver')
                    ->where('status', Permit::STATUS_VALID)
                    ->whereDate('expiry_date', '>=', now())
                    ->orderBy('expiry_date')
                    ->paginate(50)
                    ->withQueryString(),
            ],
        };

        return view('reports.index', [
            'reportType' => $type,
            'reportTitle' => $data['title'],
            'rows' => $data['rows'],
        ]);
    }
}
