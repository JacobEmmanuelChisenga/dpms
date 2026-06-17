@php
    $pageTitle = __('Dashboard');
@endphp

<x-app-layout>
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ __('Welcome, :name', ['name' => $user->name]) }}</h2>
            <p class="text-gray-500 mt-1 text-sm">{{ __('Fleet operations — permits, drivers, and renewals at a glance.') }}</p>
        </div>
        <div class="dpms-card px-5 py-3 flex items-center gap-3 shrink-0">
            <div class="w-10 h-10 rounded-lg bg-dpms-50 flex items-center justify-center text-dpms-700">
                <x-icon name="calendar-day" />
            </div>
            <div>
                <div class="text-sm font-semibold text-gray-800">{{ now()->format('d M Y') }}</div>
                <div class="text-xs text-gray-500">{{ now()->format('l') }}</div>
            </div>
        </div>
    </div>

  {{-- Summary cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="dpms-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Total Drivers') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['drivers']) }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-dpms-100 text-dpms-700 flex items-center justify-center">
                    <x-icon name="users" size="lg" />
                </div>
            </div>
            <a href="{{ route('drivers.index') }}" class="mt-4 text-sm text-dpms-700 font-medium hover:underline inline-flex items-center gap-1">{{ __('Manage drivers') }} →</a>
        </div>

        <div class="dpms-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Active Permits') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['permits_active']) }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-green-100 text-green-700 flex items-center justify-center">
                    <x-icon name="circle-check" size="lg" />
                </div>
            </div>
            <a href="{{ route('permits.index', ['status' => 'valid']) }}" class="mt-4 text-sm text-dpms-700 font-medium hover:underline inline-flex items-center gap-1">{{ __('View active permits') }} →</a>
        </div>

        <div class="dpms-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Expiring Soon') }}</p>
                    <p class="text-3xl font-bold text-amber-700 mt-2">{{ number_format($stats['expiring_soon']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Within 30 days') }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                    <x-icon name="clock" size="lg" />
                </div>
            </div>
            <a href="{{ route('permits.renewals.index') }}" class="mt-4 text-sm text-dpms-700 font-medium hover:underline inline-flex items-center gap-1">{{ __('Open renewals') }} →</a>
        </div>

        <div class="dpms-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Expired Permits') }}</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['permits_expired']) }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-red-100 text-red-700 flex items-center justify-center">
                    <x-icon name="calendar-xmark" size="lg" />
                </div>
            </div>
            <a href="{{ route('permits.index', ['status' => 'expired']) }}" class="mt-4 text-sm text-dpms-700 font-medium hover:underline inline-flex items-center gap-1">{{ __('View expired') }} →</a>
        </div>
    </div>

    @can('create', App\Models\Permit::class)
        <div class="mb-6">
            <a href="{{ route('permits.issue') }}" class="dpms-btn-primary gap-2">
                <x-icon name="file-circle-plus" />
                {{ __('Issue New Permit') }}
            </a>
        </div>
    @endcan

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="dpms-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-800">{{ __('Recent Permit Issuance') }}</h3>
                <a href="{{ route('permits.index') }}" class="text-sm text-dpms-700 hover:underline">{{ __('All permits') }}</a>
            </div>
            <ul class="space-y-4">
                @forelse ($recentIssuance as $permit)
                    <li class="flex gap-3 text-sm">
                        <div class="w-8 h-8 rounded-full bg-green-50 text-green-700 flex items-center justify-center shrink-0">
                            <x-icon name="id-card" size="sm" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-800">
                                <span class="font-mono font-medium">{{ $permit->permit_number }}</span>
                                {{ __('issued to') }}
                                <span class="font-medium">{{ $permit->driver?->full_name ?? __('Unknown driver') }}</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $permit->issue_date->format('d M Y') }} · {{ __('Valid until') }} {{ $permit->expiry_date->format('d M Y') }}</p>
                        </div>
                        <a href="{{ route('permits.show', $permit) }}" class="text-dpms-700 text-xs font-medium shrink-0 hover:underline">{{ __('View') }}</a>
                    </li>
                @empty
                    <li class="text-gray-500 text-sm py-4">{{ __('No permits issued yet.') }}</li>
                @endforelse
            </ul>
        </div>

        <div class="dpms-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-800">{{ __('Upcoming Expiries') }}</h3>
                <a href="{{ route('permits.renewals.index') }}" class="text-sm text-dpms-700 hover:underline">{{ __('Renewals') }}</a>
            </div>
            @if ($stats['expiring_soon'] > 0)
                <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mb-4">
                    @php
                        $expiryNotice = trans_choice(
                            ':count permit expiring within 30 days|:count permits expiring within 30 days',
                            $stats['expiring_soon'],
                            ['count' => $stats['expiring_soon']]
                        );
                    @endphp
                    {{ $expiryNotice }}
                </p>
            @endif
            <ul class="space-y-3">
                @forelse ($upcomingExpiry as $permit)
                    <li class="flex items-center justify-between text-sm border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                        <div>
                            <p class="font-medium text-gray-800">{{ $permit->driver?->full_name }}</p>
                            <p class="text-xs text-gray-500 font-mono">{{ $permit->permit_number }}</p>
                        </div>
                        <div class="text-end">
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $permit->days_left <= 15 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700' }}">
                                {{ $permit->days_left }} {{ __('days') }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $permit->expiry_date->format('Y-m-d') }}</p>
                        </div>
                    </li>
                @empty
                    <li class="text-gray-500 text-sm py-4">{{ __('No permits expiring in the next 60 days.') }}</li>
                @endforelse
            </ul>
        </div>
    </div>
</x-app-layout>
