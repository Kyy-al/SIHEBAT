@props([
    'state' => 'idle', // idle, melambai, senang, semangat, merayakan, sedih, tidur, berpikir, bingung
    'size' => 'md',    // sm (48), md (80), lg (120), xl (160), 2xl (200)
    'animate' => true,
])

@php
    $dimensions = match ($size) {
        'sm' => 'w-12 h-12',
        'md' => 'w-20 h-20',
        'lg' => 'w-28 h-28',
        'xl' => 'w-40 h-40',
        '2xl' => 'w-52 h-52',
        default => 'w-20 h-20',
    };

    $animationClass = $animate ? match ($state) {
        'merayakan' => 'animate-bounce',
        'semangat'  => 'animate-wiggle',
        'melambai'  => 'animate-floaty',
        default     => 'animate-floaty',
    } : '';
@endphp

<div class="inline-flex items-center justify-center {{ $dimensions }} {{ $animationClass }} select-none relative" {{ $attributes }} aria-hidden="true">
    <svg viewBox="0 0 160 160" class="w-full h-full drop-shadow-md" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <!-- Cloud Body Gradient (Sky Blue) -->
            <linearGradient id="cloudGrad" x1="80" y1="25" x2="80" y2="135" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FFFFFF" />
                <stop offset="35%" stop-color="#E0F2FE" />
                <stop offset="100%" stop-color="#BAE6FD" />
            </linearGradient>

            <!-- Ear / Tuft Gradient -->
            <linearGradient id="tuftGrad" x1="80" y1="12" x2="80" y2="35" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FFDD55" />
                <stop offset="100%" stop-color="#FFB020" />
            </linearGradient>

            <!-- Shadow beneath mascot -->
            <radialGradient id="cloudShadow" cx="80" cy="146" r="48" fx="80" fy="146" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#0F2A43" stop-opacity="0.14" />
                <stop offset="100%" stop-color="#0F2A43" stop-opacity="0" />
            </radialGradient>
        </defs>

        <!-- Soft ground shadow -->
        <ellipse cx="80" cy="146" rx="42" ry="7" fill="url(#cloudShadow)" />

        <!-- Cute Star / Tuft accessory on top -->
        <g id="star-tuft">
            <path d="M 80 14 L 83 23 L 93 25 L 85 31 L 87 40 L 80 34 L 73 40 L 75 31 L 67 25 L 77 23 Z"
                  fill="url(#tuftGrad)"
                  stroke="#FF9E0B"
                  stroke-width="1.5"
                  stroke-linejoin="round" />
            <circle cx="80" cy="27" r="2.5" fill="#FFFBEB" />
        </g>

        <!-- Puffy Cloud Body (Inspired by reference Image 2) -->
        <!-- Lobes: Left lobe, top left lobe, middle crown, top right lobe, right lobe, bottom flat curve -->
        <path d="M 46 126
                 C 28 126, 20 106, 26 90
                 C 20 78, 26 62, 40 56
                 C 46 42, 62 38, 76 42
                 C 86 36, 102 38, 110 50
                 C 126 52, 138 66, 134 82
                 C 144 96, 138 116, 122 124
                 C 112 130, 56 130, 46 126 Z"
              fill="url(#cloudGrad)"
              stroke="#0284C7"
              stroke-width="3.5"
              stroke-linejoin="round" />

        <!-- Cloud inner highlight shine -->
        <path d="M 44 64 C 54 50, 74 46, 88 48 C 76 52, 54 56, 44 68 Z" fill="#FFFFFF" opacity="0.8" />
        <path d="M 104 54 C 114 60, 124 70, 126 80 C 122 72, 114 62, 104 56 Z" fill="#FFFFFF" opacity="0.6" />

        <!-- Rosy Blush Cheeks (Bubblegum pink, like reference!) -->
        <ellipse cx="44" cy="95" rx="7" ry="5" fill="#FF8FB1" opacity="0.85" />
        <ellipse cx="116" cy="95" rx="7" ry="5" fill="#FF8FB1" opacity="0.85" />

        <!-- Arms based on state -->
        @if($state === 'melambai')
            <!-- Left hand cute resting -->
            <path d="M 32 98 C 22 102, 24 114, 34 112" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <!-- Right hand waving high up -->
            <path d="M 124 90 C 138 82, 146 66, 138 54 C 130 48, 124 64, 118 78" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" fill="#E0F2FE" />
            <!-- Waving energy sparkles -->
            <path d="M 144 48 C 148 52, 148 58, 144 62" stroke="#38BDF8" stroke-width="2.5" stroke-linecap="round" fill="none" />
            <path d="M 150 52 C 153 55, 153 59, 150 62" stroke="#38BDF8" stroke-width="2" stroke-linecap="round" fill="none" />
        @elseif($state === 'semangat')
            <!-- Both hands raised like fist pump -->
            <path d="M 34 94 C 20 84, 22 68, 32 68 C 38 74, 38 86, 38 94" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
            <path d="M 126 94 C 140 84, 138 68, 128 68 C 122 74, 122 86, 122 94" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
        @elseif($state === 'merayakan')
            <!-- Hands up in joy -->
            <path d="M 34 88 C 18 72, 24 56, 34 58 C 40 68, 40 80, 38 88" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
            <path d="M 126 88 C 142 72, 136 56, 126 58 C 120 68, 120 80, 122 88" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
            <!-- Party Confetti sparkles around -->
            <rect x="22" y="32" width="5" height="5" rx="1.5" fill="#FFC93C" transform="rotate(25 22 32)" />
            <rect x="134" y="34" width="5" height="5" rx="1.5" fill="#FF7A59" transform="rotate(-30 134 34)" />
            <circle cx="24" cy="68" r="3" fill="#34D399" />
            <circle cx="138" cy="70" r="3" fill="#A78BFA" />
            <path d="M 80 6 L 82 10 L 86 10 L 83 13 L 84 17 L 80 14 L 76 17 L 77 13 L 74 10 L 78 10 Z" fill="#FFC93C" />
        @elseif($state === 'berpikir')
            <!-- One hand touching chin -->
            <path d="M 32 98 C 22 102, 24 114, 34 112" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <path d="M 122 104 C 122 104, 110 106, 100 100 C 96 96, 100 90, 104 92" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
            <!-- Question dots -->
            <circle cx="130" cy="40" r="2.5" fill="#38BDF8" />
            <circle cx="138" cy="30" r="3.5" fill="#38BDF8" />
            <text x="140" y="24" font-size="16" font-weight="extrabold" fill="#0284C7">?</text>
        @else
            <!-- Cute stubby hands -->
            <path d="M 34 98 C 24 102, 24 112, 34 112" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <path d="M 126 98 C 136 102, 136 112, 126 112" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="none" />
        @endif

        <!-- Eyes and Mouth -->
        @if($state === 'senang' || $state === 'merayakan')
            <!-- Happy curved eyes (^ ^) with thick playful lines -->
            <path d="M 54 82 C 60 74, 68 74, 74 82" stroke="#0F2A43" stroke-width="4" stroke-linecap="round" fill="none" />
            <path d="M 86 82 C 92 74, 100 74, 106 82" stroke="#0F2A43" stroke-width="4" stroke-linecap="round" fill="none" />
            <!-- Big open cheerful smile with pink tongue -->
            <path d="M 66 94 C 66 108, 94 108, 94 94 Z" fill="#0F2A43" />
            <path d="M 72 101 C 76 106, 84 106, 88 101 Z" fill="#FF8FB1" />
        @elseif($state === 'semangat')
            <!-- Determined glossy eyes -->
            <circle cx="64" cy="80" r="6" fill="#0F2A43" />
            <circle cx="66" cy="78" r="2.2" fill="#FFFFFF" />
            <circle cx="62" cy="82" r="1.2" fill="#FFFFFF" />
            <circle cx="96" cy="80" r="6" fill="#0F2A43" />
            <circle cx="98" cy="78" r="2.2" fill="#FFFFFF" />
            <circle cx="94" cy="82" r="1.2" fill="#FFFFFF" />
            <!-- Confident smile -->
            <path d="M 68 94 C 74 102, 86 102, 92 94" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
        @elseif($state === 'sedih')
            <!-- Droopy soft eyes -->
            <ellipse cx="64" cy="83" rx="5" ry="6" fill="#0F2A43" />
            <circle cx="66" cy="81" r="1.8" fill="#FFFFFF" />
            <ellipse cx="96" cy="83" rx="5" ry="6" fill="#0F2A43" />
            <circle cx="98" cy="81" r="1.8" fill="#FFFFFF" />
            <!-- Cute tear drop -->
            <path d="M 50 93 C 48 97, 52 99, 52 96 Z" fill="#38BDF8" />
            <!-- Gentle sad curve -->
            <path d="M 72 102 C 76 97, 84 97, 88 102" stroke="#0F2A43" stroke-width="3" stroke-linecap="round" fill="none" />
        @elseif($state === 'tidur')
            <!-- Sleeping calm eyes (- -) -->
            <path d="M 56 84 C 62 86, 70 86, 74 84" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <path d="M 86 84 C 90 86, 98 86, 104 84" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <path d="M 76 96 C 78 98, 82 98, 84 96" stroke="#0F2A43" stroke-width="2.5" stroke-linecap="round" fill="none" />
            <!-- Zzz text in purple/indigo -->
            <text x="122" y="52" font-size="14" font-weight="extrabold" fill="#6366F1">z</text>
            <text x="130" y="38" font-size="18" font-weight="extrabold" fill="#6366F1">Z</text>
        @elseif($state === 'bingung')
            <!-- Big anime confused eyes -->
            <circle cx="62" cy="80" r="7" fill="#0F2A43" />
            <circle cx="64" cy="77" r="2.5" fill="#FFFFFF" />
            <circle cx="98" cy="81" r="4" fill="#0F2A43" />
            <!-- Wavy cute mouth -->
            <path d="M 70 96 C 74 99, 80 93, 86 97" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
        @else
            <!-- Standard lovely friendly anime eyes (like Image 2!) -->
            <ellipse cx="64" cy="80" rx="5.5" ry="6.5" fill="#0F2A43" />
            <circle cx="66" cy="77.5" r="2.2" fill="#FFFFFF" />
            <circle cx="62" cy="82" r="1.2" fill="#FFFFFF" />

            <ellipse cx="96" cy="80" rx="5.5" ry="6.5" fill="#0F2A43" />
            <circle cx="98" cy="77.5" r="2.2" fill="#FFFFFF" />
            <circle cx="94" cy="82" r="1.2" fill="#FFFFFF" />

            <!-- Soft curved smile -->
            <path d="M 72 94 C 76 100, 84 100, 88 94" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
        @endif
    </svg>
</div>
