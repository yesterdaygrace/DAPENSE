@extends('layouts.applayout')
@section('title', 'Pengaturan')
@section('content')

<x-dashboard.page-header
  title="Otorisator"
  description="Kelola otorisator transaksi"
/>

<div class="card">
  <div class="card-body">
    <div class="overflow-x-auto">
      <table class="data-table">
        <thead>
          <tr>
            <th>Nama Otorisator</th>
            <th>Jabatan Otorisator</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($otorisators as $otorisator)
          <tr>
            <td>{{ $otorisator->nama_otorisator }}</td>
            <td>{{ $otorisator->jabatan_otorisator }}</td>
            <td>
              <a href="{{ route('operator/otorisator/edit', $otorisator->id) }}" class="btn-warning btn-sm">Edit</a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="3">
              <x-dashboard.empty-state
                icon="users"
                title="Belum ada otorisator"
                description="Belum ada otorisator yang terdaftar."
              />
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
