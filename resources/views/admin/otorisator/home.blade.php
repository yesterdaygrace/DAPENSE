@extends('layouts.applayout')
@section('content')
@include('components.admin-sidebar', ['activeMenu' => 'dashboard'])

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">List Otorisator</h5>
            </div>

            <div class="card-body">
                @if(Session::has('success'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('success') }}
                </div>
                @endif

                <table class="table table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>Nama Otorisator</th>
                            <th>Jabatan Otorisator</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($otorisators as $otorisator)
                        <tr>
                            <td class="align-middle">{{ $otorisator->nama_otorisator }}</td>
                            <td class="align-middle">{{ $otorisator->jabatan_otorisator }}</td>
                            <td class="align-middle">
                                <a href="{{ route('admin/otorisator/edit', $otorisator->id) }}" class="btn btn-warning">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center" colspan="3">Tidak ada Otorisator ditemukan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- / Content -->
<div class="content-backdrop fade"></div>


@endsection