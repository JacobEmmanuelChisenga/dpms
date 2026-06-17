<x-guest-layout>
    <div class="dpms-auth-card">
        <div class="mb-8 text-center lg:text-left">
            <div class="hidden lg:flex items-center gap-3 mb-6">
                <img
                    src="{{ asset('images/zafficologo.jpg') }}"
                    alt="{{ __('ZAFFICO') }}"
                    class="h-12 w-12 rounded-lg object-contain bg-white border border-gray-100 shadow-sm p-1"
                />
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-dpms-700">ZAFFICO</p>
                    <h2 class="text-xl font-bold text-gray-900">{{ __('Sign in to DPMS') }}</h2>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 lg:hidden">{{ __('Sign in') }}</h2>
            <p class="mt-2 text-sm text-gray-500">
                {{ __('Enter your credentials to access the Driver Permit Management System.') }}
            </p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="dpms-label">{{ __('Email address') }}</label>
                <div class="relative mt-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none">
                        <x-icon name="envelope" size="sm" />
                    </span>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        class="dpms-input pl-10"
                        placeholder="name@zaffico.co.zm"
                    />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <label for="password" class="dpms-label">{{ __('Password') }}</label>
                <div class="relative mt-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none">
                        <x-icon name="lock" size="sm" />
                    </span>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="dpms-input pl-10"
                        placeholder="••••••••"
                    />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between gap-4">
                <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="rounded border-gray-300 text-dpms-700 focus:ring-dpms-600"
                    />
                    <span class="text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-dpms-700 hover:text-dpms-800 hover:underline shrink-0">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <button type="submit" class="dpms-btn-primary w-full py-3 text-base gap-2">
                <x-icon name="right-to-bracket" />
                {{ __('Sign in') }}
            </button>
        </form>

        <p class="mt-8 text-center text-xs text-gray-400">
            {{ __('Authorized personnel only. Activity may be logged.') }}
        </p>
    </div>
</x-guest-layout>
