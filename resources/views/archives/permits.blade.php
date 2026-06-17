<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">{{ __('Old Permits') }}</h2>
            <a href="{{ route('archives.index') }}" class="text-sm text-dpms-700 hover:underline">{{ __('Back') }}</a>
        </div>
    </x-slot>

    <div class="dpms-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full dpms-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th>{{ __('Permit #') }}</th>
                        <th>{{ __('Driver') }}</th>
                        <th>{{ __('Expiry') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permits as $permit)
                        <tr>
                            <td class="font-mono text-xs">{{ $permit->permit_number }}</td>
                            <td>{{ $permit->driver?->full_name }}</td>
                            <td>{{ $permit->expiry_date->format('Y-m-d') }}</td>
                            <td class="uppercase text-xs">{{ $permit->status }}</td>
                            <td class="text-end"><a href="{{ route('permits.show', $permit) }}" class="text-dpms-700 text-sm">{{ __('View') }}</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-8 text-gray-500">{{ __('No archived permit records.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">{{ $permits->links() }}</div>
    </div>
</x-app-layout>
