@extends('layouts.applayout')
@section('title', 'Memorial Penutup')
@section('content')

<x-dashboard.page-header
    title="Jurnal Memorial (Penutup)"
    description="Entri jurnal memorial penutup periode"
/>

<div class="filter-card mb-6">
    <div class="card-body">
        <form id="periode-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="periode-select" class="label">Periode</label>
                    <select class="select-field" id="periode-select" name="periode_id" required>
                        <option value="">Pilih Periode</option>
                        @foreach ($periodes as $periode)
                        <option value="{{ $periode->id }}" data-start="{{ $periode->tanggal_awal }}" data-end="{{ $periode->tanggal_akhir }}">
                            {{ $periode->nama_periode }} ({{ $periode->tanggal_awal }} - {{ $periode->tanggal_akhir }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" id="pilih-periode" class="btn-primary">Pilih</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5>Tambah Kolom Jurnal</h5>
    </div>
    <div class="card-body">
        <div id="form-errors" class="alert alert-danger" style="display:none;"></div>
        <div id="form-success" class="alert alert-success" style="display:none;"></div>

        <form id="jurnaling-form" action="{{ route('rootsuperuser/jurnaling/storememorialpenutup') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" value="POST" id="form-method">
            <div class="flex flex-wrap gap-4 mb-3">
                <div class="flex-1">
                    <label for="tanggal_jurnal" class="label">Tanggal Jurnal</label>
                    <input type="date" class="input-field" id="tanggal_jurnal" name="tanggal_jurnal" required>
                </div>
                <div class="flex-1">
                    <label for="kategori_jurnal" class="label">Kategori Jurnal</label>
                    <select class="select-field" id="kategori_jurnal" name="kategori_jurnal" required>
                        <option value="Memorial (Penutup)">Memorial (Penutup)</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label for="periode_id_form" class="label">Periode</label>
                    <select class="select-field" id="periode_id_form" name="periode_id" readonly>
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
                <div class="mb-3">
                    <label for="nomor_bukti" class="label">Nomor Bukti</label>
                    <div class="flex items-center gap-0">
                        <span class="px-3 py-2 border border-r-0 border-gray-300 rounded-l bg-gray-100 text-sm font-medium">JM-</span>
                        <input type="text" class="input-field rounded-none flex-1" maxlength="4" id="nomor_transaksi" name="nomor_transaksi" required placeholder="Nomor Transaksi">
                        <span class="px-3 py-2 border border-l-0 border-gray-300 rounded-r bg-gray-100 text-sm font-medium" id="bulan_tahun_bukti">/Bulan/Tahun</span>
                        <input type="hidden" id="nomor_bukti" name="nomor_bukti">
                    </div>
                </div>

                <div id="first-coa-group" class="flex flex-wrap gap-4 mb-3">
                    <div class="flex-1">
                        <label for="first-coa" class="label">Akun Debit</label>
                        <input class="input-field" data-list="coa-options" id="first-coa" required oninput="updateHiddenInput(this, 'first-coa-id')" placeholder="Masukkan Akun">
                        <input type="hidden" name="coa_id[]" id="first-coa-id">
                        <div id="first-coa-dropdown" class="custom-dropdown"></div>
                    </div>
                    <div class="flex-1">
                        <label for="debit" class="label">Debit</label>
                        <input type="text" class="input-field debit-input" name="debit[]" value="" placeholder="Masukkan Debit" required oninput="this.value = this.value.replace(/[^0-9]/g, '')" oninput="toggleHiddenInput(this, 'credit')">
                    </div>
                    <div class="hidden">
                        <label class="label" hidden>Kredit</label>
                        <input type="text" class="input-field credit-input" name="kredit[]" value="0" hidden>
                    </div>
                    <div class="flex-1">
                        <label for="keterangan" class="label">Keterangan</label>
                        <input type="text" class="input-field" name="keterangan[]" value="" required placeholder="Masukkan Keterangan">
                    </div>
                </div>

                <div id="opposite-coa-group" class="flex flex-wrap gap-4 mt-4">
                    <div class="flex-1">
                        <label for="opposite-coa" class="label">Akun Kredit</label>
                        <input class="input-field" data-list="coa-options" id="opposite-coa" required oninput="updateHiddenInput(this, 'opposite-coa-id')" placeholder="Masukkan Akun">
                        <input type="hidden" name="coa_id[]" id="opposite-coa-id">
                        <div id="opposite-coa-dropdown" class="custom-dropdown"></div>
                    </div>
                    <div class="flex-1">
                        <label for="kredit" class="label">Kredit</label>
                        <input type="text" class="input-field kredit-input" name="kredit[]" value="" placeholder="Masukkan Kredit" required oninput="this.value = this.value.replace(/[^0-9]/g, '')" oninput="toggleHiddenInput(this, 'debit')">
                    </div>
                    <div class="hidden">
                        <label class="label" hidden>Debit</label>
                        <input type="text" class="input-field debit-input" name="debit[]" value="0" hidden>
                    </div>
                    <div class="flex-1">
                        <label for="keterangan" class="label">Keterangan</label>
                        <input type="text" class="input-field" name="keterangan[]" value="" required placeholder="Masukkan Keterangan">
                    </div>
                </div>

                <datalist id="coa-options">
                    @foreach ($coas as $coa)
                    <option value="{{ $coa->kode_akun }} - {{ $coa->nama_akun }}" data-id="{{ $coa->id }}"></option>
                    @endforeach
                </datalist>

                <div id="button-container" class="flex gap-2 mt-3">
                    <button type="button" id="add-first-coa-btn" class="btn-secondary">Tambah Akun Debit</button>
                    <button type="button" id="cancel-first-coa-btn" class="btn-danger" style="display: none;">Batal Tambah Akun Debit</button>
                </div>

                <div id="button-container" class="flex gap-2 mt-3">
                    <button type="button" id="add-opposite-coa-btn" class="btn-secondary">Tambah Akun Kredit</button>
                    <button type="button" id="cancel-opposite-coa-btn" class="btn-danger" style="display: none;">Batal Tambah Akun Kredit</button>
                </div>

                <div class="flex flex-wrap gap-4 mt-4 mb-4">
                    <div><strong>Total Debit:</strong> <span id="total-debit">0</span></div>
                    <div><strong>Total Kredit:</strong> <span id="total-kredit">0</span></div>
                    <div><strong>Status:</strong> <span id="balance-status"> </span></div>
                </div>

                <button type="submit" class="btn-primary">Submit</button>
        </form>
    </div>
</div>

<x-delete-modal
    title="Konfirmasi Hapus"
    message="Apakah Anda yakin ingin menghapus item ini?"
/>

<script>
let deleteDataId = '';

function confirmDelete(dataId) {
    deleteDataId = dataId;
    window.dispatchEvent(new CustomEvent('delete-modal-open', { detail: '#' }));
}

document.addEventListener('click', function(e) {
    const link = e.target.closest('a.btn-danger');
    if (link && deleteDataId && link.closest('.fixed')) {
        e.preventDefault();
        prepareFormForDelete(deleteDataId);
    }
});
</script>

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
            return parseInt(value.replace(/[^\d]/g, ''), 10);
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
            let totalDebit = 0,
                totalKredit = 0;

            const allDebitInputs = document.querySelectorAll('.debit-input');
            const allKreditInputs = document.querySelectorAll('.kredit-input');

            allDebitInputs.forEach(input => {
                totalDebit += convertToNumber(input.value) || 0;
            });

            allKreditInputs.forEach(input => {
                totalKredit += convertToNumber(input.value) || 0;
            });

            document.getElementById('total-debit').textContent = formatNumberValue(totalDebit);
            document.getElementById('total-kredit').textContent = formatNumberValue(totalKredit);

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

            const additionalCoaGroups = document.querySelectorAll('.additional-coa-group, .opposite-coa-group, .first-coa-group');
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

        function toggleHiddenInput(input, counterpartType) {
            const oppositeType = counterpartType === 'debit' ? 'credit' : 'debit';
            const counterpartInput = input.closest('[class*="coa-group"]').querySelector(`.${oppositeType}-input`);

            if (input.value && counterpartInput.value === '0') {
                counterpartInput.value = '';
                counterpartInput.hidden = true;
            } else if (!input.value && counterpartInput.hidden) {
                counterpartInput.value = '0';
                counterpartInput.hidden = false;
            }
        }

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

            const additionalCoas = document.querySelectorAll('.additional-coa-group, .kredit-input, .debit-input');
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
                editBtn.className = 'btn-warning ml-2';
                editBtn.textContent = 'Edit';
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.parentNode.insertBefore(editBtn, submitBtn.nextSibling);

                editBtn.addEventListener('click', function() {
                    prepareFormForEdit(dataId);
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

            form.action = `/admin/jurnaling/editmempenutup/${dataId}`;
            document.getElementById('form-method').value = 'PUT';
            form.submit();
        }

        function createDeleteButton(dataId) {
            let deleteBtn = document.getElementById('delete-btn');
            if (!deleteBtn) {
                deleteBtn = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.id = 'delete-btn';
                deleteBtn.className = 'btn-danger ml-2';
                deleteBtn.textContent = 'Delete';

                const editBtn = document.getElementById('edit-btn');
                editBtn.parentNode.insertBefore(deleteBtn, editBtn.nextSibling)

                deleteBtn.addEventListener('click', function() {
                    confirmDelete(dataId);
                });
            } else {
                deleteBtn.style.display = 'inline-block';
                deleteBtn.onclick = function() {
                    prepareFormForDelete(dataId);
                };
            }
        }

        function prepareFormForDelete(dataId) {
            form.action = `/admin/jurnaling/deletemempenutup/${dataId}`;
            document.getElementById('form-method').value = 'DELETE';
            form.submit();
        }

        nomorTransaksiInput.addEventListener('input', function() {
            const nomorTransaksi = nomorTransaksiInput.value;
            const tanggalJurnal = document.getElementById('tanggal_jurnal').value;

            if (nomorTransaksi.length > 3) {
                fetch(`/admin/jurnaling/cek-nomor-buktimempenutup?nomor_transaksi=${nomorTransaksi}&tanggal_jurnal=${tanggalJurnal}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            document.getElementById('tanggal_jurnal').value = data.tanggal_jurnal;
                            document.getElementById('kategori_jurnal').value = data.kategori_jurnal;
                            document.getElementById('first-coa-id').value = data.coa1_id;
                            document.getElementById('first-coa').value = `${data.coa1_kode} - ${data.coa1_nama}`;
                            updateInputsWithNumberFormat(data);
                            document.getElementById('opposite-coa-id').value = data.coa2_id;
                            document.getElementById('opposite-coa').value = `${data.coa2_kode} - ${data.coa2_nama}`;

                            document.querySelector('#first-coa-group .input-field[name="keterangan[]"]').value = data.keterangan_debit || '';
                            document.querySelector('#opposite-coa-group .input-field[name="keterangan[]"]').value = data.keterangan_kredit || '';

                            const dateParts = data.tanggal_jurnal.split('-');
                            const bulan = String(parseInt(dateParts[1], 10)).padStart(2, '0');
                            const tahun = dateParts[0].slice(-2);
                            const nomorBukti = `JM-${str_pad(nomorTransaksi, 4, '0', 'left')}/${bulan}/${tahun}`;

                            document.getElementById('nomor_bukti').value = nomorBukti;

                            createEditButton(data.id_debit);
                            createDeleteButton(data.id_debit);
                            clearAdditionalCoas();
                            data.additional_coas.forEach(coa => {
                                if (coa.debit > 0) {
                                    addFirstCoaField(coa);
                                } else {
                                    addCoaField(coa);
                                }
                            });
                        } else {
                            clearForm();
                        }
                    });
            } else {
                clearForm();
            }
        });

        function clearForm() {
            document.querySelector('#first-coa-group .debit-input').value = '';
            document.querySelector('#first-coa-group .input-field[name="keterangan[]"]').value = '';
            document.querySelector('#opposite-coa-group .kredit-input').value = '';
            document.querySelector('#opposite-coa-group .input-field[name="keterangan[]"]').value = '';
            document.getElementById('first-coa').value = '';
            document.getElementById('first-coa-id').value = '';
            document.getElementById('opposite-coa').value = '';
            document.getElementById('opposite-coa-id').value = '';
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
            document.querySelectorAll('.additional-coa-group, .first-coa-group, .opposite-coa-group').forEach(element => {
                element.remove();
            });
        }

        function addFirstCoaField(coa) {
            const newCoaGroup = document.createElement('div');
            newCoaGroup.classList.add('mt-1', 'flex', 'flex-wrap', 'gap-4', 'first-coa-group');

            const coaValue = coa.kode_akun ? `${coa.kode_akun} - ${coa.nama_akun}` : coa.nama_akun;

            newCoaGroup.innerHTML = `
        <div class="flex-1">
            <label class="label">Akun Debit</label>
           <input class="input-field" data-list="coa-options" value="${coaValue}" required 
                placeholder="Masukkan Akun" 
                oninput="updateHiddenInput(this, 'first-coa-id-${coa.id}')">
            <input type="hidden" name="coa_id[]" id="first-coa-id-${coa.id}" value="${coa.coa_id}">
            <div class="custom-dropdown"></div>
        </div>
        <div class="flex-1">
            <label class="label">Debit</label>
            <input type="text" class="input-field debit-input" name="debit[]" value="${formatNumberValue(coa.debit)}" required placeholder="Masukkan Debit" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        </div>
        <div class="hidden">
            <label class="label" hidden>Kredit</label>
            <input type="text" class="input-field kredit-input" name="kredit[]" value="0" hidden>
        </div>
         <div class="flex-1">
            <label class="label">Keterangan</label>
            <input type="text" class="input-field" name="keterangan[]" value="${coa.keterangan || ''}" required placeholder="Masukkan Keterangan">
        </div>
    `;

            const coaContainer = document.getElementById('coa-container');
            const oppositeCoaGroup = document.getElementById('opposite-coa-group');
            coaContainer.insertBefore(newCoaGroup, oppositeCoaGroup);

            const hiddenInput = newCoaGroup.querySelector('input[type="hidden"]');
            setupCustomDropdown(newCoaGroup.querySelector('input[data-list="coa-options"]'), newCoaGroup.querySelector('.custom-dropdown'), hiddenInput.id);

            console.log('Hidden input created for debit:', hiddenInput.id);

            const debitInput = newCoaGroup.querySelector('.debit-input');
            debitInput.addEventListener('input', function() {
                formatNumberInput(debitInput);
                calculateTotals();
            });
        }


        function addCoaField(coa) {
            const newCoaGroup = document.createElement('div');
            newCoaGroup.classList.add('mt-1', 'flex', 'flex-wrap', 'gap-4', 'additional-coa-group');

            const coaValue = coa.kode_akun ? `${coa.kode_akun} - ${coa.nama_akun}` : coa.nama_akun;

            newCoaGroup.innerHTML = `
        <div class="flex-1">
            <label class="label">Akun Kredit</label>
            <input class="input-field" data-list="coa-options" value="${coaValue}" required 
                placeholder="Masukkan Akun" 
                oninput="updateHiddenInput(this, 'opposite-coa-id-${coa.id}')">
            <input type="hidden" name="coa_id[]" id="opposite-coa-id-${coa.id}" value="${coa.coa_id}">
            <div class="custom-dropdown"></div>
        </div>
        <div class="flex-1">
            <label class="label">Kredit</label>
            <input type="text" class="input-field kredit-input" name="kredit[]" value="${formatNumberValue(coa.kredit)}" required placeholder="Masukkan Kredit" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        </div>
        <div class="hidden">
            <label class="label" hidden>Debit</label>
            <input type="text" class="input-field debit-input" name="debit[]" value="0" hidden>
        </div>
         <div class="flex-1">
            <label class="label">Keterangan</label>
            <input type="text" class="input-field" name="keterangan[]" value="${coa.keterangan || ''}" required placeholder="Masukkan Keterangan">
        </div>
    `;

            const coaContainer = document.getElementById('coa-container');
            const buttonContainer = document.getElementById('button-container');
            coaContainer.insertBefore(newCoaGroup, buttonContainer);

            const hiddenInput = newCoaGroup.querySelector('input[type="hidden"]');
            setupCustomDropdown(newCoaGroup.querySelector('input[data-list="coa-options"]'), newCoaGroup.querySelector('.custom-dropdown'), hiddenInput.id);

            console.log('Hidden input created for credit:', hiddenInput.id);

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

            let found = false;
            for (let i = 0; i < options.length; i++) {
                if (options[i].value.trim() === input.value.trim()) {
                    hiddenInput.value = options[i].getAttribute('data-id');
                    console.log('Hidden input updated:', hiddenInput.value);
                    found = true;
                    break;
                }
            }

            if (!found) {
                hiddenInput.value = '';
                console.log('COA ID not found for input:', input.value);
            }
        }




        const tanggalJurnalInput = document.getElementById('tanggal_jurnal');
        const nomorBuktiInput = document.getElementById('nomor_bukti');
        const bulanTahunBukti = document.getElementById('bulan_tahun_bukti');

        function updateNomorBukti() {
            const nomorTransaksi = nomorTransaksiInput.value.padStart(4, '0');
            const tanggal = new Date(tanggalJurnalInput.value);
            const bulan = String(tanggal.getMonth() + 1).padStart(2, '0');
            const tahun = tanggal.getFullYear().toString().slice(-2);
            nomorBuktiInput.value = `JM-${nomorTransaksi}/${bulan}/${tahun}`;
            bulanTahunBukti.textContent = `/${bulan}/${tahun}`;
        }

        tanggalJurnalInput.addEventListener('change', updateNomorBukti);
        nomorTransaksiInput.addEventListener('input', updateNomorBukti);

        updateNomorBukti();

        const firstDebitInput = document.querySelector('#first-coa-group .debit-input');
        const oppositeCreditInput = document.querySelector('#opposite-coa-group .kredit-input');

        const addOppositeCoaBtn = document.getElementById('add-opposite-coa-btn');
        const cancelOppositeCoaBtn = document.getElementById('cancel-opposite-coa-btn');
        const coaContainer = document.getElementById('coa-container');

        addOppositeCoaBtn.addEventListener('click', function() {
            const newCoaGroup = document.createElement('div');
            newCoaGroup.classList.add('mt-1', 'flex', 'flex-wrap', 'gap-4', 'opposite-coa-group');

            newCoaGroup.innerHTML = `
    <div class="flex-1">
        <label class="label">Akun Kredit</label>
        <input class="input-field" data-list="coa-options" required oninput="updateHiddenInput(this, 'opposite-coa-id-${Date.now()}')" placeholder="Masukkan Akun">
        <input type="hidden" name="coa_id[]" id="opposite-coa-id-${Date.now()}">
        <div class="custom-dropdown"></div>
    </div>
    <div class="flex-1">
        <label class="label">Kredit</label>
        <input type="text" class="input-field kredit-input" name="kredit[]" required placeholder="Masukkan Kredit" oninput="this.value = this.value.replace(/[^0-9]/g, '')" oninput="formatNumberInput(this)">
    </div>
    <div class="hidden">
        <label class="label" hidden>Debit</label>
        <input type="text" class="input-field debit-input" name="debit[]" value="0" hidden oninput="formatNumberInput(this)">
    </div>
    <div class="flex-1">
        <label class="label">Keterangan</label>
        <input type="text" class="input-field" name="keterangan[]" required placeholder="Masukkan Keterangan">
     </div>
    
    `;

            coaContainer.insertBefore(newCoaGroup, document.getElementById('button-container'));

            cancelOppositeCoaBtn.style.display = 'block';

            const hiddenInput = newCoaGroup.querySelector('input[type="hidden"]');
            setupCustomDropdown(newCoaGroup.querySelector('input[data-list="coa-options"]'),
                newCoaGroup.querySelector('.custom-dropdown'),
                hiddenInput.id);

            const kreditInput = newCoaGroup.querySelector('.kredit-input');
            kreditInput.addEventListener('input', function() {
                formatNumberInput(kreditInput);
                calculateTotals();
            });

            const debitInput = newCoaGroup.querySelector('.debit-input');
            debitInput.addEventListener('input', function() {
                formatNumberInput(debitInput);
                calculateTotals();
            });
        });

        cancelOppositeCoaBtn.addEventListener('click', function() {
            const coaGroups = document.querySelectorAll('.opposite-coa-group');
            if (coaGroups.length > 0) {
                coaGroups[coaGroups.length - 1].remove();
            }
            if (coaGroups.length === 1) {
                cancelOppositeCoaBtn.style.display = 'none';
            }
        });

        cancelOppositeCoaBtn.style.display = 'none';

        addOppositeCoaBtn.addEventListener('click', function() {
            cancelOppositeCoaBtn.style.display = 'block';
        });

        const addFirstCoaBtn = document.getElementById('add-first-coa-btn');
        const cancelFirstCoaBtn = document.getElementById('cancel-first-coa-btn');

        addFirstCoaBtn.addEventListener('click', function() {
            const newCoaGroup = document.createElement('div');
            newCoaGroup.classList.add('mt-1', 'flex', 'flex-wrap', 'gap-4', 'first-coa-group');

            newCoaGroup.innerHTML = `
       <div class="flex-1">
            <label class="label">Akun Debit</label>
            <input class="input-field" data-list="coa-options" required oninput="updateHiddenInput(this, 'first-coa-id-${Date.now()}')" placeholder="Masukkan Akun">
            <input type="hidden" name="coa_id[]" id="first-coa-id-${Date.now()}">
            <div class="custom-dropdown"></div>
        </div>
        <div class="flex-1">
            <label class="label">Debit</label>
            <input type="text" class="input-field debit-input" name="debit[]" required placeholder="Masukkan Debit" oninput="this.value = this.value.replace(/[^0-9]/g, '')" oninput="formatNumberInput(this)">
        </div>
        <div class="hidden">
            <label class="label" hidden>Kredit</label>
            <input type="text" class="input-field kredit-input" name="kredit[]" value="0" hidden oninput="formatNumberInput(this)">
        </div>
        <div class="flex-1">
            <label class="label">Keterangan</label>
            <input type="text" class="input-field" name="keterangan[]" required placeholder="Masukkan Keterangan">
        </div>
    `;

            const oppositeCoaGroup = document.getElementById('opposite-coa-group');
            coaContainer.insertBefore(newCoaGroup, oppositeCoaGroup);

            cancelFirstCoaBtn.style.display = 'block';

            const hiddenInput = newCoaGroup.querySelector('input[type="hidden"]');
            setupCustomDropdown(newCoaGroup.querySelector('input[data-list="coa-options"]'),
                newCoaGroup.querySelector('.custom-dropdown'),
                hiddenInput.id);

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

                const matchedOptions = Array.from(options).filter(opt =>
                    opt.value.trim().toLowerCase() === currentValue
                );

                if (matchedOptions.length === 1) {
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

        setupCustomDropdown(document.getElementById('first-coa'), document.getElementById('first-coa-dropdown'), 'first-coa-id');
        setupCustomDropdown(document.getElementById('opposite-coa'), document.getElementById('opposite-coa-dropdown'), 'opposite-coa-id');

        function setDateConstraints() {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const todayISO = `${yyyy}-${mm}-${dd}`;

            const tanggalJurnalInput = document.getElementById('tanggal_jurnal');
            tanggalJurnalInput.setAttribute('max', todayISO);

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
                    warning.className = 'text-red-500';
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
                e.preventDefault();

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
