<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? __('Sign in') }} — {{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-800">
        <div class="min-h-screen lg:grid lg:grid-cols-2">
            {{-- Brand panel --}}
            <div class="dpms-auth-brand relative hidden lg:flex flex-col justify-between p-10 xl:p-14 text-white overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-dpms-900 via-dpms-sidebar to-dpms-950"></div>
                <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-zaffico-gold/10 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 rounded-full bg-white/5 blur-3xl"></div>

                <div class="relative z-10">
                    <img
                        src="{{ asset('images/zafficologo.jpg') }}"
                        alt="{{ __('ZAFFICO') }}"
                        class="h-24 w-auto max-w-[220px] object-contain bg-white rounded-xl p-3 shadow-lg"
                    />
                </div>

                <div class="relative z-10 space-y-4 max-w-md">
                    <h1 class="text-3xl xl:text-4xl font-bold leading-tight">
                        {{ __('Driver Permit Management System') }}
                    </h1>
                    <p class="text-white/80 text-base leading-relaxed">
                        {{ __('Secure permit issuance, driver records, and verification for ZAFFICO fleet operations.') }}
                    </p>
                    <ul class="space-y-3 pt-2 text-sm text-white/75">
                        <li class="flex items-center gap-3">
                            <x-icon name="id-card" class="text-zaffico-gold" />
                            {{ __('Digital permits with QR verification') }}
                        </li>
                        <li class="flex items-center gap-3">
                            <x-icon name="users" class="text-zaffico-gold" />
                            {{ __('Centralized driver registry') }}
                        </li>
                        <li class="flex items-center gap-3">
                            <x-icon name="shield-halved" class="text-zaffico-gold" />
                            {{ __('Role-based access and audit trail') }}
                        </li>
                    </ul>
                </div>

                <p class="relative z-10 text-xs text-white/50">
                    &copy; {{ now()->year }} ZAFFICO PLC. {{ __('All rights reserved.') }}
                </p>
            </div>

            {{-- Form panel --}}
            <div class="flex flex-col min-h-screen bg-slate-50">
                <div class="lg:hidden dpms-auth-brand px-6 py-8 text-center text-white">
                    <img
                        src="{{ asset('images/zafficologo.jpg') }}"
                        alt="{{ __('ZAFFICO') }}"
                        class="h-16 w-auto mx-auto object-contain bg-white rounded-lg p-2 shadow-md mb-3"
                    />
                    <p class="text-sm font-semibold">{{ config('app.name') }}</p>
                    <p class="text-xs text-white/70 mt-1">{{ __('Driver Permit Management System') }}</p>
                </div>

                <div class="flex-1 flex items-center justify-center p-6 sm:p-10">
                    <div class="w-full max-w-md">
                        {{ $slot }}
                    </div>
                </div>

                <p class="text-center text-xs text-gray-400 pb-6 lg:hidden">
                    &copy; {{ now()->year }} ZAFFICO PLC
                </p>
            </div>
        </div>
    </body>
</html>
