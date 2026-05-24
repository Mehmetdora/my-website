<div class="prose-custom">
    @if(!empty($html))
        {!! sanitize_content_html($html) !!}
    @else
        @foreach($blocks as $block)
            @if($block['type'] === 'paragraph')
                <p>{{ $block['text'] }}</p>
            @elseif($block['type'] === 'heading')
                <h2 id="{{ \Illuminate\Support\Str::slug($block['text']) }}">{{ $block['text'] }}</h2>
            @elseif($block['type'] === 'quote')
                <blockquote class="my-5 rounded-lg border-l-4 border-[#5DF8D8] bg-white/5 p-4 text-slate-300">{{ $block['text'] }}</blockquote>
            @elseif($block['type'] === 'callout')
                <aside class="my-5 rounded-lg border border-[#5DF8D8]/30 bg-[#5DF8D8]/10 p-4">
                    <p class="font-semibold text-white">{{ $block['title'] ?? 'Not' }}</p>
                    <p class="mt-1 text-sm leading-6 text-slate-300">{{ $block['text'] }}</p>
                </aside>
            @elseif($block['type'] === 'code')
                <div class="my-5 overflow-hidden rounded-lg border border-white/10 bg-[#0b1220] text-slate-100">
                    <div class="flex min-h-11 items-center gap-2 border-b border-white/10 px-3 text-xs text-slate-300">
                        <span class="rounded bg-white/10 px-2 py-1 font-mono">{{ $block['language'] }}</span>
                        <span class="font-mono">{{ $block['filename'] ?? '' }}</span>
                    </div>
                    <pre class="overflow-x-auto p-4 text-sm leading-6"><code>{{ $block['code'] }}</code></pre>
                </div>
            @elseif($block['type'] === 'list')
                <ul>
                    @foreach($block['items'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @endif
        @endforeach
    @endif
</div>
