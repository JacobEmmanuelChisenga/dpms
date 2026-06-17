<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Permit') }}</h2>
                <p class="text-sm text-gray-500 font-mono mt-1">{{ $permit->permit_number }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('permits.certificate', $permit) }}" target="_blank" rel="noopener" class="inline-flex items-center px-3 py-2 bg-emerald-700 text-white text-xs font-semibold uppercase rounded-md">{{ __('PDF certificate') }}</a>
                @can('update', $permit)
                    <a href="{{ route('permits.edit', $permit) }}" class="inline-flex items-center px-3 py-2 bg-gray-800 text-white text-xs font-semibold uppercase rounded-md">{{ __('Edit') }}</a>
                @endcan
                @can('revoke', $permit)
                    @if ($permit->status !== \App\Models\Permit::STATUS_REVOKED)
                        <form method="post" action="{{ route('permits.revoke', $permit) }}" onsubmit="return confirm(@json(__('Revoke this permit?')));">
                            @csrf
                            <x-danger-button type="submit">{{ __('Revoke') }}</x-danger-button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-50 text-green-800 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 grid md:grid-cols-2 gap-6">
                <div class="space-y-2 text-sm">
                    <h3 class="font-semibold text-gray-800">{{ __('Validity') }}</h3>
                    <p><span class="text-gray-500">{{ __('Status') }}:</span> <span class="uppercase font-semibold">{{ $permit->status }}</span></p>
                    <p><span class="text-gray-500">{{ __('Issue date') }}:</span> {{ $permit->issue_date->format('Y-m-d') }}</p>
                    <p><span class="text-gray-500">{{ __('Expiry date') }}:</span> {{ $permit->expiry_date->format('Y-m-d') }}</p>
                    <p><span class="text-gray-500">{{ __('Issued by') }}:</span> {{ $permit->issuer?->name ?? '—' }}</p>
                    <p class="pt-2 text-gray-600">{{ __('Verification link') }}:<br>
                        <a class="font-mono text-indigo-600 break-all" href="{{ route('permits.verify', ['code' => $permit->permit_number]) }}">{{ route('permits.verify', ['code' => $permit->permit_number]) }}</a>
                    </p>
                </div>
                <div class="space-y-4 text-sm">
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">{{ __('QR code') }}</h3>
                        <x-permit-qr :permit="$permit" size="w-44 h-44" class="shadow-sm" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ __('Driver') }}</h3>
                        @if ($permit->driver)
                            <p class="mt-1">{{ $permit->driver->full_name }}</p>
                            <p class="text-gray-500">{{ $permit->driver->department }} · {{ __('NRC') }} {{ $permit->driver->nrc }}</p>
                            @can('view', $permit->driver)
                                <a href="{{ route('drivers.show', $permit->driver) }}" class="text-indigo-600 text-sm inline-block mt-1">{{ __('Open driver record') }}</a>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>

            @can('delete', $permit)
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-red-100">
                    <form method="post" action="{{ route('permits.destroy', $permit) }}" onsubmit="return confirm(@json(__('Delete this permit record permanently?')));" class="flex flex-wrap gap-4 items-center justify-between">
                        @csrf
                        @method('delete')
                        <p class="text-sm text-gray-600">{{ __('Delete only if entered in error — prefer revoke for cancellations.') }}</p>
                        <x-danger-button type="submit">{{ __('Delete permit record') }}</x-danger-button>
                    </form>
                </div>
            @endcan

            <div class="text-sm">
                <a href="{{ route('permits.index') }}" class="text-indigo-600 hover:underline">{{ __('Back to permits') }}</a>
            </div>
        </div>
    </div>
</x-app-layout>
