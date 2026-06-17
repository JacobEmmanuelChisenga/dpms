@props([
    'tagline' => __('Driver Permit Management'),
    'href' => null,
])

@php
    $href = $href ?? (auth()->check() ? route('dashboard') : url('/'));
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'flex items-center gap-3 min-w-0']) }}>
    <img
        src="{{ asset('images/zafficologo.jpg') }}"
        alt="{{ __('ZAFFICO') }}"
        class="h-11 w-11 rounded-lg object-contain bg-white shrink-0 p-1 shadow-sm"
    />
    <div class="min-w-0">
        <div class="font-bold text-lg leading-tight tracking-tight">DPMS</div>
        <div class="text-[10px] text-white/70 leading-tight uppercase tracking-wide truncate">{{ $tagline }}</div>
    </div>
</a>
