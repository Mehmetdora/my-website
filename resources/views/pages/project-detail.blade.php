@extends('layouts.app')
@php($labels = ['planned' => 'Planned', 'in-progress' => 'In Progress', 'completed' => 'Completed', 'archived' => 'Archived'])
@section('content')
<div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <a href="/projects" class="text-sm font-medium text-[#5DF8D8] hover:underline">Back to Projects</a>
    <header class="mt-8 max-w-4xl">
        <div class="flex flex-wrap gap-2"><span class="badge border-emerald-300/40 bg-emerald-300/10 text-emerald-300">{{ $labels[$project['status']] ?? $project['status'] }}</span></div>
        <h1 class="mt-4 text-4xl font-semibold tracking-normal text-white sm:text-5xl">{{ $project['title'] }}</h1>
        <p class="mt-4 text-lg leading-8 text-slate-400">{{ $project['summary'] }}</p>
        @isset($project['github'])<div class="mt-6"><a href="{{ $project['github'] }}" class="btn-outline min-h-10 px-3">GitHub</a></div>@endisset
    </header>
    <div class="mt-10">@include('partials.tag-list', ['slugs' => $project['tags']])</div>
    <section class="mt-10">
        <div class="rounded-lg border border-white/10 bg-[#101827] p-5">
            <h2 class="font-semibold text-white">Technologies</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach($project['technologies'] as $tech)
                    <span class="badge">{{ $tech }}</span>
                @endforeach
            </div>
        </div>
    </section>
    <article class="mt-12 max-w-4xl">@include('partials.rich-content', ['blocks' => $project['content'] ?? [], 'html' => $project['content_html'] ?? null])</article>
    @if(count($related))
        <section class="mt-16"><h2 class="text-2xl font-semibold text-white">Related Projects</h2><div class="mt-6 grid gap-5 md:grid-cols-3">@foreach($related as $item) @include('partials.project-card', ['project' => $item]) @endforeach</div></section>
    @endif
</div>
@endsection
