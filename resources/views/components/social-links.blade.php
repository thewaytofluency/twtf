@props(['links', 'iconClass' => 'text-white', 'brandHover' => false])

{{--
    Renders whichever platforms are visible, in a fixed display order, using Font Awesome
    brand icons (Phase 6) rather than the per-platform PNGs the icon row used to hardcode —
    covers TikTok too, which never had a matching image asset.
--}}
@php
    $order = [
        'youtube' => 'fa-youtube',
        'facebook' => 'fa-facebook',
        'whatsapp' => 'fa-whatsapp',
        'instagram' => 'fa-instagram',
        'tiktok' => 'fa-tiktok',
    ];

    // Full class names so Tailwind's scanner picks them up; only applied when $brandHover is set.
    $brandColors = [
        'youtube' => 'hover:text-[#FF0000]',
        'facebook' => 'hover:text-[#1877F2]',
        'whatsapp' => 'hover:text-[#25D366]',
        'instagram' => 'hover:text-[#E4405F]',
        'tiktok' => 'hover:text-black',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'flex justify-evenly space-x-6']) }}>
    @foreach ($order as $platform => $icon)
        @if ($links->has($platform))
            <a
                href="{{ $links[$platform]->url }}"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="{{ $links[$platform]->platform->label() }}"
                class="{{ $iconClass }} {{ $brandHover ? $brandColors[$platform] : '' }} hover:scale-110 transition"
            >
                <i class="fa-brands {{ $icon }} text-2xl"></i>
            </a>
        @endif
    @endforeach
</div>
