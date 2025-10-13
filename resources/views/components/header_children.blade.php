@foreach($children as $child)
    <div class="card accordion-item">
        <h2 class="accordion-header" id="heading{{ $child->id }}">
            <button
                type="button"
                class="accordion-button collapsed"
                data-bs-toggle="collapse"
                data-bs-target="#collapse{{ $child->id }}"
                aria-expanded="false"
                aria-controls="collapse{{ $child->id }}"
            >
                {{ $child->nama_header }} (Level {{ $child->level }})
            </button>
        </h2>
        <div
            id="collapse{{ $child->id }}"
            class="accordion-collapse collapse"
            aria-labelledby="heading{{ $child->id }}"
        >
            <div class="accordion-body">
                @if($child->children->count())
                    @include('components.header_children', ['children' => $child->children])
                @endif

                @if($child->coas->count())
                    <ul>
                        @foreach($child->coas as $coa)
                            <li>{{ $coa->kode_akun }} - {{ $coa->nama_akun }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>No COAs available</p>
                @endif
            </div>
        </div>
    </div>
@endforeach
