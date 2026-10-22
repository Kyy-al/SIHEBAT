@props([
    'streak'  => 0,      // Number of streak days
    'size'    => 'md',   // xs (28), sm (36), md (48), lg (80), xl (120), 2xl (160)
    'animate' => true,
    'showLabel' => false,
])

@php
    $streak = (int) $streak;

    // Duolingo-style streak mascot evolution tiers
    if ($streak <= 0) {
        $tier = 'padam';
        $title = 'Api Padam';
        $subtitle = 'Mulai nyalakan hari ini!';
        $badgeBg = 'bg-slate-100 text-slate-700 border-slate-300';
    } elseif ($streak < 3) {
        $tier = 'percikan';
        $title = 'Percikan Semangat';
        $subtitle = 'Langkah awal kebiasaan hebat!';
        $badgeBg = 'bg-amber-100 text-amber-900 border-amber-300';
    } elseif ($streak < 7) {
        $tier = 'membara';
        $title = 'Api Membara';
        $subtitle = 'Konsistensi mulai terbentuk!';
        $badgeBg = 'bg-orange-100 text-orange-950 border-orange-300';
    } elseif ($streak < 14) {
        $tier = 'ksatria';
        $title = 'Ksatria Api';
        $subtitle = '1 Minggu tak terputus!';
        $badgeBg = 'bg-rose-100 text-rose-950 border-rose-300';
    } elseif ($streak < 30) {
        $tier = 'pahlawan';
        $title = 'Pahlawan Hebat';
        $subtitle = '2 Minggu+ tak terbendung!';
        $badgeBg = 'bg-purple-100 text-purple-950 border-purple-300';
    } else {
        $tier = 'legenda';
        $title = 'Legenda Abadi';
        $subtitle = '1 Bulan+ teladan tertinggi!';
        $badgeBg = 'bg-gradient-to-r from-amber-200 via-yellow-100 to-amber-200 text-amber-950 border-amber-400';
    }

    $dimensions = match ($size) {
        'xs' => 'w-7 h-7',
        'sm' => 'w-9 h-9',
        'md' => 'w-12 h-12',
        'lg' => 'w-20 h-20 sm:w-24 sm:h-24',
        'xl' => 'w-28 h-28 sm:w-32 sm:h-32',
        '2xl' => 'w-40 h-40 sm:w-48 sm:h-48',
        default => 'w-12 h-12',
    };

    $animClass = $animate ? match ($tier) {
        'padam'    => '',
        'percikan' => 'animate-floaty',
        'membara'  => 'animate-wiggle',
        'ksatria'  => 'animate-bounce',
        'pahlawan' => 'animate-floaty',
        'legenda'  => 'animate-pulse-subtle',
    } : '';

    $uid = uniqid();
@endphp

<div class="inline-flex flex-col items-center justify-center select-none shrink-0" {{ $attributes }} aria-hidden="true">
    <div class="inline-flex items-center justify-center {{ $dimensions }} {{ $animClass }} relative">
        <svg viewBox="0 0 160 160" class="w-full h-full drop-shadow-md overflow-visible" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <!-- Cloud Body Gradients -->
                @if($tier === 'padam')
                    <linearGradient id="cloudGrad_{{ $uid }}" x1="80" y1="25" x2="80" y2="135" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#F1F5F9" />
                        <stop offset="50%" stop-color="#E2E8F0" />
                        <stop offset="100%" stop-color="#CBD5E1" />
                    </linearGradient>
                @elseif($tier === 'legenda')
                    <linearGradient id="cloudGrad_{{ $uid }}" x1="80" y1="25" x2="80" y2="135" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#FFFFFF" />
                        <stop offset="30%" stop-color="#FEF08A" />
                        <stop offset="70%" stop-color="#BAE6FD" />
                        <stop offset="100%" stop-color="#C084FC" />
                    </linearGradient>
                @else
                    <linearGradient id="cloudGrad_{{ $uid }}" x1="80" y1="25" x2="80" y2="135" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#FFFFFF" />
                        <stop offset="35%" stop-color="#E0F2FE" />
                        <stop offset="100%" stop-color="#BAE6FD" />
                    </linearGradient>
                @endif

                <!-- Flame Gradient -->
                <linearGradient id="flameGrad_{{ $uid }}" x1="80" y1="0" x2="80" y2="60" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#FEF08A" />
                    <stop offset="40%" stop-color="#FB923C" />
                    <stop offset="100%" stop-color="#EA580C" />
                </linearGradient>

                <!-- Aura Glow for Super Legend -->
                <radialGradient id="auraGlow_{{ $uid }}" cx="80" cy="80" r="75" fx="80" fy="80" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#FBBF24" stop-opacity="0.5" />
                    <stop offset="60%" stop-color="#A855F7" stop-opacity="0.25" />
                    <stop offset="100%" stop-color="#A855F7" stop-opacity="0" />
                </radialGradient>

                <!-- Soft Shadow beneath mascot -->
                <radialGradient id="shadowGrad_{{ $uid }}" cx="80" cy="148" r="44" fx="80" fy="148" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#0F2A43" stop-opacity="0.18" />
                    <stop offset="100%" stop-color="#0F2A43" stop-opacity="0" />
                </radialGradient>
            </defs>

            <!-- Ground Shadow -->
            <ellipse cx="80" cy="146" rx="{{ $tier === 'padam' ? '36' : '42' }}" ry="6" fill="url(#shadowGrad_{{ $uid }})" />

            {{-- 1. BACKGROUND AURAS / CAPES --}}
            @if($tier === 'legenda')
                <!-- Cosmic Super Saiyan Aura -->
                <circle cx="80" cy="80" r="74" fill="url(#auraGlow_{{ $uid }})" />
                <!-- Radiating Aura Spikes -->
                <g opacity="0.8">
                    <path d="M 80 4 L 84 20 L 76 20 Z" fill="#FBBF24" />
                    <path d="M 136 28 L 124 38 L 120 30 Z" fill="#F59E0B" />
                    <path d="M 152 74 L 136 78 L 138 70 Z" fill="#C084FC" />
                    <path d="M 24 30 L 36 40 L 40 32 Z" fill="#F59E0B" />
                    <path d="M 8 76 L 24 80 L 22 72 Z" fill="#C084FC" />
                </g>
            @elseif($tier === 'pahlawan')
                <!-- Hero Super Cape (Red & Gold Backing) -->
                <path d="M 40 80 C 18 100, 16 138, 28 144 C 40 148, 56 128, 56 110 Z" fill="#DC2626" stroke="#991B1B" stroke-width="2" />
                <path d="M 120 80 C 142 100, 144 138, 132 144 C 120 148, 104 128, 104 110 Z" fill="#DC2626" stroke="#991B1B" stroke-width="2" />
                <!-- Cape Gold Trim -->
                <path d="M 28 144 C 34 146, 42 142, 48 134" stroke="#FDE047" stroke-width="3" stroke-linecap="round" />
                <path d="M 132 144 C 126 146, 118 142, 112 134" stroke="#FDE047" stroke-width="3" stroke-linecap="round" />
            @endif

            {{-- 2. BACK FLAMES FOR HIGH TIERS --}}
            @if($tier === 'ksatria' || $tier === 'pahlawan' || $tier === 'legenda')
                <!-- Big Roaring Flame behind head -->
                <path d="M 80 2
                         C 96 18, 118 30, 114 52
                         C 106 46, 100 44, 94 48
                         C 98 34, 90 24, 80 18
                         C 70 24, 62 34, 66 48
                         C 60 44, 54 46, 46 52
                         C 42 30, 64 18, 80 2 Z"
                      fill="url(#flameGrad_{{ $uid }})"
                      stroke="#C2410C"
                      stroke-width="2" />
                <!-- Inner Yellow Flame Core -->
                <path d="M 80 12
                         C 90 24, 100 32, 98 48
                         C 92 42, 88 42, 80 34
                         C 72 42, 68 42, 62 48
                         C 60 32, 70 24, 80 12 Z"
                      fill="#FEF08A" />
            @elseif($tier === 'membara')
                <!-- Medium Cheerful Flame on Top -->
                <path d="M 80 10
                         C 92 22, 106 32, 102 46
                         C 94 42, 90 42, 86 46
                         C 88 34, 84 28, 80 22
                         C 76 28, 72 34, 74 46
                         C 70 42, 66 42, 58 46
                         C 54 32, 68 22, 80 10 Z"
                      fill="url(#flameGrad_{{ $uid }})"
                      stroke="#EA580C"
                      stroke-width="1.8" />
                <path d="M 80 20 C 86 28, 92 34, 90 44 C 84 40, 82 40, 80 34 C 78 40, 76 40, 70 44 C 68 34, 74 28, 80 20 Z" fill="#FEF08A" />
            @elseif($tier === 'padam')
                <!-- Wisp of Thin Grey Smoke & Ice Crystals -->
                <path d="M 80 38 C 76 30, 84 22, 78 14 C 74 8, 82 4, 80 0" stroke="#94A3B8" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="3 3" fill="none" />
                <path d="M 88 34 C 92 28, 88 22, 94 16" stroke="#CBD5E1" stroke-width="2" stroke-linecap="round" fill="none" />
                <!-- Ice crystal -->
                <path d="M 32 40 L 36 44 M 36 40 L 32 44 M 34 38 L 34 46" stroke="#38BDF8" stroke-width="2" stroke-linecap="round" />
                <path d="M 128 42 L 132 46 M 132 42 L 128 46 M 130 40 L 130 48" stroke="#38BDF8" stroke-width="2" stroke-linecap="round" />
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
                  fill="url(#cloudGrad_{{ $uid }})"
                  stroke="{{ $tier === 'padam' ? '#64748B' : ($tier === 'legenda' ? '#7C3AED' : '#0284C7') }}"
                  stroke-width="3.5"
                  stroke-linejoin="round" />

            <!-- Cloud inner shine -->
            <path d="M 44 64 C 54 50, 74 46, 88 48 C 76 52, 54 56, 44 68 Z" fill="#FFFFFF" opacity="0.75" />

            {{-- 4. CHEEKS --}}
            @if($tier === 'padam')
                <!-- Cool pale blue cheeks -->
                <ellipse cx="44" cy="96" rx="6" ry="4" fill="#93C5FD" opacity="0.6" />
                <ellipse cx="116" cy="96" rx="6" ry="4" fill="#93C5FD" opacity="0.6" />
            @else
                <!-- Warm Rosy Bubblegum cheeks -->
                <ellipse cx="44" cy="95" rx="7" ry="5" fill="#FF8FB1" opacity="0.85" />
                <ellipse cx="116" cy="95" rx="7" ry="5" fill="#FF8FB1" opacity="0.85" />
            @endif

            {{-- 5. ACCESSORIES & HEADPIECES --}}
            @if($tier === 'legenda')
                <!-- Supreme Golden Crown with Flame Gem -->
                <path d="M 58 48 L 54 26 L 68 34 L 80 18 L 92 34 L 106 26 L 102 48 Z"
                      fill="#F59E0B"
                      stroke="#B45309"
                      stroke-width="2"
                      stroke-linejoin="round" />
                <circle cx="80" cy="24" r="3.5" fill="#EF4444" stroke="#B91C1C" stroke-width="1" />
                <circle cx="54" cy="26" r="2.5" fill="#3B82F6" />
                <circle cx="106" cy="26" r="2.5" fill="#10B981" />
                <!-- Sparkling Stars floating around -->
                <path d="M 28 36 L 30 40 L 34 40 L 31 43 L 32 47 L 28 44 L 24 47 L 25 43 L 22 40 L 26 40 Z" fill="#FDE047" />
                <path d="M 132 34 L 134 38 L 138 38 L 135 41 L 136 45 L 132 42 L 128 45 L 129 41 L 126 38 L 130 38 Z" fill="#FDE047" />
            @elseif($tier === 'pahlawan')
                <!-- Cool Sunglasses -->
                <g id="sunglasses">
                    <!-- Left lens -->
                    <rect x="52" y="74" width="22" height="15" rx="4" fill="#0F172A" stroke="#0284C7" stroke-width="1.5" />
                    <!-- Right lens -->
                    <rect x="86" y="74" width="22" height="15" rx="4" fill="#0F172A" stroke="#0284C7" stroke-width="1.5" />
                    <!-- Bridge bar -->
                    <path d="M 74 78 L 86 78" stroke="#0F172A" stroke-width="3" stroke-linecap="round" />
                    <!-- Gloss shine -->
                    <path d="M 55 77 L 64 77" stroke="#38BDF8" stroke-width="1.5" stroke-linecap="round" />
                    <path d="M 89 77 L 98 77" stroke="#38BDF8" stroke-width="1.5" stroke-linecap="round" />
                </g>
            @elseif($tier === 'ksatria')
                <!-- Red Flame Warrior Headband -->
                <path d="M 40 58 C 66 52, 94 52, 120 58" stroke="#DC2626" stroke-width="7" stroke-linecap="round" />
                <!-- Headband Tails Fluttering -->
                <path d="M 120 58 C 132 60, 138 68, 142 78 M 120 58 C 134 54, 144 58, 148 68" stroke="#DC2626" stroke-width="4.5" stroke-linecap="round" />
                <!-- Headband Golden Crest -->
                <circle cx="80" cy="55" r="4.5" fill="#FDE047" stroke="#B45309" stroke-width="1.5" />
            @elseif($tier === 'percikan')
                <!-- Yellow Star Tuft on head -->
                <path d="M 80 18 L 83 25 L 91 26 L 85 31 L 86 38 L 80 34 L 74 38 L 75 31 L 69 26 L 77 25 Z" fill="#FDE047" stroke="#D97706" stroke-width="1.5" />
            @endif

            {{-- 6. EYES AND MOUTH --}}
            @if($tier === 'padam')
                <!-- Sad sleepy eyes with cute tiny tear -->
                <path d="M 54 82 C 60 86, 68 86, 74 82" stroke="#475569" stroke-width="3.5" stroke-linecap="round" fill="none" />
                <path d="M 86 82 C 92 86, 100 86, 106 82" stroke="#475569" stroke-width="3.5" stroke-linecap="round" fill="none" />
                <!-- Little blue teardrop -->
                <path d="M 46 92 C 44 96, 48 98, 48 95 Z" fill="#38BDF8" />
                <!-- Sad cute pout mouth -->
                <path d="M 72 102 C 76 97, 84 97, 88 102" stroke="#475569" stroke-width="3" stroke-linecap="round" fill="none" />
            @elseif($tier === 'pahlawan')
                <!-- Smug Confident Hero Smile (Eyes covered by sunglasses) -->
                <path d="M 68 98 C 76 106, 88 104, 94 96" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
            @elseif($tier === 'legenda')
                <!-- Star-struck / Cosmic Happy Eyes (^ ^) -->
                <path d="M 54 82 C 60 74, 68 74, 74 82" stroke="#0F2A43" stroke-width="4.5" stroke-linecap="round" fill="none" />
                <path d="M 86 82 C 92 74, 100 74, 106 82" stroke="#0F2A43" stroke-width="4.5" stroke-linecap="round" fill="none" />
                <!-- Joyful open mouth -->
                <path d="M 66 94 C 66 108, 94 108, 94 94 Z" fill="#0F2A43" />
                <path d="M 72 101 C 76 106, 84 106, 88 101 Z" fill="#FF8FB1" />
            @elseif($tier === 'ksatria' || $tier === 'membara')
                <!-- Determined Fiery Anime Eyes -->
                <circle cx="64" cy="80" r="6" fill="#0F2A43" />
                <circle cx="66" cy="78" r="2.2" fill="#FFFFFF" />
                <circle cx="62" cy="82" r="1.2" fill="#FFFFFF" />
                <circle cx="96" cy="80" r="6" fill="#0F2A43" />
                <circle cx="98" cy="78" r="2.2" fill="#FFFFFF" />
                <circle cx="94" cy="82" r="1.2" fill="#FFFFFF" />
                <!-- Confident grin -->
                <path d="M 68 95 C 74 103, 86 103, 92 95" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
            @else
                <!-- Percikan: Sweet Friendly Anime Eyes -->
                <ellipse cx="64" cy="80" rx="5.5" ry="6.5" fill="#0F2A43" />
                <circle cx="66" cy="77.5" r="2.2" fill="#FFFFFF" />
                <circle cx="62" cy="82" r="1.2" fill="#FFFFFF" />
                <ellipse cx="96" cy="80" rx="5.5" ry="6.5" fill="#0F2A43" />
                <circle cx="98" cy="77.5" r="2.2" fill="#FFFFFF" />
                <circle cx="94" cy="82" r="1.2" fill="#FFFFFF" />
                <path d="M 72 95 C 76 101, 84 101, 88 95" stroke="#0F2A43" stroke-width="3.5" stroke-linecap="round" fill="none" />
            @endif

            {{-- 7. HANDS & PROPS --}}
            @if($tier === 'padam')
                <!-- Holding a tiny unlit candle with cold base -->
                <path d="M 34 100 C 24 104, 24 114, 34 114" stroke="#64748B" stroke-width="3.5" stroke-linecap="round" fill="none" />
                <path d="M 126 100 C 136 104, 136 114, 126 114" stroke="#64748B" stroke-width="3.5" stroke-linecap="round" fill="none" />
            @elseif($tier === 'percikan')
                <!-- Holding a cute baby flame in hand -->
                <path d="M 32 98 C 22 102, 24 114, 34 112" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="none" />
                <!-- Right hand cup holding spark -->
                <path d="M 124 94 C 134 94, 138 106, 126 110" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
                <!-- Little floating flame spark -->
                <path d="M 134 86 C 138 80, 142 82, 140 88 C 137 92, 132 92, 134 86 Z" fill="#FB923C" stroke="#EA580C" stroke-width="1" />
                <circle cx="136" cy="88" r="1.5" fill="#FEF08A" />
            @elseif($tier === 'membara')
                <!-- Both hands raised like fist pump -->
                <path d="M 34 94 C 20 84, 22 68, 32 68 C 38 74, 38 86, 38 94" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
                <path d="M 126 94 C 140 84, 138 68, 128 68 C 122 74, 122 86, 122 94" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
            @elseif($tier === 'ksatria')
                <!-- Fist pump with blazing torch / fire gauntlet -->
                <path d="M 34 92 C 18 80, 22 64, 32 66 C 38 72, 38 84, 38 92" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
                <path d="M 126 92 C 142 80, 138 64, 128 66 C 122 72, 122 84, 122 92" stroke="#0284C7" stroke-width="3.5" stroke-linecap="round" fill="#E0F2FE" />
                <!-- Fire sparks on left and right -->
                <circle cx="20" cy="56" r="3" fill="#FB923C" />
                <circle cx="140" cy="56" r="3" fill="#FB923C" />
            @elseif($tier === 'pahlawan')
                <!-- Hands on hips hero pose -->
                <path d="M 36 94 C 26 96, 26 112, 38 116" stroke="#0284C7" stroke-width="4" stroke-linecap="round" fill="none" />
                <path d="M 124 94 C 134 96, 134 112, 122 116" stroke="#0284C7" stroke-width="4" stroke-linecap="round" fill="none" />
            @elseif($tier === 'legenda')
                <!-- Victorious open arms with aura rings -->
                <path d="M 34 88 C 18 72, 24 54, 34 56 C 40 66, 40 78, 38 88" stroke="#7C3AED" stroke-width="3.5" stroke-linecap="round" fill="#EDE9FE" />
                <path d="M 126 88 C 142 72, 136 54, 126 56 C 120 66, 120 78, 122 88" stroke="#7C3AED" stroke-width="3.5" stroke-linecap="round" fill="#EDE9FE" />
            @endif
        </svg>
    </div>

    @if($showLabel)
        <div class="mt-1 text-center">
            <span class="inline-block text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full border {{ $badgeBg }}">
                {{ $title }}
            </span>
            <span class="block text-[11px] font-extrabold text-ink-900 mt-0.5">
                🔥 {{ $streak }} Hari
            </span>
        </div>
    @endif
</div>
