<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Rules\ZambianNrc;
use App\Support\ZambianNrc as ZambianNrcSupport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Driver::class, 'driver');
    }

    /**
     * Display a listing of active drivers (full UI in a later iteration).
     */
    public function index(Request $request): View
    {
        $drivers = Driver::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search')->trim();
                $query->where(function ($q) use ($term) {
                    $q->where('full_name', 'like', '%'.$term.'%')
                        ->orWhere('employee_id', 'like', '%'.$term.'%')
                        ->orWhere('nrc', 'like', '%'.$term.'%');
                });
            })
            ->when($request->boolean('archived'), fn ($q) => $q->archived(), fn ($q) => $q->active())
            ->orderBy('full_name')
            ->paginate(15)
            ->withQueryString();

        return view('drivers.index', compact('drivers'));
    }

    public function create(): View
    {
        return view('drivers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'string', 'max:255', 'unique:drivers,employee_id'],
            'full_name' => ['required', 'string', 'max:255'],
            'nrc' => ['required', 'string', new ZambianNrc],
            'department' => ['required', 'string', 'max:255'],
            'license_number' => ['required', 'string', 'max:255'],
            'license_class' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
        ]);

        $validated['nrc'] = ZambianNrcSupport::normalize($validated['nrc']);

        Driver::create($validated);

        return redirect()->route('drivers.index')
            ->with('status', 'Driver registered successfully.');
    }

    public function show(Driver $driver): View
    {
        $driver->load(['permits' => fn ($q) => $q->latest()->limit(50)]);

        return view('drivers.show', compact('driver'));
    }

    public function edit(Driver $driver): View
    {
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'string', 'max:255', 'unique:drivers,employee_id,'.$driver->id],
            'full_name' => ['required', 'string', 'max:255'],
            'nrc' => ['required', 'string', new ZambianNrc],
            'department' => ['required', 'string', 'max:255'],
            'license_number' => ['required', 'string', 'max:255'],
            'license_class' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
        ]);

        $validated['nrc'] = ZambianNrcSupport::normalize($validated['nrc']);

        $driver->update($validated);

        return redirect()->route('drivers.show', $driver)
            ->with('status', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        $driver->delete();

        return redirect()->route('drivers.index')
            ->with('status', 'Driver removed.');
    }

    public function archive(Driver $driver): RedirectResponse
    {
        $this->authorize('archive', $driver);

        $driver->update(['archived_at' => now()]);

        return redirect()->route('drivers.index')
            ->with('status', 'Driver archived.');
    }

    public function restore(Driver $driver): RedirectResponse
    {
        $this->authorize('restore', $driver);

        $driver->update(['archived_at' => null]);

        return redirect()->route('drivers.index')
            ->with('status', 'Driver restored.');
    }
}
