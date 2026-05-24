@include('partials.cards')
<a href="/blog/{{ $post['slug'] }}" class="group block h-full rounded-lg border border-[hsl(var(--border))] bg-[hsl(var(--panel))] p-3 shadow-soft transition hover:-translate-y-0.5 hover:border-[hsl(var(--accent))]">
    <img src="{{ $post['cover']['url'] }}" alt="{{ $post['cover']['alt'] }}" class="aspect-[2/1] w-full rounded-lg object-cover">
    <div class="p-2">
        <div class="mb-3 mt-2 inline-flex items-center gap-1 text-xs text-[hsl(var(--soft))]">
            <svg viewBox="0 0 24 24" class="h-[13px] w-[13px]" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
            {{ $post['reading_time'] ?? 1 }} dk
        </div>
        <h2 class="text-xl font-semibold tracking-normal text-[hsl(var(--ink))] group-hover:text-[hsl(var(--accent))]">{{ $post['title'] }}</h2>
        <p class="mt-2 line-clamp-3 text-sm leading-6 text-[hsl(var(--soft))]">{{ $post['summary'] }}</p>
        <div class="mt-4">@include('partials.tag-list', ['slugs' => array_slice($post['tags'], 0, 3), 'linked' => false])</div>
        <p class="mt-4 text-xs text-[hsl(var(--soft))]">{{ tr_date($post['published_at']) }}</p>
    </div>
</a>
