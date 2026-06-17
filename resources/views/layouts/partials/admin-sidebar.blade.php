@php
    $user = auth()->user();
    $roleLabel = match ($user?->role) {
        \App\Models\User::ROLE_ADMIN => __('Administrator'),
        \App\Models\User::ROLE_FLEET_OFFICER => __('Fleet Officer'),
        \App\Models\User::ROLE_MANAGEMENT => __('Management'),
        default => __('User'),
    };

    $isActive = fn (array $patterns) => collect($patterns)->contains(fn ($p) => request()->routeIs($p));
@endphp

{{-- Mobile overlay --}}
<div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false"></div>

<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-dpms-sidebar flex flex-col text-white transform transition-transform duration-200 lg:z-30"
>
    <div class="px-5 py-5 border-b border-white/10">
        <x-sidebar-brand :tagline="__('Driver Permit Management')" />
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-sm">
        <a href="{{ route('dashboard') }}" class="dpms-sidebar-link {{ request()->routeIs('dashboard') ? 'dpms-sidebar-link-active' : '' }}">
            <x-icon name="gauge-high" class="opacity-90" />
            {{ __('Dashboard') }}
        </a>

        @can('viewAny', App\Models\Driver::class)
            <div x-data="{ open: {{ $isActive(['drivers.*']) ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" class="dpms-sidebar-link w-full justify-between {{ $isActive(['drivers.*']) ? 'dpms-sidebar-link-active' : '' }}">
                    <span class="flex items-center gap-3">
                        <x-icon name="users" />
                        {{ __('Drivers') }}
                    </span>
                    <x-icon name="chevron-down" size="sm" class="transition-transform" ::class="open && 'rotate-180'" />
                </button>
                <div x-show="open" x-cloak class="ml-4 mt-1 space-y-0.5 border-l border-white/20 pl-3">
                    <a href="{{ route('drivers.index') }}" class="dpms-sidebar-sublink {{ request()->routeIs('drivers.index') && ! request()->boolean('archived') ? 'dpms-sidebar-sublink-active' : '' }}">{{ __('All Drivers') }}</a>
                    @can('create', App\Models\Driver::class)
                        <a href="{{ route('drivers.create') }}" class="dpms-sidebar-sublink {{ request()->routeIs('drivers.create') ? 'dpms-sidebar-sublink-active' : '' }}">{{ __('Add Driver') }}</a>
                    @endcan
                    <a href="{{ route('permits.index', ['status' => 'expired']) }}" class="dpms-sidebar-sublink">{{ __('Expired Licenses') }}</a>
                    <a href="{{ route('drivers.index', ['archived' => 1]) }}" class="dpms-sidebar-sublink {{ request()->boolean('archived') ? 'dpms-sidebar-sublink-active' : '' }}">{{ __('Suspended / Archived') }}</a>
                </div>
            </div>
        @endcan

        @can('viewAny', App\Models\Permit::class)
            <div x-data="{ open: {{ $isActive(['permits.*']) && ! request()->routeIs('permits.verify') ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" class="dpms-sidebar-link w-full justify-between {{ $isActive(['permits.*']) && ! request()->routeIs('permits.verify') ? 'dpms-sidebar-link-active' : '' }}">
                    <span class="flex items-center gap-3">
                        <x-icon name="id-card" />
                        {{ __('Permits') }}
                    </span>
                    <x-icon name="chevron-down" size="sm" class="transition-transform" ::class="open && 'rotate-180'" />
                </button>
                <div x-show="open" x-cloak class="ml-4 mt-1 space-y-0.5 border-l border-white/20 pl-3">
                    <a href="{{ route('permits.index') }}" class="dpms-sidebar-sublink {{ request()->routeIs('permits.index') && ! request('status') ? 'dpms-sidebar-sublink-active' : '' }}">{{ __('All Permits') }}</a>
                    <a href="{{ route('permits.index', ['status' => 'valid']) }}" class="dpms-sidebar-sublink {{ request('status') === 'valid' ? 'dpms-sidebar-sublink-active' : '' }}">{{ __('Active Permits') }}</a>
                    <a href="{{ route('permits.index', ['status' => 'expired']) }}" class="dpms-sidebar-sublink {{ request('status') === 'expired' ? 'dpms-sidebar-sublink-active' : '' }}">{{ __('Expired Permits') }}</a>
                    <a href="{{ route('permits.index', ['status' => 'revoked']) }}" class="dpms-sidebar-sublink {{ request('status') === 'revoked' ? 'dpms-sidebar-sublink-active' : '' }}">{{ __('Revoked Permits') }}</a>
                    @can('create', App\Models\Permit::class)
                        <a href="{{ route('permits.issue') }}" class="dpms-sidebar-sublink {{ request()->routeIs('permits.issue*') ? 'dpms-sidebar-sublink-active' : '' }}">{{ __('Issue Permit') }}</a>
                    @endcan
                </div>
            </div>
        @endcan

        @can('viewAny', App\Models\User::class)
            <div x-data="{ open: {{ $isActive(['users.*']) ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" class="dpms-sidebar-link w-full justify-between {{ $isActive(['users.*']) ? 'dpms-sidebar-link-active' : '' }}">
                    <span class="flex items-center gap-3">
                        <x-icon name="user-group" />
                        {{ __('Users') }}
                    </span>
                    <x-icon name="chevron-down" size="sm" class="transition-transform" ::class="open && 'rotate-180'" />
                </button>
                <div x-show="open" x-cloak class="ml-4 mt-1 space-y-0.5 border-l border-white/20 pl-3">
                    <a href="{{ route('users.index') }}" class="dpms-sidebar-sublink {{ request()->routeIs('users.index') ? 'dpms-sidebar-sublink-active' : '' }}">{{ __('All Users') }}</a>
                    @can('create', App\Models\User::class)
                        <a href="{{ route('users.create') }}" class="dpms-sidebar-sublink {{ request()->routeIs('users.create') ? 'dpms-sidebar-sublink-active' : '' }}">{{ __('Add User') }}</a>
                    @endcan
                    <a href="{{ route('users.index') }}#roles" class="dpms-sidebar-sublink">{{ __('Roles & Permissions') }}</a>
                </div>
            </div>
        @endcan

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
                    <a href="{{ route('reports.index', ['type' => 'drivers']) }}" class="dpms-sidebar-sublink">{{ __('Driver Reports') }}</a>
                    <a href="{{ route('reports.index', ['type' => 'issuance']) }}" class="dpms-sidebar-sublink">{{ __('Permit Reports') }}</a>
                    <a href="{{ route('reports.index', ['type' => 'expired_permits']) }}" class="dpms-sidebar-sublink">{{ __('Expiry Reports') }}</a>
                    <a href="{{ route('reports.index', ['type' => 'active_permits']) }}" class="dpms-sidebar-sublink">{{ __('Monthly Reports') }}</a>
                </div>
            </div>
        @endcan

        <div x-data="{ open: {{ request()->routeIs('permits.verify') ? 'true' : 'false' }} }">
            <button type="button" @click="open = !open" class="dpms-sidebar-link w-full justify-between {{ request()->routeIs('permits.verify') ? 'dpms-sidebar-link-active' : '' }}">
                <span class="flex items-center gap-3">
                    <x-icon name="qrcode" />
                    {{ __('Verification') }}
                </span>
                <x-icon name="chevron-down" size="sm" class="transition-transform" ::class="open && 'rotate-180'" />
            </button>
            <div x-show="open" x-cloak class="ml-4 mt-1 space-y-0.5 border-l border-white/20 pl-3">
                <a href="{{ route('permits.verify') }}" class="dpms-sidebar-sublink {{ request()->routeIs('permits.verify') ? 'dpms-sidebar-sublink-active' : '' }}">{{ __('Verify Permit') }}</a>
                <a href="{{ route('permits.verify') }}" class="dpms-sidebar-sublink">{{ __('QR Verification') }}</a>
            </div>
        </div>

        @can('viewAny', App\Models\Driver::class)
            <div x-data="{ open: {{ $isActive(['archives.*']) ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" class="dpms-sidebar-link w-full justify-between {{ $isActive(['archives.*']) ? 'dpms-sidebar-link-active' : '' }}">
                    <span class="flex items-center gap-3">
                        <x-icon name="box-archive" />
                        {{ __('Archives') }}
                    </span>
                    <x-icon name="chevron-down" size="sm" class="transition-transform" ::class="open && 'rotate-180'" />
                </button>
                <div x-show="open" x-cloak class="ml-4 mt-1 space-y-0.5 border-l border-white/20 pl-3">
                    <a href="{{ route('archives.permits') }}" class="dpms-sidebar-sublink">{{ __('Old Permits') }}</a>
                    <a href="{{ route('archives.drivers') }}" class="dpms-sidebar-sublink">{{ __('Archived Drivers') }}</a>
                    <a href="{{ route('archives.index') }}" class="dpms-sidebar-sublink">{{ __('Historical Records') }}</a>
                </div>
            </div>
        @endcan

        @can('viewAuditLogs')
            <a href="{{ route('audit-logs.index') }}" class="dpms-sidebar-link {{ request()->routeIs('audit-logs.*') ? 'dpms-sidebar-link-active' : '' }}">
                <x-icon name="clipboard-list" />
                {{ __('Audit Logs') }}
            </a>
        @endcan

        @can('viewAny', App\Models\User::class)
            <div x-data="{ open: {{ $isActive(['settings.*']) ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" class="dpms-sidebar-link w-full justify-between {{ $isActive(['settings.*']) ? 'dpms-sidebar-link-active' : '' }}">
                    <span class="flex items-center gap-3">
                        <x-icon name="gear" />
                        {{ __('Settings') }}
                    </span>
                    <x-icon name="chevron-down" size="sm" class="transition-transform" ::class="open && 'rotate-180'" />
                </button>
                <div x-show="open" x-cloak class="ml-4 mt-1 space-y-0.5 border-l border-white/20 pl-3">
                    <a href="{{ route('settings.index', ['section' => 'company']) }}" class="dpms-sidebar-sublink">{{ __('Company Information') }}</a>
                    <a href="{{ route('settings.index', ['section' => 'signature']) }}" class="dpms-sidebar-sublink">{{ __('Signature Upload') }}</a>
                    <a href="{{ route('settings.index', ['section' => 'preferences']) }}" class="dpms-sidebar-sublink">{{ __('System Preferences') }}</a>
                    <a href="{{ route('settings.index', ['section' => 'permit-design']) }}" class="dpms-sidebar-sublink">{{ __('Permit Design') }}</a>
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

    <!-- <div class="px-5 py-4 border-t border-white/10">
        <div class="flex items-center gap-2 text-white/80">
            <img src="{{ asset('images/zafficologo.jpg') }}" alt="ZAFFICO" class="h-8 w-8 rounded object-contain bg-white p-0.5 shrink-0" />
            <div>
                <div class="text-xs font-semibold">ZAFFICO</div>
                <div class="text-[9px] text-white/50 leading-tight">{{ __('Forestry. Our Heritage. Our Future.') }}</div>
            </div>
        </div>
    </div> -->
</aside>
