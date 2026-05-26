@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#0a0f1e]">
    @include('admin.partials.sidebar', ['active' => 'life'])

    <div class="lg:pl-72">
        <header class="sticky top-[65px] z-30 border-b border-white/10 bg-[#0a0f1e]/95 px-4 py-4 backdrop-blur sm:px-6 lg:top-0 lg:px-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <span class="section-label">My Life</span>
                    <h1 class="mt-1 text-2xl font-extrabold text-white">Life Posts</h1>
                </div>
                <a href="{{ route('admin.life.create') }}" class="btn-primary min-h-10 px-4">New post</a>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="mb-6 rounded-md border border-[#5DF8D8]/35 bg-[#5DF8D8]/10 p-4 text-sm font-semibold text-[#5DF8D8]">{{ session('status') }}</div>
            @endif

            <section class="panel overflow-hidden">
                <div class="border-b border-white/10 p-6">
                    <h2 class="text-xl font-extrabold text-white">All personal posts</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-400">Description, location, date, and multiple photos are managed here.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left text-sm">
                        <thead class="bg-white/[0.03] text-xs uppercase tracking-[0.12em] text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Description</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Location</th>
                                <th class="px-6 py-4">Visibility</th>
                                <th class="px-6 py-4">Photo</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach($lifePosts as $lifePost)
                                <tr class="text-slate-300">
                                    <td class="px-6 py-5"><div class="line-clamp-2 max-w-lg text-white">{{ $lifePost->excerpt }}</div></td>
                                    <td class="px-6 py-5">{{ optional($lifePost->published_at)->toDateString() }}</td>
                                    <td class="px-6 py-5">{{ $lifePost->location ?: '-' }}</td>
                                    <td class="px-6 py-5">{{ $lifePost->visibility }}</td>
                                    <td class="px-6 py-5">{{ $lifePost->images_count ?? $lifePost->images->count() }}</td>
                                    <td class="px-6 py-5">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.life.edit', $lifePost) }}" class="btn-outline min-h-10 px-4">Edit</a>
                                            <form method="POST" action="{{ route('admin.life.destroy', $lifePost) }}" onsubmit="return confirm('Are you sure you want to delete this Life post? Its photos will also be deleted. This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="min-h-10 rounded-md border border-red-400/35 bg-red-500/10 px-4 text-sm font-bold text-red-100 transition hover:border-red-300 hover:bg-red-500/20">Delete</button>
                                            </form>
                                        </div>
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
