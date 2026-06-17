<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }} — {{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-slate-100 text-gray-800" x-data="{ sidebarOpen: false }">
        @if (auth()->user()?->isAdmin())
            @include('layouts.partials.admin-sidebar')
        @elseif (auth()->user()?->usesManagementLayout())
            @include('layouts.partials.management-sidebar')
        @else
            @include('layouts.partials.fleet-sidebar')
        @endif

        {{-- Main column: full width beside fixed sidebar (pl-64 = sidebar w-64) --}}
        <div class="flex flex-col min-h-screen w-full lg:pl-64">
            @include('layouts.partials.admin-topbar')

            @isset($header)
                <div class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 py-4 shrink-0">
                    {{ $header }}
                </div>
            @endisset

            <main class="flex-1 w-full max-w-full p-4 sm:p-6 lg:p-8">
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                {{ $slot }}
            </main>

            @include('layouts.partials.admin-footer')
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        @stack('scripts')
    </body>
</html>
