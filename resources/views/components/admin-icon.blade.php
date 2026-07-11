{{--
    Komponen Icon Admin (components/admin-icon.blade.php)
    Menyediakan ikon SVG bawaan (feather icons style) dan ikon PNG kustom (untuk prestasi, guru, ekskul)
    yang dapat dipanggil secara fleksibel dengan mempassing nama ikon dan ukuran (size).
--}}
@props(['name', 'size' => 20])

@if($name === 'award')
    <img src="{{ asset('images/icon-prestasi.png') }}" 
         alt="" 
         width="{{ $size }}" 
         height="{{ $size }}" 
         class="admin-icon-png-img {{ $attributes->get('class') }}" 
         style="object-fit: contain; vertical-align: middle; {{ $attributes->get('style') }}">
@elseif($name === 'guru_staff')
    <img src="{{ asset('images/icon-guru-staff.png') }}" 
         alt="" 
         width="{{ $size }}" 
         height="{{ $size }}" 
         class="admin-icon-png-img {{ $attributes->get('class') }}" 
         style="object-fit: contain; vertical-align: middle; {{ $attributes->get('style') }}">
@elseif($name === 'ekstrakurikuler')
    <img src="{{ asset('images/icon-ekstrakurikuler.png') }}" 
         alt="" 
         width="{{ $size }}" 
         height="{{ $size }}" 
         class="admin-icon-png-img {{ $attributes->get('class') }}" 
         style="object-fit: contain; vertical-align: middle; {{ $attributes->get('style') }}">
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
                <path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c3 2 9 2 12 0v-5M22 10v6"/>
                @break
            @case('classes')
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
            @case('pulse')
                <path d="M3 12h4l3-9 4 18 3-9h4"/>
                @break
            @case('school')
                <path d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-6h6v6M9 11h.01M15 11h.01"/>
                @break
            @case('message')
                <path d="M4 5h16v12H7l-3 3V5Z"/><path d="M8 9h8M8 13h5"/>
                @break
            @case('settings')
                <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.83 2.83-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1V21h-4v-.09A1.7 1.7 0 0 0 8.6 19.4a1.7 1.7 0 0 0-1.88.34l-.06.06-2.83-2.83.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1-.4H3v-4h.09A1.7 1.7 0 0 0 4.6 8.6a1.7 1.7 0 0 0-.34-1.88l-.06-.06 2.83-2.83.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1V3h4v.09A1.7 1.7 0 0 0 15.4 4.6a1.7 1.7 0 0 0 1.88-.34l.06-.06 2.83 2.83-.06.06A1.7 1.7 0 0 0 19.4 9c.16.37.37.7.6 1 .27.28.62.44 1 .4h.09v4H21a1.7 1.7 0 0 0-1.6.6Z"/>
                @break
            @case('external')
                <path d="M14 5h5v5M10 14l9-9M19 13v6H5V5h6"/>
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
            @case('arrow-right')
                <path d="m9 18 6-6-6-6"/>
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
            @default
                <circle cx="12" cy="12" r="9"/>
        @endswitch
    </svg>
@endif
