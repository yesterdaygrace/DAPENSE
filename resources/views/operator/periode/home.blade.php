@extends('layouts.applayout')
@section('title', 'Periode')
@section('content')

<x-dashboard.page-header
    title="Periode"
    description="Atur periode akuntansi perusahaan"
    :actions="'<a href=\'' . route('operator/periodes/create') . '\' class=\'btn-primary\'>Tambah Periode</a>'"
/>

<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Periode</th>
                        <th>Tanggal Awal</th>
                        <th>Tanggal Akhir</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($periodes as $periode)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $periode->nama_periode }}</td>
                        <td>{{ $periode->tanggal_awal }}</td>
                        <td>{{ $periode->tanggal_akhir }}</td>
                        <td class="text-right">
                            <button type="button" class="btn-danger btn-sm" onclick="window.dispatchEvent(new CustomEvent('delete-modal-open', {detail: '{{ route('operator/periodes/delete', $periode->id) }}'}))">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <x-dashboard.empty-state
                                icon="calendar"
                                title="Belum ada periode"
                                description="Mulai dengan membuat periode akuntansi baru"
                                :action="'<a href=\'' . route('operator/periodes/create') . '\' class=\'btn-primary btn-sm\'>Tambah Periode</a>'"
                            />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<x-delete-modal title="Hapus Periode" message="Apakah Anda yakin ingin menghapus periode ini? Data yang sudah dihapus tidak dapat dikembalikan." />

@endsection
