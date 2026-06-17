<x-guest-layout>
    <div class="dpms-auth-card">
        <div class="mb-6">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm text-dpms-700 hover:underline mb-4">
                <x-icon name="arrow-left" size="sm" />
                {{ __('Back to sign in') }}
            </a>
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Reset password') }}</h2>
            <p class="mt-2 text-sm text-gray-500">
                {{ __('Enter your email and we will send you a link to choose a new password.') }}
            </p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="dpms-label">{{ __('Email address') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="dpms-input mt-1" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <button type="submit" class="dpms-btn-primary w-full py-3">
                {{ __('Email reset link') }}
            </button>
        </form>
    </div>
</x-guest-layout>
