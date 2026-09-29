@props([
    'paginator'
])

@if ($paginator->hasPages())
    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Result count text -->
        <div class="text-xs text-slate-500 font-medium">
            Menampilkan
            <span class="font-bold text-slate-800">{{ $paginator->firstItem() ?? 0 }}</span>
            sampai
            <span class="font-bold text-slate-800">{{ $paginator->lastItem() ?? 0 }}</span>
            dari total
            <span class="font-bold text-blue-600">{{ number_format($paginator->total()) }}</span>
            data
        </div>

        <!-- Pagination Page Controls -->
        <div class="flex items-center gap-1.5 flex-wrap">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="w-8 h-8 rounded-lg bg-slate-100/80 text-slate-400 flex items-center justify-center text-xs cursor-not-allowed border border-slate-200/50"
                      aria-disabled="true">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   data-spa="true"
                   class="pagination-link w-8 h-8 rounded-lg bg-white hover:bg-slate-100 text-slate-700 hover:text-blue-600 flex items-center justify-center text-xs border border-slate-200/90 shadow-2xs transition-colors"
                   title="Halaman Sebelumnya">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @php
                // Build elements window
                $elements = [];
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $onEachSide = 1;

                if ($lastPage < 8) {
                    $elements = [range(1, $lastPage)];
                } else {
                    $start = max(1, $currentPage - $onEachSide);
                    $end = min($lastPage, $currentPage + $onEachSide);

                    $pages = [];
                    if ($start > 1) {
                        $pages[] = 1;
                        if ($start > 2) $pages[] = '...';
                    }
                    for ($i = $start; $i <= $end; $i++) {
                        $pages[] = $i;
                    }
                    if ($end < $lastPage) {
                        if ($end < $lastPage - 1) $pages[] = '...';
                        $pages[] = $lastPage;
                    }
                    $elements = [$pages];
                }
            @endphp

            @foreach ($elements as $element)
                @foreach ($element as $page)
                    @if ($page === '...')
                        <span class="px-1.5 py-1 text-xs text-slate-400 font-bold select-none">&bull;&bull;&bull;</span>
                    @elseif ($page == $currentPage)
                        <span class="w-8 h-8 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black text-xs flex items-center justify-center shadow-xs">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($page) }}" 
                           data-spa="true"
                           class="pagination-link w-8 h-8 rounded-lg bg-white hover:bg-slate-100 text-slate-700 hover:text-blue-600 flex items-center justify-center text-xs font-semibold border border-slate-200/90 shadow-2xs transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   data-spa="true"
                   class="pagination-link w-8 h-8 rounded-lg bg-white hover:bg-slate-100 text-slate-700 hover:text-blue-600 flex items-center justify-center text-xs border border-slate-200/90 shadow-2xs transition-colors"
                   title="Halaman Berikutnya">
                    <i class="fas fa-chevron-right text-[10px]"></i>
                </a>
            @else
                <span class="w-8 h-8 rounded-lg bg-slate-100/80 text-slate-400 flex items-center justify-center text-xs cursor-not-allowed border border-slate-200/50"
                      aria-disabled="true">
                    <i class="fas fa-chevron-right text-[10px]"></i>
                </span>
            @endif
        </div>
    </div>
@elseif($paginator->total() > 0)
    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs text-slate-500 font-medium">
        <span>Menampilkan seluruh <strong class="text-blue-600">{{ $paginator->total() }}</strong> data</span>
        <span class="text-slate-400">Semua data pada 1 halaman</span>
    </div>
@endif
