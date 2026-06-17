@php
    $pageTitle = $pageTitle ?? match (true) {
        request()->routeIs('dashboard') => __('Dashboard'),
        request()->routeIs('drivers.*') => __('Drivers'),
        request()->routeIs('permits.renewals.*') => __('Renewals'),
        request()->routeIs('permits.issue*') => __('Issue Permit'),
        request()->routeIs('permits.*') && ! request()->routeIs('permits.verify') => __('Permits'),
        request()->routeIs('permits.verify') => __('Verification'),
        request()->routeIs('users.*') => __('Users'),
        request()->routeIs('reports.*') => __('Reports'),
        request()->routeIs('audit-logs.*') => __('Audit Logs'),
        request()->routeIs('archives.*') => __('Archives'),
        request()->routeIs('settings.*') => __('Settings'),
        request()->routeIs('profile.*') => __('Profile'),
        default => config('app.name'),
    };
    $user = auth()->user();
    $roleLabel = match ($user?->role) {
        \App\Models\User::ROLE_ADMIN => __('Administrator'),
        \App\Models\User::ROLE_FLEET_OFFICER => __('Fleet Management Officer'),
        \App\Models\User::ROLE_MANAGEMENT => __('Management'),
        default => __('User'),
    };
@endphp

<header class="sticky top-0 z-30 bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between shrink-0">
    <div class="flex items-center gap-4">
        <button type="button" @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100" aria-label="{{ __('Toggle menu') }}">
            <x-icon name="bars" size="lg" />
        </button>
        <h1 class="text-lg font-semibold text-gray-800">{{ $pageTitle }}</h1>
    </div>

    <div class="flex items-center gap-3 sm:gap-5">
        <div x-data="{ open: false }" class="relative">
            <button type="button" @click="open = !open" class="flex items-center gap-3 pl-2 pr-1 py-1 rounded-lg hover:bg-gray-50">
                <div class="w-9 h-9 rounded-full bg-dpms-700 text-white flex items-center justify-center text-sm font-semibold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="hidden sm:block text-left">
                    <div class="text-sm font-medium text-gray-800 leading-tight">{{ $user->name }}</div>
                    <div class="text-xs text-gray-500">{{ $roleLabel }}</div>
                </div>
                <x-icon name="chevron-down" size="sm" class="text-gray-400 hidden sm:inline" />
            </button>
            <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <x-icon name="user" size="sm" class="text-gray-400" />
                    {{ __('Profile') }}
                </a>
                @can('viewAny', App\Models\User::class)
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <x-icon name="gear" size="sm" class="text-gray-400" />
                        {{ __('Settings') }}
                    </a>
                @endcan
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <x-icon name="right-from-bracket" size="sm" />
                        {{ __('Logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
