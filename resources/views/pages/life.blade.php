@extends('layouts.app')

@section('content')
<div class="bg-[#0a0f1e]">
    <section class="relative overflow-hidden border-b border-white/10">
        <div class="relative mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <div class="max-w-4xl">
                <span class="section-label">Personal feed</span>
                <h1 class="mt-4 text-[clamp(2.7rem,6vw,5.4rem)] font-extrabold leading-[1.02] tracking-normal text-white">
                    Life outside the systems.
                </h1>
                <p class="mt-6 max-w-3xl text-base leading-8 text-slate-400 sm:text-lg">
                    This is where I share random everyday moments outside work and embedded projects.
                </p>
            </div>
        </div>
    </section>

    <section class="mx-auto w-full max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="mb-8">
            <span class="section-label">Timeline</span>
            <h2 class="mt-3 text-3xl font-extrabold text-white sm:text-4xl">Latest Posts</h2>
        </div>

        @if(count($lifePosts))
            <div data-life-feed data-life-next-url="{{ $lifePosts->nextPageUrl() }}">
                <div data-life-cards class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($lifePosts as $item)
                    @include('partials.life-card', ['item' => $item])
                @endforeach
                </div>
                <div data-life-sentinel class="{{ $lifePosts->hasMorePages() ? '' : 'hidden' }} py-10 text-center">
                    <span data-life-loading class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-400">Loading more posts...</span>
                </div>
            </div>
        @else
            <div class="rounded-lg border border-white/10 bg-[#101827] p-8 text-center">
                <h2 class="text-xl font-bold text-white">No personal posts yet</h2>
                <p class="mt-2 text-sm leading-6 text-slate-400">This space is prepared for social life, music, sports, and hobbies.</p>
            </div>
        @endif
    </section>
</div>

<div data-life-modals>
    @foreach($lifePosts as $item)
        @include('partials.life-modal', ['item' => $item])
    @endforeach
</div>
@endsection
