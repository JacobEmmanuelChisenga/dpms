<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Permit issued') }}</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        @include('permits.issue._steps', ['currentStep' => 5])

        <div class="dpms-card p-6 sm:p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 rounded-full bg-green-100 text-green-700 flex items-center justify-center mx-auto mb-4">
                    <x-icon name="circle-check" size="2xl" />
                </div>
                <h3 class="text-xl font-bold text-gray-900">{{ __('Step 5: Print & distribute') }}</h3>
                <p class="text-sm text-gray-500 mt-2">
                    {{ __(':number issued to :name', ['number' => $permit->permit_number, 'name' => $permit->driver?->full_name]) }}
                </p>
            </div>

            <div class="grid sm:grid-cols-2 gap-6 mb-8">
                <div class="text-center p-4 border border-gray-100 rounded-xl">
                    <x-permit-qr :permit="$permit" />
                    <p class="text-xs text-gray-500 mt-2">{{ __('Scan for verification') }}</p>
                </div>
                <div class="text-sm space-y-2 flex flex-col justify-center">
                    <p><span class="text-gray-500">{{ __('Permit number') }}:</span> <span class="font-mono font-semibold">{{ $permit->permit_number }}</span></p>
                    <p><span class="text-gray-500">{{ __('Valid until') }}:</span> {{ $permit->expiry_date->format('d M Y') }}</p>
                    <p><span class="text-gray-500">{{ __('Issued by') }}:</span> {{ $permit->issuer?->name }}</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-center gap-3">
                <a href="{{ route('permits.certificate', $permit) }}" target="_blank" rel="noopener" class="dpms-btn-primary gap-2 justify-center py-3">
                    <x-icon name="print" />
                    {{ __('Print PDF certificate') }}
                </a>
                <a href="{{ route('permits.show', $permit) }}" class="inline-flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    {{ __('View permit details') }}
                </a>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex flex-wrap justify-center gap-4 text-sm">
                <a href="{{ route('permits.issue') }}" class="text-dpms-700 font-medium hover:underline">{{ __('Issue another permit') }}</a>
                <a href="{{ route('permits.index') }}" class="text-gray-500 hover:text-gray-700">{{ __('Back to permits list') }}</a>
            </div>
        </div>
    </div>
</x-app-layout>
