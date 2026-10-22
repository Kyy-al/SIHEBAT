@props([
    'habit' => 'bangun-pagi', // bangun-pagi, beribadah, berolahraga, makan-sehat, gemar-belajar, bermasyarakat, tidur-cepat, hari-sempurna
    'size'  => 'md',          // sm (w-8 h-8), md (w-11 h-11), lg (w-14 h-14), xl (w-20 h-20)
    'animate' => false,
])

@php
    $dimensions = match ($size) {
        'xs' => 'w-7 h-7',
        'sm' => 'w-9 h-9',
        'md' => 'w-11 h-11',
        'lg' => 'w-14 h-14',
        'xl' => 'w-20 h-20',
        default => 'w-11 h-11',
    };

    $slug = strtolower($habit);
@endphp

<div class="inline-flex items-center justify-center {{ $dimensions }} select-none relative shrink-0" {{ $attributes }} aria-hidden="true">
    <svg viewBox="0 0 160 160" class="w-full h-full drop-shadow-sm" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <!-- Cloud Body Gradient (Sky Blue) -->
            <linearGradient id="cloudBodyGrad_{{ $slug }}" x1="80" y1="25" x2="80" y2="135" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FFFFFF" />
                <stop offset="40%" stop-color="#E0F2FE" />
                <stop offset="100%" stop-color="#BAE6FD" />
            </linearGradient>

            <!-- Star Tuft Gradient -->
            <linearGradient id="starGrad_{{ $slug }}" x1="80" y1="12" x2="80" y2="35" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FFDD55" />
                <stop offset="100%" stop-color="#F59E0B" />
            </linearGradient>

            <!-- Shadow beneath mascot -->
            <radialGradient id="cloudShadow_{{ $slug }}" cx="80" cy="148" r="44" fx="80" fy="148" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#0F2A43" stop-opacity="0.16" />
                <stop offset="100%" stop-color="#0F2A43" stop-opacity="0" />
            </radialGradient>
        </defs>

        <!-- Soft ground shadow -->
        <ellipse cx="80" cy="146" rx="38" ry="6" fill="url(#cloudShadow_{{ $slug }})" />

        {{-- HABIT-SPECIFIC BACKGROUND ACCENTS --}}
        @if($slug === 'bangun-pagi')
            <!-- Morning Sun rising behind mascot -->
            <circle cx="122" cy="48" r="22" fill="#FDE047" opacity="0.9" />
            <path d="M 122 18 L 122 24 M 146 36 L 140 40 M 152 48 L 146 48 M 140 60 L 146 64" stroke="#F59E0B" stroke-width="3" stroke-linecap="round" />
        @elseif($slug === 'beribadah')
            <!-- Serene Aura / Light rays -->
            <circle cx="80" cy="75" r="54" stroke="#38BDF8" stroke-width="2" stroke-dasharray="4 4" opacity="0.6" />
            <path d="M 80 18 L 80 24 M 50 30 L 54 34 M 110 30 L 106 34" stroke="#38BDF8" stroke-width="2" stroke-linecap="round" opacity="0.8" />
        @elseif($slug === 'tidur-cepat')
            <!-- Night Moon & Stars -->
            <path d="M 132 30 A 18 18 0 1 1 116 54 A 14 14 0 0 0 132 30 Z" fill="#FBBF24" />
            <path d="M 28 42 L 30 45 L 34 45 L 31 47 L 32 50 L 28 48 L 24 50 L 25 47 L 22 45 L 26 45 Z" fill="#FDE047" />
        @elseif($slug === 'hari-sempurna' || $slug === 'perfect-day')
            <!-- Sparkles around -->
            <circle cx="28" cy="44" r="3" fill="#FBBF24" />
            <circle cx="134" cy="40" r="3" fill="#F43F5E" />
            <circle cx="138" cy="75" r="2.5" fill="#3B82F6" />
            <circle cx="22" cy="72" r="2.5" fill="#10B981" />
        @endif

        {{-- HEAD ACCESSORY (Behind or Top of cloud) --}}
        @if($slug === 'tidur-cepat')
            <!-- Cozy Nightcap Hat -->
            <path d="M 64 44 C 64 20, 110 16, 128 34 C 118 42, 98 42, 64 44 Z" fill="#6366F1" stroke="#4338CA" stroke-width="2.5" />
            <circle cx="130" cy="36" r="6" fill="#FDE047" />
        @elseif($slug === 'hari-sempurna' || $slug === 'perfect-day')
            <!-- Golden 3-point Crown -->
            <path d="M 62 38 L 56 22 L 68 28 L 80 16 L 92 28 L 104 22 L 98 38 Z" fill="#F59E0B" stroke="#D97706" stroke-width="2" stroke-linejoin="round" />
            <circle cx="56" cy="22" r="2.5" fill="#EF4444" />
            <circle cx="80" cy="16" r="3" fill="#3B82F6" />
            <circle cx="104" cy="22" r="2.5" fill="#10B981" />
        @else
            <!-- Standard Yellow Star Tuft -->
            <g id="star-tuft-{{ $slug }}">
                <path d="M 80 14 L 83 23 L 93 25 L 85 31 L 87 40 L 80 34 L 73 40 L 75 31 L 67 25 L 77 23 Z"
                      fill="url(#starGrad_{{ $slug }})"
                      stroke="#D97706"
                      stroke-width="1.5"
                      stroke-linejoin="round" />
                <circle cx="80" cy="27" r="2.5" fill="#FFFBEB" />
            </g>
        @endif

        {{-- PUFFY CLOUD BODY (Signature Si Awan) --}}
        <path d="M 46 126
                 C 28 126, 20 106, 26 90
                 C 20 78, 26 62, 40 56
                 C 46 42, 62 38, 76 42
                 C 86 36, 102 38, 110 50
                 C 126 52, 138 66, 134 82
                 C 144 96, 138 116, 122 124
                 C 112 130, 56 130, 46 126 Z"
              fill="url(#cloudBodyGrad_{{ $slug }})"
              stroke="#0284C7"
              stroke-width="3.5"
              stroke-linejoin="round" />

        <!-- Highlights -->
        <path d="M 44 64 C 54 50, 74 46, 88 48 C 76 52, 54 56, 44 68 Z" fill="#FFFFFF" opacity="0.85" />

        <!-- Rosy Blush Cheeks -->
        <ellipse cx="44" cy="95" rx="7" ry="5" fill="#FF8FB1" opacity="0.85" />
        <ellipse cx="116" cy="95" rx="7" ry="5" fill="#FF8FB1" opacity="0.85" />

        {{-- HABIT SPECIALIZED GEAR ON CLOUD BODY --}}
        @if($slug === 'berolahraga')
            <!-- Sporty Headband -->
            <path d="M 38 64 C 60 56, 100 56, 122 64" stroke="#EF4444" stroke-width="7" stroke-linecap="round" />
            <path d="M 44 64 C 64 57, 96 57, 116 64" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" />
            <!-- Sweat Drop -->
            <path d="M 124 50 C 122 54, 126 56, 126 53 Z" fill="#38BDF8" />
        @elseif($slug === 'gemar-belajar')
            <!-- Cute Scholar Glasses -->
            <circle cx="62" cy="80" r="11" stroke="#0F172A" stroke-width="2.5" fill="none" />
            <circle cx="98" cy="80" r="11" stroke="#0F172A" stroke-width="2.5" fill="none" />
            <path d="M 73 80 L 87 80" stroke="#0F172A" stroke-width="2.5" />
        @endif

        {{-- EYES & EXPRESSION PER HABIT --}}
        @if($slug === 'tidur-cepat')
            <!-- Calm Sleepy Eyes (- -) -->
            <path d="M 54 84 C 60 87, 68 87, 72 84" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <path d="M 88 84 C 92 87, 100 87, 106 84" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <!-- Tiny sleeping mouth -->
            <path d="M 76 96 C 78 98, 82 98, 84 96" stroke="#0F2A43" stroke-width="2.5" stroke-linecap="round" fill="none" />
            <!-- Zzz floats -->
            <text x="120" y="52" font-size="13" font-weight="900" fill="#6366F1">z</text>
            <text x="128" y="38" font-size="17" font-weight="900" fill="#6366F1">Z</text>
        @elseif($slug === 'beribadah')
            <!-- Serene Peaceful Eyes (^ ^) -->
            <path d="M 54 82 C 60 75, 68 75, 74 82" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <path d="M 86 82 C 92 75, 100 75, 106 82" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <!-- Gentle soft smile -->
            <path d="M 74 95 C 77 98, 83 98, 86 95" stroke="#0F2A43" stroke-width="3" stroke-linecap="round" fill="none" />
        @elseif($slug === 'bangun-pagi' || $slug === 'hari-sempurna' || $slug === 'perfect-day')
            <!-- Cheerful Sparkly Eyes & Big Smile -->
            <ellipse cx="64" cy="79" rx="5.5" ry="6.5" fill="#0F2A43" />
            <circle cx="66" cy="76.5" r="2.2" fill="#FFFFFF" />
            <circle cx="62" cy="81" r="1.2" fill="#FFFFFF" />

            <ellipse cx="96" cy="79" rx="5.5" ry="6.5" fill="#0F2A43" />
            <circle cx="98" cy="76.5" r="2.2" fill="#FFFFFF" />
            <circle cx="94" cy="81" r="1.2" fill="#FFFFFF" />

            <!-- Open happy smile -->
            <path d="M 68 93 C 68 105, 92 105, 92 93 Z" fill="#0F2A43" />
            <path d="M 74 99 C 77 103, 83 103, 86 99 Z" fill="#FF8FB1" />
        @else
            <!-- Standard Friendly Anime Eyes -->
            <ellipse cx="64" cy="80" rx="5.5" ry="6.5" fill="#0F2A43" />
            <circle cx="66" cy="77.5" r="2.2" fill="#FFFFFF" />
            <circle cx="62" cy="82" r="1.2" fill="#FFFFFF" />

            <ellipse cx="96" cy="80" rx="5.5" ry="6.5" fill="#0F2A43" />
            <circle cx="98" cy="77.5" r="2.2" fill="#FFFFFF" />
            <circle cx="94" cy="82" r="1.2" fill="#FFFFFF" />

            <!-- Sweet Smile -->
            <path d="M 72 94 C 76 100, 84 100, 88 94" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
        @endif

        {{-- HANDS & PROPS PER HABIT --}}
        @if($slug === 'bangun-pagi')
            <!-- Both hands stretching up enthusiastically -->
            <path d="M 32 94 C 20 80, 24 64, 34 66" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
            <path d="M 128 94 C 140 80, 136 64, 126 66" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
        @elseif($slug === 'beribadah')
            <!-- Clasped Hands in Prayer at Center Chest -->
            <path d="M 70 108 C 70 96, 76 90, 80 90 C 84 90, 90 96, 90 108 Z" fill="#E0F2FE" stroke="#0284C7" stroke-width="3" stroke-linejoin="round" />
            <path d="M 80 92 L 80 108" stroke="#0284C7" stroke-width="2" />
        @elseif($slug === 'berolahraga')
            <!-- Running arms / Fist pump -->
            <path d="M 32 94 C 18 90, 20 74, 32 74" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
            <path d="M 126 94 C 140 82, 138 66, 128 68" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
        @elseif($slug === 'makan-sehat')
            <!-- Holding Fresh Red Apple -->
            <circle cx="80" cy="110" r="12" fill="#EF4444" stroke="#B91C1C" stroke-width="2" />
            <path d="M 80 98 C 80 94, 82 92, 84 90" stroke="#78350F" stroke-width="2" stroke-linecap="round" />
            <path d="M 82 92 C 86 90, 90 92, 90 94 C 88 96, 84 96, 82 92 Z" fill="#22C55E" />
            <!-- Hands holding apple -->
            <path d="M 64 110 C 66 104, 70 104, 72 108" stroke="#0284C7" stroke-width="3" stroke-linecap="round" fill="none" />
            <path d="M 96 110 C 94 104, 90 104, 88 108" stroke="#0284C7" stroke-width="3" stroke-linecap="round" fill="none" />
        @elseif($slug === 'gemar-belajar')
            <!-- Holding Open Book -->
            <path d="M 60 104 L 80 110 L 100 104 L 98 120 L 80 124 L 62 120 Z" fill="#3B82F6" stroke="#1D4ED8" stroke-width="2" stroke-linejoin="round" />
            <path d="M 80 110 L 80 124" stroke="#FFFFFF" stroke-width="2" />
            <path d="M 64 108 L 76 112 M 64 114 L 76 117" stroke="#DBEAFE" stroke-width="1.5" stroke-linecap="round" />
            <path d="M 84 112 L 96 108 M 84 117 L 96 114" stroke="#DBEAFE" stroke-width="1.5" stroke-linecap="round" />
            <!-- Hands holding book -->
            <circle cx="58" cy="112" r="3.5" fill="#E0F2FE" stroke="#0284C7" stroke-width="2" />
            <circle cx="102" cy="112" r="3.5" fill="#E0F2FE" stroke="#0284C7" stroke-width="2" />
        @elseif($slug === 'bermasyarakat')
            <!-- Hands Holding Warm Pink Heart of Community -->
            <path d="M 80 102 C 76 96, 68 98, 68 104 C 68 112, 80 120, 80 120 C 80 120, 92 112, 92 104 C 92 98, 84 96, 80 102 Z" fill="#F43F5E" stroke="#BE123C" stroke-width="1.5" />
            <circle cx="66" cy="106" r="3" fill="#E0F2FE" stroke="#0284C7" stroke-width="2" />
            <circle cx="94" cy="106" r="3" fill="#E0F2FE" stroke="#0284C7" stroke-width="2" />
        @elseif($slug === 'hari-sempurna' || $slug === 'perfect-day')
            <!-- Cheerful Celebration Hands Up -->
            <path d="M 32 92 C 18 78, 24 60, 34 62" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
            <path d="M 128 92 C 142 78, 136 60, 126 62" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
        @else
            <!-- Standard Hands -->
            <path d="M 34 98 C 24 102, 24 112, 34 112" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <path d="M 126 98 C 136 102, 136 112, 126 112" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="none" />
        @endif
    </svg>
</div>
