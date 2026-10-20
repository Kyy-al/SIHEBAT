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

<div class="inline-flex items-center justify-center {{ $dimensions }} {{ $animationClass }} select-none" {{ $attributes }} aria-hidden="true">
    <svg viewBox="0 0 160 160" class="w-full h-full drop-shadow-sm" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <!-- Soft Gradient for Mascot Body -->
            <linearGradient id="mascotBody" x1="40" y1="20" x2="120" y2="140" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#E0F2FE" />
                <stop offset="50%" stop-color="#BAE6FD" />
                <stop offset="100%" stop-color="#7DD3FC" />
            </linearGradient>
            <!-- Star Gradient -->
            <linearGradient id="starGrad" x1="80" y1="8" x2="80" y2="34" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FFDD55" />
                <stop offset="100%" stop-color="#FFB020" />
            </linearGradient>
            <!-- Shadow under mascot -->
            <radialGradient id="bottomShadow" cx="80" cy="148" r="50" fx="80" fy="148" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#0F2A43" stop-opacity="0.12" />
                <stop offset="100%" stop-color="#0F2A43" stop-opacity="0" />
            </radialGradient>
        </defs>

        <!-- Ground shadow -->
        <ellipse cx="80" cy="146" rx="42" ry="7" fill="url(#bottomShadow)" />

        <!-- Star / Tuft on head -->
        <g id="mascot-star">
            <path d="M 80 10 L 83 22 L 95 24 L 85 31 L 88 42 L 80 35 L 72 42 L 75 31 L 65 24 L 77 22 Z" fill="url(#starGrad)" stroke="#FFAA00" stroke-width="1.5" stroke-linejoin="round" />
            <circle cx="80" cy="26" r="3" fill="#FFFBEB" />
        </g>

        <!-- Main Body (Friendly Blob) -->
        <path d="M 46 64
                 C 40 40, 60 30, 80 30
                 C 100 30, 120 40, 114 64
                 C 126 80, 128 108, 116 126
                 C 104 142, 56 142, 44 126
                 C 32 108, 34 80, 46 64 Z"
              fill="url(#mascotBody)"
              stroke="#0284C7"
              stroke-width="3.5"
              stroke-linejoin="round" />

        <!-- Body Highlight / Sheen -->
        <path d="M 54 48 C 66 38, 86 38, 96 44 C 92 46, 70 44, 58 54 C 54 52, 54 50, 54 48 Z" fill="#FFFFFF" opacity="0.6" />

        <!-- Cheeks (Blush) -->
        <ellipse cx="50" cy="95" rx="7" ry="4.5" fill="#FF8FB1" opacity="0.85" />
        <ellipse cx="110" cy="95" rx="7" ry="4.5" fill="#FF8FB1" opacity="0.85" />

        <!-- State-specific Arms -->
        @if($state === 'melambai')
            <!-- Left hand resting -->
            <path d="M 38 98 C 28 102, 28 114, 38 112" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <!-- Right hand waving high -->
            <path d="M 118 88 C 132 80, 142 66, 136 56 C 130 50, 122 62, 114 74" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" fill="#BAE6FD" />
            <!-- Wave lines -->
            <path d="M 142 50 C 146 54, 146 60, 142 64" stroke="#38BDF8" stroke-width="2" stroke-linecap="round" fill="none" />
        @elseif($state === 'semangat')
            <!-- Both hands raised like fist pump -->
            <path d="M 40 92 C 26 84, 26 70, 36 68 C 42 74, 44 84, 44 92" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#BAE6FD" />
            <path d="M 120 92 C 134 84, 134 70, 124 68 C 118 74, 116 84, 116 92" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#BAE6FD" />
        @elseif($state === 'merayakan')
            <!-- Hands up in excitement -->
            <path d="M 40 88 C 24 74, 28 58, 38 60 C 44 68, 46 80, 44 88" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#BAE6FD" />
            <path d="M 120 88 C 136 74, 132 58, 122 60 C 116 68, 114 80, 116 88" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#BAE6FD" />
            <!-- Confetti around -->
            <rect x="25" y="35" width="5" height="5" rx="1" fill="#FFC93C" transform="rotate(25 25 35)" />
            <rect x="130" y="38" width="5" height="5" rx="1" fill="#FF7A59" transform="rotate(-30 130 38)" />
            <circle cx="28" cy="70" r="3" fill="#34D399" />
            <circle cx="135" cy="72" r="3" fill="#A78BFA" />
        @elseif($state === 'berpikir')
            <!-- Right hand touching chin -->
            <path d="M 38 100 C 28 105, 30 115, 40 112" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <path d="M 118 106 C 118 106, 106 108, 98 102 C 94 98, 98 92, 102 94" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#BAE6FD" />
            <!-- Question dots -->
            <circle cx="126" cy="42" r="2.5" fill="#38BDF8" />
            <circle cx="134" cy="32" r="3.5" fill="#38BDF8" />
            <text x="138" y="26" font-size="16" font-weight="bold" fill="#0284C7">?</text>
        @else
            <!-- Default cute stubby arms -->
            <path d="M 40 98 C 30 102, 30 112, 40 112" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <path d="M 120 98 C 130 102, 130 112, 120 112" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="none" />
        @endif

        <!-- Eyes & Mouth based on state -->
        @if($state === 'senang' || $state === 'merayakan')
            <!-- Happy curved eyes (^ ^) -->
            <path d="M 58 82 C 63 75, 71 75, 76 82" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <path d="M 84 82 C 89 75, 97 75, 102 82" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <!-- Big open smile -->
            <path d="M 68 94 C 68 106, 92 106, 92 94 Z" fill="#0F2A43" />
            <path d="M 73 99 C 76 104, 84 104, 87 99 Z" fill="#FF8FB1" />
        @elseif($state === 'semangat')
            <!-- Determined cute eyes -->
            <circle cx="68" cy="80" r="5" fill="#0F2A43" />
            <circle cx="70" cy="78" r="1.8" fill="#FFFFFF" />
            <circle cx="92" cy="80" r="5" fill="#0F2A43" />
            <circle cx="94" cy="78" r="1.8" fill="#FFFFFF" />
            <!-- Confident smile -->
            <path d="M 70 94 C 76 102, 88 100, 92 93" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
        @elseif($state === 'sedih')
            <!-- Droopy soft eyes -->
            <ellipse cx="67" cy="83" rx="4.5" ry="5.5" fill="#0F2A43" />
            <circle cx="69" cy="81" r="1.5" fill="#FFFFFF" />
            <ellipse cx="93" cy="83" rx="4.5" ry="5.5" fill="#0F2A43" />
            <circle cx="95" cy="81" r="1.5" fill="#FFFFFF" />
            <!-- Tear on left cheek -->
            <path d="M 53 92 C 51 96, 55 98, 55 95 Z" fill="#38BDF8" />
            <!-- Soft sad mouth -->
            <path d="M 72 102 C 76 97, 84 97, 88 102" stroke="#0F2A43" stroke-width="3" stroke-linecap="round" fill="none" />
        @elseif($state === 'tidur')
            <!-- Sleeping eyes (- -) -->
            <path d="M 60 84 C 65 86, 73 86, 76 84" stroke="#0F2A43" stroke-width="3" stroke-linecap="round" fill="none" />
            <path d="M 84 84 C 87 86, 95 86, 100 84" stroke="#0F2A43" stroke-width="3" stroke-linecap="round" fill="none" />
            <!-- Calm small smile -->
            <path d="M 76 96 C 78 98, 82 98, 84 96" stroke="#0F2A43" stroke-width="2.5" stroke-linecap="round" fill="none" />
            <!-- Zzz text -->
            <text x="122" y="55" font-size="14" font-weight="bold" fill="#6366F1">z</text>
            <text x="130" y="42" font-size="18" font-weight="bold" fill="#6366F1">Z</text>
        @elseif($state === 'bingung')
            <!-- One eye big, one small -->
            <circle cx="66" cy="80" r="6" fill="#0F2A43" />
            <circle cx="68" cy="78" r="2" fill="#FFFFFF" />
            <circle cx="94" cy="81" r="3.5" fill="#0F2A43" />
            <!-- Wavy mouth -->
            <path d="M 71 96 C 75 99, 81 94, 87 97" stroke="#0F2A43" stroke-width="3" stroke-linecap="round" fill="none" />
        @else
            <!-- Standard friendly happy face (melambai, idle, berpikir) -->
            <circle cx="66" cy="80" r="5" fill="#0F2A43" />
            <circle cx="68" cy="78" r="1.8" fill="#FFFFFF" />
            <circle cx="94" cy="80" r="5" fill="#0F2A43" />
            <circle cx="96" cy="78" r="1.8" fill="#FFFFFF" />
            <!-- Gentle curved smile -->
            <path d="M 72 94 C 76 100, 84 100, 88 94" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
        @endif
    </svg>
</div>
