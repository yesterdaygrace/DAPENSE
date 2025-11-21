@extends('layouts.operatorlayout')
@section('content')
<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('operator/dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ Auth::user()->name }}</span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="py-1 menu-inner">
        <!-- Dashboard -->
        <li class="menu-item">
            <a href="{{ route('operator/dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('operator/periodes') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div data-i18n="Analytics">Periode</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Layouts">Accounts</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/account/header') }}" class="menu-link">
                        <div data-i18n="Without menu">Header</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/account/coa') }}" class="menu-link">
                        <div data-i18n="Without menu">COA</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/account/headercoa') }}" class="menu-link">
                        <div data-i18n="Without menu">Combine Header & COA</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item active">
            <a href="{{ route('operator/saldoawal') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div data-i18n="Analytics">Saldo Awal</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-notepad"></i>
                <div data-i18n="Layouts">Jurnaling</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling') }}" class="menu-link">
                        <div data-i18n="Without menu">Kas Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/kaskeluar') }}" class="menu-link">
                        <div data-i18n="Without menu">Kas Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/bankmasuk') }}" class="menu-link">
                        <div data-i18n="Without menu">Bank Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/bankkeluar') }}" class="menu-link">
                        <div data-i18n="Without menu">Bank Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/memorial') }}" class="menu-link">
                        <div data-i18n="Without menu">Memorial</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/memorialpenutup') }}" class="menu-link">
                        <div data-i18n="Without menu">Memorial (Penutup)</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/showing') }}" class="menu-link">
                        <div data-i18n="Without menu">Tampil</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('operator/bukubesar') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div data-i18n="Analytics">Buku Besar</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('operator/neracasaldo/') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calculator"></i>
                <div data-i18n="Analytics">Neraca Saldo</div>
            </a>
        </li>
    </ul>
</aside>

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="container mt-5">
                    <h2>Saldo Awal</h2>

                    <!-- Form Filter Periode dan Bulan -->
                    <form method="GET" action="{{ route('operator/saldoawal') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="periode">Pilih Periode</label>
                                <select name="periode_id" id="periode" class="form-control">
                                    <option value="">Pilih Periode</option>
                                    @foreach ($periodes as $periode)
                                    <option value="{{ $periode->id }}" {{ request('periode_id') == $periode->id ? 'selected' : '' }}>
                                        {{ $periode->nama_periode }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="bulan">Pilih Bulan</label>
                                <select name="bulan" id="bulan" class="form-control">
                                    <option value="">Pilih Bulan</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                        </option>
                                        @endfor
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Tampil</button>
                            </div>
                        </div>
                    </form>

                    <a href="{{ route('operator/saldoawal/create') }}" class="mb-3 btn btn-primary">Tambah Saldo Awal</a>

                    <!-- Tampilkan tabel hanya jika periode & bulan sudah dipilih -->
                    @if(request()->filled('periode_id') && request()->filled('bulan'))
                    <div class="mb-3 row">
                        <div class="col">
                            <label for="coa-search" class="form-label">Cari Saldo Awal</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text" id="basic-addon-search31"><i class="bx bx-search"></i></span>
                                <input type="text" id="search-field" class="form-control" placeholder="Cari Saldo Awal" onkeyup="searchCOA()">
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kode COA</th>
                                <th>Tanggal Saldo</th>
                                <th>COA</th>
                                <th>Saldo Awal</th>
                                <th>Periode</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($saldo_awals->sortBy('coa.kode_akun') as $saldo_awal)
                            <tr>
                                <td>{{ $saldo_awal->coa->kode_akun }}</td>
                                <td>{{ $saldo_awal->tanggal_saldo }}</td>
                                <td>{{ $saldo_awal->coa->nama_akun }}</td>
                                <td>
                                    @if($saldo_awal->debit < 0)
                                        ({{ number_format(abs($saldo_awal->debit), 2) }})
                                        @else
                                        {{ number_format($saldo_awal->debit, 2) }}
                                        @endif
                                        </td>
                                <td>{{ $saldo_awal->periode->nama_periode }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('operator.saldoawal.edit', $saldo_awal->id) }}" class="btn btn-warning btn-sm me-2">Edit</a>
                                        <form action="{{ route('operator.saldoawal.destroy', $saldo_awal->id) }}" method="POST" style="display: inline-block; margin: 0;" id="deleteForm{{ $saldo_awal->id }}">
                                            @csrf
                                            @method('DELETE')
                                            @php
                                            $deleteUrl = route('operator.saldoawal.destroy', $saldo_awal->id);
                                            @endphp
                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $deleteUrl }}')">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="p-3 toast-container position-fixed top-50 start-50 translate-middle" style="z-index: 1050;">
    <div id="deleteToast" class="text-white toast bg-warning" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="bx bx-bell me-2"></i>
            <strong class="me-auto">Delete Confirmation</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Apakah Anda yakin ingin menghapus Saldo Awal ini?
            <div class="pt-2 mt-4 d-flex justify-content-end border-top">
                <button type="button" class="btn btn-light btn-sm me-2" data-bs-dismiss="toast">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    function searchCOA() {
        var input = document.getElementById("search-field").value.toLowerCase();
        var table = document.querySelector("table tbody");
        var rows = table.getElementsByTagName("tr");

        for (var i = 0; i < rows.length; i++) {
            var coaCode = rows[i].getElementsByTagName("td")[0]; // Kolom Kode COA
            var coaName = rows[i].getElementsByTagName("td")[2]; // Kolom COA Name

            if (coaCode && coaName) {
                var codeText = coaCode.textContent || coaCode.innerText;
                var nameText = coaName.textContent || coaName.innerText;

                if (codeText.toLowerCase().includes(input) || nameText.toLowerCase().includes(input)) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    }


    var deleteUrl = "";

    function confirmDelete(url) {
        deleteUrl = url; // Simpan URL yang akan digunakan
        var toastEl = document.getElementById('deleteToast');
        var toast = new bootstrap.Toast(toastEl);
        toast.show();
    }

    document.getElementById('confirmDeleteBtn').onclick = function() {
        if (deleteUrl) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteUrl;

            var csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = '{{ csrf_token() }}';

            var methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';

            form.appendChild(csrfField);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    };
</script>

@endsection