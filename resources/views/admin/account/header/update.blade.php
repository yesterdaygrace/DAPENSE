@extends('layouts.applayout')
@section('title', 'Header - Edit')
@section('content')

<x-dashboard.page-header
    title="Edit Header COA"
    description="Perbarui data header akun"
    :actions="'<a href=\'' . route('admin/account/header') . '\' class=\'btn-secondary\'>Kembali</a>'"
/>

<div class="card rounded-card border border-gray-100 shadow-card">
  <div class="card-body p-6">
    <form action="{{ route('admin/account/header/update', $headerCoa->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div>
          <label for="kode_header" class="label">Kode Header</label>
          <input style="text-transform: uppercase;" type="text" id="kode_header" name="kode_header" maxlength="7" class="input-field @error('kode_header') border-danger @enderror" placeholder="Masukkan kode header" value="{{ old('kode_header', $headerCoa->kode_header) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
          @error('kode_header')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="nama_header" class="label">Nama Header</label>
          <input style="text-transform: uppercase;" type="text" id="nama_header" name="nama_header" class="input-field @error('nama_header') border-danger @enderror" placeholder="Masukkan nama header" value="{{ old('nama_header', $headerCoa->nama_header) }}">
          @error('nama_header')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="level" class="label">Level</label>
          <select id="level" name="level" class="select-field @error('level') border-danger @enderror" required>
            <option value="">-- Pilih level --</option>
            <option value="0" {{ old('level', $headerCoa->level) == '0' ? 'selected' : '' }}>0</option>
            <option value="1" {{ old('level', $headerCoa->level) == '1' ? 'selected' : '' }}>1</option>
            <option value="2" {{ old('level', $headerCoa->level) == '2' ? 'selected' : '' }}>2</option>
            <option value="3" {{ old('level', $headerCoa->level) == '3' ? 'selected' : '' }}>3</option>
          </select>
          @error('level')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="parent_id" class="label">Parent Header</label>
          <select id="parent_id" name="parent_id" class="select-field @error('parent_id') border-danger @enderror">
            <option value="">NULL</option>
            @foreach($headerCoas as $headerCoaItem)
            <option value="{{ $headerCoaItem->id }}"
              data-level="{{ $headerCoaItem->level }}"
              {{ old('parent_id', $headerCoa->parent_id) == $headerCoaItem->id ? 'selected' : '' }}>
              {{ $headerCoaItem->kode_header }} - {{ $headerCoaItem->nama_header }}
            </option>
            @endforeach
          </select>
          @error('parent_id')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>
      <div class="flex items-center gap-3 mt-6">
        <button type="submit" class="btn-primary">Update</button>
        <a href="{{ route('admin/account/header') }}" class="btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const levelSelect = document.getElementById('level');
    const parentSelect = document.getElementById('parent_id');
    const allOptions = Array.from(parentSelect.options);

    function filterParentOptions() {
      const selectedLevel = levelSelect.value;
      parentSelect.innerHTML = '';
      if (selectedLevel === '0') {
        parentSelect.innerHTML = '<option value="">NULL</option>';
      } else {
        parentSelect.innerHTML = '<option value="">NULL</option>';
        const parentLevel = parseInt(selectedLevel) - 1;
        allOptions.forEach(opt => {
          if (opt.dataset.level == parentLevel) {
            parentSelect.appendChild(opt.cloneNode(true));
          }
        });
      }
    }

    levelSelect.addEventListener('change', filterParentOptions);
    filterParentOptions();
  });
</script>

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
