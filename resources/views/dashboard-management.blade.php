@php $pageTitle = __('Management overview'); @endphp

<x-app-layout>
    <div class="mb-6">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-800 text-xs font-semibold uppercase tracking-wide mb-3">
            <x-icon name="eye" size="sm" />
            {{ __('Read-only access') }}
        </div>
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Management overview') }}</h2>
        <p class="text-gray-500 mt-1 text-sm">{{ __('Permit statistics and reports — no editing permissions.') }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="dpms-stat-card">
            <p class="text-xs font-medium text-gray-500 uppercase">{{ __('Total Drivers') }}</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['drivers']) }}</p>
        </div>
        <div class="dpms-stat-card">
            <p class="text-xs font-medium text-gray-500 uppercase">{{ __('Active Permits') }}</p>
            <p class="text-3xl font-bold text-green-700 mt-2">{{ number_format($stats['permits_active']) }}</p>
        </div>
        <div class="dpms-stat-card">
            <p class="text-xs font-medium text-gray-500 uppercase">{{ __('Expiring Soon') }}</p>
            <p class="text-3xl font-bold text-amber-700 mt-2">{{ number_format($stats['expiring_soon']) }}</p>
        </div>
        <div class="dpms-stat-card">
            <p class="text-xs font-medium text-gray-500 uppercase">{{ __('Expired') }}</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['permits_expired']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="dpms-card p-6">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('Recent permit issuance') }}</h3>
            <ul class="space-y-3 text-sm">
                @forelse ($recentIssuance as $permit)
                    <li class="flex justify-between gap-2 border-b border-gray-50 pb-2">
                        <span class="font-mono text-dpms-700">{{ $permit->permit_number }}</span>
                        <span class="text-gray-600 truncate">{{ $permit->driver?->full_name }}</span>
                    </li>
                @empty
                    <li class="text-gray-500">{{ __('No permits on record.') }}</li>
                @endforelse
            </ul>
            <a href="{{ route('reports.index', ['type' => 'issuance']) }}" class="mt-4 inline-block text-sm text-dpms-700 font-medium hover:underline">{{ __('View issuance reports') }} →</a>
        </div>
        <div class="dpms-card p-6">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('Quick reports') }}</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('reports.index', ['type' => 'active_permits']) }}" class="text-dpms-700 hover:underline">{{ __('Active permits report') }}</a></li>
                <li><a href="{{ route('reports.index', ['type' => 'expired_permits']) }}" class="text-dpms-700 hover:underline">{{ __('Expired permits report') }}</a></li>
                <li><a href="{{ route('reports.index', ['type' => 'drivers']) }}" class="text-dpms-700 hover:underline">{{ __('Driver report') }}</a></li>
                <li><a href="{{ route('permits.index') }}" class="text-dpms-700 hover:underline">{{ __('Browse all permits') }}</a></li>
            </ul>
        </div>
    </div>
</x-app-layout>
