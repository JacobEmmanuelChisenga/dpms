<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Issue permit') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="post" action="{{ route('permits.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="driver_id" :value="__('Driver')" />
                        <select id="driver_id" name="driver_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="">{{ __('Select driver') }}</option>
                            @foreach ($drivers as $d)
                                <option value="{{ $d->id }}" @selected(old('driver_id', $driver?->id) == $d->id)>{{ $d->full_name }} — {{ $d->employee_id }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('driver_id')" class="mt-2" />
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="issue_date" :value="__('Issue date')" />
                            <x-text-input id="issue_date" name="issue_date" type="date" class="mt-1 block w-full" :value="old('issue_date', now()->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('issue_date')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="expiry_date" :value="__('Expiry date')" />
                            <x-text-input id="expiry_date" name="expiry_date" type="date" class="mt-1 block w-full" :value="old('expiry_date')" required />
                            <x-input-error :messages="$errors->get('expiry_date')" class="mt-2" />
                        </div>
                    </div>
                    <p class="text-sm text-gray-500">{{ __('A unique permit number is generated automatically. QR and printable PDF follow in a later step.') }}</p>
                    <div class="flex gap-2">
                        <x-primary-button>{{ __('Issue permit') }}</x-primary-button>
                        <a href="{{ route('permits.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
