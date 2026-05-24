@include('partials.cards')
<div class="flex flex-wrap gap-2">
    @foreach(tag_items($slugs ?? []) as $tag)
        @if($linked ?? true)
            <a href="/tags/{{ $tag['slug'] }}" class="badge hover:border-[hsl(var(--accent)/0.7)] hover:text-[hsl(var(--accent))]">#{{ $tag['name'] }}</a>
        @else
            <span class="badge">#{{ $tag['name'] }}</span>
        @endif
    @endforeach
</div>
