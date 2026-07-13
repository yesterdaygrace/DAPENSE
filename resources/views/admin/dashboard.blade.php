@extends('layouts.applayout')
@section('content')
@include('components.admin-sidebar', ['activeMenu' => 'dashboard'])

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
        \Carbon\Carbon::setLocale('id');
        @endphp

        <div class="row">
            <div class="mb-3 col-lg-12 order-0">
                <div class="card shadow-lg border-0 bg-light p-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h3 class="fw-bold text-primary">{{ Auth::user()->usertype }}</h3>
                            <h5 class="text-muted">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</h5>
                        </div>
                        <h5 class="text-secondary">Selamat Datang, <span class="fw-semibold">{{ Auth::user()->name }}</span></h5>
                    </div>
                </div>
            </div>
        </div>



        <div class="row">
            @php
            $menus = [
            ['name' => 'User Management', 'route' => 'admin/products', 'icon' => 'bx bx-user'],
            ['name' => 'Periode', 'route' => 'admin/periodes', 'icon' => 'bx bx-calendar'],
            ['name' => 'COA (Charts of Account)', 'route' => 'admin/account/coa', 'icon' => 'bx bx-spreadsheet'],
            ['name' => 'Saldo Awal', 'route' => 'admin/saldoawal', 'icon' => 'bx bx-money'],
            ['name' => 'Jurnaling', 'route' => 'admin/jurnaling', 'icon' => 'bx bx-notepad'],
            ['name' => 'Buku Besar', 'route' => 'admin/bukubesar', 'icon' => 'bx bx-book'],
            ['name' => 'Neraca Saldo', 'route' => 'admin/neracasaldo/', 'icon' => 'bx bx-calculator'],
            ];
            @endphp

            @foreach ($menus as $menu)
            <div class="col-md-4 col-lg-3 mb-3">
                <a href="{{ route($menu['route']) }}" class="text-decoration-none">
                    <div class="card text-center shadow-lg p-4 h-100">
                        <i class="menu-icon tf-icons {{ $menu['icon'] }} fs-1 mb-3" style="color: var(--bs-primary, #1E3A8A);"></i>
                        <h6 class="fw-semibold mb-0">{{ $menu['name'] }}</h6>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

    </div>
    <!-- / Content -->

    <div class="content-backdrop fade"></div>
</div>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.toggle-rekap');

        buttons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const form = this.closest('form');
                fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.textContent = this.textContent === 'Unrekap' ? 'Rekap Jurnal' : 'Unrekap';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    });
</script>