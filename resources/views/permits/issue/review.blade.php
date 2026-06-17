<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Issue Permit') }}</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        @include('permits.issue._steps', ['currentStep' => 3])

        <div class="dpms-card p-6 sm:p-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ __('Step 3: Approve issuance') }}</h3>
            <p class="text-sm text-gray-500 mb-6">{{ __('Review details before generating the permit record.') }}</p>

            <dl class="grid sm:grid-cols-2 gap-4 text-sm mb-6 p-4 bg-slate-50 rounded-xl border border-gray-100">
                <div>
                    <dt class="text-gray-500">{{ __('Driver') }}</dt>
                    <dd class="font-medium text-gray-900">{{ $driver->full_name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Employee ID') }}</dt>
                    <dd class="font-mono">{{ $driver->employee_id }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Department') }}</dt>
                    <dd>{{ $driver->department }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('License') }}</dt>
                    <dd>{{ $driver->license_number }} ({{ $driver->license_class }})</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Issue date') }}</dt>
                    <dd>{{ $wizard['issue_date'] }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Expiry date') }}</dt>
                    <dd>{{ $wizard['expiry_date'] }}</dd>
                </div>
            </dl>

            @if (! empty($wizard['notes']))
                <p class="text-sm text-gray-600 mb-6"><span class="font-medium">{{ __('Notes') }}:</span> {{ $wizard['notes'] }}</p>
            @endif

            <form method="post" action="{{ route('permits.issue.review.store') }}" class="space-y-5 border-t border-gray-100 pt-6">
                @csrf
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="approved" value="1" required class="mt-1 rounded border-gray-300 text-dpms-700 focus:ring-dpms-600" />
                    <span class="text-sm text-gray-700">
                        {{ __('I confirm this driver is authorised to receive a permit for the stated period and approve issuance as the issuing officer.') }}
                    </span>
                </label>
                <x-input-error :messages="$errors->get('approved')" class="mt-2" />

                <div class="flex justify-between gap-3">
                    <a href="{{ route('permits.issue.validity') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 inline-flex items-center gap-1">
                        <x-icon name="arrow-left" size="sm" /> {{ __('Back') }}
                    </a>
                    <button type="submit" class="dpms-btn-primary gap-2">
                        <x-icon name="circle-check" />
                        {{ __('Approve & continue') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
