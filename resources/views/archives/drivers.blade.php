<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4 flex-wrap">
            <h2 class="text-lg font-semibold text-gray-800">{{ __('Archived Drivers') }}</h2>
            <a href="{{ route('archives.index') }}" class="text-sm text-dpms-700 hover:underline">{{ __('Back') }}</a>
        </div>
    </x-slot>

    <div class="dpms-card p-6 space-y-4">
        <form method="get" class="flex gap-2">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Search…') }}" class="rounded-lg border-gray-300 text-sm flex-1 max-w-md">
            <button type="submit" class="dpms-btn-primary">{{ __('Search') }}</button>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full dpms-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Employee ID') }}</th>
                        <th>{{ __('Department') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($drivers as $driver)
                        <tr>
                            <td>{{ $driver->full_name }}</td>
                            <td class="font-mono text-xs">{{ $driver->employee_id }}</td>
                            <td>{{ $driver->department }}</td>
                            <td class="text-end">
                                <a href="{{ route('drivers.show', $driver) }}" class="text-dpms-700 text-sm">{{ __('View') }}</a>
                                @can('restore', $driver)
                                    <form method="post" action="{{ route('drivers.restore', $driver) }}" class="inline ms-2">
                                        @csrf
                                        <button type="submit" class="text-sm text-green-700 hover:underline">{{ __('Restore') }}</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-8 text-gray-500">{{ __('No archived drivers.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $drivers->links() }}
    </div>
</x-app-layout>
