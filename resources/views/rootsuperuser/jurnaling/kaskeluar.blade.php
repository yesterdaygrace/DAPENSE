@extends('layouts.rootsuperuserlayout')
@section('content')
<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('rootsuperuser/dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ Auth::user()->name }}</span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="py-1 menu-inner">
        <!-- Dashboard -->
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/products') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Analytics">User Management</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/periodes') }}" class="menu-link">
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
                    <a href="{{ route('rootsuperuser/account/header') }}" class="menu-link">
                        <div data-i18n="Without menu">Header</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/account/coa') }}" class="menu-link">
                        <div data-i18n="Without menu">COA</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/account/headercoa') }}" class="menu-link">
                        <div data-i18n="Without menu">Combine Header & COA</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/saldoawal') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div data-i18n="Analytics">Saldo Awal</div>
            </a>
        </li>
        <li class="menu-item active open">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-notepad"></i>
                <div data-i18n="Layouts">Jurnaling</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/jurnaling') }}" class="menu-link">
                        <div data-i18n="Without menu">Kas Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item active">
                    <a href="{{ route('rootsuperuser/jurnaling/kaskeluar') }}" class="menu-link">
                        <div data-i18n="Without menu">Kas Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/jurnaling/bankmasuk') }}" class="menu-link">
                        <div data-i18n="Without menu">Bank Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/jurnaling/bankkeluar') }}" class="menu-link">
                        <div data-i18n="Without menu">Bank Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/jurnaling/memorial') }}" class="menu-link">
                        <div data-i18n="Without menu">Memorial</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/jurnaling/memorialpenutup') }}" class="menu-link">
                        <div data-i18n="Without menu">Memorial (Penutup)</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/jurnaling/showing') }}" class="menu-link">
                        <div data-i18n="Without menu">Tampil</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/bukubesar') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div data-i18n="Analytics">Buku Besar</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/neracasaldo/') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calculator"></i>
                <div data-i18n="Analytics">Neraca Saldo</div>
            </a>
        </li>
    </ul>
</aside>

@section('content')
<!-- Content wrapper -->
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Pilih Periode</h5>
            </div>
            <div class="card-body">
                <div class="card-body">
                    <form id="periode-form">
                        <div class="mb-3 row">
                            <div class="col">
                                <label for="periode-select" class="form-label">Periode</label>
                                <select class="form-control" id="periode-select" name="periode_id" required>
                                    <option value="">Pilih Periode</option>
                                    @foreach ($periodes as $periode)
                                    <option value="{{ $periode->id }}" data-start="{{ $periode->tanggal_awal }}" data-end="{{ $periode->tanggal_akhir }}">
                                        {{ $periode->nama_periode }} ({{ $periode->tanggal_awal }} - {{ $periode->tanggal_akhir }})
                                    </option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="col">
                                <button type="submit" id="pilih-periode" class="mt-4 btn btn-primary">Pilih</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tambah Kolom Jurnal Card -->
        <div class="mt-3 card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Tambah Kolom Jurnal</h5>
            </div>
            <div class="card-body">
                <div id="form-errors" class="alert alert-danger" style="display:none;"></div>
                <div id="form-success" class="alert alert-success" style="display:none;"></div>
                @if (Session::has('success'))
                <div class="alert alert-success">
                    {{ Session::get('success') }}
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form id="jurnaling-form" action="{{ route('rootsuperuser/jurnaling/storekaskeluar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="POST" id="form-method">
                    <div class="mb-3 row">
                        <div class="col">
                            <label for="tanggal_jurnal" class="form-label">Tanggal Jurnal</label>
                            <input type="date" class="form-control" id="tanggal_jurnal" name="tanggal_jurnal" required>
                        </div>
                        <div class="col">
                            <label for="kategori_jurnal" class="form-label">Kategori Jurnal</label>
                            <select class="form-control" id="kategori_jurnal" name="kategori_jurnal" required>
                                <option value="Kas Keluar">Kas Keluar</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="periode_id_form" class="form-label">Periode</label>
                            <select class="form-control" id="periode_id_form" name="periode_id" readonly>
                                @foreach ($periodes as $periode)
                                <option value="{{ $periode->id }}" {{ request()->get('periode_id') == $periode->id ?
                                    'selected' : '' }}>
                                    {{ $periode->nama_periode }} ({{ $periode->tanggal_awal }} - {{
                                    $periode->tanggal_akhir }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="coa-container">
                        <div class="mb-3 row">
                            <div class="col">
                                <label for="nomor_bukti" class="form-label">Nomor Bukti</label>
                                <div class="input-group">
                                    <span class="input-group-text">KK-</span>
                                    <input type="text" class="form-control" maxlength="4" id="nomor_transaksi" name="nomor_transaksi" required placeholder="Nomor Transaksi">
                                    <span class="input-group-text" id="bulan_tahun_bukti">/Bulan/Tahun</span>
                                    <input type="hidden" id="nomor_bukti" name="nomor_bukti">
                                </div>
                            </div>
                        </div>

                        <div id="first-coa-group" class="mb-0 row">
                            <div class="col">
                                <label for="first-coa" class="form-label" style="display: inline-block; margin-bottom: 2px;">Akun Debit</label>
                                <input class="form-control" data-list="coa-options" class="form-control" id="first-coa" required oninput="updateHiddenInput(this, 'first-coa-id')" placeholder="Masukkan Akun">
                                <input type="hidden" name="coa_id[]" id="first-coa-id">
                                <div id="coa-dropdown" class="custom-dropdown"></div>
                            </div>
                            <div class="col">
                                <label for="debit" class="form-label" style="display: inline-block; margin-bottom: 2px;">Debit</label>
                                <input type="text" class="form-control debit-input" name="debit[]" value="" required placeholder="Masukkan Debit" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div>
                                <label for="kredit" class="form-label" style="display: inline-block; margin-bottom: 2px;" hidden>Kredit</label>
                                <input type="text" class="form-control credit-input" name="kredit[]" value="0" hidden>
                            </div>
                            <div class="col">
                                <label for="keterangan" class="form-label" style="display: inline-block; margin-bottom: 2px;">Keterangan</label>
                                <input type="text" class="form-control" name="keterangan[]" value="" required placeholder="Masukkan Keterangan">
                            </div>
                        </div>


                        <div id="opposite-coa-group" class="mt-4 row">
                            <div class="col">
                                <label for="opposite-coa-id" class="form-label" style="display: inline-block; margin-bottom: 2px;">Akun Kredit</label>
                                <select class="form-control" id="opposite-coa-id" name="coa_id[]" required>
                                    @foreach ($coas as $coa)
                                    @if ($coa->kode_akun === '12110001')
                                    <option value="{{ $coa->id }}">{{ $coa->kode_akun }} - {{ $coa->nama_akun }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label for="kredit" class="form-label" style="display: inline-block; margin-bottom: 2px;">Kredit</label>
                                <input type="text" class="form-control kredit-input" name="kredit[]" value="" required placeholder="Masukkan Kredit">
                            </div>
                            <div>
                                <label for="debit" class="form-label" style="display: inline-block; margin-bottom: 2px;" hidden>Debit</label>
                                <input type="text" class="form-control debit-input" name="debit[]" value="0" hidden>
                            </div>
                            <div>
                                <label for="keterangan" class="form-label" style="display: inline-block; margin-bottom: 2px;" hidden>Keterangan</label>
                                <input type="text" class="form-control" name="keterangan[]" value="-" hidden>
                            </div>
                        </div>

                        <datalist id="coa-options">
                            @foreach ($coas as $coa)
                            <option value="{{ $coa->kode_akun }} - {{ $coa->nama_akun }}" data-id="{{ $coa->id }}">
                                @endforeach
                        </datalist>

                        <div id="button-container" class="d-flex gap-2 mt-3">
                            <button type="button" id="add-first-coa-btn" class="btn btn-secondary">Tambah Akun Debit</button>
                            <button type="button" id="cancel-first-coa-btn" class="btn btn-danger" style="display: none;">Batal Tambah Akun Debit</button>
                        </div>

                        <div class="mt-4 mb-4 row">
                            <div class="col-md-auto">
                                <strong>Total Debit:</strong> <span id="total-debit">0</span>
                            </div>
                            <div class="col-md-auto">
                                <strong>Total Kredit:</strong> <span id="total-kredit">0</span>
                            </div>
                            <div class="col-md-auto">
                                <strong>Status:</strong> <span id="balance-status"> </span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>

        <!-- Jurnaling Entries Card -->

    </div>
</div>
<div class="p-3 toast-container position-fixed top-50 start-50 translate-middle" style="z-index: 1050;">
    <div id="editToast" class="text-white toast bg-info" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="bx bx-edit me-2"></i>
            <strong class="me-auto">Konfirmasi Edit</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Apakah Anda yakin ingin memperbarui entri jurnal ini?
            <div class="pt-2 mt-4 d-flex justify-content-end border-top">
                <button type="button" class="btn btn-light btn-sm me-2" data-bs-dismiss="toast">Batal</button>
                <button type="button" class="btn btn-primary btn-sm" id="confirmEditBtn">Edit</button>
            </div>
        </div>
    </div>
</div>

<div class="p-3 toast-container position-fixed top-50 start-50 translate-middle" style="z-index: 1050;">
    <div id="deleteToast" class="text-white toast bg-danger" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="bx bx-trash me-2"></i>
            <strong class="me-auto">Konfirmasi Hapus</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Apakah Anda yakin ingin menghapus entri jurnal ini?
            <div class="pt-2 mt-4 d-flex justify-content-end border-top">
                <button type="button" class="btn btn-light btn-sm me-2" data-bs-dismiss="toast">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        function saveSelectedPeriode() {
            const selectedPeriode = document.getElementById('periode-select').value;
            localStorage.setItem('selectedPeriode', selectedPeriode);
        }

        function setSelectedPeriode() {
            const selectedPeriode = localStorage.getItem('selectedPeriode');
            if (selectedPeriode) {
                document.getElementById('periode-select').value = selectedPeriode;
                document.getElementById('periode_id_form').value = selectedPeriode;
            }
        }

        document.getElementById('periode-select').addEventListener('change', function() {
            saveSelectedPeriode();
            document.getElementById('periode_id_form').value = this.value;
        });

        setSelectedPeriode();

        document.getElementById('periode-form').addEventListener('submit', function(event) {
            saveSelectedPeriode();
        });

        document.getElementById('jurnaling-form').addEventListener('submit', function(event) {
            saveSelectedPeriode();
        });

        function formatNumberInput(input) {
            let value = input.value.replace(/[^\d]/g, '');
            if (value) {
                input.value = new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(value);
            }
        }

        function formatNumberValue(value) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        }

        function convertToNumber(value) {
            let number = parseInt(value.replace(/[^\d]/g, ''), 10);
            return isNaN(number) ? 0 : number;
        }


        function updateInputsWithNumberFormat(data) {
            const firstDebitInput = document.querySelector('#first-coa-group .debit-input');
            const oppositeCreditInput = document.querySelector('#opposite-coa-group .kredit-input');

            firstDebitInput.value = formatNumberValue(data.debit);
            oppositeCreditInput.value = formatNumberValue(data.kredit);
        }

        const debitInputs = document.querySelectorAll('.debit-input');
        const kreditInputs = document.querySelectorAll('.kredit-input');

        const totalDebitElement = document.getElementById('total-debit');
        const totalKreditElement = document.getElementById('total-kredit');

        const balanceStatus = document.getElementById('balance-status');

        function updateBalanceStatus() {
            const totalDebit = convertToNumber(totalDebitElement.textContent);
            const totalKredit = convertToNumber(totalKreditElement.textContent);

            if (totalDebit === totalKredit) {
                balanceStatus.textContent = 'Balance';
                balanceStatus.style.fontWeight = 'bold';
                balanceStatus.style.color = 'green';
            } else {
                balanceStatus.textContent = 'Tidak Balance';
                balanceStatus.style.fontWeight = 'bold';
                balanceStatus.style.color = 'red';
            }
        }

        kreditInputs.forEach(input => {
            input.addEventListener('input', function() {
                formatNumberInput(input);
                calculateTotals();
            });
        });

        debitInputs.forEach(input => {
            input.addEventListener('input', function() {
                const originalValue = input.value;
                formatNumberInput(input);
                calculateTotals();
            });
        });

        function calculateTotals() {
            let totalDebit = 0;

            const allDebitInputs = document.querySelectorAll('.debit-input');

            allDebitInputs.forEach(input => {
                totalDebit += convertToNumber(input.value) || 0;
            });

            document.getElementById('total-debit').textContent = formatNumberValue(totalDebit);
            document.getElementById('total-kredit').textContent = formatNumberValue(totalDebit); // Kredit otomatis mengikuti

            const kreditInput = document.querySelector('#opposite-coa-group .kredit-input');
            if (kreditInput) {
                kreditInput.value = formatNumberValue(totalDebit); // Mengisi form kredit
            }

            updateBalanceStatus();
        }

        const form = document.getElementById('jurnaling-form');

        form.addEventListener('submit', function(event) {
            debitInputs.forEach(input => {
                input.value = convertToNumber(input.value);
            });
            kreditInputs.forEach(input => {
                input.value = convertToNumber(input.value);
            });

            const additionalCoaGroups = document.querySelectorAll('.additional-coa-group, .first-coa-group');
            additionalCoaGroups.forEach(group => {
                const kreditInput = group.querySelector('.kredit-input');
                const debitInput = group.querySelector('.debit-input');
                if (kreditInput) {
                    kreditInput.value = convertToNumber(kreditInput.value);
                }
                if (debitInput) {
                    debitInput.value = convertToNumber(debitInput.value);
                }
            });
        });

        calculateTotals();

        const nomorTransaksiInput = document.getElementById('nomor_transaksi');

        function stripFormatting(input) {
            return input.value.replace(/[^\d]/g, '');
        }

        function convertToNumber(value) {
            return parseInt(value.replace(/[^\d]/g, ''), 10);
        }


        function updateInputValues() {
            debitInputs.forEach(input => {
                input.value = stripFormatting(input);
            });
            kreditInputs.forEach(input => {
                input.value = stripFormatting(input);
            });

            const additionalCoas = document.querySelectorAll('.additional-coa-group .debit-input');
            additionalCoas.forEach(input => {
                input.value = stripFormatting(input);
            });
        }

        form.addEventListener('submit', function(event) {
            updateInputValues();
        });

        function createEditButton(dataId) {
            let editBtn = document.getElementById('edit-btn');
            if (!editBtn) {
                editBtn = document.createElement('button');
                editBtn.type = 'button';
                editBtn.id = 'edit-btn';
                editBtn.className = 'btn btn-warning ms-2';
                editBtn.textContent = 'Edit';
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.parentNode.insertBefore(editBtn, submitBtn.nextSibling);

                editBtn.addEventListener('click', function() {
                    const editToast = new bootstrap.Toast(document.getElementById('editToast'));
                    editToast.show();

                    document.getElementById('confirmEditBtn').onclick = function() {
                        prepareFormForEdit(dataId);
                        editToast.hide();
                    };
                });
            } else {
                editBtn.style.display = 'inline-block';
                editBtn.onclick = function() {
                    prepareFormForEdit(dataId);
                };
            }
        }

        function prepareFormForEdit(dataId) {
            const additionalCoas = document.querySelectorAll('.additional-coa-group');
            additionalCoas.forEach(group => {
                const coaInput = group.querySelector('input[data-list="coa-options"]');
                const hiddenInput = group.querySelector('input[type="hidden"]');
                updateHiddenInput(coaInput, hiddenInput.id);
            });

            const allDebitInputs = document.querySelectorAll('.debit-input');
            const allKreditInputs = document.querySelectorAll('.kredit-input');

            allDebitInputs.forEach(input => {
                input.value = convertToNumber(input.value);
            });

            allKreditInputs.forEach(input => {
                input.value = convertToNumber(input.value);
            });

            form.action = `/rootsuperuser/jurnaling/editkk/${dataId}`;
            document.getElementById('form-method').value = 'PUT';
            form.submit();
        }

        function createDeleteButton(dataId) {
            let deleteBtn = document.getElementById('delete-btn');
            if (!deleteBtn) {
                deleteBtn = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.id = 'delete-btn';
                deleteBtn.className = 'btn btn-danger ms-2';
                deleteBtn.textContent = 'Delete';

                const editBtn = document.getElementById('edit-btn');
                editBtn.parentNode.insertBefore(deleteBtn, editBtn.nextSibling)

                deleteBtn.addEventListener('click', function() {
                    const deleteToast = new bootstrap.Toast(document.getElementById('deleteToast'));
                    deleteToast.show();

                    document.getElementById('confirmDeleteBtn').onclick = function() {
                        prepareFormForDelete(dataId);
                        deleteToast.hide();
                    };
                });
            } else {
                deleteBtn.style.display = 'inline-block';
                deleteBtn.onclick = function() {
                    prepareFormForDelete(dataId);
                };
            }
        }

        function prepareFormForDelete(dataId) {
            form.action = `/rootsuperuser/jurnaling/deletekk/${dataId}`;
            document.getElementById('form-method').value = 'DELETE';
            form.submit();
        }


        nomorTransaksiInput.addEventListener('input', function() {
            const nomorTransaksi = nomorTransaksiInput.value;
            const tanggalJurnal = document.getElementById('tanggal_jurnal').value;

            if (nomorTransaksi.length > 3) {
                fetch(`/rootsuperuser/jurnaling/cek-nomor-buktikk?nomor_transaksi=${nomorTransaksi}&tanggal_jurnal=${tanggalJurnal}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            document.getElementById('tanggal_jurnal').value = data.tanggal_jurnal;
                            document.getElementById('kategori_jurnal').value = data.kategori_jurnal;
                            document.getElementById('first-coa-id').value = data.coa1_id;
                            document.getElementById('first-coa').value = `${data.coa1_kode} - ${data.coa1_nama}`;
                            updateInputsWithNumberFormat(data);

                            document.getElementById('opposite-coa-id').value = data.coa2_id;

                            document.querySelector('#first-coa-group .form-control[name="keterangan[]"]').value = data.keterangan_debit || '';
                            document.querySelector('#opposite-coa-group .form-control[name="keterangan[]"]').value = data.keterangan_kredit || '';

                            const dateParts = data.tanggal_jurnal.split('-');
                            const bulan = String(parseInt(dateParts[1], 10)).padStart(2, '0');
                            const tahun = dateParts[0].slice(-2);
                            const nomorBukti = `KK-${str_pad(nomorTransaksi, 4, '0', 'left')}/${bulan}/${tahun}`;

                            document.getElementById('nomor_bukti').value = nomorBukti;

                            createEditButton(data.id_debit);
                            createDeleteButton(data.id_debit);
                            clearAdditionalCoas();
                            data.additional_coas.forEach(coa => {
                                addCoaField(coa);
                            });

                        } else {
                            document.querySelector('#first-coa-group .debit-input').value = '';
                            document.querySelector('#first-coa-group .form-control[name="keterangan[]"]').value = '';
                            document.querySelector('#opposite-coa-group .kredit-input').value = '';
                            document.querySelector('#opposite-coa-group .form-control[name="keterangan[]"]').value = '';
                            document.getElementById('first-coa').value = '';
                            document.getElementById('first-coa-id').value = '';

                            const editBtn = document.getElementById('edit-btn');
                            const deleteBtn = document.getElementById('delete-btn');
                            if (editBtn) {
                                editBtn.style.display = 'none';
                            }

                            if (deleteBtn) {
                                deleteBtn.style.display = 'none';
                            }

                            clearAdditionalCoas();
                            document.querySelectorAll('.additional-coa-group').forEach(element => {
                                element.remove();
                            });
                        }
                    });
            } else {
                document.querySelector('#first-coa-group .debit-input').value = '';
                document.querySelector('#first-coa-group .form-control[name="keterangan[]"]').value = '';
                document.querySelector('#opposite-coa-group .kredit-input').value = '';
                document.querySelector('#opposite-coa-group .form-control[name="keterangan[]"]').value = '';
                document.getElementById('first-coa').value = '';
                document.getElementById('first-coa-id').value = '';

                const editBtn = document.getElementById('edit-btn');
                const deleteBtn = document.getElementById('delete-btn');
                if (editBtn) {
                    editBtn.style.display = 'none';
                }

                if (deleteBtn) {
                    deleteBtn.style.display = 'none';
                }
                clearAdditionalCoas();
            }
        });

        function str_pad(input, length, padString, padType) {
            let str = input.toString();
            while (str.length < length) {
                if (padType === 'left') {
                    str = padString + str;
                } else {
                    str = str + padString;
                }
            }
            return str;
        }

        function clearAdditionalCoas() {
            document.querySelectorAll('.additional-coa-group').forEach(element => {
                element.remove();
            });
        }

        function addCoaField(coa) {
            const newCoaGroup = document.createElement('div');
            newCoaGroup.classList.add('mt-1', 'row', 'additional-coa-group');

            const coaValue = coa.kode_akun ? `${coa.kode_akun} - ${coa.nama_akun}` : coa.nama_akun;

            newCoaGroup.innerHTML = `
        <div class="col">
            <label for="first-coa" class="form-label" style="display: inline-block; margin-bottom: 2px;">Akun Debit</label>
            <input class="form-control" data-list="coa-options" class="form-control" value="${coaValue}" required 
                placeholder="Masukkan Akun" 
                oninput="updateHiddenInput(this, 'first-coa-id-${coa.id}')">
            <input type="hidden" name="coa_id[]" id="first-coa-id-${coa.id}" value="${coa.coa_id}">
            <div class="custom-dropdown"></div>
        </div>
        <div class="col">
            <label for="debit" class="form-label" style="display: inline-block; margin-bottom: 2px;">Debit</label>
            <input type="text" class="form-control debit-input" name="debit[]" value="${formatNumberValue(coa.debit)}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required placeholder="Masukkan Debit">
        </div>
        <div>
            <label for="kredit" class="form-label" style="display: inline-block; margin-bottom: 2px;" hidden>Kredit</label>
            <input type="text" class="form-control kredit-input" name="kredit[]" value="0" hidden>
        </div>
         <div class="col">
            <label for="keterangan" class="form-label" style="display: inline-block; margin-bottom: 2px;">Keterangan</label>
            <input type="text" class="form-control" name="keterangan[]" value="${coa.keterangan || ''}" required placeholder="Masukkan Keterangan">
        </div>
    `;

            const coaContainer = document.getElementById('coa-container');
            const oppositeCoaGroup = document.getElementById('opposite-coa-group');
            coaContainer.insertBefore(newCoaGroup, oppositeCoaGroup);

            const hiddenInput = newCoaGroup.querySelector('input[type="hidden"]');
            setupCustomDropdown(newCoaGroup.querySelector('input[data-list="coa-options"]'), newCoaGroup.querySelector('.custom-dropdown'), hiddenInput.id);
            // setupCustomDropdown(newCoaGroup.querySelector('input[list="coa-options"]'), newCoaGroup.querySelector('.custom-dropdown'), newCoaGroup.querySelector('input[type="hidden"]').id);

            const debitInput = newCoaGroup.querySelector('.debit-input');
            debitInput.addEventListener('input', function() {
                formatNumberInput(debitInput);
                calculateTotals();
            });

            const kreditInput = newCoaGroup.querySelector('.kredit-input');
            kreditInput.addEventListener('input', function() {
                formatNumberInput(kreditInput);
                calculateTotals();
            });
        }

        function updateHiddenInput(input, hiddenInputId) {
            const dataList = document.getElementById('coa-options');
            const hiddenInput = document.getElementById(hiddenInputId);
            const options = dataList.options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === input.value) {
                    hiddenInput.value = options[i].getAttribute('data-id');
                    return;
                }
            }
            hiddenInput.value = '';
        }


        const tanggalJurnalInput = document.getElementById('tanggal_jurnal');
        const nomorBuktiInput = document.getElementById('nomor_bukti');
        const bulanTahunBukti = document.getElementById('bulan_tahun_bukti');

        function updateNomorBukti() {
            const nomorTransaksi = nomorTransaksiInput.value.padStart(3, '0');
            const tanggal = new Date(tanggalJurnalInput.value);
            const bulan = String(tanggal.getMonth() + 1).padStart(2, '0');
            const tahun = tanggal.getFullYear().toString().slice(-2);
            nomorBuktiInput.value = `KK-${nomorTransaksi}/${bulan}/${tahun}`;
            bulanTahunBukti.textContent = `/${bulan}/${tahun}`;
        }

        tanggalJurnalInput.addEventListener('change', updateNomorBukti);
        nomorTransaksiInput.addEventListener('input', updateNomorBukti);

        updateNomorBukti();

        const firstDebitInput = document.querySelector('#first-coa-group .debit-input');
        const oppositeCreditInput = document.querySelector('#opposite-coa-group .kredit-input');

        const addFirstCoaBtn = document.getElementById('add-first-coa-btn');
        const cancelFirstCoaBtn = document.getElementById('cancel-first-coa-btn');
        const coaContainer = document.getElementById('coa-container');

        addFirstCoaBtn.addEventListener('click', function() {
            const newCoaGroup = document.createElement('div');
            newCoaGroup.classList.add('mt-1', 'row', 'first-coa-group');

            newCoaGroup.innerHTML = `
       <div class="col">
            <label for="first-coa" class="form-label" style="display: inline-block; margin-bottom: 2px;">Akun Debit</label>
            <input class="form-control" data-list="coa-options" class="form-control" required oninput="updateHiddenInput(this, 'first-coa-id-${Date.now()}')" placeholder="Masukkan Akun">
            <input type="hidden" name="coa_id[]" id="first-coa-id-${Date.now()}">
            <div class="custom-dropdown"></div>
        </div>
        <div class="col">
            <label for="debit" class="form-label" style="display: inline-block; margin-bottom: 2px;">Debit</label>
            <input type="text" class="form-control debit-input" name="debit[]" required placeholder="Masukkan Debit" oninput="this.value = this.value.replace(/[^0-9]/g, '')" oninput="formatNumberInput(this)">
        </div>
        <div>
            <label for="kredit" class="form-label" style="display: inline-block; margin-bottom: 2px;" hidden>Kredit</label>
            <input type="text" class="form-control kredit-input" name="kredit[]" value="0" hidden oninput="formatNumberInput(this)">
        </div>
        <div class="col">
            <label for="keterangan" class="form-label" style="display: inline-block; margin-bottom: 2px;">Keterangan</label>
            <input type="text" class="form-control" name="keterangan[]" required placeholder="Masukkan Keterangan">
        </div>
    `;

            const oppositeCoaGroup = document.getElementById('opposite-coa-group');
            coaContainer.insertBefore(newCoaGroup, oppositeCoaGroup);

            cancelFirstCoaBtn.style.display = 'block';

            const hiddenInput = newCoaGroup.querySelector('input[type="hidden"]');
            setupCustomDropdown(newCoaGroup.querySelector('input[data-list="coa-options"]'), newCoaGroup.querySelector('.custom-dropdown'), hiddenInput.id);
            // setupCustomDropdown(newCoaGroup.querySelector('input[list="coa-options"]'), newCoaGroup.querySelector('.custom-dropdown'), newCoaGroup.querySelector('input[type="hidden"]').id);

            const debitInput = newCoaGroup.querySelector('.debit-input');
            debitInput.addEventListener('input', function() {
                formatNumberInput(debitInput);
                calculateTotals();
            });

            const kreditInput = newCoaGroup.querySelector('.kredit-input');
            kreditInput.addEventListener('input', function() {
                formatNumberInput(kreditInput);
                calculateTotals();
            });
        });

        cancelFirstCoaBtn.addEventListener('click', function() {
            const coaGroups = document.querySelectorAll('.first-coa-group');
            if (coaGroups.length > 0) {
                coaGroups[coaGroups.length - 1].remove();
            }
            if (coaGroups.length === 1) {
                cancelFirstCoaBtn.style.display = 'none';
            }
        });

        cancelFirstCoaBtn.style.display = 'none';

        addFirstCoaBtn.addEventListener('click', function() {
            cancelFirstCoaBtn.style.display = 'block';
        });

        function setupCustomDropdown(input, dropdown, hiddenInputId) {
            const options = document.getElementById('coa-options').options;
            let activeIndex = -1;

            input.addEventListener('focus', function() {
                const currentValue = input.value.trim().toLowerCase();
                dropdown.innerHTML = '';
                activeIndex = -1;

                // Jika input cocok persis dengan salah satu opsi
                const matchedOptions = Array.from(options).filter(opt =>
                    opt.value.trim().toLowerCase() === currentValue
                );

                if (matchedOptions.length === 1) {
                    // Tampilkan hanya akun yang cocok persis
                    const option = matchedOptions[0];
                    const div = document.createElement('div');
                    div.textContent = option.value;
                    div.setAttribute('data-id', option.getAttribute('data-id'));
                    div.classList.add('dropdown-item');
                    div.style.padding = '8px';
                    div.style.cursor = 'pointer';
                    div.style.backgroundColor = 'white';

                    div.addEventListener('click', function() {
                        selectItem(div, input, hiddenInputId, dropdown);
                    });

                    dropdown.appendChild(div);
                } else {
                    // Jika tidak cocok persis, tampilkan semua lalu filter
                    showDropdown(input, dropdown, hiddenInputId, options);
                    filterDropdown(input, dropdown, options);
                }

                dropdown.style.display = 'block';
            });

            input.addEventListener('input', function() {
                filterDropdown(input, dropdown, options);
                activeIndex = -1;

                const visible = getVisibleItems(dropdown);
                if (visible.length) highlightItem(visible, 0);
            });

            input.addEventListener('keydown', function(e) {
                const visible = getVisibleItems(dropdown);
                if (visible.length === 0) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeIndex = (activeIndex + 1) % visible.length;
                    highlightItem(visible, activeIndex);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeIndex = (activeIndex - 1 + visible.length) % visible.length;
                    highlightItem(visible, activeIndex);
                } else if (e.key === 'Enter') {
                    e.preventDefault();

                    if (visible.length === 1) {
                        selectItem(visible[0], input, hiddenInputId, dropdown);
                    } else {
                        const exactMatch = visible.find(item => item.textContent.trim() === input.value.trim());
                        if (exactMatch) {
                            selectItem(exactMatch, input, hiddenInputId, dropdown);
                        } else if (activeIndex >= 0 && visible[activeIndex]) {
                            selectItem(visible[activeIndex], input, hiddenInputId, dropdown);
                        }
                    }
                }
            });

            document.addEventListener('click', function(event) {
                if (!input.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.style.display = 'none';
                }
            });
        }

        function showDropdown(input, dropdown, hiddenInputId, options) {
            dropdown.innerHTML = '';

            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                const div = document.createElement('div');
                div.textContent = option.value;
                div.setAttribute('data-id', option.getAttribute('data-id'));
                div.classList.add('dropdown-item');
                div.style.padding = '8px';
                div.style.cursor = 'pointer';
                div.style.backgroundColor = 'white';

                div.addEventListener('click', function() {
                    selectItem(div, input, hiddenInputId, dropdown);
                });

                dropdown.appendChild(div);
            }

            dropdown.style.position = 'absolute';
            dropdown.style.backgroundColor = 'white';
            dropdown.style.border = '1px solid #ccc';
            dropdown.style.maxHeight = '200px';
            dropdown.style.overflowY = 'auto';
            dropdown.style.zIndex = '1000';
            dropdown.style.display = 'block';
        }

        function filterDropdown(input, dropdown, options) {
            const filter = input.value.toLowerCase();
            const divs = dropdown.getElementsByClassName('dropdown-item');
            for (let i = 0; i < divs.length; i++) {
                const div = divs[i];
                div.style.display = div.textContent.toLowerCase().includes(filter) ? '' : 'none';
            }
        }

        function getVisibleItems(dropdown) {
            return Array.from(dropdown.querySelectorAll('.dropdown-item'))
                .filter(item => item.style.display !== 'none');
        }

        function highlightItem(items, index) {
            items.forEach((item, i) => {
                item.style.backgroundColor = i === index ? '#e0e0e0' : 'white';
            });
            items[index].scrollIntoView({
                block: 'nearest'
            });
        }

        function selectItem(div, input, hiddenInputId, dropdown) {
            input.value = div.textContent;
            document.getElementById(hiddenInputId).value = div.getAttribute('data-id');
            dropdown.style.display = 'none';
        }
        setupCustomDropdown(document.getElementById('first-coa'), document.getElementById('coa-dropdown'), 'first-coa-id');


        function setDateConstraints() {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const todayISO = `${yyyy}-${mm}-${dd}`;

            const tanggalJurnalInput = document.getElementById('tanggal_jurnal');
            tanggalJurnalInput.setAttribute('max', todayISO);

            // const firstDayOfLastMonth = new Date(yyyy, today.getMonth() - 1, 1);
            // const minISO = firstDayOfLastMonth.toISOString().split('T')[0];
            // tanggalJurnalInput.setAttribute('min', minISO);

            tanggalJurnalInput.addEventListener('input', function() {
                validateDate(this);
            });

            tanggalJurnalInput.addEventListener('blur', function() {
                validateDate(this);
            });

            function validateDate(inputEl) {
                const val = inputEl.value;
                if (!isValidDate(val)) {
                    inputEl.value = todayISO;
                    showTanggalWarning("Tanggal tidak boleh melebihi hari ini");
                    return;
                }

                const inputDate = new Date(val);
                if (inputDate > today) {
                    inputEl.value = todayISO;
                    showTanggalWarning("Tanggal tidak boleh melebihi hari ini");
                } else {
                    hideTanggalWarning();
                }
            }

            function isValidDate(dateString) {
                const parts = dateString.split("-");
                if (parts.length !== 3) return false;

                const yyyy = parseInt(parts[0], 10);
                const mm = parseInt(parts[1], 10);
                const dd = parseInt(parts[2], 10);

                if (isNaN(yyyy) || isNaN(mm) || isNaN(dd)) return false;
                if (mm < 1 || mm > 12) return false;

                const maxDay = new Date(yyyy, mm, 0).getDate();
                return dd >= 1 && dd <= maxDay;
            }

            function showTanggalWarning(msg) {
                let warning = document.getElementById('tanggal-warning');
                if (!warning) {
                    warning = document.createElement('small');
                    warning.id = 'tanggal-warning';
                    warning.className = 'text-danger';
                    tanggalJurnalInput.parentNode.appendChild(warning);
                }
                warning.textContent = msg;
                warning.style.display = 'inline';
            }

            function hideTanggalWarning() {
                const warning = document.getElementById('tanggal-warning');
                if (warning) {
                    warning.style.display = 'none';
                }
            }
        }
        setDateConstraints();

        $(document).ready(function() {
            $('#jurnaling-form').on('submit', function(e) {
                e.preventDefault(); // cegah reload halaman

                let form = $(this);
                let url = form.attr('action');
                let data = form.serialize();

                $('#form-errors').hide().html('');
                $('#form-success').hide().html('');

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            $('#form-success').show().html(response.success);
                            window.location.href = response.redirect;
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorHtml = '<ul>';
                            if (Array.isArray(errors)) {
                                errors.forEach(function(err) {
                                    errorHtml += '<li>' + err + '</li>';
                                });
                            } else if (typeof errors === 'object') {
                                $.each(errors, function(key, value) {
                                    errorHtml += '<li>' + value[0] + '</li>';
                                });
                            }
                            errorHtml += '</ul>';
                            $('#form-errors').show().html(errorHtml);
                        } else {
                            $('#form-errors').show().html('Terjadi kesalahan server.');
                        }
                    }
                });
            });
        });
    });
</script>


@endsection