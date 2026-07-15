@props(['activeMenu' => null])

@php
if (!$activeMenu) {
    $path = request()->path();
    $activeMenu = match(true) {
        str_contains($path, 'account/header') => 'coa',
        str_contains($path, 'account/coa') => 'coa',
        str_contains($path, 'account/headercoa') => 'coa',
        str_contains($path, 'periode') => 'periode',
        str_contains($path, 'saldoawal') => 'saldoawal',
        str_contains($path, 'jurnaling') => 'jurnaling',
        str_contains($path, 'bukubesar') => 'bukubesar',
        str_contains($path, 'neracasaldo') => 'neracasaldo',
        str_contains($path, 'product') => 'users',
        str_contains($path, 'rekaptampil') => 'rekapjurnal',
        str_contains($path, 'otorisator') => 'pengaturan',
        str_contains($path, 'profile') => 'profile',
        str_contains($path, 'posting') => 'posting',
        default => 'dashboard',
    };
}

$u = Auth::user()->usertype;
$prefix = match($u) { 'rootsuperuser' => 'rootsuperuser', 'bod' => 'bod', 'operator' => 'operator', default => 'admin' };
$canManage = in_array($u, ['rootsuperuser', 'admin']);
$canFullAccess = in_array($u, ['rootsuperuser', 'admin', 'operator']);

$navItems = [
    'MAIN' => [
        ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bx bx-home-circle', 'route' => $prefix . '/dashboard', 'visible' => true],
    ],
    'MASTER' => [
        ['id' => 'periode', 'label' => 'Periode', 'icon' => 'bx bx-calendar', 'route' => $prefix . '/periodes', 'visible' => $canFullAccess],
        ['id' => 'coa', 'label' => 'COA', 'icon' => 'bx bx-spreadsheet', 'route' => '#', 'visible' => $canFullAccess, 'children' => [
            ['id' => 'coa-header', 'label' => 'Header', 'route' => $prefix . '/account/header'],
            ['id' => 'coa-akun', 'label' => 'COA', 'route' => $prefix . '/account/coa'],
            ['id' => 'coa-combine', 'label' => 'Header & COA', 'route' => $prefix . '/account/headercoa'],
        ]],
        ['id' => 'saldoawal', 'label' => 'Saldo Awal', 'icon' => 'bx bx-money', 'route' => $prefix . '/saldoawal', 'visible' => $canFullAccess],
    ],
    'TRANSACTION' => [
        ['id' => 'jurnaling', 'label' => 'Jurnaling', 'icon' => 'bx bx-notepad', 'route' => '#', 'visible' => $canFullAccess, 'children' => [
            ['id' => 'jurnaling-kasmasuk', 'label' => 'Kas Masuk', 'route' => $prefix . '/jurnaling'],
            ['id' => 'jurnaling-kaskeluar', 'label' => 'Kas Keluar', 'route' => $prefix . '/jurnaling/kaskeluar'],
            ['id' => 'jurnaling-bankmasuk', 'label' => 'Bank Masuk', 'route' => $prefix . '/jurnaling/bankmasuk'],
            ['id' => 'jurnaling-bankkeluar', 'label' => 'Bank Keluar', 'route' => $prefix . '/jurnaling/bankkeluar'],
            ['id' => 'jurnaling-memorial', 'label' => 'Memorial', 'route' => $prefix . '/jurnaling/memorial'],
            ['id' => 'jurnaling-memorialpenutup', 'label' => 'Memorial Penutup', 'route' => $prefix . '/jurnaling/memorialpenutup'],
            ['id' => 'jurnaling-showing', 'label' => 'Lihat Jurnal', 'route' => $prefix . '/jurnaling/showing'],
        ]],
    ],
    'REPORT' => [
        ['id' => 'rekapjurnal', 'label' => 'Rekap Jurnal', 'icon' => 'bx bx-receipt', 'route' => $prefix . '/jurnaling/showing', 'visible' => true],
        ['id' => 'bukubesar', 'label' => 'Buku Besar', 'icon' => 'bx bx-book', 'route' => $prefix . '/bukubesar', 'visible' => true],
        ['id' => 'neracasaldo', 'label' => 'Neraca Saldo', 'icon' => 'bx bx-calculator', 'route' => $prefix . '/neracasaldo/', 'visible' => true],
    ],
    'SYSTEM' => [
        ['id' => 'users', 'label' => 'User Management', 'icon' => 'bx bx-user', 'route' => $prefix . '/products', 'visible' => $canManage],
        ['id' => 'posting', 'label' => 'Posting', 'icon' => 'bx bx-upload', 'route' => $prefix . '/posting', 'visible' => $u === 'rootsuperuser'],
        ['id' => 'pengaturan', 'label' => 'Pengaturan', 'icon' => 'bx bx-cog', 'route' => $prefix . '/otorisator/home', 'visible' => $canManage],
    ],
];
@endphp

<aside class="w-sidebar bg-white border-r border-gray-200 flex flex-col flex-shrink-0 h-screen overflow-hidden">
    <!-- Brand -->
    <div class="h-16 flex items-center gap-3 px-5 border-b border-gray-100 flex-shrink-0">
        <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center shadow-sm">
            <span class="text-white font-bold text-sm">W</span>
        </div>
        <div>
            <span class="font-bold text-sm text-gray-900">WAS</span>
            <span class="block text-[10px] text-gray-400 font-semibold tracking-wider uppercase">Accounting System</span>
        </div>
    </div>

    <!-- Scrollable nav -->
    <nav class="flex-1 overflow-y-auto py-3 space-y-1 scrollbar-thin">
        @foreach($navItems as $section => $items)
        @php
        $hasVisible = count(array_filter($items, fn($i) => $i['visible'] ?? false));
        @endphp
        @if($hasVisible)
        <div class="sidebar-section">{{ $section }}</div>
        @foreach($items as $item)
        @if($item['visible'] ?? true)
        @if(isset($item['children']))
        @php
        $isParentActive = $activeMenu === $item['id'] || collect($item['children'])->pluck('id')->contains($activeMenu);
        @endphp
        <div x-cloak x-data="{ open: {{ $isParentActive ? 'true' : 'false' }} }">
            <button @click="open = !open" class="sidebar-item w-full text-left {{ $isParentActive ? 'active' : '' }}">
                <i class="{{ $item['icon'] }} text-lg"></i>
                <span class="flex-1">{{ $item['label'] }}</span>
                <i class="bx bx-chevron-down text-sm transition-transform duration-150" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" x-collapse.duration.150ms>
                <div class="ml-11 space-y-0.5 py-1">
                    @foreach($item['children'] as $child)
                    @php $isChildActive = $activeMenu === $child['id']; @endphp
                    <a href="{{ route($child['route']) }}"
                       class="flex items-center gap-2.5 px-3 py-1.5 text-sm rounded-lg transition-all duration-150
                              {{ $isChildActive ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        <span class="w-1 h-1 rounded-full {{ $isChildActive ? 'bg-primary' : 'bg-gray-300' }}"></span>
                        {{ $child['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @else
        <a href="{{ route($item['route']) }}"
           class="sidebar-item {{ $activeMenu === $item['id'] ? 'active' : '' }}">
            <i class="{{ $item['icon'] }} text-lg"></i>
            <span>{{ $item['label'] }}</span>
        </a>
        @endif
        @endif
        @endforeach
        @endif
        @endforeach
    </nav>

    <!-- User section at bottom -->
    <div class="border-t border-gray-100 p-3 flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}" x-data>
            @csrf
            <button type="submit" class="sidebar-item w-full text-left text-gray-400 hover:text-red-600">
                <i class="bx bx-log-out text-lg"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
