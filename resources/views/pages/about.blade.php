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
                    Embedded sistemler, low-level programming ve proje tabanlı öğrenme etrafında kendini geliştiren bir bilgisayar mühendisliği öğrencisiyim. STM32, ESP32, C/C++, IoT ve elektronik üzerine çalışıyor; öğrendiklerimi yazılar, kod parçaları, proje günlükleri ve kişisel paylaşımlar halinde düzenli bir arşive dönüştürmeyi seviyorum.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                @foreach(config('content.about.stats') as $stat)
                    <div class="rounded-lg border border-white/10 bg-[#101827] p-5">
                        <span class="block text-3xl font-extrabold text-[hsl(var(--accent))]">{{ $stat['value'] }}</span>
                        <span class="mt-2 block text-sm font-semibold text-slate-400">{{ $stat['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </header>

        <div class="mt-16 space-y-12">
            <section class="grid gap-5 border-t border-white/10 pt-8 lg:grid-cols-[290px_minmax(0,1fr)]">
                <h2 class="text-2xl font-extrabold text-white">Bu Site Neden Var?</h2>
                <div class="space-y-5 text-base leading-8 text-slate-400">
                    <p>Bu site benim için hem kişisel bir vitrin hem de düzenli bir mühendislik defteri. Blog kısmında teknik yazılar, projelerde geliştirdiğim uygulamalar ve My Life tarafında daha kişisel kayıtlar yer alacak.</p>
                    <p>Hedefim zamanla sadece sonuçları değil, öğrenme sürecini de görünür kılmak: karşılaştığım hatalar, denediğim çözümler, donanım seçimleri ve proje kararları bu arşivin parçası olacak.</p>
                </div>
            </section>
        </div>

        <section class="mt-16 grid gap-5 border-t border-white/10 pt-8 lg:grid-cols-[290px_minmax(0,1fr)]">
            <h2 class="text-2xl font-extrabold text-white">Hobilerim ve Kişisel Tarafım</h2>
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

            <aside class="rounded-lg border border-white/10 bg-[#101827] p-6">
                <h2 class="text-2xl font-extrabold text-white">Kısa Profil</h2>
                <div class="mt-5 space-y-4 text-sm leading-7 text-slate-400">
                    <p class="flex gap-3">
                        <svg viewBox="0 0 24 24" class="mt-1 h-[18px] w-[18px] shrink-0 text-[hsl(var(--accent))]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        İstanbul merkezli, embedded sistemler ve teknik içerik üretimi odaklı kişisel çalışma alanı.
                    </p>
                    <p class="flex gap-3">
                        <svg viewBox="0 0 24 24" class="mt-1 h-[18px] w-[18px] shrink-0 text-[hsl(var(--accent))]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13.997 4a2 2 0 0 1 1.76 1.05l.486.9A2 2 0 0 0 18.003 7H20a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.997a2 2 0 0 0 1.76-1.05l.486-.9A2 2 0 0 1 10.003 4z"></path><circle cx="12" cy="13" r="3"></circle></svg>
                        Projeleri yalnızca kod olarak değil; görsel, deneyim ve sonuçlarıyla birlikte belgelemeyi önemsiyorum.
                    </p>
                    <p class="flex gap-3">
                        <svg viewBox="0 0 24 24" class="mt-1 h-[18px] w-[18px] shrink-0 text-[hsl(var(--accent))]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="6" x2="10" y1="12" y2="12"></line><line x1="8" x2="8" y1="10" y2="14"></line><line x1="15" x2="15.01" y1="13" y2="13"></line><line x1="18" x2="18.01" y1="11" y2="11"></line><rect width="20" height="12" x="2" y="6" rx="2"></rect></svg>
                        Teknik üretimin dışında zihni dinlendiren yaratıcı ve keyifli uğraşlara da alan açıyorum.
                    </p>
                </div>
            </aside>
        </section>

        <div class="mt-16 flex flex-wrap gap-4">
            <a href="/projects" class="focus-ring inline-flex min-h-12 items-center rounded-md bg-[hsl(var(--accent))] px-7 text-sm font-bold text-[#07101f]">
                Projeleri Gör
            </a>
            <a href="/cv" class="focus-ring inline-flex min-h-12 items-center rounded-md border border-cyan-300/25 px-7 text-sm font-bold text-slate-100 hover:border-[hsl(var(--accent))] hover:text-[hsl(var(--accent))]">
                Resume Sayfası
            </a>
        </div>
    </div>
</div>
@endsection
