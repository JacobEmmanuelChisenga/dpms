@props([
    'permit',
    'size' => 'w-40 h-40',
])

@php
    $dataUri = \App\Services\PermitQrService::dataUri($permit);
@endphp

@if ($dataUri)
    <img
        src="{{ $dataUri }}"
        alt="{{ __('QR code') }}"
        {{ $attributes->merge(['class' => trim("{$size} mx-auto border rounded-lg bg-white p-2 object-contain")]) }}
    />
@else
    <div {{ $attributes->merge(['class' => trim("{$size} mx-auto flex items-center justify-center border rounded-lg bg-gray-50 text-xs text-gray-400")]) }}>
        {{ __('QR unavailable') }}
    </div>
@endif
