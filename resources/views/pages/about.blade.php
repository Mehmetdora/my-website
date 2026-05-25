@extends('layouts.app')
@php($site = config('content.site'))

@section('content')
<div class="bg-[#0a0f1e]">
    <div class="mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <header class="grid gap-10 lg:grid-cols-[1.15fr_0.85fr] lg:items-end">
            <div>
                <span class="section-label">About Me</span>
                <h1 class="mt-4 sm:text-5xl font-extrabold leading-[1.02] tracking-normal text-white">
                    {{ $site['name'] }}
                </h1>
                <p class="mt-4 text-lg font-semibold text-slate-300">
                    {{ $site['role'] }} · {{ $site['location'] }}
                </p>
                <p class="mt-6 max-w-4xl text-base leading-8 text-slate-400 sm:text-lg">
                    I am a computer engineering student focused on embedded systems, low-level programming, and project-based learning. I work with STM32, ESP32, C/C++, IoT, and electronics, and I like turning what I learn into a structured archive of writing, code, project notes, and personal updates.
                </p>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-slate-400 mb-3">My Cores</h3>
                <div class="grid grid-cols-2 gap-4">
                    @foreach(config('content.about.stats') as $stat)
                        <div class="rounded-lg border border-white/10 bg-[#101827] p-5">
                            <span class="block text-3xl font-extrabold text-[hsl(var(--accent))]">{{ $stat['value'] }}</span>
                            <span class="mt-2 block text-sm font-semibold text-slate-400">{{ $stat['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </header>

        <div class="mt-16 space-y-12">
            <section class="grid gap-5 border-t border-white/10 pt-8 lg:grid-cols-[290px_minmax(0,1fr)]">
                <h2 class="text-2xl font-extrabold text-white">Why This Site Exists</h2>
                <div class="space-y-5 text-base leading-8 text-slate-400">
                    <p>This site is both a personal showcase and a structured engineering notebook. The blog is for technical writing, the projects section is for the applications I build, and My Life is for more personal moments.</p>
                    <p>My goal is to make the learning process visible over time, not only the final results: the bugs I meet, the solutions I try, hardware choices, and project decisions all become part of this archive.</p>
                </div>
            </section>
        </div>

        <section class="mt-16 grid gap-5 border-t border-white/10 pt-8 lg:grid-cols-[290px_minmax(0,1fr)]">
            <h2 class="text-2xl font-extrabold text-white">Hobbies and Personal Side</h2>
            <div class="space-y-5 text-base leading-8 text-slate-400">
                @foreach(config('content.about.hobbies') as $hobby)
                    <p>
                        <span class="font-bold text-white">{{ $hobby['title'] }}:</span>
                        {{ $hobby['description'] }}
                    </p>
                @endforeach
            </div>
        </section>

        <section class="mt-16 grid gap-8 lg:grid-cols-[1fr_0.85fr]">
            <div>
                <h2 class="flex items-center gap-3 text-3xl font-extrabold text-white">
                    <svg viewBox="0 0 24 24" class="h-[30px] w-[30px] text-[hsl(var(--accent))]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"></path><path d="M22 10v6"></path><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"></path></svg>
                    Education
                </h2>
                <div class="mt-6 space-y-4">
                    @foreach(config('content.about.education') as $item)
                        <div class="rounded-lg border border-white/10 bg-[#101827] p-5">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <span class="font-bold text-white">{{ $item['degree'] }}</span>
                                <span class="rounded-full bg-white/5 px-3 py-1 text-xs font-semibold text-slate-400">{{ $item['period'] }}</span>
                            </div>
                            <p class="mt-2 text-sm leading-6 text-slate-400">{{ $item['org'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>


        </section>

        <div class="mt-16 flex flex-wrap gap-4">
            <a href="/projects" class="focus-ring inline-flex min-h-12 items-center rounded-md bg-[hsl(var(--accent))] px-7 text-sm font-bold text-[#07101f]">
                View Projects
            </a>
            <a href="/cv" class="focus-ring inline-flex min-h-12 items-center rounded-md border border-cyan-300/25 px-7 text-sm font-bold text-slate-100 hover:border-[hsl(var(--accent))] hover:text-[hsl(var(--accent))]">
                Resume Page
            </a>
        </div>
    </div>
</div>
@endsection
