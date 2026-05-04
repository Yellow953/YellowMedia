@if ($paginator->hasPages())
<nav aria-label="Pagination" class="ym-pagination-nav">
    <ul class="ym-pagination">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <li class="ym-page-item disabled">
                <span class="ym-page-link"><i class="bi bi-chevron-left"></i></span>
            </li>
        @else
            <li class="ym-page-item">
                <a class="ym-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="ym-page-item disabled">
                    <span class="ym-page-link">{{ $element }}</span>
                </li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="ym-page-item active">
                            <span class="ym-page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="ym-page-item">
                            <a class="ym-page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <li class="ym-page-item">
                <a class="ym-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        @else
            <li class="ym-page-item disabled">
                <span class="ym-page-link"><i class="bi bi-chevron-right"></i></span>
            </li>
        @endif

    </ul>

    <div class="ym-pagination-info">
        Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
    </div>
</nav>
@endif
