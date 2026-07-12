{{--
    Komponen Icon Admin (components/admin-icon.blade.php)
    Ikon SVG outline bergaya Lucide agar pemanggilan ikon konsisten lewat <x-admin-icon>.
--}}
@props(['name', 'size' => 20])

@if(in_array($name, ['award', 'ekstrakurikuler'], true))
    @php
        $customIcons = [
            'award' => [
                'src' => asset('assets/icons/award-badge-icon.svg'),
                'alt' => 'Prestasi',
            ],
            'ekstrakurikuler' => [
                'src' => asset('assets/icons/icon-ekstrakurikuler.svg'),
                'alt' => 'Ekstrakurikuler',
            ],
        ];
    @endphp
    <img {{ $attributes->merge([
        'src' => $customIcons[$name]['src'],
        'alt' => $customIcons[$name]['alt'],
        'class' => 'sidebar-custom-icon',
        'width' => $size,
        'height' => $size,
        'loading' => 'lazy',
    ]) }}>
@else
<svg {{ $attributes->merge([
    'width' => $size,
    'height' => $size,
    'viewBox' => '0 0 24 24',
    'fill' => 'none',
    'stroke' => 'currentColor',
    'stroke-width' => '1.8',
    'stroke-linecap' => 'round',
    'stroke-linejoin' => 'round',
    'aria-hidden' => 'true',
]) }}>
    @switch($name)
        @case('dashboard')
            <path d="M4 13h6V4H4zM14 20h6v-9h-6zM4 20h6v-3H4zM14 7h6V4h-6z"/>
            @break
        @case('students')
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            @break
        @case('classes')
        @case('calendar')
            <path d="M4 5h16v14H4zM8 3v4M16 3v4M4 9h16"/>
            @break
        @case('news')
            <path d="M4 5h16v14H4zM8 9h8M8 13h8M8 17h5"/>
            @break
        @case('users')
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            @break
        @case('activity')
            <path d="M4 17 10 11l4 4 6-7"/><path d="M14 8h6v6"/>
            @break
        @case('ekstrakurikuler')
            <circle cx="12" cy="12" r="9"/><path d="m12 7 4 3-1.5 5h-5L8 10z"/><path d="M12 7V3M16 10l4-1M14.5 15l2.5 4M9.5 15 7 19M8 10 4 9"/>
            @break
        @case('pulse')
            <path d="M3 12h4l3-9 4 18 3-9h4"/>
            @break
        @case('school')
            <path d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-6h6v6M9 11h.01M15 11h.01"/>
            @break
        @case('message')
            <path d="M4 5h16v12H7l-3 3V5Z"/><path d="M8 9h8M8 13h5"/>
            @break
        @case('home')
            <path d="M3 11 12 4l9 7"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/>
            @break
        @case('map-pin')
            <path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/>
            @break
        @case('phone')
            <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.7.6 2.5a2 2 0 0 1-.5 2.1L8 9.5a16 16 0 0 0 6.5 6.5l1.2-1.2a2 2 0 0 1 2.1-.5c.8.3 1.6.5 2.5.6a2 2 0 0 1 1.7 2Z"/>
            @break
        @case('phone-out')
            <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.7.6 2.5a2 2 0 0 1-.5 2.1L8 9.5a16 16 0 0 0 6.5 6.5l1.2-1.2a2 2 0 0 1 2.1-.5c.8.3 1.6.5 2.5.6a2 2 0 0 1 1.7 2Z"/><path d="M16 3h5v5M15 9l6-6"/>
            @break
        @case('settings')
            <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.83 2.83-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1V21h-4v-.09A1.7 1.7 0 0 0 8.6 19.4a1.7 1.7 0 0 0-1.88.34l-.06.06-2.83-2.83.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1-.4H3v-4h.09A1.7 1.7 0 0 0 4.6 8.6a1.7 1.7 0 0 0-.34-1.88l-.06-.06 2.83-2.83.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1V3h4v.09A1.7 1.7 0 0 0 15.4 4.6a1.7 1.7 0 0 0 1.88-.34l.06-.06 2.83 2.83-.06.06A1.7 1.7 0 0 0 19.4 9c.16.37.37.7.6 1 .27.28.62.44 1 .4h.09v4H21a1.7 1.7 0 0 0-1.6.6Z"/>
            @break
        @case('external')
            <path d="M14 5h5v5M10 14l9-9M19 13v6H5V5h6"/>
            @break
        @case('search')
            <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
            @break
        @case('csv')
            <path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z"/><path d="M14 3v6h6M8 14h8M8 17h5"/>
            @break
        @case('refresh')
            <path d="M21 12a9 9 0 0 1-15.5 6.2L3 16"/><path d="M3 21v-5h5"/><path d="M3 12A9 9 0 0 1 18.5 5.8L21 8"/><path d="M21 3v5h-5"/>
            @break
        @case('arrow-up')
            <path d="M12 19V5M6 11l6-6 6 6"/>
            @break
        @case('arrow-left')
        @case('chevron-left')
            <path d="m15 18-6-6 6-6"/>
            @break
        @case('arrow-right')
        @case('chevron-right')
            <path d="m9 18 6-6-6-6"/>
            @break
        @case('save')
            <path d="M5 3h12l2 2v16H5z"/><path d="M8 3v6h8V3M8 21v-7h8v7"/>
            @break
        @case('edit')
            <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/>
            @break
        @case('trash')
            <path d="M3 6h18M8 6V4h8v2M6 6l1 15h10l1-15M10 11v6M14 11v6"/>
            @break
        @case('x')
            <path d="M18 6 6 18M6 6l12 12"/>
            @break
        @case('user')
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            @break
        @case('person-badge')
        @case('guru_staff')
            <path d="M5 4h14v16H5z"/><path d="M9 4V2h6v2"/><circle cx="12" cy="10" r="3"/><path d="M8.5 17a3.8 3.8 0 0 1 7 0"/>
            @break
        @case('user-shield')
            <path d="M12 3 5 6v5c0 4.5 3 8.5 7 10 4-1.5 7-5.5 7-10V6z"/><path d="M9 12l2 2 4-4"/>
            @break
        @case('users-slash')
            <path d="m3 3 18 18M16 21v-2a4 4 0 0 0-4-4H8M9 11a4 4 0 0 1-4-4c0-.8.2-1.5.6-2.1M15 7a4 4 0 0 1-4 4M22 21v-2a4 4 0 0 0-3-3.87"/>
            @break
        @case('folder-open')
            <path d="M3 7h6l2 2h10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M3 11h18l-2 7"/>
            @break
        @case('envelope')
            <path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/>
            @break
        @case('envelope-open')
            <path d="M4 10 12 4l8 6v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><path d="m4 11 8 5 8-5"/>
            @break
        @case('image')
            <path d="M4 5h16v14H4z"/><circle cx="9" cy="10" r="2"/><path d="m4 17 4.5-4.5 3.5 3.5 2.5-2.5L20 19"/>
            @break
        @case('file-image')
            <path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z"/><path d="M14 3v6h6"/><circle cx="9" cy="13" r="1.5"/><path d="m8 18 2.5-2.5 2 2 1.5-1.5 2 2"/>
            @break
        @case('eye')
            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>
            @break
        @case('eye-off')
            <path d="m3 3 18 18M10.6 10.6a3 3 0 0 0 3.8 3.8"/><path d="M9.9 5.4A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-3.1 4.1M6.5 6.5C3.6 8.3 2 12 2 12s3.5 7 10 7c1.2 0 2.3-.2 3.3-.6"/>
            @break
        @case('camera')
            <path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="14" r="4"/>
            @break
        @case('warning')
            <path d="M12 3 2.5 20h19z"/><path d="M12 9v5M12 17h.01"/>
            @break
        @case('info')
            <circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7h.01"/>
            @break
        @case('check')
            <path d="m5 12 5 5L20 7"/>
            @break
        @case('patch-check')
            <path d="M12 2 9.8 4.2 6.7 3.7 6.1 6.8 3.2 8.2 4.6 11 3.2 13.8 6.1 15.2 6.7 18.3l3.1-.5L12 20l2.2-2.2 3.1.5.6-3.1 2.9-1.4L19.4 11l1.4-2.8-2.9-1.4-.6-3.1-3.1.5Z"/><path d="m8.5 11.5 2.3 2.3 4.7-5"/>
            @break
        @case('shield-check')
            <path d="M12 3 5 6v5c0 4.5 3 8.5 7 10 4-1.5 7-5.5 7-10V6z"/><path d="m9 12 2 2 4-4"/>
            @break
        @case('bullhorn')
            <path d="M4 13h3l9 4V7l-9 4H4z"/><path d="M7 13v5M18 10a3 3 0 0 1 0 4"/>
            @break
        @case('trophy')
            <path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0z"/><path d="M7 6H4v2a4 4 0 0 0 4 4M17 6h3v2a4 4 0 0 1-4 4"/>
            @break
        @case('award')
            <circle cx="12" cy="7.5" r="4.5"/><path d="M9.4 11.1 7 21l5-3 5 3-2.4-9.9"/><path d="M9.2 15.4 12 13.8l2.8 1.6"/><path d="M12 5.5v4M10 7.5h4"/>
            @break
        @case('stars')
            <path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5Z"/><path d="M5 14l.8 2.2L8 17l-2.2.8L5 20l-.8-2.2L2 17l2.2-.8Z"/><path d="M19 13l.7 1.8 1.8.7-1.8.7L19 19l-.7-1.8-1.8-.7 1.8-.7Z"/>
            @break
        @case('facebook')
            <path d="M15 8h-2a2 2 0 0 0-2 2v2H9v3h2v6h3v-6h2.5l.5-3h-3v-1.5c0-.8.4-1.5 1.5-1.5H17V6.2A9 9 0 0 0 15 6Z"/>
            @break
        @case('instagram')
            <rect x="4" y="4" width="16" height="16" rx="5"/><circle cx="12" cy="12" r="3.5"/><path d="M17 7.5h.01"/>
            @break
        @case('tiktok')
            <path d="M14 3v10.5a4.5 4.5 0 1 1-4.5-4.5"/><path d="M14 5a6 6 0 0 0 5 5"/>
            @break
        @case('youtube')
            <path d="M22 12s0-4-1-5a3 3 0 0 0-2-1c-3-.4-7-.4-7-.4s-4 0-7 .4a3 3 0 0 0-2 1c-1 1-1 5-1 5s0 4 1 5a3 3 0 0 0 2 1c3 .4 7 .4 7 .4s4 0 7-.4a3 3 0 0 0 2-1c1-1 1-5 1-5Z"/><path d="m10 9 5 3-5 3z"/>
            @break
        @case('sun')
            <circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
            @break
        @case('moon')
            <path d="M21 12.8A8.5 8.5 0 1 1 11.2 3a6.5 6.5 0 0 0 9.8 9.8Z"/>
            @break
        @case('menu')
            <path d="M4 7h16M4 12h16M4 17h16"/>
            @break
        @case('logout')
            <path d="M10 17l5-5-5-5M15 12H3M14 3h7v18h-7"/>
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14"/>
            @break
        @case('clock')
            <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
            @break
        @case('graduation')
            <path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/><path d="M22 10v6"/>
            @break
        @case('help')
            <circle cx="12" cy="12" r="9"/><path d="M9.8 9a2.3 2.3 0 1 1 3.5 2c-.8.5-1.3 1-1.3 2M12 17h.01"/>
            @break
        @case('circle')
            <circle cx="12" cy="12" r="5"/>
            @break
        @default
            <circle cx="12" cy="12" r="9"/>
    @endswitch
</svg>
@endif
