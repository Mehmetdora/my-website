@extends('layouts.app')
@php($site = config('content.site'))

@section('content')
<section class="bg-[#0a0f1e]">
    <div class="mx-auto grid min-h-[calc(100vh-5rem)] w-full max-w-7xl items-center gap-12 px-4 py-14 sm:px-6 lg:grid-cols-[1.18fr_0.82fr] lg:px-8 lg:py-20">
        <div class="max-w-4xl">
            <span class="section-label">Welcome</span>
            <h1 class="mt-5 max-w-4xl text-[clamp(1.6rem,3.5vw,2.8rem)] font-extrabold leading-[1.05] tracking-normal text-white">
                Hi, I'm <span class="text-[hsl(var(--accent))]">{{ $site['name'] }}</span><br> Embedded Systems & Software Developer.
            </h1>
            <p class="mt-6 max-w-3xl text-base leading-8 text-slate-400 sm:text-lg">
                As a computer engineering student, I work on STM32, ESP32, C/C++, IoT, and electronics. I collect what I learn here through projects, technical writing, and personal updates.
            </p>

            <div class="mt-8 w-full max-w-[400px] lg:hidden">
                <div class="relative">
                    <div class="absolute inset-5 rounded-[28px] bg-[hsl(var(--accent)/0.18)] blur-3xl"></div>
                    <div class="relative rounded-[28px] border border-cyan-300/18 bg-[#101827] p-4 shadow-[0_30px_100px_-55px_#22d3ee]">
                        <img src="{{ $site['profile_image'] }}" alt="{{ $site['name'] }} profile photo" class="aspect-[400/346] w-full rounded-[20px] object-cover sm:aspect-[400/494]">
                        <div class="mt-4 flex justify-center">
                            <div class="rounded-full bg-[hsl(var(--accent))] px-4 py-2 text-sm font-bold text-[#07101f] shadow-lg">
                                Available for projects
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-9 grid grid-cols-1 gap-8">
                <div>
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Find me on</span>
                    <div class="mt-3 flex flex-wrap gap-3">
                        <a href="{{ $site['links']['github'] }}" aria-label="GitHub" data-tooltip="Open GitHub profile" target="_blank" rel="noopener noreferrer" class="icon-tooltip focus-ring inline-flex h-12 w-12 items-center justify-center rounded-md border border-white/10 bg-white/5 text-slate-200 transition hover:border-[hsl(var(--accent))] hover:bg-[hsl(var(--accent))] hover:text-[#07101f]">
                            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.4 5.4 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65S8.93 17.38 9 18v4"></path><path d="M9 18c-4.51 2-5-2-7-2"></path></svg>
                        </a>
                        <a href="{{ $site['links']['linkedin'] }}" aria-label="LinkedIn" data-tooltip="Open LinkedIn profile" target="_blank" rel="noopener noreferrer" class="icon-tooltip focus-ring inline-flex h-12 w-12 items-center justify-center rounded-md border border-white/10 bg-white/5 text-slate-200 transition hover:border-[hsl(var(--accent))] hover:bg-[hsl(var(--accent))] hover:text-[#07101f]">
                            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"></path><rect width="4" height="12" x="2" y="9"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                        </a>
                        <a href="{{ $site['links']['telegram'] }}" aria-label="Telegram" data-tooltip="Open Telegram link" target="_blank" rel="noopener noreferrer" class="icon-tooltip focus-ring inline-flex h-12 w-12 items-center justify-center rounded-md border border-white/10 bg-white/5 text-slate-200 transition hover:border-[hsl(var(--accent))] hover:bg-[hsl(var(--accent))] hover:text-[#07101f]">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 2L11 13"></path><path d="M22 2L15 22l-4-9-9-4 18-7z"></path></svg>
                        </a>
                        <a href="{{ $site['links']['email'] }}" aria-label="Email" data-tooltip="Send email" target="_blank" rel="noopener noreferrer" class="icon-tooltip focus-ring inline-flex h-12 w-12 items-center justify-center rounded-md border border-white/10 bg-white/5 text-slate-200 transition hover:border-[hsl(var(--accent))] hover:bg-[hsl(var(--accent))] hover:text-[#07101f]">
                            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect></svg>
                        </a>
                    </div>
                </div>

                <div>
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Top skills</span>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach(config('content.home.top_skills') as $skill)
                            <span class="rounded-full border border-cyan-300/18 bg-cyan-300/7 px-3 py-1.5 text-sm font-semibold text-slate-200">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-9 flex flex-wrap gap-4">
                <a href="{{ route('cv.pdf') }}" class="focus-ring inline-flex min-h-12 items-center rounded-md bg-[hsl(var(--accent))] px-7 text-sm font-bold text-[#07101f] transition hover:translate-y-[-1px] hover:shadow-[0_14px_30px_-18px_#22d3ee]">
                    CV
                </a>
                <a href="/projects" class="focus-ring inline-flex min-h-12 items-center gap-2 rounded-md border border-cyan-300/25 bg-transparent px-7 text-sm font-bold text-slate-100 transition hover:border-[hsl(var(--accent))] hover:text-[hsl(var(--accent))]">
                    <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    View projects
                </a>
            </div>
        </div>

        <div class="hidden justify-center lg:flex lg:justify-end">
            <div class="relative w-full max-w-[400px]">
                <div class="absolute inset-5 rounded-[28px] bg-[hsl(var(--accent)/0.18)] blur-3xl"></div>
                <div class="relative rounded-[28px] border border-cyan-300/18 bg-[#101827] p-4 shadow-[0_30px_100px_-55px_#22d3ee]">
                    <img src="{{ $site['profile_image'] }}" alt="{{ $site['name'] }} profile photo" class="aspect-[400/494] w-full rounded-[20px] object-cover">
                    <div class="mt-4 flex justify-center">
                        <div class="rounded-full bg-[hsl(var(--accent))] px-4 py-2 text-sm font-bold text-[#07101f] shadow-lg">
                            Available for projects
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="expertise" class="bg-[#0a0f1e] py-16">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <span class="section-label">What I do</span>
        <h2 class="mt-3 text-2xl font-extrabold tracking-normal text-white sm:text-4xl">Areas of Expertise</h2>
        <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach(config('content.home.expertise') as $item)
                <article class="group relative rounded-lg border border-white/10 bg-[#101827] p-5 transition hover:-translate-y-1 hover:border-[hsl(var(--accent)/0.6)]">
                    <div class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-cyan-300/10 text-[hsl(var(--accent))]">
                        @switch($item['icon'])
                            @case('cpu')
                                <svg viewBox="0 0 24 24" class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="16" height="16" x="4" y="4" rx="2"></rect><rect width="6" height="6" x="9" y="9" rx="1"></rect><path d="M15 2v2"></path><path d="M15 20v2"></path><path d="M2 15h2"></path><path d="M2 9h2"></path><path d="M20 15h2"></path><path d="M20 9h2"></path><path d="M9 2v2"></path><path d="M9 20v2"></path></svg>
                                @break
                            @case('timer')
                                <svg viewBox="0 0 24 24" class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="10" x2="14" y1="2" y2="2"></line><line x1="12" x2="15" y1="14" y2="11"></line><circle cx="12" cy="14" r="8"></circle></svg>
                                @break
                            @case('radio')
                                <svg viewBox="0 0 24 24" class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4.9 19.1C1 15.2 1 8.8 4.9 4.9"></path><path d="M7.8 16.2c-2.3-2.3-2.3-6.1 0-8.5"></path><circle cx="12" cy="12" r="2"></circle><path d="M16.2 7.8c2.3 2.3 2.3 6.1 0 8.5"></path><path d="M19.1 4.9C23 8.8 23 15.1 19.1 19"></path></svg>
                                @break
                            @default
                                <svg viewBox="0 0 24 24" class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 3H5a2 2 0 0 0-2 2v3"></path><path d="M16 3h3a2 2 0 0 1 2 2v3"></path><path d="M8 21H5a2 2 0 0 1-2-2v-3"></path><path d="M16 21h3a2 2 0 0 0 2-2v-3"></path></svg>
                        @endswitch
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-white">{{ $item['title'] }}</h3>
                    <p class="mt-2 min-h-20 text-sm leading-6 text-slate-400">{{ $item['description'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-[#0a0f1e] py-14">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4">
            <div>
                <span class="section-label">Portfolio</span>
                <h2 class="mt-3 text-2xl font-extrabold tracking-normal text-white sm:text-4xl">Featured Projects</h2>
            </div>
            <a href="/projects" class="hidden text-sm font-bold text-[hsl(var(--accent))] hover:underline sm:inline">View all projects →</a>
        </div>
        <div class="mt-8 grid gap-5 md:grid-cols-3">
            @foreach($projects as $project)
                @include('partials.project-card', ['project' => $project])
            @endforeach
        </div>
    </div>
</section>

<section class="bg-[#0a0f1e] py-16">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4">
            <div>
                <span class="section-label">From the blog</span>
                <h2 class="mt-3 text-2xl font-extrabold tracking-normal text-white sm:text-4xl">Recent Posts</h2>
            </div>
            <a href="/blog" class="hidden text-sm font-bold text-[hsl(var(--accent))] hover:underline sm:inline">View all →</a>
        </div>
        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            @foreach($posts as $post)
                <a href="/blog/{{ $post['slug'] }}" class="group flex min-h-[330px] flex-col rounded-lg border border-white/10 bg-[#101827] p-7 shadow-soft transition hover:-translate-y-1 hover:border-[hsl(var(--accent)/0.6)]">
                    <div class="text-sm text-slate-500">
                        <time datetime="{{ $post['published_at'] }}">{{ tr_date($post['published_at']) }}</time>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach(tag_items(array_slice($post['tags'], 0, 4)) as $tag)
                                <span class="inline-flex items-center rounded-md border border-[hsl(var(--border))] bg-[hsl(var(--muted))] px-2 py-1 text-xs font-medium text-[hsl(var(--soft))]">#{{ $tag['name'] }}</span>
                            @endforeach
                        </div>
                    </div>
                    <h3 class="mt-7 text-2xl font-bold leading-8 text-white group-hover:text-[hsl(var(--accent))]">{{ $post['title'] }}</h3>
                    <p class="mt-4 line-clamp-4 text-sm leading-7 text-slate-400">{{ $post['summary'] }}</p>
                    <span class="mt-auto pt-7 text-sm font-bold text-[hsl(var(--accent))]">Read more →</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

@endsection
