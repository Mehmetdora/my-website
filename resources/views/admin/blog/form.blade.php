@extends('layouts.app')

@php
    $isEdit = $mode === 'edit';
    $selectedTags = array_map(fn ($tag) => strtolower((string) $tag), $post['tags'] ?? []);
    $contentBlocks = $post['content'] ?? [];
    $initialContentHtml = old('content_html', $post['content_html'] ?? '');
    $hasStoredCover = $isEdit && !empty($post['cover']['url'] ?? null) && ($post['cover']['url'] ?? '') !== '/profile.svg';

    if ($initialContentHtml === '') {
        $initialContentHtml = collect($contentBlocks)->map(function (array $block): string {
        if (($block['type'] ?? '') === 'heading') {
            return '<h2>'.e($block['text'] ?? '').'</h2>';
        }

        if (($block['type'] ?? '') === 'quote') {
            return '<blockquote>'.e($block['text'] ?? '').'</blockquote>';
        }

        if (($block['type'] ?? '') === 'code') {
            return '<pre>'.e($block['code'] ?? '').'</pre>';
        }

        if (($block['type'] ?? '') === 'list') {
            $items = collect($block['items'] ?? [])->map(fn ($item): string => '<li>'.e($item).'</li>')->implode('');

            return '<ul>'.$items.'</ul>';
        }

        return '<p>'.e($block['text'] ?? 'Blog yazısının detaylarını burada anlat.').'</p>';
        })->implode('');
    }

    if ($initialContentHtml === '') {
        $initialContentHtml = '<h2>Giriş</h2><p>Bu yazıda neyi anlattığını kısaca yaz.</p><h2>Teknik detaylar</h2><p>Kod, notlar, bağlantılar ve öğrendiğin noktaları burada toparla.</p><pre>// Örnek kod bloğu</pre>';
    }
@endphp

@section('content')
<div class="min-h-screen bg-[#0a0f1e]">
    @include('admin.partials.sidebar', ['active' => $isEdit ? 'blog' : 'blog-create'])

    <div class="lg:pl-72">
        <header class="sticky top-0 z-30 border-b border-white/10 bg-[#0a0f1e]/95 px-4 py-4 backdrop-blur sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <span class="section-label">Blog Editor</span>
                    <h1 class="mt-1 text-2xl font-extrabold text-white">{{ $isEdit ? 'Blog Düzenle' : 'Yeni Blog Oluştur' }}</h1>
                </div>
                <a href="{{ route('admin.blog.index') }}" class="btn-outline min-h-10 px-4">Bloglar</a>
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

            <form method="POST" action="{{ $isEdit ? route('admin.blog.update', $post['slug']) : route('admin.blog.store') }}" class="grid gap-8" enctype="multipart/form-data">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif
                <section class="panel p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-extrabold text-white">Blog bilgileri</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">Başlık, yayın durumu, tarih, ortak tagler ve kapak görseli tek yerde. Bloglarda kategori yok; en az bir ortak tag seçilmesi yeterli.</p>
                        </div>
                        <button class="btn-primary min-h-10 px-4">{{ $isEdit ? 'Blogu kaydet' : 'Blogu oluştur' }}</button>
                    </div>

                    <div class="mt-6 grid gap-4 lg:grid-cols-2">
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Başlık<input class="admin-input" name="title" value="{{ $post['title'] ?? '' }}" placeholder="STM32 UART DMA Receive Mantığını Kurmak"></label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Slug<input class="admin-input" name="slug" value="{{ $post['slug'] ?? '' }}" placeholder="stm32-uart-dma-receive" data-lowercase></label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300 lg:col-span-2">Özet<textarea class="admin-textarea min-h-24" name="summary" placeholder="Yazının public listelerde görünecek kısa özeti">{{ $post['summary'] ?? '' }}</textarea></label>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Durum
                            <select class="admin-input" name="status">
                                @foreach(['draft' => 'Taslak', 'published' => 'Yayında', 'archived' => 'Arşivlendi'] as $value => $label)
                                    <option value="{{ $value }}" @selected(($post['status'] ?? 'draft') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Görünürlük
                            <select class="admin-input" name="visibility">
                                @foreach(['public' => 'Public', 'hidden' => 'Hidden', 'private' => 'Private'] as $value => $label)
                                    <option value="{{ $value }}" @selected(($post['visibility'] ?? 'public') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Yayın tarihi<input class="admin-input" type="date" name="published_at" value="{{ $post['published_at'] ?? now()->toDateString() }}"></label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Okuma süresi<input class="admin-input" type="number" min="1" name="reading_time" value="{{ $post['reading_time'] ?? 4 }}"></label>
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

                        <div class="grid gap-3 text-sm font-semibold text-slate-300" data-image-cropper data-aspect-width="2" data-aspect-height="1" data-cropper-mode="single">
                            <div>
                                <h3 class="text-sm font-bold text-white">Kapak görseli</h3>
                                <p class="mt-1 text-xs leading-5 text-slate-500">Görseli seçtikten sonra 2:1 oranında yatay kadrajla. Kaydedilen görsel public blog sayfasında kullanılır.</p>
                            </div>

                            @if($hasStoredCover)
                                <div class="overflow-hidden rounded-md border border-white/10 bg-white/5">
                                    <img src="{{ $post['cover']['url'] }}" alt="{{ $post['cover']['alt'] }}" class="max-h-64 w-full object-cover">
                                    <label class="flex items-center justify-between gap-3 border-t border-white/10 p-3 text-xs font-semibold text-slate-300">
                                        <span>Mevcut kapak görselini sil</span>
                                        <input type="checkbox" name="delete_cover_image" value="1" class="h-4 w-4 accent-red-400">
                                    </label>
                                </div>
                            @endif

                            <input type="hidden" name="cover_crop" data-cropper-output>
                            <input class="admin-input file:mr-4 file:rounded-md file:border-0 file:bg-[#5DF8D8] file:px-4 file:py-2 file:text-sm file:font-black file:text-[#07101f]" type="file" name="cover_image" accept="image/png,image/jpeg,image/gif,image/webp" data-cropper-input>
                            <span class="text-xs font-normal text-slate-500">PNG, JPG, GIF veya WebP. En fazla 4 MB.</span>

                            <div class="cropper-panel hidden" data-cropper-panel>
                                <div class="cropper-stage" data-cropper-stage>
                                    <img alt="Kapak görseli kırpma önizlemesi" data-cropper-image>
                                    <div class="cropper-box" data-cropper-box></div>
                                </div>
                                <div class="mt-3 grid gap-3">
                                    <label class="grid gap-2 text-xs font-bold text-slate-400">Kadraj boyutu
                                        <input type="range" min="30" max="100" value="100" data-cropper-zoom class="accent-[#5DF8D8]">
                                    </label>
                                    <div class="flex flex-wrap gap-2" data-cropper-thumbs></div>
                                </div>
                            </div>

                            @error('cover_image')
                                <span class="rounded-md border border-red-400/30 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-100">{{ $message }}</span>
                            @enderror
                            @error('cover_crop')
                                <span class="rounded-md border border-red-400/30 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-100">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </section>

                <section class="panel overflow-hidden">
                    <div class="border-b border-white/10 p-6">
                        <span class="section-label">Quill JS Editor</span>
                        <h2 class="mt-2 text-xl font-extrabold text-white">Blog açıklaması / içeriği</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">Quill JS ile başlık, font, boyut, renk, link, video, kod bloğu, liste, hizalama ve resim dosyası ekleme desteklenir. Resimler güvenli upload endpoint'i üzerinden kaydedilir.</p>
                    </div>

                    <div class="p-6">
                        <input id="blog-content-html" type="hidden" name="content_html" value="{{ $initialContentHtml }}">
                        <div id="blog-content-editor" class="admin-quill-editor" data-quill-editor data-quill-input="blog-content-html" data-placeholder="Blog yazını, kod örneklerini, bağlantıları ve görselleri burada oluştur."></div>
                    </div>
                </section>
            </form>
        </main>
    </div>
</div>
@endsection
