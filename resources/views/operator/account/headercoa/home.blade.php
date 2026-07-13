@extends('layouts.applayout')
@section('content')

@include('components.admin-sidebar', ['activeMenu' => 'account-headercoa'])

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Accordion -->
        <h5 class="mt-4">Header & COA</h5>
        <div class="row">
            <div class="mb-4 col-md mb-md-0">
                <div class="mt-3 accordion" id="headerCoaAccordion">
                    @foreach($headerCoas as $headerCoa)
                    @if(!$headerCoa->parent_id)
                    <div class="card accordion-item">
                        <h2 class="accordion-header" id="heading{{ $headerCoa->id }}">
                            <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapse{{ $headerCoa->id }}" aria-expanded="false" aria-controls="collapse{{ $headerCoa->id }}">
                                {{ $headerCoa->nama_header }} (Level {{ $headerCoa->level }})
                            </button>
                        </h2>
                        <div id="collapse{{ $headerCoa->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $headerCoa->id }}">
                            <div class="accordion-body">
                                @if($headerCoa->children->count())
                                @include('components.header_children', ['children' => $headerCoa->children])
                                @else
                                <p>No child headers</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>

                <!-- Pagination Links -->
                <div class="mt-3 d-flex justify-content-center">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            @if ($headerCoas->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                            @else
                            <li class="page-item"><a class="page-link" href="{{ $headerCoas->previousPageUrl() }}" rel="prev">&laquo;</a></li>
                            @endif

                            @foreach ($headerCoas->links()->elements[0] as $page => $url)
                            @if ($page == $headerCoas->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                            @endforeach

                            @if ($headerCoas->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $headerCoas->nextPageUrl() }}" rel="next">&raquo;</a></li>
                            @else
                            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
    <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->

@endsection