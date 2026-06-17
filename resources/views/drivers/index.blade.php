<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Drivers') }}
            </h2>
            @can('create', App\Models\Driver::class)
                <a href="{{ route('drivers.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Register driver') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="dpms-card p-6 space-y-4">
                <form method="get" class="flex flex-wrap gap-2 items-end">
                    <div>
                        <label class="block text-sm text-gray-600">{{ __('Search') }}</label>
                        <input type="search" name="search" value="{{ request('search') }}" class="rounded-md border-gray-300 shadow-sm text-sm" placeholder="{{ __('Name, employee ID, NRC') }}">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">{{ __('View') }}</label>
                        <select name="archived" class="rounded-md border-gray-300 shadow-sm text-sm" onchange="this.form.submit()">
                            <option value="0" @selected(! request()->boolean('archived'))>{{ __('Active') }}</option>
                            <option value="1" @selected(request()->boolean('archived'))>{{ __('Archived') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="px-3 py-2 bg-gray-200 rounded-md text-sm">{{ __('Filter') }}</button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="border-b text-gray-600">
                            <tr>
                                <th class="py-2 pe-4">{{ __('Employee ID') }}</th>
                                <th class="py-2 pe-4">{{ __('Name') }}</th>
                                <th class="py-2 pe-4">{{ __('Department') }}</th>
                                <th class="py-2 pe-4">{{ __('Phone') }}</th>
                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($drivers as $driver)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 pe-4 font-mono">{{ $driver->employee_id }}</td>
                                    <td class="py-2 pe-4">{{ $driver->full_name }}</td>
                                    <td class="py-2 pe-4">{{ $driver->department }}</td>
                                    <td class="py-2 pe-4">{{ $driver->phone }}</td>
                                    <td class="py-2 text-end">
                                        <a href="{{ route('drivers.show', $driver) }}" class="text-indigo-600 hover:underline">{{ __('View') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-gray-500">{{ __('No drivers found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $drivers->links() }}
    </div>
</x-app-layout>
