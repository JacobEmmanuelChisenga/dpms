<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Issue Permit') }}</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        @include('permits.issue._steps', ['currentStep' => 4])

        <div class="dpms-card p-6 sm:p-8 text-center">
            <div class="w-16 h-16 rounded-full bg-dpms-100 text-dpms-700 flex items-center justify-center mx-auto mb-4">
                <x-icon name="file-circle-plus" size="2xl" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Step 4: Generate permit') }}</h3>
            <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto">
                {{ __('This will create permit record :preview, generate the QR code, and prepare the printable PDF certificate.', ['preview' => 'DPMS-'.now()->format('Y').'-####']) }}
            </p>

            <dl class="text-sm text-left max-w-sm mx-auto mb-8 space-y-2">
                <div class="flex justify-between"><dt class="text-gray-500">{{ __('Driver') }}</dt><dd class="font-medium">{{ $driver->full_name }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">{{ __('Valid') }}</dt><dd>{{ $wizard['issue_date'] }} → {{ $wizard['expiry_date'] }}</dd></div>
            </dl>

            <form method="post" action="{{ route('permits.issue.generate.store') }}" class="flex flex-col sm:flex-row justify-center gap-3">
                @csrf
                <a href="{{ route('permits.issue.review') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">{{ __('Back') }}</a>
                <button type="submit" class="dpms-btn-primary gap-2 px-8 py-3">
                    <x-icon name="bolt" />
                    {{ __('Generate permit') }}
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
