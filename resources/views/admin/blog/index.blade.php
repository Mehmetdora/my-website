@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#0a0f1e]">
    @include('admin.partials.sidebar', ['active' => 'blog'])

    <div class="lg:pl-72">
        <header class="sticky top-0 z-30 border-b border-white/10 bg-[#0a0f1e]/95 px-4 py-4 backdrop-blur sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <span class="section-label">Blog</span>
                    <h1 class="mt-1 text-2xl font-extrabold text-white">Blog Yönetimi</h1>
                </div>
                <a href="{{ route('admin.blog.create') }}" class="btn-primary min-h-10 px-4">Yeni blog</a>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="mb-6 rounded-md border border-[#5DF8D8]/35 bg-[#5DF8D8]/10 p-4 text-sm font-semibold text-[#5DF8D8]">{{ session('status') }}</div>
            @endif

            <section class="panel overflow-hidden">
                <div class="border-b border-white/10 p-6">
                    <h2 class="text-xl font-extrabold text-white">Tüm blog yazıları</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-400">Kategori sistemi kaldırıldı; bloglar sadece ortak tag listesiyle etiketlenir.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[820px] text-left text-sm">
                        <thead class="bg-white/[0.03] text-xs uppercase tracking-[0.12em] text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Blog</th>
                                <th class="px-6 py-4">Ortak tagler</th>
                                <th class="px-6 py-4">Durum</th>
                                <th class="px-6 py-4">Görünürlük</th>
                                <th class="px-6 py-4">Yayın tarihi</th>
                                <th class="px-6 py-4 text-right">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach($posts as $post)
                                <tr class="text-slate-300">
                                    <td class="px-6 py-5">
                                        <div class="font-bold text-white">{{ $post['title'] }}</div>
                                        <div class="mt-1 text-xs text-slate-500">{{ $post['slug'] }}</div>
                                    </td>
                                    <td class="px-6 py-5">@include('partials.tag-list', ['slugs' => $post['tags'], 'linked' => false])</td>
                                    <td class="px-6 py-5">{{ $post['status'] }}</td>
                                    <td class="px-6 py-5">{{ $post['visibility'] }}</td>
                                    <td class="px-6 py-5">{{ $post['published_at'] ?? '-' }}</td>
                                    <td class="px-6 py-5 text-right">
                                        <a href="{{ route('admin.blog.edit', $post['slug']) }}" class="btn-outline min-h-10 px-4">Düzenle</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</div>
@endsection
