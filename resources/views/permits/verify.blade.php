<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Verify permit') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="get" action="{{ route('permits.verify') }}" class="flex flex-wrap gap-2 items-end">
                    <div class="flex-1 min-w-[12rem]">
                        <label for="code" class="block text-sm text-gray-600 mb-1">{{ __('Permit number or reference') }}</label>
                        <input id="code" name="code" type="text" value="{{ old('code', $code) }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm" placeholder="{{ __('Scan result or pasted code') }}">
                    </div>
                    <x-primary-button type="submit">{{ __('Verify') }}</x-primary-button>
                </form>

                @if (($code ?? '') !== '' && $permit === null)
                    <div class="mt-6 p-4 bg-red-50 text-red-800 rounded-md">{{ __('No permit matched that reference.') }}</div>
                @endif

                @if ($permit)
                    @php
                        $valid = $permit->status === \App\Models\Permit::STATUS_VALID && $permit->expiry_date->gte(now()->startOfDay());
                    @endphp
                    <div class="mt-6 border-t pt-6 space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex px-2 py-1 text-xs font-bold rounded {{ $valid ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-900' }}">
                                {{ $valid ? __('Likely usable') : __('Not valid') }}
                            </span>
                            <span class="text-sm text-gray-600 uppercase">{{ $permit->status }}</span>
                        </div>
                        <dl class="grid sm:grid-cols-2 gap-3 text-sm">
                            <div><dt class="text-gray-500">{{ __('Permit') }}</dt><dd class="font-mono">{{ $permit->permit_number }}</dd></div>
                            <div><dt class="text-gray-500">{{ __('Driver') }}</dt><dd>{{ $permit->driver?->full_name }}</dd></div>
                            <div><dt class="text-gray-500">{{ __('Issue date') }}</dt><dd>{{ $permit->issue_date->format('Y-m-d') }}</dd></div>
                            <div><dt class="text-gray-500">{{ __('Expiry date') }}</dt><dd>{{ $permit->expiry_date->format('Y-m-d') }}</dd></div>
                            <div class="sm:col-span-2"><dt class="text-gray-500">{{ __('Department') }}</dt><dd>{{ $permit->driver?->department }}</dd></div>
                        </dl>
                        <p class="text-xs text-gray-500">{{ __('Confirm details against HR / Fleet records. This screen is informational.') }}</p>
                    </div>
                @elseif (($code ?? '') === '')
                    <p class="mt-4 text-sm text-gray-500">{{ __('Enter a permit number or scan QR payload once QR generation is enabled.') }}</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
