@if ($paginator->hasPages())
    <nav class="d-flex flex-wrap align-items-center justify-content-between gap-3 py-2">
        <div class="text-muted small" style="font-size: 12.5px;">
            Menampilkan <span class="fw-bold text-navy">{{ $paginator->firstItem() ?? 0 }}</span> - <span class="fw-bold text-navy">{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="fw-bold text-navy">{{ $paginator->total() }}</span> data
        </div>

        <ul class="pagination pagination-sm m-0 gap-1 flex-wrap">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link px-2.5 py-1.5 rounded-2 text-muted bg-light border-0" aria-hidden="true" style="font-size: 12px; font-weight: 500;">
                        <i class="bi bi-chevron-left me-1"></i> Prev
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link px-2.5 py-1.5 rounded-2 text-navy border-0 bg-light-subtle shadow-2xs" href="{{ $paginator->previousPageUrl() }}" rel="prev" style="font-size: 12px; font-weight: 600;">
                        <i class="bi bi-chevron-left me-1"></i> Prev
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link px-2 py-1.5 border-0 text-muted" style="font-size: 12px;">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link px-3 py-1.5 rounded-2 fw-bold text-white border-0" style="background: var(--dt-navy, #0f172a); font-size: 12px;">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link px-3 py-1.5 rounded-2 text-navy fw-medium border-0 bg-light" href="{{ $url }}" style="font-size: 12px;">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link px-2.5 py-1.5 rounded-2 text-navy border-0 bg-light-subtle shadow-2xs" href="{{ $paginator->nextPageUrl() }}" rel="next" style="font-size: 12px; font-weight: 600;">
                        Next <i class="bi bi-chevron-right ms-1"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link px-2.5 py-1.5 rounded-2 text-muted bg-light border-0" aria-hidden="true" style="font-size: 12px; font-weight: 500;">
                        Next <i class="bi bi-chevron-right ms-1"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
