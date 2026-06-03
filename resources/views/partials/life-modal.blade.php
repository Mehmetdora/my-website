<div data-life-modal="{{ $item['id'] }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/82 p-3 backdrop-blur-sm" role="dialog" aria-modal="true">
    <button type="button" data-life-close aria-label="Close post" data-tooltip="Close post" data-tooltip-side="left" class="icon-tooltip absolute right-4 top-4 z-10 inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/15 bg-[#101827]/90 text-white transition hover:border-rose-300 hover:text-rose-200">
        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
    </button>

    <article class="max-h-[calc(100vh-1.5rem)] w-full max-w-5xl overflow-hidden rounded-3xl border border-white/10 bg-[#101827] shadow-[0_30px_100px_-45px_rgb(244_114_182)]">
        @if(($item['type'] ?? 'image') === 'audio')
            <div class="relative overflow-hidden bg-[radial-gradient(circle_at_26%_18%,rgba(93,248,216,0.24),transparent_30%),linear-gradient(135deg,#0a0f1e,#132137_55%,#093c5d)] px-5 py-8 sm:px-8 sm:py-12">
                <div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-black/45 to-transparent"></div>
                <div class="relative z-10 mx-auto max-w-3xl">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full border border-[#5DF8D8]/35 bg-[#5DF8D8]/12 text-[#5DF8D8] shadow-[0_0_45px_-18px_#5DF8D8]">
                            <svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
                        </div>
                        <div>
                            <span class="section-label">Records</span>
                            <p class="mt-1 text-sm font-semibold text-slate-300">{{ $item['audio']['name'] ?? 'Record' }}</p>
                        </div>
                    </div>
                    <div class="mt-8 flex h-20 items-end justify-center gap-2 rounded-2xl border border-white/10 bg-black/20 px-4 py-5">
                        @foreach([30, 52, 38, 70, 48, 88, 58, 42, 76, 54, 66, 36, 60, 44, 72, 40, 56, 34] as $height)
                            <span class="w-2 rounded-full bg-[#5DF8D8]/75" style="height: {{ $height }}%"></span>
                        @endforeach
                    </div>
                    @if(!empty($item['audio']['url']))
                        <audio controls preload="metadata" src="{{ $item['audio']['url'] }}" class="mt-6 w-full"></audio>
                    @endif
                </div>
            </div>
        @elseif(!count($item['images']))
            <div class="bg-[#0a0f1e]">
                <div class="flex aspect-[2/1] w-full items-center justify-center bg-[#172132]">
                    <span class="font-mono text-sm text-slate-500">Life</span>
                </div>
            </div>
        @elseif(count($item['images']) === 1)
            @php($image = $item['images'][0])
            <div class="aspect-[2/1] w-full bg-black">
                <img src="{{ $image['url'] }}" alt="{{ $image['alt'] ?? $item['excerpt'] }}" class="block h-full w-full object-cover">
            </div>
        @else
            <div data-carousel class="bg-black">
                <div data-carousel-track class="aspect-[2/1] w-full touch-pan-x overflow-x-auto overscroll-x-contain scroll-smooth [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                    <div class="flex h-full snap-x snap-mandatory">
                        @foreach($item['images'] as $image)
                            <div class="h-full min-w-full snap-start snap-always">
                                <img src="{{ $image['url'] }}" alt="{{ $image['alt'] ?? $item['excerpt'].' photo '.$loop->iteration }}" class="block h-full w-full object-cover" draggable="false">
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex h-9 items-center justify-center gap-2 bg-black" aria-label="Image position">
                    @foreach($item['images'] as $image)
                        <button type="button" data-carousel-dot="{{ $loop->index }}" aria-label="Go to image {{ $loop->iteration }}" data-tooltip="Go to image {{ $loop->iteration }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" class="icon-tooltip h-2 rounded-full transition-all {{ $loop->first ? 'w-6 bg-white' : 'w-2 bg-white/35 hover:bg-white/70' }}"></button>
                    @endforeach
                </div>
            </div>
        @endif

        <footer class="border-t border-white/10 bg-[#101827] p-4 sm:p-5">
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm font-semibold text-slate-500">
                @if(($item['type'] ?? 'image') === 'audio')
                    <span class="inline-flex items-center gap-2 text-[#5DF8D8]">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
                        Records
                    </span>
                @endif
                <span class="inline-flex items-center gap-2">
                    <svg viewBox="0 0 24 24" class="h-4 w-4 text-rose-300" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                    {{ tr_date($item['published_at']) }}
                </span>
                @isset($item['location'])
                    <span class="inline-flex items-center gap-2">
                        <svg viewBox="0 0 24 24" class="h-4 w-4 text-rose-300" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        {{ $item['location'] }}
                    </span>
                @endisset
            </div>
            <p class="mt-3 text-base font-normal leading-7 text-slate-100 sm:text-lg">{{ $item['excerpt'] }}</p>
        </footer>
    </article>
</div>
