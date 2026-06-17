{{--
  Field / emblem icons for PDF certificates. DomPDF handles base64 SVG in <img> reliably.
  @props string kind: user|calendar|calendar-check|license|expiry|id-card|class|document|department|verified|status-warn|shield
--}}
@props([
    'kind' => 'dot',
    'size' => 28,
])

@php
    $stroke = '#0f766e';
    $accent = '#115e59';

    $svgs = [
        'dot' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4" fill="' . $stroke . '"/></svg>',
        'user' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="' . $stroke . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M6 21v-1c0-2.8 2.4-5 6-5s6 2.2 6 5v1"/></svg>',
        'calendar' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="' . $stroke . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="M8 14h3M13 17h3M8 17h2"/></svg>',
        'calendar-check' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="' . $stroke . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="m9 16 3 3 5-7"/></svg>',
        'license' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="' . $stroke . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="14" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M15 12h2M13 14h4"/><path d="M7 16h10"/></svg>',
        'expiry' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="' . $stroke . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 14"/></svg>',
        'id-card' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="' . $stroke . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9.5" cy="11.5" r="2"/><path d="M14 9h5M14 13h5M14 17h5"/></svg>',
        'class' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="' . $stroke . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/><path d="M12 4v5M12 15v5M4 12h5M15 12h5"/></svg>',
        'document' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="' . $stroke . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M9 13h6M9 17h6M9 9h4"/></svg>',
        'department' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="' . $stroke . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M6 21V7l6-4 6 4v14"/><path d="M9 21v-5h6v5"/><path d="M9 10h2M13 10h2M11 13h2"/></svg>',
        // Filled statuses for crisp PDF output
        'verified' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#0d9488"/><path fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" d="m8 13 4 4 9-13"/></svg>',
        'status-warn' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#ea580c"/><circle cx="12" cy="17.2" r="1.35" fill="#fffefe"/><path fill="none" stroke="#fffefe" stroke-width="2" stroke-linecap="round" d="M12 7v8"/></svg>',
        'shield' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="' . $accent . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><circle cx="12" cy="11" r="3"/><path d="M12 8V6"/><path d="M9 17h6"/></svg>',
    ];

    $svgKey = isset($svgs[$kind]) ? $kind : 'dot';
    $svg = $svgs[$svgKey];

    // Rawurlencode is smaller than base64 & works in Chromium/DomPDF for SVG data URIs when needed
    $dataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);

    $px = max(22, min(36, (int) $size));
@endphp

<img src="{{ $dataUri }}" alt="" role="presentation" width="{{ $px }}" height="{{ $px }}" style="display:block;width:{{ $px }}px;height:{{ $px }}px;">
