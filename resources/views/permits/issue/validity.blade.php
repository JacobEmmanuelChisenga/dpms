<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Issue Permit') }}</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        @include('permits.issue._steps', ['currentStep' => 2])

        <div class="dpms-card p-6 sm:p-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ __('Step 2: Set validity period') }}</h3>
            <p class="text-sm text-gray-500 mb-6">{{ __('Permit for') }} <span class="font-medium text-gray-800">{{ $driver->full_name }}</span> ({{ $driver->employee_id }})</p>

            <form method="post" action="{{ route('permits.issue.validity.store') }}" class="space-y-5" x-data="{
                issueDate: @js($issueDate),
                expiryDate: @js($expiryDate),
                setMonths(m) {
                    const start = new Date(this.issueDate);
                    const end = new Date(start);
                    end.setMonth(end.getMonth() + m);
                    this.expiryDate = end.toISOString().slice(0, 10);
                }
            }">
                @csrf

                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="setMonths(6)" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 hover:bg-gray-50">{{ __('6 months') }}</button>
                    <button type="button" @click="setMonths(12)" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 hover:bg-gray-50">{{ __('12 months') }}</button>
                    <button type="button" @click="setMonths(24)" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 hover:bg-gray-50">{{ __('24 months') }}</button>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="issue_date" class="dpms-label">{{ __('Issue date') }}</label>
                        <input id="issue_date" name="issue_date" type="date" x-model="issueDate" required class="dpms-input mt-1" />
                        <x-input-error :messages="$errors->get('issue_date')" class="mt-2" />
                    </div>
                    <div>
                        <label for="expiry_date" class="dpms-label">{{ __('Expiry date') }}</label>
                        <input id="expiry_date" name="expiry_date" type="date" x-model="expiryDate" required class="dpms-input mt-1" />
                        <x-input-error :messages="$errors->get('expiry_date')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <label for="notes" class="dpms-label">{{ __('Internal notes') }} <span class="text-gray-400 font-normal">({{ __('optional') }})</span></label>
                    <textarea id="notes" name="notes" rows="2" class="dpms-input mt-1">{{ $notes }}</textarea>
                </div>

                <div class="flex justify-between gap-3 pt-2">
                    <a href="{{ route('permits.issue') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 inline-flex items-center gap-1">
                        <x-icon name="arrow-left" size="sm" /> {{ __('Back') }}
                    </a>
                    <button type="submit" class="dpms-btn-primary">
                        {{ __('Continue to approval') }}
                        <x-icon name="arrow-right" size="sm" class="ms-1" />
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
