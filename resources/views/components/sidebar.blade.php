@props(['activeMenu' => null])

@php
if (!$activeMenu) {
    $path = request()->path();
    $activeMenu = match(true) {
        str_contains($path, 'dashboard') => 'dashboard',
        str_contains($path, 'master-data') => 'master-data',
        str_contains($path, 'transactions') => 'transactions',
        str_contains($path, 'reports') => 'reports',
        str_contains($path, 'finance') => 'finance',
        str_contains($path, 'administration') => 'administration',
        str_contains($path, 'settings') => 'settings',
        default => 'dashboard',
    };
}

$u = Auth::user()->usertype;

$navItems = [
    ['id' => 'dashboard', 'label' => 'Dasbor', 'icon' => 'bx bx-home-circle', 'route' => match($u) {
        'rootsuperuser' => 'rootsuperuser/dashboard',
        'admin' => 'admin/dashboard',
        'operator' => 'operator/dashboard',
        'bod' => 'bod/dashboard',
        default => 'dashboard',
    }, 'visible' => true],
    ['id' => 'master-data', 'label' => 'Data Master', 'icon' => 'bx bx-data', 'route' => 'master-data', 'visible' => true],
    ['id' => 'transactions', 'label' => 'Transaksi', 'icon' => 'bx bx-transfer', 'route' => 'transactions', 'visible' => true],
    ['id' => 'reports', 'label' => 'Laporan', 'icon' => 'bx bx-bar-chart-alt-2', 'route' => 'reports', 'visible' => true],
    ['id' => 'finance', 'label' => 'Keuangan', 'icon' => 'bx bx-dollar', 'route' => 'finance', 'visible' => true],
    ['id' => 'administration', 'label' => 'Administrasi', 'icon' => 'bx bx-shield-quarter', 'route' => 'administration', 'visible' => in_array($u, ['rootsuperuser', 'admin'])],
    ['id' => 'settings', 'label' => 'Pengaturan', 'icon' => 'bx bx-cog', 'route' => 'settings', 'visible' => true],
];
@endphp

{{--
  Two-panel sidebar — no layout reflow, GPU-accelerated transform hover.

  ICONS COLUMN (72px, always visible on desktop):
    Fixed-width left column with nav icons centered.
    Main content permanently sits at lg:ml-[72px].

  LABELS PANEL (188px, slides out on hover via transform: translateX):
    Absolutely positioned to the right of icons column.
    On desktop hover: slides in from the left, overlaying content.
    200ms debounce on mouseleave prevents flickering.
    visibility toggling prevents panel bleed-through in collapsed state.

  Mobile: entire sidebar slides in/out via sidebarOpen.
--}}

<aside x-data="{ hovered: false, hoverTimer: null }"
       x-on:mouseenter="if (window.innerWidth >= 1024) { hovered = true; clearTimeout(hoverTimer); }"
       x-on:mouseleave="if (window.innerWidth >= 1024) { hoverTimer = setTimeout(() => hovered = false, 200); }"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed top-0 left-0 h-screen z-30
              w-[72px]
              -translate-x-full
              lg:translate-x-0
              transition-transform duration-300 ease-in-out
              will-change-transform">

    {{-- ===== ICONS COLUMN (72px, always visible on desktop) ===== --}}
    <div class="absolute inset-0 w-[72px] flex flex-col bg-white border-r border-gray-200 z-10">
        <!-- Brand icon -->
        <div class="h-16 flex items-center justify-center border-b border-gray-100 flex-shrink-0">
            <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center shadow-sm">
                <span class="text-white font-bold text-sm">W</span>
            </div>
        </div>

        <!-- Nav icons -->
        <nav class="flex-1 overflow-y-auto py-3 space-y-1 sidebar-nav">
            @foreach($navItems as $item)
            @if($item['visible'] ?? true)
            <a href="{{ route($item['route']) }}"
               class="flex items-center justify-center h-10 mx-2 rounded-[--radius-button] transition-colors duration-150
                      {{ $activeMenu === $item['id'] ? 'bg-primary-50 text-primary-700' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="{{ $item['icon'] }} text-lg"></i>
            </a>
            @endif
            @endforeach
        </nav>

        <!-- Logout icon -->
        <div class="border-t border-gray-100 py-2 flex-shrink-0">
            <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <button type="submit"
                        class="flex items-center justify-center h-10 mx-auto w-10 rounded-[--radius-button] text-gray-400 hover:text-red-600 hover:bg-gray-100 transition-colors duration-150">
                    <i class="bx bx-log-out text-lg"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- ===== LABELS PANEL (188px, absolutely positioned, slides via transform) ===== --}}
    {{-- Collapsed: shifted left via -translate-x-full, invisible to prevent bleed-through.
         Expanded: slides to natural position (right of icons column). --}}
    <div x-bind:class="hovered ? 'lg:translate-x-0 lg:visible' : 'lg:-translate-x-full lg:invisible'"
         class="absolute top-0 left-full w-[188px] h-full
                bg-white border-r border-gray-200 shadow-sidebar
                flex flex-col
                transition-all duration-300 ease-in-out will-change-transform
                overflow-hidden">
        <!-- Brand text -->
        <div class="h-16 flex items-center border-b border-gray-100 px-5 flex-shrink-0">
            <div>
                <span class="font-bold text-sm text-gray-900">WAS</span>
                <span class="block text-[10px] text-gray-400 font-semibold tracking-wider uppercase">Sistem Akuntansi</span>
            </div>
        </div>

        <!-- Nav labels -->
        <nav class="flex-1 overflow-y-auto py-3 space-y-1 sidebar-nav">
            @foreach($navItems as $item)
            @if($item['visible'] ?? true)
            <a href="{{ route($item['route']) }}"
               class="flex items-center h-10 px-5 text-sm font-medium rounded-[--radius-button] transition-colors duration-150
                      {{ $activeMenu === $item['id'] ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}">
                <span>{{ $item['label'] }}</span>
            </a>
            @endif
            @endforeach
        </nav>

        <!-- Logout label -->
        <div class="border-t border-gray-100 py-2 flex-shrink-0">
            <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <button type="submit"
                        class="flex items-center h-10 px-5 w-full text-sm font-medium text-gray-400 hover:text-red-600 hover:bg-gray-100 rounded-[--radius-button] transition-colors duration-150">
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</aside>
