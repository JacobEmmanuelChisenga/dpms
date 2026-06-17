<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Issue Permit') }}</h2>
            <form method="post" action="{{ route('permits.issue.cancel') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">{{ __('Cancel workflow') }}</button>
            </form>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        @include('permits.issue._steps', ['currentStep' => 1])

        <div class="dpms-card p-6 sm:p-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ __('Step 1: Select driver') }}</h3>
            <p class="text-sm text-gray-500 mb-6">{{ __('Choose the driver who will receive this permit.') }}</p>

            <form method="post" action="{{ route('permits.issue.driver') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="driver_id" class="dpms-label">{{ __('Driver') }}</label>
                    <select id="driver_id" name="driver_id" required class="dpms-input mt-1">
                        <option value="">{{ __('Select driver…') }}</option>
                        @foreach ($drivers as $d)
                            <option value="{{ $d->id }}" @selected((string) $selectedDriverId === (string) $d->id)>
                                {{ $d->full_name }} — {{ $d->employee_id }} ({{ $d->department }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('driver_id')" class="mt-2" />
                </div>

                @if ($drivers->isEmpty())
                    <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3">
                        {{ __('No drivers are available for a new permit. Everyone registered may already hold a valid permit, or you need to register a driver first.') }}
                        @can('create', App\Models\Driver::class)
                            <a href="{{ route('drivers.create') }}" class="font-medium underline">{{ __('Register a driver') }}</a>
                        @endcan
                    </p>
                @endif

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('permits.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">{{ __('Back') }}</a>
                    <button type="submit" class="dpms-btn-primary" @disabled($drivers->isEmpty())>
                        {{ __('Continue') }}
                        <x-icon name="arrow-right" size="sm" class="ms-1" />
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
