@php
    $isActive = fn (array $patterns) => collect($patterns)->contains(fn ($p) => request()->routeIs($p));
@endphp

<div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false"></div>

<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-dpms-sidebar flex flex-col text-white transform transition-transform duration-200 lg:z-30"
>
    <div class="px-5 py-5 border-b border-white/10">
        <x-sidebar-brand :tagline="__('Management overview')" />
        <p class="mt-2 text-[10px] text-white/50 uppercase tracking-wide flex items-center gap-1">
            <x-icon name="eye" size="sm" /> {{ __('Read-only') }}
        </p>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-sm">
        <a href="{{ route('dashboard') }}" class="dpms-sidebar-link {{ request()->routeIs('dashboard') ? 'dpms-sidebar-link-active' : '' }}">
            <x-icon name="gauge-high" />
            {{ __('Dashboard') }}
        </a>

        <a href="{{ route('drivers.index') }}" class="dpms-sidebar-link {{ request()->routeIs('drivers.*') ? 'dpms-sidebar-link-active' : '' }}">
            <x-icon name="users" />
            {{ __('Drivers') }}
        </a>

        <a href="{{ route('permits.index') }}" class="dpms-sidebar-link {{ request()->routeIs('permits.index', 'permits.show') ? 'dpms-sidebar-link-active' : '' }}">
            <x-icon name="id-card" />
            {{ __('Permits') }}
        </a>

        <a href="{{ route('permits.verify') }}" class="dpms-sidebar-link {{ request()->routeIs('permits.verify') ? 'dpms-sidebar-link-active' : '' }}">
            <x-icon name="qrcode" />
            {{ __('Verification') }}
        </a>

        @can('viewReports')
            <div x-data="{ open: {{ $isActive(['reports.*']) ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" class="dpms-sidebar-link w-full justify-between {{ $isActive(['reports.*']) ? 'dpms-sidebar-link-active' : '' }}">
                    <span class="flex items-center gap-3">
                        <x-icon name="chart-column" />
                        {{ __('Reports') }}
                    </span>
                    <x-icon name="chevron-down" size="sm" class="transition-transform" ::class="open && 'rotate-180'" />
                </button>
                <div x-show="open" x-cloak class="ml-4 mt-1 space-y-0.5 border-l border-white/20 pl-3">
                    <a href="{{ route('reports.index', ['type' => 'active_permits']) }}" class="dpms-sidebar-sublink">{{ __('Active Permits') }}</a>
                    <a href="{{ route('reports.index', ['type' => 'expired_permits']) }}" class="dpms-sidebar-sublink">{{ __('Expired Permits') }}</a>
                    <a href="{{ route('reports.index', ['type' => 'drivers']) }}" class="dpms-sidebar-sublink">{{ __('Driver Report') }}</a>
                    <a href="{{ route('reports.index', ['type' => 'issuance']) }}" class="dpms-sidebar-sublink">{{ __('Monthly Issuance') }}</a>
                </div>
            </div>
        @endcan

        <div class="pt-4 mt-4 border-t border-white/10 space-y-1">
            <a href="{{ route('profile.edit') }}" class="dpms-sidebar-link {{ request()->routeIs('profile.*') ? 'dpms-sidebar-link-active' : '' }}">
                <x-icon name="user" />
                {{ __('Profile') }}
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dpms-sidebar-link w-full text-left">
                    <x-icon name="right-from-bracket" />
                    {{ __('Logout') }}
                </button>
            </form>
        </div>
    </nav>

    <div class="px-5 py-4 border-t border-white/10">
        <div class="flex items-center gap-2 text-white/80">
            <img src="{{ asset('images/zafficologo.jpg') }}" alt="ZAFFICO" class="h-8 w-8 rounded object-contain bg-white p-0.5 shrink-0" />
            <div>
                <div class="text-xs font-semibold">ZAFFICO</div>
                <div class="text-[9px] text-white/50 leading-tight">{{ __('Forestry. Our Heritage. Our Future.') }}</div>
            </div>
        </div>
    </div>
</aside>
