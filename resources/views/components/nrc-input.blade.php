@props([
    'name' => 'nrc',
    'value' => '',
])

@php
    $inputId = $attributes->get('id', $name);
@endphp

<div>
    <x-input-label :for="$inputId" :value="__('NRC')" />
    <input
        {{ $attributes->except('id', 'value', 'name', 'type')->merge([
            'type' => 'text',
            'name' => $name,
            'id' => $inputId,
            'inputmode' => 'numeric',
            'autocomplete' => 'off',
            'placeholder' => \App\Support\ZambianNrc::DISPLAY_HINT,
            'maxlength' => '11',
            'class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full font-mono tracking-wide',
            'required' => true,
        ]) }}
        x-data="nrcInput(@js(old($name, $value)))"
        x-model="display"
        x-on:input="onInput($event)"
        x-on:paste="onPaste($event)"
    >
    <p class="mt-1 text-xs text-gray-500">{{ __('Format: :format (slashes are added automatically).', ['format' => \App\Support\ZambianNrc::DISPLAY_HINT]) }}</p>
    <x-input-error :messages="$errors->get($name)" class="mt-2" />
</div>
