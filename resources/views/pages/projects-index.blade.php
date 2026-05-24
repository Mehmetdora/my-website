@extends('layouts.app')
@section('content')
<div class="bg-[#0a0f1e]">
    <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
        <h1 class="max-w-3xl text-4xl font-semibold tracking-normal text-white sm:text-5xl">Projeler</h1>
        <p class="mt-4 max-w-2xl text-lg leading-8 text-slate-400">Mikrodenetleyici, haberleşme protokolleri, IoT ve düşük seviye yazılım denemeleri.</p>
    </div>
    <div class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($projects as $project)
                @include('partials.project-card', ['project' => $project])
            @endforeach
        </div>
    </div>
</div>
@endsection
