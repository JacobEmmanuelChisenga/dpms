<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Permit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArchiveController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Driver::class);

        return view('archives.index');
    }

    public function permits(Request $request): View
    {
        $this->authorize('viewAny', Permit::class);

        $permits = Permit::query()
            ->with(['driver', 'issuer'])
            ->where(function ($q) {
                $q->where('status', Permit::STATUS_EXPIRED)
                    ->orWhereDate('expiry_date', '<', now()->subYears(2));
            })
            ->latest('expiry_date')
            ->paginate(25)
            ->withQueryString();

        return view('archives.permits', compact('permits'));
    }

    public function drivers(Request $request): View
    {
        $this->authorize('viewAny', Driver::class);

        $drivers = Driver::query()
            ->archived()
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search')->trim();
                $query->where(function ($q) use ($term) {
                    $q->where('full_name', 'like', '%'.$term.'%')
                        ->orWhere('employee_id', 'like', '%'.$term.'%');
                });
            })
            ->orderBy('full_name')
            ->paginate(25)
            ->withQueryString();

        return view('archives.drivers', compact('drivers'));
    }
}
