@props([
    'name',
    'style' => 'solid',
    'size' => 'md',
])

@php
    $styleClass = match ($style) {
        'regular' => 'fa-regular',
        'brands' => 'fa-brands',
        default => 'fa-solid',
    };

    $sizeClass = match ($size) {
        'xs' => 'text-xs',
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
        'xl' => 'text-xl',
        '2xl' => 'text-2xl',
        default => 'text-base',
    };
@endphp

<i {{ $attributes->merge(['class' => trim("{$styleClass} fa-{$name} fa-fw shrink-0 {$sizeClass}")]) }} aria-hidden="true"></i>
