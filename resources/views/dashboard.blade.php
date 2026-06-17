@php
    $pageTitle = __('Dashboard');
@endphp

<x-app-layout>
    {{-- Welcome banner --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ __('Welcome back, :name!', ['name' => $user->name]) }}</h2>
            <p class="text-gray-500 mt-1 text-sm">{{ __('Here is an overview of the Driver Permit Management System.') }}</p>
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
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
        @can('viewAny', App\Models\Driver::class)
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
                <a href="{{ route('drivers.index') }}" class="mt-4 text-sm text-dpms-700 font-medium hover:underline inline-flex items-center gap-1">{{ __('View all drivers') }} →</a>
            </div>
        @endcan

        @can('viewAny', App\Models\Permit::class)
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
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Expired Permits') }}</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['permits_expired']) }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                        <x-icon name="clock" size="lg" />
                    </div>
                </div>
                <a href="{{ route('permits.index', ['status' => 'expired']) }}" class="mt-4 text-sm text-dpms-700 font-medium hover:underline inline-flex items-center gap-1">{{ __('View expired permits') }} →</a>
            </div>

            <div class="dpms-stat-card">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Revoked Permits') }}</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['permits_revoked']) }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-red-100 text-red-700 flex items-center justify-center">
                        <x-icon name="ban" size="lg" />
                    </div>
                </div>
                <a href="{{ route('permits.index', ['status' => 'revoked']) }}" class="mt-4 text-sm text-dpms-700 font-medium hover:underline inline-flex items-center gap-1">{{ __('View revoked permits') }} →</a>
            </div>
        @endcan

        @can('viewAny', App\Models\User::class)
            <div class="dpms-stat-card">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Total Users') }}</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['users']) }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-dpms-800 text-white flex items-center justify-center">
                        <x-icon name="user-group" size="lg" />
                    </div>
                </div>
                <a href="{{ route('users.index') }}" class="mt-4 text-sm text-dpms-700 font-medium hover:underline inline-flex items-center gap-1">{{ __('Manage users') }} →</a>
            </div>
        @endcan
    </div>

    {{-- Charts row --}}
    @can('viewAny', App\Models\Permit::class)
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
            <div class="dpms-card p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4">{{ __('Permit Overview') }}</h3>
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="w-48 h-48 relative">
                        <canvas id="permitDonutChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-2xl font-bold text-gray-800">{{ $stats['permits_active'] }}</span>
                            <span class="text-xs text-gray-500">{{ __('Active') }}</span>
                        </div>
                    </div>
                    <ul class="space-y-3 text-sm flex-1">
                        <li class="flex items-center justify-between"><span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-500"></span>{{ __('Active Permits') }}</span><span class="font-medium">{{ $permitChart['active'] }} ({{ round($permitChart['active'] / $permitTotal * 100) }}%)</span></li>
                        <li class="flex items-center justify-between"><span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-amber-500"></span>{{ __('Expired Permits') }}</span><span class="font-medium">{{ $permitChart['expired'] }} ({{ round($permitChart['expired'] / $permitTotal * 100) }}%)</span></li>
                        <li class="flex items-center justify-between"><span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-500"></span>{{ __('Revoked Permits') }}</span><span class="font-medium">{{ $permitChart['revoked'] }} ({{ round($permitChart['revoked'] / $permitTotal * 100) }}%)</span></li>
                        <li class="flex items-center justify-between"><span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-gray-400"></span>{{ __('Expiring Soon (30d)') }}</span><span class="font-medium">{{ $permitChart['pending'] }}</span></li>
                    </ul>
                </div>
            </div>

            <div class="dpms-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-800">{{ __('Permit Issuance (This Year)') }}</h3>
                    <form method="get" class="flex items-center gap-2">
                        <select name="year" onchange="this.form.submit()" class="text-sm rounded-lg border-gray-300 py-1">
                            @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" @selected($chartYear === $y)>{{ $y }}</option>
                            @endfor
                        </select>
                    </form>
                </div>
                <div class="h-56">
                    <canvas id="permitLineChart"></canvas>
                </div>
            </div>
        </div>
    @endcan

    {{-- Bottom row --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        @can('viewAuditLogs')
            <div class="dpms-card p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4">{{ __('Recent Activities') }}</h3>
                <ul class="space-y-4">
                    @forelse ($recentActivities as $log)
                        <li class="flex gap-3 text-sm">
                            <div class="w-8 h-8 rounded-full bg-dpms-50 text-dpms-700 flex items-center justify-center shrink-0">
                                <x-icon name="circle-info" size="sm" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-gray-800">{{ $log->actor?->name ?? __('System') }} — <span class="font-medium">{{ $log->event }}</span> {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $log->logged_at?->diffForHumans() }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="text-gray-500 text-sm py-4">{{ __('No recent activity recorded.') }}</li>
                    @endforelse
                </ul>
                <a href="{{ route('audit-logs.index') }}" class="mt-4 inline-block text-sm text-dpms-700 font-medium hover:underline">{{ __('View all activities') }} →</a>
            </div>
        @endcan

        @can('viewAny', App\Models\Permit::class)
            <div class="dpms-card p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4">{{ __('Upcoming Expiry') }}</h3>
                <div class="overflow-x-auto">
                    <table class="w-full dpms-table">
                        <thead>
                            <tr>
                                <th>{{ __('Driver Name') }}</th>
                                <th>{{ __('Permit Number') }}</th>
                                <th>{{ __('Expiry Date') }}</th>
                                <th>{{ __('Days Left') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($upcomingExpiry as $permit)
                                <tr>
                                    <td>{{ $permit->driver?->full_name }}</td>
                                    <td class="font-mono text-xs">{{ $permit->permit_number }}</td>
                                    <td>{{ $permit->expiry_date->format('Y-m-d') }}</td>
                                    <td>
                                        @php $days = $permit->days_left; @endphp
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $days <= 15 ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $days }} {{ __('days') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-gray-500 py-6">{{ __('No permits expiring in the next 60 days.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('reports.index', ['type' => 'expired_permits']) }}" class="mt-4 inline-block text-sm text-dpms-700 font-medium hover:underline">{{ __('View all expiring permits') }} →</a>
            </div>
        @endcan
    </div>

    @push('scripts')
    @php
        $donutChartLabels = [__('Active'), __('Expired'), __('Revoked'), __('Expiring soon')];
        $donutChartData = array_values($permitChart);
        $lineChartLabel = __('Permits issued');
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @can('viewAny', App\Models\Permit::class)
            const donutCtx = document.getElementById('permitDonutChart');
            if (donutCtx) {
                new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($donutChartLabels),
                        datasets: [{
                            data: @json($donutChartData),
                            backgroundColor: ['#22c55e', '#f59e0b', '#ef4444', '#9ca3af'],
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        cutout: '70%',
                        plugins: { legend: { display: false } },
                    },
                });
            }

            const lineCtx = document.getElementById('permitLineChart');
            if (lineCtx) {
                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: @json($monthlyLabels),
                        datasets: [{
                            label: @json($lineChartLabel),
                            data: @json($monthlyData),
                            borderColor: '#15803d',
                            backgroundColor: 'rgba(21, 128, 61, 0.1)',
                            fill: true,
                            tension: 0.3,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                        plugins: { legend: { display: false } },
                    },
                });
            }
            @endcan
        });
    </script>
    @endpush
</x-app-layout>
