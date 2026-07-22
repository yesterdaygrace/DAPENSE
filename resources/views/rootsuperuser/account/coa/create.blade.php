@extends('layouts.applayout')
@section('title', 'COA - Tambah')
@section('content')

<x-dashboard.page-header
    title="Tambah COA"
    description="Tambahkan akun baru ke dalam Chart of Accounts"
    :actions="'<a href=\'' . route('rootsuperuser/account/coa') . '\' class=\'btn-secondary\'>Kembali</a>'"
/>

<div class="card rounded-card border border-gray-100 shadow-card">
  <div class="card-body p-6">
    <form action="{{ route('rootsuperuser/account/coa/save') }}" method="POST">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div>
          <label for="kode_akun" class="label">Kode Akun</label>
          <input type="text" id="kode_akun" name="kode_akun" minlength="8" maxlength="8" class="input-field @error('kode_akun') border-danger @enderror" placeholder="Masukkan kode akun" value="{{ old('kode_akun') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
          <x-form-error name="kode_akun" />
        </div>
        <div>
          <label for="nama_akun" class="label">Nama Akun</label>
          <input type="text" id="nama_akun" name="nama_akun" class="input-field @error('nama_akun') border-danger @enderror input-uppercase" placeholder="Masukkan nama akun" value="{{ old('nama_akun') }}">
          <x-form-error name="nama_akun" />
        </div>
        <div>
          <label for="saldo_normal" class="label">Saldo Normal</label>
          <select id="saldo_normal" name="saldo_normal" class="select-field @error('saldo_normal') border-danger @enderror">
            <option value="">PILIH</option>
            <option value="Debit" {{ old('saldo_normal') == 'Debit' ? 'selected' : '' }}>Debit</option>
            <option value="Kredit" {{ old('saldo_normal') == 'Kredit' ? 'selected' : '' }}>Kredit</option>
          </select>
          <x-form-error name="saldo_normal" />
        </div>
        <div>
          <label for="kategori" class="label">Kategori</label>
          <select id="kategori" name="kategori" class="select-field @error('kategori') border-danger @enderror">
            <option value="Aktiva">Aktiva</option>
            <option value="Kewajiban">Kewajiban</option>
            <option value="Beban">Beban</option>
            <option value="Pendapatan">Pendapatan</option>
          </select>
          <x-form-error name="kategori" />
        </div>
        <div>
          <label for="level" class="label">Level</label>
          <select id="level" name="level" class="select-field @error('level') border-danger @enderror" required>
            <option value="4" {{ old('level') == '0' ? 'selected' : '' }}>4</option>
          </select>
          <x-form-error name="level" />
        </div>
        <div>
          <label for="header_coa_id" class="label">Header COA</label>
          <select id="header_coa_id" name="header_coa_id" class="select-field @error('header_coa_id') border-danger @enderror">
            @foreach($headers as $header)
            <option value="{{ $header->id }}">{{ $header->kode_header }} - {{ $header->nama_header }}</option>
            @endforeach
          </select>
          <x-form-error name="header_coa_id" />
        </div>
      </div>
      <div class="flex items-center gap-3 mt-6">
        <button type="submit" class="btn-primary">Submit</button>
        <a href="{{ route('rootsuperuser/account/coa') }}" class="btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($errors->any())
        @foreach($errors->all() as $error)
            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: '{{ $error }}' } }));
        @endforeach
    @endif
});
</script>
@endpush
