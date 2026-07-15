@foreach($children as $child)
    <div class="card accordion-item" x-data="{ open: false }">
        <h2 class="accordion-header">
            <button
                type="button"
                class="accordion-button"
                :class="{ 'collapsed': !open }"
                @click="open = !open"
                :aria-expanded="open"
            >
                {{ $child->nama_header }} (Level {{ $child->level }})
            </button>
        </h2>
        <div
            x-show="open"
            x-collapse.duration.200ms
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
