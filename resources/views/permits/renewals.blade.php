<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Renewals') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Permits expiring within :days days — renew before expiry.', ['days' => $days]) }}</p>
            </div>
            @can('create', App\Models\Permit::class)
                <a href="{{ route('permits.issue') }}" class="dpms-btn-primary text-xs uppercase tracking-wide">
                    <x-icon name="file-circle-plus" size="sm" class="me-2" />
                    {{ __('Issue Permit') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="dpms-card p-6 space-y-4">
        <div class="flex flex-wrap gap-4 items-center justify-between">
            @php
                $expiryBanner = trans_choice(
                    ':count permit expiring within 30 days|:count permits expiring within 30 days',
                    $expiringWithin30,
                    ['count' => $expiringWithin30]
                );
            @endphp
            <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2">
                <x-icon name="triangle-exclamation" size="sm" class="me-1" />
                {{ $expiryBanner }}
            </p>
            <form method="get" class="flex items-end gap-2">
                <div>
                    <label class="block text-sm text-gray-600">{{ __('Window (days)') }}</label>
                    <select name="days" class="rounded-md border-gray-300 text-sm shadow-sm" onchange="this.form.submit()">
                        @foreach ([30, 60, 90, 120] as $option)
                            <option value="{{ $option }}" @selected($days === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left dpms-table">
                <thead>
                    <tr>
                        <th>{{ __('Permit #') }}</th>
                        <th>{{ __('Driver') }}</th>
                        <th>{{ __('Expiry Date') }}</th>
                        <th>{{ __('Days Left') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permits as $permit)
                        <tr>
                            <td class="font-mono">{{ $permit->permit_number }}</td>
                            <td>{{ $permit->driver?->full_name }}</td>
                            <td>{{ $permit->expiry_date->format('Y-m-d') }}</td>
                            <td>
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $permit->days_left <= 15 ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $permit->days_left }} {{ __('days') }}
                                </span>
                            </td>
                            <td class="text-end space-x-2">
                                <a href="{{ route('permits.show', $permit) }}" class="text-dpms-700 hover:underline">{{ __('View') }}</a>
                                @can('update', $permit)
                                    <a href="{{ route('permits.edit', $permit) }}" class="text-dpms-700 font-medium hover:underline">{{ __('Renew') }}</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">{{ __('No permits due for renewal in this period.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $permits->links() }}
    </div>
</x-app-layout>
