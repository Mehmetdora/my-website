@extends('layouts.app')

@php
    $isEdit = $mode === 'edit';
    $selectedTags = array_map(fn ($tag) => strtolower((string) $tag), $project['tags'] ?? []);
    $technologies = $project['technologies'] ?? [];
    $contentBlocks = $project['content'] ?? [];
    $initialContentHtml = old('content_html', $project['content_html'] ?? '');

    if ($initialContentHtml === '') {
        $initialContentHtml = collect($contentBlocks)->map(function (array $block): string {
        if (($block['type'] ?? '') === 'heading') {
            return '<h2>'.e($block['text'] ?? '').'</h2>';
        }

        if (($block['type'] ?? '') === 'list') {
            $items = collect($block['items'] ?? [])->map(fn ($item): string => '<li>'.e($item).'</li>')->implode('');

            return '<ul>'.$items.'</ul>';
        }

        return '<p>'.e($block['text'] ?? 'Proje amacını, devre/kod yapısını, karşılaştığın sorunları ve sonucu burada anlat.').'</p>';
        })->implode('');
    }

    if ($initialContentHtml === '') {
        $initialContentHtml = '<h2>Proje amacı</h2><p>Bu projede neyi çözmek veya öğrenmek istediğini yaz.</p><h2>Teknik detaylar</h2><p>Donanım, yazılım akışı, haberleşme protokolleri ve mimari kararları burada anlat.</p><pre>// Örnek kod bloğu</pre>';
    }
@endphp

@section('content')
<div class="min-h-screen bg-[#0a0f1e]">
    @include('admin.partials.sidebar', ['active' => $isEdit ? 'projects' : 'project-create'])

    <div class="lg:pl-72">
        <header class="sticky top-0 z-30 border-b border-white/10 bg-[#0a0f1e]/95 px-4 py-4 backdrop-blur sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <span class="section-label">Project Editor</span>
                    <h1 class="mt-1 text-2xl font-extrabold text-white">{{ $isEdit ? 'Proje Düzenle' : 'Yeni Proje Oluştur' }}</h1>
                </div>
                <a href="{{ route('admin.projects.index') }}" class="btn-outline min-h-10 px-4">Projeler</a>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="mb-6 rounded-md border border-[#5DF8D8]/35 bg-[#5DF8D8]/10 p-4 text-sm font-semibold text-[#5DF8D8]">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-6 rounded-md border border-red-400/35 bg-red-500/10 p-4 text-sm font-semibold text-red-100">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ $isEdit ? route('admin.projects.update', $project['slug']) : route('admin.projects.store') }}" class="grid gap-8" data-rich-editor-form>
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <section class="panel p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-extrabold text-white">Proje bilgileri</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">Başlık, durum, tagler, teknolojiler ve ana sayfa görünürlüğü tek yerde. Ekran sade kalsın diye alanlar iki ana kutuda toplandı.</p>
                        </div>
                        <button class="btn-primary min-h-10 px-4">{{ $isEdit ? 'Projeyi kaydet' : 'Projeyi oluştur' }}</button>
                    </div>

                    <div class="mt-6 grid gap-4 lg:grid-cols-2">
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Proje adı<input class="admin-input" name="title" value="{{ $project['title'] ?? '' }}" placeholder="STM32 Sensor Dashboard"></label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Slug<input class="admin-input" name="slug" value="{{ $project['slug'] ?? '' }}" placeholder="stm32-sensor-dashboard" data-lowercase></label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300 lg:col-span-2">Kısa açıklama<textarea class="admin-textarea min-h-24" name="summary" placeholder="Projenin public listelerde görünecek kısa özeti">{{ $project['summary'] ?? '' }}</textarea></label>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Durum
                            <select class="admin-input" name="status">
                                @foreach(['planned' => 'Planlandı', 'in-progress' => 'Devam ediyor', 'completed' => 'Tamamlandı', 'archived' => 'Arşivlendi'] as $value => $label)
                                    <option value="{{ $value }}" @selected(($project['status'] ?? 'planned') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Görünürlük
                            <select class="admin-input" name="visibility">
                                @foreach(['public' => 'Public', 'hidden' => 'Hidden', 'private' => 'Private'] as $value => $label)
                                    <option value="{{ $value }}" @selected(($project['visibility'] ?? 'public') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Ana sayfa
                            <select class="admin-input" name="featured">
                                <option value="1" @selected($project['featured'] ?? false)>Ana sayfada göster</option>
                                <option value="0" @selected(!($project['featured'] ?? false))>Gösterme</option>
                            </select>
                        </label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">GitHub URL<input class="admin-input" name="github" value="{{ $project['github'] ?? '' }}" placeholder="https://github.com/..."></label>
                    </div>

                    <div class="mt-6 grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                        <div data-tag-picker>
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-sm font-bold text-white">Ortak tag seçimi</h3>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">Blog ve projeler aynı tag listesini kullanır. Tag seçmek için üstüne tıkla.</p>
                                </div>
                                <span class="rounded-md border border-white/10 bg-white/5 px-2 py-1 text-xs font-bold text-slate-400">Çoklu seçim</span>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2" data-tag-options>
                                @foreach($tags as $tag)
                                    @php($isSelected = in_array(strtolower($tag['slug']), $selectedTags, true))
                                    <button type="button" value="{{ strtolower($tag['slug']) }}" data-tag-option aria-pressed="{{ $isSelected ? 'true' : 'false' }}" class="rounded-md border px-3 py-2 text-sm font-bold transition {{ $isSelected ? 'border-[#5DF8D8] bg-[#5DF8D8]/12 text-[#5DF8D8]' : 'border-white/10 bg-white/5 text-slate-300 hover:border-[#5DF8D8]/60 hover:text-[#5DF8D8]' }}">#{{ $tag['name'] }}</button>
                                @endforeach
                            </div>
                            <div data-tag-hidden>
                                @foreach($selectedTags as $tag)
                                    <input type="hidden" name="tags[]" value="{{ $tag }}">
                                @endforeach
                            </div>
                        </div>

                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Teknolojiler
                            <textarea class="admin-textarea min-h-40" name="technologies" placeholder="Her satıra bir teknoloji veya donanım yaz">{{ implode("\n", $technologies) }}</textarea>
                            <span class="text-xs font-normal text-slate-500">Örn. STM32, UART, DMA, STM32CubeIDE, logic analyzer.</span>
                        </label>
                    </div>
                </section>

                <section class="panel overflow-hidden">
                    <div class="border-b border-white/10 p-6">
                        <span class="section-label">Quill JS Editor</span>
                        <h2 class="mt-2 text-xl font-extrabold text-white">Detay içeriği</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">Quill JS ile başlık, font, boyut, renk, link, video, kod bloğu, liste, hizalama ve resim dosyası ekleme desteklenir. Resimler güvenli upload endpoint'i üzerinden kaydedilir.</p>
                    </div>

                    <div class="p-6">
                        <input id="project-content-html" type="hidden" name="content_html" value="{{ $initialContentHtml }}">
                        <div id="project-content-editor" class="admin-quill-editor" data-quill-editor data-quill-input="project-content-html" data-placeholder="Proje amacı, devre/kod yapısı, karşılaşılan sorunlar, çözüm ve sonuçları yaz."></div>
                    </div>
                </section>
            </form>
        </main>
    </div>
</div>
@endsection
