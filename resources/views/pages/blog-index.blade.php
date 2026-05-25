@extends('layouts.app')
@section('content')
<div class="bg-[#0a0f1e]">
    <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
        <h1 class="max-w-3xl text-4xl font-semibold tracking-normal text-white sm:text-5xl">Blog</h1>
        <p class="mt-4 max-w-2xl text-lg leading-8 text-slate-400">Technical writing on embedded systems, C/C++, microcontrollers, and things I learn along the way.</p>
    </div>
    <div class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-2xl border border-white/10 bg-[#101827] p-5">
            <p class="mb-4 text-sm font-semibold text-slate-400">Filter by tag</p>
            <div class="flex flex-wrap gap-2">
                <a href="/blog" class="badge {{ empty($activeTag) ? 'border-[#5DF8D8] bg-[#5DF8D8]/12 text-[#5DF8D8]' : '' }}">All</a>
                @foreach(config('content.tags') as $tag)
                    <a href="/blog?tag={{ $tag['slug'] }}" class="badge {{ $activeTag === $tag['slug'] ? 'border-[#5DF8D8] bg-[#5DF8D8]/12 text-[#5DF8D8]' : '' }}">#{{ $tag['name'] }}</a>
                @endforeach
            </div>
        </div>
        @if(count($posts))
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach($posts as $post)
                    @include('partials.post-card', ['post' => $post])
                @endforeach
            </div>
            @include('partials.pagination', ['paginator' => $posts])
        @else
            <div class="panel p-8 text-center text-slate-400">No posts found for the selected tag.</div>
        @endif
    </div>
</div>
@endsection
