@props(['monthlyWithTrend'])

<div class="card h-full">
    <div class="card-header">
        <div class="flex items-center gap-2">
            <i data-lucide="bar-chart-3" class="w-4 h-4 text-primary"></i>
            <h5 class="font-semibold text-sm">Ringkasan Bulanan</h5>
        </div>
    </div>
    <div class="card-body !p-0">
        @if($monthlyWithTrend->count() > 0)
        <div class="overflow-x-auto">
            <table class="data-table mb-0">
                <thead>
                    <tr>
                        <th class="!px-4">Bulan</th>
                        <th class="!px-4 text-center">Jurnal</th>
                        <th class="!px-4 text-right">Debit</th>
                        <th class="!px-4 text-right">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyWithTrend as $month)
                    @php
                    $monthName = \Carbon\Carbon::createFromFormat('Y-m', $month->month)->translatedFormat('M Y');
                    @endphp
                    <tr>
                        <td class="!px-4 font-medium">{{ $monthName }}</td>
                        <td class="!px-4 text-center">
                            <span class="badge badge-neutral">{{ $month->total }}</span>
                        </td>
                        <td class="!px-4 text-right font-medium text-success">Rp {{ number_format($month->total_debit, 0, ',', '.') }}</td>
                        <td class="!px-4 text-right font-medium text-danger">Rp {{ number_format($month->total_kredit, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-dashboard.empty-state icon="bar-chart-3" message="Belum ada data bulanan" />
        @endif
    </div>
</div>
