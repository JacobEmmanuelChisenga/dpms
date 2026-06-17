<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit permit') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="post" action="{{ route('permits.update', $permit) }}" class="space-y-4">
                    @csrf
                    @method('patch')
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Permit number') }}: <span class="font-mono font-semibold">{{ $permit->permit_number }}</span></p>
                        <p class="text-sm text-gray-600 mt-1">{{ __('Driver') }}: {{ $permit->driver?->full_name }}</p>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="issue_date" :value="__('Issue date')" />
                            <x-text-input id="issue_date" name="issue_date" type="date" class="mt-1 block w-full" :value="old('issue_date', $permit->issue_date->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('issue_date')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="expiry_date" :value="__('Expiry date')" />
                            <x-text-input id="expiry_date" name="expiry_date" type="date" class="mt-1 block w-full" :value="old('expiry_date', $permit->expiry_date->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('expiry_date')" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="valid" @selected(old('status', $permit->status) === 'valid')>{{ __('Valid') }}</option>
                            <option value="expired" @selected(old('status', $permit->status) === 'expired')>{{ __('Expired') }}</option>
                            <option value="revoked" @selected(old('status', $permit->status) === 'revoked')>{{ __('Revoked') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                    <div class="flex gap-2">
                        <x-primary-button>{{ __('Save changes') }}</x-primary-button>
                        <a href="{{ route('permits.show', $permit) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
