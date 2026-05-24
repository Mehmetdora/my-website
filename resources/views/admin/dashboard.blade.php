@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#0a0f1e]">
    @include('admin.partials.sidebar', ['active' => 'dashboard'])

    <div class="lg:pl-72">
        <header class="sticky top-0 z-30 border-b border-white/10 bg-[#0a0f1e]/95 px-4 py-4 backdrop-blur sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <span class="section-label">Content Studio</span>
                    <h1 class="mt-1 text-2xl font-extrabold text-white">Genel Ayarlar</h1>
                </div>
                <div class="flex gap-3">
                    <a href="/" class="btn-outline min-h-10 px-4">Siteyi gör</a>
                    <form method="POST" action="{{ route('admin.logout') }}" class="lg:hidden">@csrf<button class="btn-outline min-h-10 px-4">Çıkış</button></form>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="mb-6 rounded-md border border-[#5DF8D8]/35 bg-[#5DF8D8]/10 p-4 text-sm font-semibold text-[#5DF8D8]">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-6 rounded-md border border-red-400/35 bg-red-500/10 p-4 text-sm text-red-100">
                    <p class="font-bold">Kaydedilemeyen alanlar var:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.dashboard.update') }}" class="grid gap-8" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <section id="profile" class="panel p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <span class="section-label">Profile</span>
                            <h2 class="mt-2 text-2xl font-extrabold text-white">Kişisel bilgiler</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">Public sayfalarda görünen meslek, konum ve sosyal bağlantılar burada düzenlenir.</p>
                        </div>
                    </div>
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Ad Soyad<input class="admin-input" name="site_name" value="{{ $site['name'] }}"></label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Meslek / Ünvan<input class="admin-input" name="site_role" value="{{ $site['role'] }}"></label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Konum<input class="admin-input" name="site_location" value="{{ $site['location'] }}"></label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Email<input class="admin-input" name="site_email" value="{{ str_replace('mailto:', '', $site['links']['email'] ?? '') }}"></label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">GitHub URL<input class="admin-input" name="github" value="{{ $site['links']['github'] }}"></label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">LinkedIn URL<input class="admin-input" name="linkedin" value="{{ $site['links']['linkedin'] }}"></label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Telegram URL<input class="admin-input" name="telegram" value="{{ $site['links']['telegram'] }}"></label>
                    </div>

                    <div class="mt-6 rounded-md border border-white/10 bg-white/5 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="font-bold text-white">Profile image</h3>
                                <p class="mt-1 text-sm leading-6 text-slate-400">Ana sayfada görünen profil fotoğrafını buradan yükleyebilirsiniz.</p>
                            </div>
                            @if(!empty($site['profile_image']))
                                <div class="flex items-center gap-4">
                                    <img src="{{ $site['profile_image'] }}" alt="{{ $site['name'] }}" class="h-20 w-20 rounded-full object-cover border border-white/10">
                                </div>
                            @endif
                        </div>
                        <div class="mt-4 grid gap-4 lg:grid-cols-[1fr_auto] lg:items-end">
                            <label class="grid gap-2 text-sm font-semibold text-slate-300">
                                Yeni profil fotoğrafı yükle
                                <input class="admin-input file:mr-4 file:rounded-md file:border-0 file:bg-[#5DF8D8] file:px-4 file:py-2 file:text-sm file:font-black file:text-[#07101f]" type="file" name="profile_image" accept="image/*">
                                <span class="text-xs font-normal text-slate-500">JPG / PNG / WEBP. Maksimum 4 MB.</span>
                                @error('profile_image')
                                    <span class="rounded-md border border-red-400/30 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-100">{{ $message }}</span>
                                @enderror
                            </label>
                            @if(!empty($site['profile_image']) && str_starts_with($site['profile_image'], '/storage/'))
                                <label class="inline-flex min-h-10 items-center gap-2 rounded-md border border-red-400/30 bg-red-500/10 px-4 text-sm font-bold text-red-100">
                                    <input type="checkbox" name="delete_profile_image" value="1" class="h-4 w-4 accent-red-400">
                                    Profil fotoğrafını sil
                                </label>
                            @endif
                        </div>
                        @if(!empty($site['profile_image']) && str_starts_with($site['profile_image'], '/storage/'))
                            <p class="mt-3 text-xs text-slate-500">Yüklü dosya: {{ basename($site['profile_image']) }}</p>
                        @endif
                    </div>

                    <div class="mt-6 rounded-md border border-white/10 bg-white/5 p-4">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <h3 class="font-bold text-white">CV PDF</h3>
                                <p class="mt-1 text-sm leading-6 text-slate-400">PDF yüklediğinde ana sayfadaki CV butonu ve Resume sayfasındaki CV butonu bu dosyayı görüntüler. Tab bardaki Resume linki normal sayfada kalır.</p>
                            </div>
                            @if(!empty($site['cv_pdf_url']))
                                <a href="{{ route('cv.pdf') }}" target="_blank" rel="noopener noreferrer" class="btn-outline min-h-10 px-4">Mevcut CV'yi aç</a>
                            @endif
                        </div>
                        <div class="mt-4 grid gap-4 lg:grid-cols-[1fr_auto] lg:items-end">
                            <label class="grid gap-2 text-sm font-semibold text-slate-300">
                                Yeni CV PDF yükle
                                <input class="admin-input file:mr-4 file:rounded-md file:border-0 file:bg-[#5DF8D8] file:px-4 file:py-2 file:text-sm file:font-black file:text-[#07101f]" type="file" name="cv_pdf" accept="application/pdf">
                                <span class="text-xs font-normal text-slate-500">Sadece PDF, en fazla 10 MB.</span>
                                @error('cv_pdf')
                                    <span class="rounded-md border border-red-400/30 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-100">{{ $message }}</span>
                                @enderror
                            </label>
                            @if(!empty($site['cv_pdf_url']))
                                <label class="inline-flex min-h-10 items-center gap-2 rounded-md border border-red-400/30 bg-red-500/10 px-4 text-sm font-bold text-red-100">
                                    <input type="checkbox" name="delete_cv_pdf" value="1" class="h-4 w-4 accent-red-400">
                                    CV PDF'i sil
                                </label>
                            @endif
                        </div>
                        @if(!empty($site['cv_pdf_name']))
                            <p class="mt-3 text-xs text-slate-500">Yüklü dosya: {{ $site['cv_pdf_name'] }}</p>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-end border-t border-white/10 pt-5">
                        <button class="btn-primary min-h-10 px-5">Kişisel bilgileri kaydet</button>
                    </div>
                </section>

                <section id="home" class="panel p-6">
                    <span class="section-label">Homepage</span>
                    <h2 class="mt-2 text-2xl font-extrabold text-white">Ana sayfa içerikleri</h2>
                    <div class="mt-6 grid gap-6 xl:grid-cols-[0.8fr_1fr]">
                        <div>
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h3 class="font-bold text-white">Top skills</h3>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">Liste mantığı yerine array mantığıyla tek tek eklenip çıkarılır.</p>
                                </div>
                                <button type="button" class="btn-outline min-h-9 px-3 text-xs" data-array-add="top-skills-list">Skill ekle</button>
                            </div>
                            <div id="top-skills-list" class="mt-4 grid gap-3" data-array-list>
                                @foreach($home['top_skills'] as $skill)
                                    <div class="flex gap-2" data-array-row>
                                        <input class="admin-input" name="top_skills[]" value="{{ $skill }}">
                                        <button type="button" class="btn-outline min-h-10 px-3" data-array-remove>Sil</button>
                                    </div>
                                @endforeach
                                <template data-array-template>
                                    <div class="flex gap-2" data-array-row>
                                        <input class="admin-input" name="top_skills[]" value="" placeholder="Örn. STM32">
                                        <button type="button" class="btn-outline min-h-10 px-3" data-array-remove>Sil</button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="rounded-md border border-[#5DF8D8]/20 bg-[#5DF8D8]/8 p-4">
                            <h3 class="font-bold text-white">Öne çıkan projeler</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-400">Ana sayfada gösterilecek projeler artık bu ekrandan seçilmez. Her proje kendi düzenleme sayfasında “Ana sayfada göster / gösterme” alanıyla yönetilir.</p>
                            <a href="{{ route('admin.projects.index') }}" class="btn-outline mt-4 min-h-10 px-4">Proje yönetimine git</a>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="font-bold text-white">Uzmanlık alanlarım</h3>
                        <div class="mt-4 grid gap-4 lg:grid-cols-2">
                            @foreach($home['expertise'] as $index => $item)
                                <div class="rounded-md border border-white/10 bg-white/5 p-4">
                                    <label class="grid gap-2 text-sm font-semibold text-slate-300">Başlık<input class="admin-input" name="expertise[{{ $index }}][title]" value="{{ $item['title'] }}"></label>
                                    <label class="mt-3 grid gap-2 text-sm font-semibold text-slate-300">Açıklama<textarea class="admin-textarea" name="expertise[{{ $index }}][description]">{{ $item['description'] }}</textarea></label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end border-t border-white/10 pt-5">
                        <button class="btn-primary min-h-10 px-5">Ana sayfa içeriklerini kaydet</button>
                    </div>
                </section>

                <section id="hobbies" class="panel p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <span class="section-label">Personal Side</span>
                            <h2 class="mt-2 text-2xl font-extrabold text-white">Hobilerim ve kişisel tarafım</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">About sayfasındaki kişisel metinler ve hobi alanları burada ayrı olarak düzenlenir.</p>
                        </div>
                        <button type="button" class="btn-outline min-h-10 px-4" data-array-add="hobbies-list">Hobi ekle</button>
                    </div>
                    <div id="hobbies-list" class="mt-6 grid gap-4 lg:grid-cols-2" data-array-list>
                        @foreach($about['hobbies'] as $index => $hobby)
                            <div class="rounded-md border border-white/10 bg-white/5 p-4" data-array-row>
                                <div class="flex items-start justify-between gap-3">
                                    <label class="grid flex-1 gap-2 text-sm font-semibold text-slate-300">Başlık<input class="admin-input" name="hobbies[{{ $index }}][title]" value="{{ $hobby['title'] }}"></label>
                                    <button type="button" class="btn-outline mt-6 min-h-10 px-3" data-array-remove>Sil</button>
                                </div>
                                <label class="mt-3 grid gap-2 text-sm font-semibold text-slate-300">Metin<textarea class="admin-textarea" name="hobbies[{{ $index }}][description]">{{ $hobby['description'] }}</textarea></label>
                            </div>
                        @endforeach
                        <template data-array-template>
                            <div class="rounded-md border border-white/10 bg-white/5 p-4" data-array-row>
                                <div class="flex items-start justify-between gap-3">
                                    <label class="grid flex-1 gap-2 text-sm font-semibold text-slate-300">Başlık<input class="admin-input" name="hobbies[__INDEX__][title]" value="" placeholder="Örn. Müzik"></label>
                                    <button type="button" class="btn-outline mt-6 min-h-10 px-3" data-array-remove>Sil</button>
                                </div>
                                <label class="mt-3 grid gap-2 text-sm font-semibold text-slate-300">Metin<textarea class="admin-textarea" name="hobbies[__INDEX__][description]" placeholder="Bu alanın about sayfasında nasıl görüneceğini yaz."></textarea></label>
                            </div>
                        </template>
                    </div>

                    <div class="mt-6 flex justify-end border-t border-white/10 pt-5">
                        <button class="btn-primary min-h-10 px-5">Hobileri kaydet</button>
                    </div>
                </section>

                <section id="education" class="panel p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <span class="section-label">Education</span>
                            <h2 class="mt-2 text-2xl font-extrabold text-white">Education</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">About sayfasındaki eğitim geçmişi bu ayrı kutudan yönetilir.</p>
                        </div>
                        <button type="button" class="btn-outline min-h-10 px-4" data-array-add="education-list">Eğitim ekle</button>
                    </div>
                    <div id="education-list" class="mt-6 grid gap-4" data-array-list>
                        @foreach($about['education'] as $index => $item)
                            <div class="rounded-md border border-white/10 bg-white/5 p-4" data-array-row>
                                <div class="flex items-start justify-between gap-3">
                                    <label class="grid flex-1 gap-2 text-sm font-semibold text-slate-300">Derece / Başlık<input class="admin-input" name="education[{{ $index }}][degree]" value="{{ $item['degree'] }}"></label>
                                    <button type="button" class="btn-outline mt-6 min-h-10 px-3" data-array-remove>Sil</button>
                                </div>
                                <div class="mt-3 grid gap-3 md:grid-cols-[0.45fr_1fr]">
                                    <label class="grid gap-2 text-sm font-semibold text-slate-300">Dönem<input class="admin-input" name="education[{{ $index }}][period]" value="{{ $item['period'] }}"></label>
                                    <label class="grid gap-2 text-sm font-semibold text-slate-300">Kurum / Açıklama<input class="admin-input" name="education[{{ $index }}][org]" value="{{ $item['org'] }}"></label>
                                </div>
                            </div>
                        @endforeach
                        <template data-array-template>
                            <div class="rounded-md border border-white/10 bg-white/5 p-4" data-array-row>
                                <div class="flex items-start justify-between gap-3">
                                    <label class="grid flex-1 gap-2 text-sm font-semibold text-slate-300">Derece / Başlık<input class="admin-input" name="education[__INDEX__][degree]" value="" placeholder="Örn. Computer Engineering"></label>
                                    <button type="button" class="btn-outline mt-6 min-h-10 px-3" data-array-remove>Sil</button>
                                </div>
                                <div class="mt-3 grid gap-3 md:grid-cols-[0.45fr_1fr]">
                                    <label class="grid gap-2 text-sm font-semibold text-slate-300">Dönem<input class="admin-input" name="education[__INDEX__][period]" value="" placeholder="Örn. 2024 - Devam ediyor"></label>
                                    <label class="grid gap-2 text-sm font-semibold text-slate-300">Kurum / Açıklama<input class="admin-input" name="education[__INDEX__][org]" value="" placeholder="Kurum veya açıklama"></label>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-6 flex justify-end border-t border-white/10 pt-5">
                        <button class="btn-primary min-h-10 px-5">Education bilgilerini kaydet</button>
                    </div>
                </section>

                <section id="tags" class="panel p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <span class="section-label">Shared Tags</span>
                            <h2 class="mt-2 text-2xl font-extrabold text-white">Ortak blog/proje tag listesi</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">Blog ve projeler aynı tag listesini kullanır. Slug değerleri küçük harfe çevrilir; public filtreleme de büyük/küçük harf ayırmadan çalışır.</p>
                        </div>
                        <button type="button" class="btn-outline min-h-10 px-4" data-array-add="shared-tags-list">Tag ekle</button>
                    </div>
                    <div id="shared-tags-list" class="mt-6 grid gap-3 md:grid-cols-2 xl:grid-cols-3" data-array-list>
                        @foreach($tags as $index => $tag)
                            <div class="grid gap-3 rounded-md border border-white/10 bg-white/5 p-4 md:grid-cols-[1fr_1fr]" data-array-row>
                                <input class="admin-input" name="tags[{{ $index }}][name]" value="{{ $tag['name'] }}" placeholder="Tag adı">
                                <div class="flex gap-2">
                                    <input class="admin-input" name="tags[{{ $index }}][slug]" value="{{ strtolower($tag['slug']) }}" placeholder="slug" data-lowercase>
                                    <button type="button" class="btn-outline min-h-10 px-3" data-array-remove>Sil</button>
                                </div>
                            </div>
                        @endforeach
                        <template data-array-template>
                            <div class="grid gap-3 rounded-md border border-white/10 bg-white/5 p-4 md:grid-cols-[1fr_1fr]" data-array-row>
                                <input class="admin-input" name="tags[__INDEX__][name]" value="" placeholder="Tag adı">
                                <div class="flex gap-2">
                                    <input class="admin-input" name="tags[__INDEX__][slug]" value="" placeholder="slug" data-lowercase>
                                    <button type="button" class="btn-outline min-h-10 px-3" data-array-remove>Sil</button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-6 flex justify-end border-t border-white/10 pt-5">
                        <button class="btn-primary min-h-10 px-5">Tag listesini kaydet</button>
                    </div>
                </section>
            </form>
        </main>
    </div>
</div>
@endsection
