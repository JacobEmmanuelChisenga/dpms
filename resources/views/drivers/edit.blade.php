<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit driver') }} — {{ $driver->full_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="post" action="{{ route('drivers.update', $driver) }}" class="space-y-4">
                    @csrf
                    @method('patch')
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="employee_id" :value="__('Employee number')" />
                            <x-text-input id="employee_id" name="employee_id" type="text" class="mt-1 block w-full" :value="old('employee_id', $driver->employee_id)" required />
                            <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="full_name" :value="__('Full name')" />
                            <x-text-input id="full_name" name="full_name" type="text" class="mt-1 block w-full" :value="old('full_name', $driver->full_name)" required />
                            <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                        </div>
                        <div>
                            <x-nrc-input :value="old('nrc', $driver->nrc)" />
                        </div>
                        <div>
                            <x-input-label for="department" :value="__('Department')" />
                            <x-text-input id="department" name="department" type="text" class="mt-1 block w-full" :value="old('department', $driver->department)" required />
                            <x-input-error :messages="$errors->get('department')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="license_number" :value="__('License number')" />
                            <x-text-input id="license_number" name="license_number" type="text" class="mt-1 block w-full" :value="old('license_number', $driver->license_number)" required />
                            <x-input-error :messages="$errors->get('license_number')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="license_class" :value="__('License class')" />
                            <x-text-input id="license_class" name="license_class" type="text" class="mt-1 block w-full" :value="old('license_class', $driver->license_class)" required />
                            <x-input-error :messages="$errors->get('license_class')" class="mt-2" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="phone" :value="__('Phone')" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $driver->phone)" required />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <x-primary-button>{{ __('Update') }}</x-primary-button>
                        <a href="{{ route('drivers.show', $driver) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
