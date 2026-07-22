@extends('layouts.applayout')
@section('title', 'COA - Edit')
@section('content')

<x-dashboard.page-header
    title="Edit COA"
    description="Perbarui data akun"
    :actions="'<a href=\'' . route('rootsuperuser/account/coa') . '\' class=\'btn-secondary\'>Kembali</a>'"
/>

<div class="card rounded-card border border-gray-100 shadow-card">
  <div class="card-body p-6">
    <form action="{{ route('rootsuperuser/account/coa/update', $coa->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div>
          <label for="kode_akun" class="label">Kode Akun</label>
          <input style="text-transform: uppercase;" type="text" id="kode_akun" name="kode_akun" minlength="8" maxlength="8" class="input-field @error('kode_akun') border-danger @enderror" placeholder="Masukkan kode akun" value="{{ old('kode_akun', $coa->kode_akun) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
          @error('kode_akun')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="nama_akun" class="label">Nama Akun</label>
          <input style="text-transform: uppercase;" type="text" id="nama_akun" name="nama_akun" class="input-field @error('nama_akun') border-danger @enderror" placeholder="Masukkan nama akun" value="{{ old('nama_akun', $coa->nama_akun) }}">
          @error('nama_akun')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="saldo_normal" class="label">Saldo Normal</label>
          <select id="saldo_normal" name="saldo_normal" class="select-field @error('saldo_normal') border-danger @enderror">
            <option value="">PILIH</option>
            <option value="Debit" {{ old('saldo_normal', $coa->saldo_normal) == 'Debit' ? 'selected' : '' }}>Debit</option>
            <option value="Kredit" {{ old('saldo_normal', $coa->saldo_normal) == 'Kredit' ? 'selected' : '' }}>Kredit</option>
          </select>
          @error('saldo_normal')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="kategori" class="label">Kategori</label>
          <select style="text-transform: uppercase;" id="kategori" name="kategori" class="select-field @error('kategori') border-danger @enderror">
            <option value="Aktiva" {{ old('kategori', $coa->kategori) == 'Aktiva' ? 'selected' : '' }}>Aktiva</option>
            <option value="Kewajiban" {{ old('kategori', $coa->kategori) == 'Kewajiban' ? 'selected' : '' }}>Kewajiban</option>
            <option value="Beban" {{ old('kategori', $coa->kategori) == 'Beban' ? 'selected' : '' }}>Beban</option>
            <option value="Pendapatan" {{ old('kategori', $coa->kategori) == 'Pendapatan' ? 'selected' : '' }}>Pendapatan</option>
          </select>
          @error('kategori')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="level" class="label">Level</label>
          <select id="level" name="level" class="select-field @error('level') border-danger @enderror" required>
            <option value="4" {{ old('level', $coa->level) == '4' ? 'selected' : '' }}>4</option>
          </select>
          @error('level')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="header_coa_id" class="label">Header COA</label>
          <select id="header_coa_id" name="header_coa_id" class="select-field @error('header_coa_id') border-danger @enderror">
            @foreach($headers as $header)
            <option value="{{ $header->id }}" {{ old('header_coa_id', $coa->header_coa_id) == $header->id ? 'selected' : '' }}>
              {{ $header->kode_header }} - {{ $header->nama_header }}
            </option>
            @endforeach
          </select>
          @error('header_coa_id')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>
      <div class="flex items-center gap-3 mt-6">
        <button type="submit" class="btn-primary">Update</button>
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
