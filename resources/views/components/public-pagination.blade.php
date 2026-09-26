@props(['paginator'])

@if($paginator && $paginator->hasPages())
    <div class="public-pagination-footer">
        <span>Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>
        <div class="pager">
            @if ($paginator->onFirstPage())
                <span class="pager-link disabled">Sebelumnya</span>
            @else
                <a class="pager-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Sebelumnya</a>
            @endif

            @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                @if ($i == $paginator->currentPage())
                    <span class="pager-link active">{{ $i }}</span>
                @else
                    <a class="pager-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                @endif
            @endfor

            @if ($paginator->hasMorePages())
                <a class="pager-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya</a>
            @else
                <span class="pager-link disabled">Berikutnya</span>
            @endif
        </div>
    </div>
@endif

@once
@push('styles')
<style>
    .public-pagination-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 2rem;
        padding-top: 1.25rem;
        border-top: 1px solid #e2e8f0;
        font-size: 13px;
        color: #475569;
    }
    .public-pagination-footer span {
        font-weight: 500;
        color: #475569;
    }
    .public-pagination-footer .pager {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
    }
    .public-pagination-footer .pager-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 600;
        color: #172554;
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
        line-height: 1.4;
    }
    .public-pagination-footer .pager-link:hover {
        background-color: #f1f5f9;
        border-color: #94a3b8;
        color: #0f172a;
    }
    .public-pagination-footer .pager-link.active {
        color: #ffffff;
        background-color: #172554;
        border-color: #172554;
        cursor: default;
    }
    .public-pagination-footer .pager-link.active:hover {
        color: #ffffff;
        background-color: #172554;
    }
    .public-pagination-footer .pager-link.disabled {
        color: #94a3b8;
        background-color: #f8fafc;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }
</style>
@endpush
@endonce
