@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <a href="/blog" class="text-sm font-medium text-[#5DF8D8] hover:underline">Blog'a dön</a>
    <header class="mt-8 max-w-4xl">
        <h1 class="text-4xl font-semibold tracking-normal text-white sm:text-5xl">{{ $post['title'] }}</h1>
        <p class="mt-4 text-lg leading-8 text-slate-400">{{ $post['summary'] }}</p>
        <div class="mt-5 flex flex-wrap gap-3 text-sm text-slate-400">
            <span>{{ tr_date($post['published_at']) }}</span>
            @isset($post['updated_at'])<span>Güncellendi: {{ tr_date($post['updated_at']) }}</span>@endisset
            <span>{{ $post['reading_time'] ?? 1 }} dk okuma</span>
        </div>
        <div class="mt-5">@include('partials.tag-list', ['slugs' => $post['tags']])</div>
    </header>
    <div class="mt-10"><img src="{{ $post['cover']['url'] }}" alt="{{ $post['cover']['alt'] }}" class="aspect-[2/1] w-full rounded-lg object-cover"></div>
    <div class="mt-12 grid gap-10 lg:grid-cols-[minmax(0,1fr)_260px]">
        <article>@include('partials.rich-content', ['blocks' => $post['content'] ?? [], 'html' => $post['content_html'] ?? null])</article>
        <aside class="hidden lg:block">
            <div class="sticky top-24 rounded-lg border border-white/10 bg-[#101827] p-4">
                <p class="text-sm font-semibold text-white">İçindekiler</p>
                <div class="mt-3 grid gap-2 text-sm text-slate-400">
                    @foreach(content_headings($post['content'] ?? [], $post['content_html'] ?? null) as $block)
                        <a href="#{{ $block['id'] }}" class="hover:text-[#5DF8D8]">{{ $block['text'] }}</a>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
    @if(count($related))
        <section class="mt-16">
            <h2 class="text-2xl font-semibold text-white">İlgili yazılar</h2>
            <div class="mt-6 grid gap-5 md:grid-cols-3">
                @foreach($related as $item)
                    @include('partials.post-card', ['post' => $item])
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
