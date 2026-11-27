{{-- $paginator + $pageUrl (closure halaman -> URL) --}}
@if ($paginator->lastPage() > 1)
    <nav class="pagination">
        @if ($paginator->currentPage() > 1)
            <a href="{{ $pageUrl($paginator->currentPage() - 1) }}">&lsaquo;</a>
        @endif
        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            @if ($i === $paginator->currentPage())
                <span class="on">{{ $i }}</span>
            @else
                <a href="{{ $pageUrl($i) }}">{{ $i }}</a>
            @endif
        @endfor
        @if ($paginator->hasMorePages())
            <a href="{{ $pageUrl($paginator->currentPage() + 1) }}">&rsaquo;</a>
        @endif
    </nav>
@endif
