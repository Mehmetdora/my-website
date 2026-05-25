@php($site = config('content.site'))
<header class="sticky top-0 z-40 border-b border-white/10 bg-[#0a0f1e]/95 backdrop-blur">
    <div class="mx-auto flex min-h-20 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <a href="/" class="focus-ring flex items-center gap-3 rounded-md" aria-label="Go to home page">
            <span class="inline-flex h-11 w-11 items-center justify-center text-[34px] leading-none" aria-hidden="true">🇹🇷</span>
            <span class="hidden text-base font-semibold text-white sm:inline">{{ $site['name'] }}</span>
        </a>

        <nav class="hidden items-center gap-1 lg:flex">
            @foreach($site['nav'] as $item)
                @if(!($item['special'] ?? false))
                    <a href="{{ $item['href'] }}" class="focus-ring rounded-md px-3 py-2 text-sm font-semibold text-slate-300 transition hover:bg-white/5 hover:text-[hsl(var(--accent))]">{{ $item['title'] }}</a>
                @endif
            @endforeach
            <div class="ml-3 flex items-center gap-4">
                <span class="h-8 w-px bg-white/15" aria-hidden="true"></span>
                @foreach($site['nav'] as $item)
                    @if($item['special'] ?? false)
                        <a href="{{ $item['href'] }}" class="focus-ring relative h-[34px] min-w-[95px] overflow-hidden rounded-full border border-[#6FD1D7]/65 bg-[#3B7597] px-3.5 py-2 text-[11px] font-black text-white shadow-[inset_0_3px_5px_rgba(255,255,255,0.42),inset_0_-6px_10px_rgba(9,60,93,0.38),0_14px_28px_-23px_rgb(9_60_93)] transition hover:translate-y-[-1px] hover:border-[#5DF8D8] hover:shadow-[inset_0_3px_5px_rgba(255,255,255,0.48),inset_0_-6px_10px_rgba(9,60,93,0.34),0_16px_32px_-24px_rgb(93_248_216)]">
                            @include('partials.beach-scene')
                            <span class="relative z-20 drop-shadow-[0_1px_2px_rgba(9,60,93,0.8)]">{{ $item['title'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </nav>

        <button type="button" data-mobile-toggle class="icon-tooltip focus-ring inline-flex h-11 w-11 items-center justify-center rounded-md border border-white/10 bg-white/5 text-white lg:hidden" aria-label="Open menu" data-tooltip="Open menu" data-tooltip-side="bottom">
            <span class="text-2xl leading-none">☰</span>
        </button>
    </div>

    <div data-mobile-menu class="hidden px-4 pb-4 lg:hidden">
        <nav class="grid gap-2 rounded-lg border border-white/10 bg-[#101827] p-4 shadow-2xl">
            @foreach($site['nav'] as $item)
                @if(!($item['special'] ?? false))
                    <a href="{{ $item['href'] }}" class="rounded-md px-3 py-2 font-medium text-slate-200 hover:bg-white/5 hover:text-[hsl(var(--accent))]">{{ $item['title'] }}</a>
                @endif
            @endforeach
            <div class="mt-2 border-t border-white/10 pt-3">
                <a href="/life" class="relative flex min-h-12 items-center overflow-hidden rounded-md border border-[#6FD1D7]/65 bg-[#3B7597] px-3 py-2 font-black text-white">
                    @include('partials.beach-scene')
                    <span class="relative z-20">My Life</span>
                </a>
            </div>
        </nav>
    </div>
</header>
