<tr>
    <td><strong>{{ $header->kode_header }}</strong></td>
    <td><strong>{{ $header->nama_header }}</strong></td>
    <td><strong>{{ number_format($header->total_saldo_awal_debit - $header->total_saldo_awal_kredit, 2) }}</strong></td>
    <td><strong>{{ number_format($header->total_debit, 2) }}</strong></td>
    <td><strong>{{ number_format($header->total_kredit, 2) }}</strong></td>
    <td><strong>{{ number_format(abs($header->total_saldo_akhir), 2) }}</strong></td>
</tr>
@foreach ($header->children as $child)
    @include('rootsuperuser/partials/neracasaldo_row', ['header' => $child])
@endforeach
