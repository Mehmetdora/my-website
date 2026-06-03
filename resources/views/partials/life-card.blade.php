@php($cover = $item['images'][0] ?? null)
@php($isAudio = ($item['type'] ?? 'image') === 'audio')
<button type="button" data-life-open="{{ $item['id'] }}" class="group block overflow-hidden rounded-2xl border border-white/10 bg-[#101827] text-left transition hover:-translate-y-1 hover:border-rose-300/60">
    @if($isAudio)
        <div class="relative flex aspect-[2/1] w-full items-center justify-center overflow-hidden bg-[radial-gradient(circle_at_25%_20%,rgba(93,248,216,0.24),transparent_32%),linear-gradient(135deg,#0a0f1e,#132137_52%,#093c5d)]">
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/45 to-transparent"></div>
            <div class="relative z-10 flex h-20 w-20 items-center justify-center rounded-full border border-[#5DF8D8]/35 bg-[#5DF8D8]/12 text-[#5DF8D8] shadow-[0_0_45px_-18px_#5DF8D8]">
                <svg viewBox="0 0 24 24" class="h-9 w-9" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
            </div>
            <div class="absolute bottom-7 left-6 right-6 flex h-10 items-end justify-center gap-1.5 opacity-80">
                @foreach([28, 46, 34, 62, 44, 74, 52, 38, 68, 48, 58, 32, 50, 40, 60, 36] as $height)
                    <span class="w-1.5 rounded-full bg-[#5DF8D8]/70" style="height: {{ $height }}%"></span>
                @endforeach
            </div>
            <span class="absolute left-4 top-4 rounded-full border border-white/10 bg-black/25 px-3 py-1 text-xs font-bold text-slate-100">Audio</span>
        </div>
    @elseif($cover)
        <img src="{{ $cover['url'] }}" alt="{{ $cover['alt'] ?? $item['excerpt'] }}" class="block aspect-[2/1] w-full object-cover transition duration-300 group-hover:scale-[1.015]">
    @else
        <div class="flex aspect-[2/1] items-center justify-center bg-[#172132]">
            <span class="font-mono text-sm text-slate-500">Life</span>
        </div>
    @endif
    <div class="p-5">
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-semibold text-slate-500">
            @if($isAudio)
                <span class="inline-flex items-center gap-2 text-[#5DF8D8]">
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
                    Records
                </span>
            @endif
            <span class="inline-flex items-center gap-2">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 text-rose-300" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                {{ tr_date($item['published_at']) }}
            </span>
            @isset($item['location'])
                <span class="inline-flex items-center gap-2">
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 text-rose-300" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    {{ $item['location'] }}
                </span>
            @endisset
        </div>
        <p class="mt-4 text-lg font-normal leading-8 text-slate-200">{{ $item['excerpt'] }}</p>
    </div>
</button>
