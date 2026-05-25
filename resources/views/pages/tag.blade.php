@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
    <h1 class="max-w-3xl text-4xl font-semibold tracking-normal text-white sm:text-5xl">#{{ $tag['name'] }}</h1>
    <p class="mt-4 max-w-2xl text-lg leading-8 text-slate-400">Posts and projects using this tag.</p>
</div>
<div class="mx-auto max-w-7xl space-y-12 px-4 pb-16 sm:px-6 lg:px-8">
    @if(!count($posts) && !count($projects))
        <div class="panel p-8 text-center text-slate-400">No content found for this tag.</div>
    @endif
    @if(count($posts))
        <section><h2 class="mb-5 text-2xl font-semibold text-white">Posts</h2><div class="grid gap-5 md:grid-cols-3">@foreach($posts as $post) @include('partials.post-card', ['post' => $post]) @endforeach</div></section>
    @endif
    @if(count($projects))
        <section><h2 class="mb-5 text-2xl font-semibold text-white">Projects</h2><div class="grid gap-5 md:grid-cols-3">@foreach($projects as $project) @include('partials.project-card', ['project' => $project]) @endforeach</div></section>
    @endif
</div>
@endsection
