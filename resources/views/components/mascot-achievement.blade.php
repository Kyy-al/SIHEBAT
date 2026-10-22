@props([
    'icon'    => 'trophy',  // trophy, fire, sparkle, sun, sun-horizon, medal, book, heart, crown, star
    'slug'    => null,      // perfect-day, streak-7, early-bird, xp-300, athlete, etc.
    'earned'  => true,      // true = unlocked full color, false = locked silhouette
    'size'    => 'md',      // xs (28), sm (36), md (48), lg (64), xl (96)
    'animate' => false,
])

@php
    $iconKey = strtolower($icon);
    if ($slug) {
        $s = strtolower($slug);
        if (str_contains($s, 'streak') || str_contains($s, 'fire')) $iconKey = 'fire';
        elseif (str_contains($s, 'perfect') || str_contains($s, 'sempurna')) $iconKey = 'sparkle';
        elseif (str_contains($s, 'early') || str_contains($s, 'pagi') || str_contains($s, 'sun')) $iconKey = 'sun';
        elseif (str_contains($s, 'xp') || str_contains($s, 'trophy')) $iconKey = 'trophy';
        elseif (str_contains($s, 'athlete') || str_contains($s, 'olahraga') || str_contains($s, 'medal')) $iconKey = 'medal';
        elseif (str_contains($s, 'belajar') || str_contains($s, 'book')) $iconKey = 'book';
        elseif (str_contains($s, 'masyarakat') || str_contains($s, 'heart')) $iconKey = 'heart';
    }

    $dimensions = match ($size) {
        'xs' => 'w-7 h-7',
        'sm' => 'w-9 h-9',
        'md' => 'w-12 h-12',
        'lg' => 'w-16 h-16',
        'xl' => 'w-24 h-24',
        default => 'w-12 h-12',
    };

    $uid = uniqid();
    $animClass = ($animate && $earned) ? 'animate-floaty' : '';
@endphp

<div class="inline-flex items-center justify-center {{ $dimensions }} {{ $animClass }} select-none relative shrink-0" {{ $attributes }} aria-hidden="true">
    <svg viewBox="0 0 160 160" class="w-full h-full drop-shadow-xs overflow-visible {{ $earned ? '' : 'grayscale opacity-50' }}" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <!-- Cloud Body Gradient -->
            @if($earned)
                <linearGradient id="achvCloud_{{ $uid }}" x1="80" y1="25" x2="80" y2="135" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#FFFFFF" />
                    <stop offset="35%" stop-color="#E0F2FE" />
                    <stop offset="100%" stop-color="#BAE6FD" />
                </linearGradient>
            @else
                <linearGradient id="achvCloud_{{ $uid }}" x1="80" y1="25" x2="80" y2="135" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#E2E8F0" />
                    <stop offset="100%" stop-color="#94A3B8" />
                </linearGradient>
            @endif

            <!-- Gold Gradient for Trophy & Crowns -->
            <linearGradient id="achvGold_{{ $uid }}" x1="80" y1="10" x2="80" y2="70" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FEF08A" />
                <stop offset="50%" stop-color="#F59E0B" />
                <stop offset="100%" stop-color="#D97706" />
            </linearGradient>

            <!-- Shadow -->
            <radialGradient id="achvShadow_{{ $uid }}" cx="80" cy="148" r="42" fx="80" fy="148" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#0F2A43" stop-opacity="0.16" />
                <stop offset="100%" stop-color="#0F2A43" stop-opacity="0" />
            </radialGradient>
        </defs>

        <!-- Soft ground shadow -->
        <ellipse cx="80" cy="146" rx="38" ry="6" fill="url(#achvShadow_{{ $uid }})" />

        {{-- 1. BACKGROUND GLOW / PROPS BEHIND MASCOT --}}
        @if($iconKey === 'sun' || $iconKey === 'sun-horizon')
            <!-- Rising Sun behind -->
            <circle cx="122" cy="46" r="22" fill="#FDE047" />
            <path d="M 122 18 L 122 22 M 144 34 L 140 38 M 150 46 L 146 46" stroke="#F59E0B" stroke-width="2.5" stroke-linecap="round" />
        @elseif($iconKey === 'sparkle' || $iconKey === 'crown')
            <!-- Royal Shimmer Stars -->
            <circle cx="28" cy="40" r="3" fill="#FBBF24" />
            <circle cx="132" cy="38" r="3" fill="#F43F5E" />
            <circle cx="136" cy="74" r="2.5" fill="#38BDF8" />
            <circle cx="24" cy="72" r="2.5" fill="#10B981" />
        @elseif($iconKey === 'fire')
            <!-- Fire Flame Glow -->
            <path d="M 80 8 C 94 22, 108 32, 102 46 C 94 40, 90 40, 84 46 C 88 32, 82 26, 80 20 C 78 26, 72 32, 76 46 C 70 40, 66 40, 58 46 C 52 32, 66 22, 80 8 Z" fill="#FB923C" />
            <path d="M 80 18 C 86 26, 92 32, 88 42 C 82 38, 80 38, 78 34 C 76 38, 74 38, 70 42 C 68 32, 74 26, 80 18 Z" fill="#FEF08A" />
        @endif

        {{-- 2. HEADPIECES / ACCESSORIES --}}
        @if($iconKey === 'sparkle' || $iconKey === 'crown')
            <!-- Golden 3-point Crown with Rubies -->
            <path d="M 60 42 L 54 22 L 68 30 L 80 16 L 92 30 L 106 22 L 100 42 Z"
                  fill="url(#achvGold_{{ $uid }})"
                  stroke="#B45309"
                  stroke-width="2"
                  stroke-linejoin="round" />
            <circle cx="54" cy="22" r="2.5" fill="#EF4444" />
            <circle cx="80" cy="16" r="3" fill="#3B82F6" />
            <circle cx="106" cy="22" r="2.5" fill="#10B981" />
        @elseif($iconKey === 'medal')
            <!-- Winner Laurel / Athlete Headband -->
            <path d="M 46 54 C 68 46, 92 46, 114 54" stroke="#10B981" stroke-width="5" stroke-linecap="round" />
            <circle cx="80" cy="49" r="3" fill="#FEF08A" />
        @elseif($iconKey === 'fire')
            <!-- Red Flame Headband -->
            <path d="M 44 56 C 68 48, 92 48, 116 56" stroke="#DC2626" stroke-width="6" stroke-linecap="round" />
            <circle cx="80" cy="52" r="3.5" fill="#FEF08A" />
        @elseif($iconKey === 'book')
            <!-- Graduation / Scholar Cap or Star -->
            <polygon points="80,18 112,28 80,38 48,28" fill="#1E293B" />
            <rect x="70" y="36" width="20" height="8" rx="2" fill="#0F172A" />
            <path d="M 112 28 L 118 42" stroke="#F59E0B" stroke-width="2" />
            <circle cx="118" cy="44" r="2.5" fill="#F59E0B" />
        @else
            <!-- Standard Yellow Star Tuft -->
            <path d="M 80 14 L 83 23 L 93 25 L 85 31 L 87 40 L 80 34 L 73 40 L 75 31 L 67 25 L 77 23 Z"
                  fill="url(#achvGold_{{ $uid }})"
                  stroke="#D97706"
                  stroke-width="1.5" />
            <circle cx="80" cy="27" r="2" fill="#FFFBEB" />
        @endif

        {{-- 3. CLOUD BODY (Si Awan Signature) --}}
        <path d="M 46 126
                 C 28 126, 20 106, 26 90
                 C 20 78, 26 62, 40 56
                 C 46 42, 62 38, 76 42
                 C 86 36, 102 38, 110 50
                 C 126 52, 138 66, 134 82
                 C 144 96, 138 116, 122 124
                 C 112 130, 56 130, 46 126 Z"
              fill="url(#achvCloud_{{ $uid }})"
              stroke="{{ $earned ? '#0284C7' : '#64748B' }}"
              stroke-width="3.5"
              stroke-linejoin="round" />

        <!-- Highlights -->
        <path d="M 44 64 C 54 50, 74 46, 88 48 C 76 52, 54 56, 44 68 Z" fill="#FFFFFF" opacity="0.75" />

        <!-- Blush Cheeks -->
        <ellipse cx="44" cy="95" rx="6" ry="4" fill="{{ $earned ? '#FF8FB1' : '#CBD5E1' }}" opacity="0.85" />
        <ellipse cx="116" cy="95" rx="6" ry="4" fill="{{ $earned ? '#FF8FB1' : '#CBD5E1' }}" opacity="0.85" />

        {{-- 4. EYES & EXPRESSIONS --}}
        @if($iconKey === 'trophy' || $iconKey === 'sparkle')
            <!-- Star / Cheerful Happy Eyes -->
            <path d="M 54 82 C 60 74, 68 74, 74 82" stroke="#0F2A43" stroke-width="4" stroke-linecap="round" fill="none" />
            <path d="M 86 82 C 92 74, 100 74, 106 82" stroke="#0F2A43" stroke-width="4" stroke-linecap="round" fill="none" />
            <!-- Open laughing mouth -->
            <path d="M 66 94 C 66 108, 94 108, 94 94 Z" fill="#0F2A43" />
            <path d="M 72 101 C 76 106, 84 106, 88 101 Z" fill="#FF8FB1" />
        @elseif($iconKey === 'book')
            <!-- Smart Round Glasses -->
            <circle cx="64" cy="80" r="9" stroke="#1E293B" stroke-width="2.5" fill="#FFFFFF" fill-opacity="0.4" />
            <circle cx="96" cy="80" r="9" stroke="#1E293B" stroke-width="2.5" fill="#FFFFFF" fill-opacity="0.4" />
            <path d="M 73 80 L 87 80" stroke="#1E293B" stroke-width="2" />
            <circle cx="64" cy="80" r="3.5" fill="#0F2A43" />
            <circle cx="96" cy="80" r="3.5" fill="#0F2A43" />
            <path d="M 72 96 C 76 100, 84 100, 88 96" stroke="#0F2A43" stroke-width="2.5" stroke-linecap="round" fill="none" />
        @else
            <!-- Friendly standard eyes -->
            <circle cx="64" cy="80" r="5.5" fill="#0F2A43" />
            <circle cx="66" cy="78" r="2" fill="#FFFFFF" />
            <circle cx="96" cy="80" r="5.5" fill="#0F2A43" />
            <circle cx="98" cy="78" r="2" fill="#FFFFFF" />
            <path d="M 72 95 C 76 101, 84 101, 88 95" stroke="#0F2A43" stroke-width="3" stroke-linecap="round" fill="none" />
        @endif

        {{-- 5. PROPS HELD IN HANDS --}}
        @if($iconKey === 'trophy')
            <!-- Mascot Holding Golden Trophy Cup with both hands -->
            <path d="M 34 88 C 22 74, 26 58, 38 64 C 44 72, 44 82, 40 88" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
            <path d="M 126 88 C 138 74, 134 58, 122 64 C 116 72, 116 82, 120 88" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
            <!-- Golden Trophy Cup in center -->
            <path d="M 66 52 L 94 52 L 90 74 C 88 84, 72 84, 70 74 Z" fill="url(#achvGold_{{ $uid }})" stroke="#B45309" stroke-width="2" />
            <path d="M 66 56 C 56 56, 56 68, 68 70" stroke="#D97706" stroke-width="2" fill="none" />
            <path d="M 94 56 C 104 56, 104 68, 92 70" stroke="#D97706" stroke-width="2" fill="none" />
            <rect x="76" y="80" width="8" height="6" fill="#D97706" />
            <rect x="72" y="86" width="16" height="4" rx="1.5" fill="#B45309" />
            <path d="M 80 58 L 81 62 L 85 62 L 82 65 L 83 69 L 80 67 L 77 69 L 78 65 L 75 62 L 79 62 Z" fill="#FFFFFF" />
        @elseif($iconKey === 'medal')
            <!-- Mascot Wearing Big Gold Medal around neck -->
            <path d="M 34 98 C 24 102, 24 112, 34 112" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <path d="M 126 98 C 136 102, 136 112, 126 112" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="none" />
            <!-- V-ribbon (Blue & Gold) -->
            <path d="M 68 104 L 80 120 L 92 104" stroke="#2563EB" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none" />
            <!-- Gold Medal -->
            <circle cx="80" cy="126" r="11" fill="url(#achvGold_{{ $uid }})" stroke="#B45309" stroke-width="2" />
            <circle cx="80" cy="126" r="8" stroke="#D97706" stroke-width="1.2" fill="none" />
            <path d="M 80 120 L 81.5 124 L 85.5 124 L 82.5 126.5 L 83.5 130.5 L 80 128 L 76.5 130.5 L 77.5 126.5 L 74.5 124 L 78.5 124 Z" fill="#FFFBEB" />
        @elseif($iconKey === 'heart')
            <!-- Hugging a Warm Pink Heart -->
            <path d="M 80 88 C 76 80, 62 80, 62 92 C 62 104, 80 114, 80 114 C 80 114, 98 104, 98 92 C 98 80, 84 80, 80 88 Z" fill="#F43F5E" stroke="#BE123C" stroke-width="2" />
            <!-- Hands wrapping around heart -->
            <path d="M 54 94 C 54 94, 62 100, 66 102" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" />
            <path d="M 106 94 C 106 94, 98 100, 94 102" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" />
        @elseif($iconKey === 'book')
            <!-- Holding Open Book -->
            <path d="M 56 102 C 66 100, 78 104, 80 108 C 82 104, 94 100, 104 102 L 102 120 C 92 118, 82 122, 80 124 C 78 122, 68 118, 58 120 Z" fill="#FFFFFF" stroke="#0284C7" stroke-width="2" />
            <path d="M 80 108 L 80 124" stroke="#0284C7" stroke-width="2" />
            <!-- Blue Book Cover bottom -->
            <path d="M 56 120 C 68 118, 78 122, 80 124 C 82 122, 92 118, 104 120" stroke="#0369A1" stroke-width="2.5" fill="none" />
        @else
            <!-- Thumbs up double hands -->
            <path d="M 34 94 C 22 84, 24 72, 34 74 C 38 80, 38 90, 38 94" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
            <path d="M 126 94 C 138 84, 136 72, 126 74 C 122 80, 122 90, 122 94" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
        @endif

        {{-- 6. LOCKED OVERLAY BADGE IF NOT EARNED --}}
        @if(!$earned)
            <g id="lock-overlay" transform="translate(112, 110)">
                <circle cx="12" cy="12" r="16" fill="#0F172A" opacity="0.85" />
                <path d="M 8 11 L 8 8 C 8 5.79, 9.79 4, 12 4 C 14.21 4, 16 5.79, 16 8 L 16 11 M 7 11 L 17 11 C 17.55 11, 18 11.45, 18 12 L 18 17 C 18 17.55, 17.55 18, 17 18 L 7 18 C 6.45 18, 6 17.55, 6 17 L 6 12 C 6 11.45, 6.45 11, 7 11 Z" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round" fill="none" />
                <circle cx="12" cy="14.5" r="1" fill="#FFFFFF" />
            </g>
        @endif
    </svg>
</div>
