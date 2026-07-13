@php
$bgGradient = 'bg-gradient-to-br from-[#1E3A8A] via-[#1E3A8A] to-[#2563EB]';
@endphp

<div class="flex w-full min-h-screen">
    {{-- Left Panel: Decorative (hidden on mobile) --}}
    <div class="hidden lg:flex lg:w-[35%] {{ $bgGradient }} relative overflow-hidden items-center justify-center">
        <svg class="absolute bottom-0 left-0 w-full h-auto text-white/5" viewBox="0 0 600 400" preserveAspectRatio="none" fill="currentColor">
            <path d="M0 300 Q150 200 300 250 T600 200 L600 400 L0 400 Z" opacity="0.3"/>
            <path d="M0 350 Q200 250 400 300 T600 250 L600 400 L0 400 Z" opacity="0.15"/>
        </svg>
        <svg class="absolute top-0 right-0 w-3/4 h-auto text-white/5" viewBox="0 0 500 500" fill="currentColor">
            <circle cx="400" cy="100" r="180" opacity="0.15"/>
            <circle cx="450" cy="150" r="120" opacity="0.1"/>
        </svg>

        <div class="relative z-10 w-full max-w-sm px-12">
            <svg viewBox="0 0 400 320" fill="none" stroke="white" stroke-width="1.5" class="w-full h-auto opacity-30" stroke-linecap="round" stroke-linejoin="round">
                <rect x="40" y="160" width="36" height="100" opacity="0.4"/>
                <rect x="48" y="140" width="20" height="20" opacity="0.4"/>
                <rect x="96" y="120" width="40" height="140" opacity="0.4"/>
                <rect x="104" y="100" width="24" height="20" opacity="0.4"/>
                <rect x="156" y="140" width="32" height="120" opacity="0.4"/>
                <rect x="208" y="100" width="44" height="160" opacity="0.4"/>
                <rect x="216" y="80" width="28" height="20" opacity="0.4"/>
                <rect x="272" y="130" width="30" height="130" opacity="0.4"/>
                <rect x="312" y="110" width="36" height="150" opacity="0.4"/>
                <path d="M40 260 L80 240 L130 250 L180 210 L230 220 L280 180 L340 190 L370 150" stroke-width="2" opacity="0.5"/>
                <circle cx="370" cy="150" r="3" fill="white" fill-opacity="0.6" stroke="none"/>
                <line x1="40" y1="280" x2="370" y2="280" opacity="0.2" stroke-dasharray="4 4"/>
                <line x1="40" y1="260" x2="370" y2="260" opacity="0.15" stroke-dasharray="4 4"/>
                <line x1="40" y1="240" x2="370" y2="240" opacity="0.15" stroke-dasharray="4 4"/>
                <line x1="40" y1="220" x2="370" y2="220" opacity="0.15" stroke-dasharray="4 4"/>
                <line x1="40" y1="200" x2="370" y2="200" opacity="0.15" stroke-dasharray="4 4"/>
                <line x1="40" y1="180" x2="370" y2="180" opacity="0.15" stroke-dasharray="4 4"/>
                <line x1="40" y1="160" x2="370" y2="160" opacity="0.15" stroke-dasharray="4 4"/>
            </svg>
        </div>

        <div class="absolute inset-0 bg-gradient-to-t from-[#1E3A8A]/60 via-transparent to-transparent"></div>
    </div>

    {{-- Right Panel: Card --}}
    <div class="flex w-full lg:w-[65%] items-center justify-center px-4 sm:px-6 lg:px-12 py-8">
        <div class="w-full max-w-[460px] mx-auto">
            <div class="bg-white rounded-[20px] shadow-[0_1px_2px_rgba(0,0,0,0.04),0_8px_24px_rgba(0,0,0,0.06)] p-12 animate-[cardIn_500ms_cubic-bezier(0.23,1,0.32,1)_both]">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes cardIn {
        from { opacity: 0; transform: translateY(16px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    [x-cloak] { display: none !important; }
</style>
