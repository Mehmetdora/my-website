@extends('layouts.app')
@php($site = config('content.site'))

@section('content')
<div class="bg-[#0a0f1e]">
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
        <span class="section-label">Resume</span>
        <div class="mt-4 grid gap-8 lg:grid-cols-[1fr_0.52fr] lg:items-start">
            <div>
                <h1 class="text-4xl font-extrabold tracking-normal text-white sm:text-5xl">{{ $site['name'] }}</h1>
                <p class="mt-4 text-lg font-semibold text-slate-300">{{ $site['role'] }}</p>
                <p class="mt-5 max-w-3xl text-base leading-8 text-slate-400">
                    Computer engineering student building projects around embedded systems, C/C++, STM32, ESP32, and IoT, while supporting technical work with writing and project documentation.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('cv.pdf') }}" class="btn-primary" target="_blank" rel="noopener noreferrer">Open CV PDF</a>
                    <a href="{{ $site['links']['email'] }}" class="btn-primary">Send Email</a>
                    <a href="{{ $site['links']['github'] }}" class="btn-outline" target="_blank" rel="noopener noreferrer">GitHub</a>
                    <a href="{{ $site['links']['linkedin'] }}" class="btn-outline" target="_blank" rel="noopener noreferrer">LinkedIn</a>
                    <a href="{{ $site['links']['telegram'] }}" class="btn-outline" target="_blank" rel="noopener noreferrer">Telegram</a>
                </div>
            </div>
            <aside class="panel p-6">
                <h2 class="text-xl font-bold text-white">Personal Information</h2>
                <dl class="mt-5 space-y-4 text-sm">
                    <div><dt class="font-semibold text-slate-500">Location</dt><dd class="mt-1 text-slate-200">{{ $site['location'] }}</dd></div>
                    <div><dt class="font-semibold text-slate-500">Email</dt><dd class="mt-1 text-slate-200">mehmetdora333@gmail.com</dd></div>
                </dl>
            </aside>
        </div>

        <div class="mt-12 grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
            <section class="panel p-6">
                <h2 class="text-2xl font-extrabold text-white">Top Skills</h2>
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach(config('content.home.top_skills') as $skill)
                        <span class="badge">{{ $skill }}</span>
                    @endforeach
                </div>
            </section>

            <section class="panel p-6">
                <h2 class="text-2xl font-extrabold text-white">Education</h2>
                <div class="mt-5 space-y-4">
                    @foreach(config('content.about.education') as $item)
                        <div class="border-l border-[#5DF8D8]/40 pl-4">
                            <div class="flex flex-wrap justify-between gap-2">
                                <h3 class="font-bold text-white">{{ $item['degree'] }}</h3>
                                <span class="text-xs font-semibold text-slate-500">{{ $item['period'] }}</span>
                            </div>
                            <p class="mt-1 text-sm leading-6 text-slate-400">{{ $item['org'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </section>
</div>
@endsection
