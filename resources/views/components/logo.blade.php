@props([
    'size'     => 'md',                  // sm, md, lg, xl
    'iconOnly' => false,                 // true = show icon mark only
    'subtitle' => '7 Kebiasaan Hebat',   // custom tagline or null
    'badge'    => null,                  // optional role badge, e.g. 'Admin', 'Guru'
    'stacked'  => false,                 // true = centered vertical layout (e.g. login card)
])

@php
    $iconSize = match($size) {
        'xs' => 'size-7',
        'sm' => 'size-8',
        'md' => 'size-10 sm:size-11',
        'lg' => 'size-14',
        'xl' => 'size-20',
        default => 'size-10 sm:size-11',
    };

    $titleSize = match($size) {
        'xs' => 'text-sm font-black',
        'sm' => 'text-base font-black',
        'md' => 'text-lg sm:text-xl font-extrabold',
        'lg' => 'text-2xl font-black',
        'xl' => 'text-3xl font-black',
        default => 'text-lg sm:text-xl font-extrabold',
    };

    $subSize = match($size) {
        'xs', 'sm' => 'text-[9px]',
        'md' => 'text-[10px] sm:text-[11px]',
        'lg', 'xl' => 'text-xs',
        default => 'text-[10px] sm:text-[11px]',
    };

    $uid = uniqid();
@endphp

<div class="inline-flex {{ $stacked ? 'flex-col items-center text-center' : 'items-center text-left' }} gap-2.5 select-none" {{ $attributes }}>
    {{-- Mascot Cloud Head Squircle Icon --}}
    <div class="{{ $iconSize }} shrink-0 relative flex items-center justify-center transition-transform hover:scale-105">
        <svg viewBox="0 0 70 70" class="w-full h-full drop-shadow-xs overflow-visible" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="logoBg_{{ $uid }}" x1="5" y1="5" x2="65" y2="65" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#F0F9FF"/>
                    <stop offset="100%" stop-color="#E0F2FE"/>
                </linearGradient>
                <linearGradient id="logoCloud_{{ $uid }}" x1="10" y1="10" x2="60" y2="60" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#FFFFFF"/>
                    <stop offset="40%" stop-color="#E0F2FE"/>
                    <stop offset="100%" stop-color="#BAE6FD"/>
                </linearGradient>
                <linearGradient id="logoStar_{{ $uid }}" x1="35" y1="2" x2="35" y2="20" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#FDE047"/>
                    <stop offset="100%" stop-color="#F59E0B"/>
                </linearGradient>
            </defs>

            <!-- Container Squircle Background -->
            <rect x="3" y="3" width="64" height="64" rx="20" fill="url(#logoBg_{{ $uid }})" stroke="#38BDF8" stroke-width="2" />

            <!-- Soft Mascot Shadow -->
            <ellipse cx="35" cy="56" rx="19" ry="3.5" fill="#0F2A43" fill-opacity="0.12" />

            <!-- Golden Star Tuft on Top -->
            <polygon points="35,6 37.5,13 44,14 39,18 40.5,25 35,21 29.5,25 31,18 26,14 32.5,13"
                     fill="url(#logoStar_{{ $uid }})"
                     stroke="#D97706"
                     stroke-width="1.2"
                     stroke-linejoin="round" />
            <circle cx="35" cy="16" r="1.8" fill="#FFFBEB" />

            <!-- Cloud Body Hero (Signature Si Awan) -->
            <path d="M 18 52
                     C 10 52, 6 43, 9 36
                     C 6 30, 9 23, 16 20
                     C 19 14, 26 12, 33 14
                     C 38 11, 46 12, 50 18
                     C 58 19, 63 26, 61 33
                     C 65 39, 62 48, 55 51
                     C 50 54, 23 54, 18 52 Z"
                  fill="url(#logoCloud_{{ $uid }})"
                  stroke="#0284C7"
                  stroke-width="2.5"
                  stroke-linejoin="round" />

            <!-- Top inner shine -->
            <path d="M 19 26 C 24 19, 34 17, 40 18" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" fill="none" />

            <!-- Rosy Cheeks -->
            <ellipse cx="18" cy="38" rx="4" ry="2.5" fill="#FF8FB1" opacity="0.9" />
            <ellipse cx="52" cy="38" rx="4" ry="2.5" fill="#FF8FB1" opacity="0.9" />

            <!-- Anime Eyes -->
            <circle cx="27" cy="32" r="3.8" fill="#0F2A43" />
            <circle cx="28.5" cy="30.5" r="1.5" fill="#FFFFFF" />
            <circle cx="43" cy="32" r="3.8" fill="#0F2A43" />
            <circle cx="44.5" cy="30.5" r="1.5" fill="#FFFFFF" />

            <!-- Cheerful Smile -->
            <path d="M 31 39 C 33 43, 37 43, 39 39" stroke="#0F2A43" stroke-width="2" stroke-linecap="round" fill="none" />
        </svg>
    </div>

    {{-- Brand Typography --}}
    @if(!$iconOnly)
        <div class="min-w-0 {{ $stacked ? 'mt-1' : '' }}">
            <div class="flex items-center gap-1.5 {{ $stacked ? 'justify-center' : '' }}">
                <span class="{{ $titleSize }} tracking-tight text-ink-900 leading-tight">
                    Si<span class="text-primary-700">Hebat</span>
                </span>
                @if($badge)
                    <span class="chip bg-primary-100 text-primary-900 font-extrabold text-[10px] py-0 px-2 rounded-full border border-primary-200">
                        {{ $badge }}
                    </span>
                @endif
            </div>

            @if($subtitle)
                <span class="block {{ $subSize }} text-ink-500 font-bold -mt-0.5 truncate leading-normal">
                    {{ $subtitle }}
                </span>
            @endif
        </div>
    @endif
</div>
