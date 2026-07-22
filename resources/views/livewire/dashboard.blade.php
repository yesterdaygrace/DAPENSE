<div wire:key="dashboard-root" class="space-y-6">
    {{-- Hero --}}
    <x-dashboard.hero :roleLabel="$this->roleLabel()" />

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
        <x-dashboard.kpi-card icon="file-text" title="Total Jurnal" :value="number_format($totalEntries, 0, ',', '.')" :trend="$entriesTrend" color="#1D4ED8" bg="rgba(29,78,216,0.08)" />
        <x-dashboard.kpi-card icon="arrow-down-circle" title="Total Debit" :value="'Rp ' . number_format($totalDebit, 0, ',', '.')" :trend="$debitTrend" color="#16A34A" bg="rgba(22,163,74,0.08)" />
        <x-dashboard.kpi-card icon="arrow-up-circle" title="Total Kredit" :value="'Rp ' . number_format($totalKredit, 0, ',', '.')" :trend="$kreditTrend" color="#DC2626" bg="rgba(220,38,38,0.08)" />
        <x-dashboard.kpi-card icon="calendar" title="Periode Aktif" :value="$periodeAktif->nama_periode ?? '—'" color="#F59E0B" bg="rgba(245,158,11,0.08)" />
    </div>

    {{-- Module Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @if($this->isRole('bod'))
            @foreach([
                ['name' => 'Jurnaling', 'route' => 'jurnaling', 'icon' => 'bx bx-notepad', 'desc' => 'Lihat jurnal transaksi'],
                ['name' => 'Buku Besar', 'route' => 'bukubesar', 'icon' => 'bx bx-book', 'desc' => 'Ringkasan akun per buku besar'],
                ['name' => 'Rekap Jurnal', 'route' => 'jurnaling-list', 'icon' => 'bx bx-receipt', 'desc' => 'Rekapitulasi jurnal', 'badge' => 'NEW'],
                ['name' => 'Neraca Saldo', 'route' => 'neraca-saldo', 'icon' => 'bx bx-calculator', 'desc' => 'Neraca saldo periode'],
            ] as $menu)
                <x-dashboard.module-card :name="$menu['name']" :route="$menu['route']" :icon="$menu['icon']" :desc="$menu['desc']" :badge="($menu['badge'] ?? null)" />
            @endforeach
        @elseif($this->isRole('operator'))
            @foreach([
                ['name' => 'Periode', 'route' => 'periodes', 'icon' => 'bx bx-calendar', 'desc' => 'Atur periode akuntansi'],
                ['name' => 'COA', 'route' => 'coa-workspace', 'icon' => 'bx bx-spreadsheet', 'desc' => 'Kode akun'],
                ['name' => 'Saldo Awal', 'route' => 'saldo-awal', 'icon' => 'bx bx-money', 'desc' => 'Saldo awal periode'],
                ['name' => 'Jurnaling', 'route' => 'jurnaling', 'icon' => 'bx bx-notepad', 'desc' => 'Entri jurnal transaksi'],
                ['name' => 'Buku Besar', 'route' => 'bukubesar', 'icon' => 'bx bx-book', 'desc' => 'Ringkasan akun per buku besar'],
                ['name' => 'Rekap Jurnal', 'route' => 'jurnaling-list', 'icon' => 'bx bx-receipt', 'desc' => 'Rekapitulasi jurnal', 'badge' => 'NEW'],
                ['name' => 'Neraca Saldo', 'route' => 'neraca-saldo', 'icon' => 'bx bx-calculator', 'desc' => 'Neraca saldo periode'],
            ] as $menu)
                <x-dashboard.module-card :name="$menu['name']" :route="$menu['route']" :icon="$menu['icon']" :desc="$menu['desc']" :badge="($menu['badge'] ?? null)" />
            @endforeach
        @else
            @foreach([
                ['name' => 'Manajemen Pengguna', 'route' => 'users', 'icon' => 'bx bx-user', 'desc' => 'Kelola pengguna sistem'],
                ['name' => 'Periode', 'route' => 'periodes', 'icon' => 'bx bx-calendar', 'desc' => 'Atur periode akuntansi'],
                ['name' => 'COA', 'route' => 'coa-workspace', 'icon' => 'bx bx-spreadsheet', 'desc' => 'Kode akun'],
                ['name' => 'Saldo Awal', 'route' => 'saldo-awal', 'icon' => 'bx bx-money', 'desc' => 'Saldo awal periode'],
                ['name' => 'Jurnaling', 'route' => 'jurnaling', 'icon' => 'bx bx-notepad', 'desc' => 'Entri jurnal transaksi'],
                ['name' => 'Buku Besar', 'route' => 'bukubesar', 'icon' => 'bx bx-book', 'desc' => 'Ringkasan akun per buku besar'],
                ['name' => 'Rekap Jurnal', 'route' => 'jurnaling-list', 'icon' => 'bx bx-receipt', 'desc' => 'Rekapitulasi jurnal', 'badge' => 'NEW'],
                ['name' => 'Neraca Saldo', 'route' => 'neraca-saldo', 'icon' => 'bx bx-calculator', 'desc' => 'Neraca saldo periode'],
            ] as $menu)
                <x-dashboard.module-card :name="$menu['name']" :route="$menu['route']" :icon="$menu['icon']" :desc="$menu['desc']" :badge="($menu['badge'] ?? null)" />
            @endforeach
        @endif
    </div>

    {{-- Activity & Monthly Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-7 gap-6">
        <div class="lg:col-span-4">
            <x-dashboard.activity-list :activities="$activities" />
        </div>
        <div class="lg:col-span-3">
            <x-dashboard.monthly-summary :monthlyWithTrend="$monthlySummary" />
        </div>
    </div>
</div>
