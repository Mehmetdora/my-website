@php($site = config('content.site'))
<footer class="border-t border-white/10 bg-[#0a0f1e]">
    <div class="mx-auto grid w-full max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-[1.2fr_1.5fr_1fr] lg:px-8">
        <div>
            <p class="text-lg font-semibold text-white">{{ $site['name'] }}</p>
            <p class="mt-1 text-sm text-slate-400">{{ $site['role'] }}</p>
            <p class="mt-1 text-sm text-slate-500">{{ $site['location'] }}</p>
        </div>

        <div>
            <nav class="flex flex-wrap gap-x-4 gap-y-2 text-sm text-slate-400" aria-label="Footer navigation">
                @foreach($site['nav'] as $item)
                    <a href="{{ $item['href'] }}" class="hover:text-[hsl(var(--accent))]">{{ $item['title'] }}</a>
                @endforeach
            </nav>
        </div>

        <div class="md:text-right">
            <div class="flex gap-2 md:justify-end">
                <a href="{{ $site['links']['github'] }}" aria-label="GitHub" data-tooltip="Open GitHub profile" target="_blank" rel="noopener noreferrer" class="icon-tooltip badge hover:border-[hsl(var(--accent))] hover:text-[hsl(var(--accent))]">
                    <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.4 5.4 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65S8.93 17.38 9 18v4"></path><path d="M9 18c-4.51 2-5-2-7-2"></path></svg>
                </a>
                <a href="{{ $site['links']['linkedin'] }}" aria-label="LinkedIn" data-tooltip="Open LinkedIn profile" target="_blank" rel="noopener noreferrer" class="icon-tooltip badge hover:border-[hsl(var(--accent))] hover:text-[hsl(var(--accent))]">
                    <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"></path><rect width="4" height="12" x="2" y="9"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                </a>
                <a href="{{ $site['links']['telegram'] }}" aria-label="Telegram" data-tooltip="Open Telegram link" target="_blank" rel="noopener noreferrer" class="icon-tooltip badge hover:border-[hsl(var(--accent))] hover:text-[hsl(var(--accent))]">
                    <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path></svg>
                </a>
                <a href="{{ $site['links']['email'] }}" aria-label="Email" data-tooltip="Send email" target="_blank" rel="noopener noreferrer" class="icon-tooltip badge hover:border-[hsl(var(--accent))] hover:text-[hsl(var(--accent))]">
                    <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect></svg>
                </a>
            </div>
            <p class="mt-4 text-sm text-slate-500">© 2026 {{ $site['name'] }}</p>
        </div>
    </div>
</footer>
