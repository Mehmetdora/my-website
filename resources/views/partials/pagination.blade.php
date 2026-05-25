@if($paginator->hasPages())
    <nav class="mt-10 flex flex-wrap items-center justify-center gap-2" aria-label="Pagination">
        @if($paginator->onFirstPage())
            <span class="rounded-md border border-white/10 px-3 py-2 text-sm font-semibold text-slate-600">Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="rounded-md border border-white/10 px-3 py-2 text-sm font-semibold text-slate-300 transition hover:border-[#5DF8D8] hover:text-[#5DF8D8]">Previous</a>
        @endif

        @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
            @if($page === $paginator->currentPage())
                <span aria-current="page" class="rounded-md border border-[#5DF8D8] bg-[#5DF8D8]/12 px-3 py-2 text-sm font-black text-[#5DF8D8]">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="rounded-md border border-white/10 px-3 py-2 text-sm font-semibold text-slate-300 transition hover:border-[#5DF8D8] hover:text-[#5DF8D8]">{{ $page }}</a>
            @endif
        @endforeach

        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="rounded-md border border-white/10 px-3 py-2 text-sm font-semibold text-slate-300 transition hover:border-[#5DF8D8] hover:text-[#5DF8D8]">Next</a>
        @else
            <span class="rounded-md border border-white/10 px-3 py-2 text-sm font-semibold text-slate-600">Next</span>
        @endif
    </nav>
@endif
