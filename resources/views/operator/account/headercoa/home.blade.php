@extends('layouts.applayout')
@section('title', 'Header & COA')
@section('content')

<x-dashboard.page-header
    title="Header & COA"
    description="Lihat struktur hierarki header dan akun"
/>

<div class="space-y-3 mb-6" id="headerCoaAccordion">
  @foreach($headerCoas as $headerCoa)
  @if(!$headerCoa->parent_id)
  <div class="card rounded-card border border-gray-100 shadow-card">
    <div class="card-header cursor-pointer border-b border-gray-100 px-6 py-4 flex items-center justify-between" onclick="toggleAccordion('collapse{{ $headerCoa->id }}', this)">
      <div class="flex items-center gap-3">
        <span class="badge badge-info">{{ $headerCoa->level }}</span>
        <span class="font-semibold text-gray-900">{{ $headerCoa->nama_header }}</span>
      </div>
      <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" id="icon{{ $headerCoa->id }}"></i>
    </div>
    <div id="collapse{{ $headerCoa->id }}" class="hidden">
      <div class="card-body p-6">
        @if($headerCoa->children->count())
        @include('components.header_children', ['children' => $headerCoa->children])
        @else
        <p class="text-sm text-gray-500">No child headers</p>
        @endif
      </div>
    </div>
  </div>
  @endif
  @endforeach
</div>

@if($headerCoas->hasPages())
<div class="flex justify-center">
  <nav class="flex items-center gap-1">
    @if ($headerCoas->onFirstPage())
    <span class="px-3 py-1.5 text-sm text-gray-400">&laquo;</span>
    @else
    <a href="{{ $headerCoas->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900">&laquo;</a>
    @endif

    @foreach ($headerCoas->links()->elements[0] as $page => $url)
    @if ($page == $headerCoas->currentPage())
    <span class="px-3 py-1.5 text-sm bg-primary text-white rounded-lg">{{ $page }}</span>
    @else
    <a href="{{ $url }}" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-100">{{ $page }}</a>
    @endif
    @endforeach

    @if ($headerCoas->hasMorePages())
    <a href="{{ $headerCoas->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900">&raquo;</a>
    @else
    <span class="px-3 py-1.5 text-sm text-gray-400">&raquo;</span>
    @endif
  </nav>
</div>
@endif

<script>
  function toggleAccordion(id, headerEl) {
    const content = document.getElementById(id);
    const icon = document.getElementById('icon' + id.replace('collapse', ''));
    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
  }

  document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') lucide.createIcons();
  });
</script>

@endsection
