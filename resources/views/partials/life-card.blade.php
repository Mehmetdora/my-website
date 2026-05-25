@php($cover = $item['images'][0] ?? null)
<button type="button" data-life-open="{{ $item['id'] }}" class="group block overflow-hidden rounded-2xl border border-white/10 bg-[#101827] text-left transition hover:-translate-y-1 hover:border-rose-300/60">
    @if($cover)
        <img src="{{ $cover['url'] }}" alt="{{ $cover['alt'] ?? $item['excerpt'] }}" class="block aspect-[2/1] w-full object-cover transition duration-300 group-hover:scale-[1.015]">
    @else
        <div class="flex aspect-[2/1] items-center justify-center bg-[#172132]">
            <span class="font-mono text-sm text-slate-500">Life</span>
        </div>
    @endif
    <div class="p-5">
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-semibold text-slate-500">
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
