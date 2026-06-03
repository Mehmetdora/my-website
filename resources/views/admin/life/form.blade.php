@extends('layouts.app')

@php($isEdit = $mode === 'edit')
@php($postType = old('post_type', $lifePost->post_type ?? 'image'))

@section('content')
<div class="min-h-screen bg-[#0a0f1e]">
    @include('admin.partials.sidebar', ['active' => $isEdit ? 'life' : 'life-create'])

    <div class="lg:pl-72">
        <header class="sticky top-[65px] z-30 border-b border-white/10 bg-[#0a0f1e]/95 px-4 py-4 backdrop-blur sm:px-6 lg:top-0 lg:px-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <span class="section-label">My Life Editor</span>
                    <h1 class="mt-1 text-2xl font-extrabold text-white">{{ $isEdit ? 'Edit Life Post' : 'New Life Post' }}</h1>
                </div>
                <a href="{{ route('admin.life.index') }}" class="btn-outline min-h-10 px-4">My Life</a>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="mb-6 rounded-md border border-[#5DF8D8]/35 bg-[#5DF8D8]/10 p-4 text-sm font-semibold text-[#5DF8D8]">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-6 rounded-md border border-red-400/35 bg-red-500/10 p-4 text-sm text-red-100">
                    <p class="font-bold">Post could not be saved:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $isEdit ? route('admin.life.update', $lifePost) : route('admin.life.store') }}" class="grid gap-8" enctype="multipart/form-data">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <section class="panel p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-extrabold text-white">Post Information</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">Choose a photo post for one or more images, or a record post for a single audio file. A post cannot contain photos and audio at the same time.</p>
                        </div>
                        <button class="btn-primary min-h-10 px-4">{{ $isEdit ? 'Save Post' : 'Create Post' }}</button>
                    </div>

                    <div class="mt-6 grid gap-4 lg:grid-cols-2">
                        <div class="grid gap-3 lg:col-span-2" data-life-post-type>
                            <span class="text-sm font-semibold text-slate-300">Post type</span>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-white/10 bg-white/5 p-4 transition has-[:checked]:border-[#5DF8D8] has-[:checked]:bg-[#5DF8D8]/10">
                                    <input type="radio" name="post_type" value="image" class="mt-1 h-4 w-4 accent-[#5DF8D8]" data-life-post-type-option @checked($postType === 'image')>
                                    <span>
                                        <span class="block font-bold text-white">Photo post</span>
                                        <span class="mt-1 block text-sm font-normal leading-6 text-slate-400">Share one or more photos with a description.</span>
                                    </span>
                                </label>
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-white/10 bg-white/5 p-4 transition has-[:checked]:border-[#5DF8D8] has-[:checked]:bg-[#5DF8D8]/10">
                                    <input type="radio" name="post_type" value="audio" class="mt-1 h-4 w-4 accent-[#5DF8D8]" data-life-post-type-option @checked($postType === 'audio')>
                                    <span>
                                        <span class="block font-bold text-white">Record post</span>
                                        <span class="mt-1 block text-sm font-normal leading-6 text-slate-400">Share one record with a description.</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300 lg:col-span-2">Description<textarea class="admin-textarea min-h-32" name="excerpt" placeholder="Post description">{{ old('excerpt', $lifePost->excerpt ?? '') }}</textarea></label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Location<input class="admin-input" name="location" value="{{ old('location', $lifePost->location ?? '') }}" placeholder="Istanbul"></label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Date<input class="admin-input" type="date" name="published_at" value="{{ old('published_at', optional($lifePost?->published_at)->toDateString() ?? now()->toDateString()) }}"></label>
                        <label class="grid gap-2 text-sm font-semibold text-slate-300">Visibility
                            <select class="admin-input" name="visibility">
                                @foreach(['public' => 'Public', 'hidden' => 'Hidden', 'private' => 'Private'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('visibility', $lifePost->visibility ?? 'public') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                </section>

                <section class="panel p-6 {{ $postType === 'image' ? '' : 'hidden' }}" data-life-media-panel="image">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <span class="section-label">Images</span>
                            <h2 class="mt-2 text-xl font-extrabold text-white">Photos</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-400">Upload photos directly without entering URLs and crop them in a horizontal 2:1 ratio. The first photo is used as the cover; posts are sorted by date.</p>
                        </div>
                    </div>

                    @if($isEdit && ($lifePost?->images ?? collect())->isNotEmpty())
                        <div class="mt-6">
                            <h3 class="text-sm font-bold text-white">Current photos</h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">Uncheck the photos you want to remove.</p>
                            <div class="mt-3 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach($lifePost->images as $image)
                                    <label class="group overflow-hidden rounded-md border border-white/10 bg-white/5">
                                        <img src="{{ $image->url }}" alt="{{ $image->alt }}" class="aspect-[2/1] w-full object-cover">
                                        <span class="flex items-center justify-between gap-3 p-3 text-sm font-semibold text-slate-300">
                                            <span>Keep photo</span>
                                            <input class="h-4 w-4 accent-[#5DF8D8]" type="checkbox" name="keep_image_ids[]" value="{{ $image->id }}" checked>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 grid gap-3 text-sm font-semibold text-slate-300" data-image-cropper data-aspect-width="2" data-aspect-height="1" data-cropper-mode="multiple">
                        Upload new photos
                        <input type="hidden" name="new_image_crops" data-cropper-output>
                        <input class="admin-input file:mr-4 file:rounded-md file:border-0 file:bg-[#5DF8D8] file:px-4 file:py-2 file:text-sm file:font-black file:text-[#07101f]" type="file" name="new_images[]" accept="image/png,image/jpeg,image/gif,image/webp" multiple data-cropper-input>
                        <span class="text-xs font-normal text-slate-500">PNG, JPG, GIF, or WebP. Each image can be up to 4 MB.</span>

                        <div class="cropper-panel hidden" data-cropper-panel>
                            <div class="cropper-stage" data-cropper-stage>
                                <img alt="Photo crop preview" data-cropper-image>
                                <div class="cropper-box" data-cropper-box></div>
                            </div>
                            <div class="mt-3 grid gap-3">
                                <label class="grid gap-2 text-xs font-bold text-slate-400">Crop size
                                    <input type="range" min="30" max="100" value="100" data-cropper-zoom class="accent-[#5DF8D8]">
                                </label>
                                <div class="flex flex-wrap gap-2" data-cropper-thumbs></div>
                            </div>
                        </div>

                        @error('new_images')
                            <span class="rounded-md border border-red-400/30 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-100">{{ $message }}</span>
                        @enderror
                        @error('new_image_crops')
                            <span class="rounded-md border border-red-400/30 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-100">{{ $message }}</span>
                        @enderror
                        @foreach($errors->get('new_images.*') as $fieldErrors)
                            @foreach($fieldErrors as $message)
                                <span class="rounded-md border border-red-400/30 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-100">{{ $message }}</span>
                            @endforeach
                        @endforeach
                    </div>
                </section>

                <section class="panel p-6 {{ $postType === 'audio' ? '' : 'hidden' }}" data-life-media-panel="audio">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <span class="section-label">Audio</span>
                            <h2 class="mt-2 text-xl font-extrabold text-white">Records</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-400">Upload one MP3, WAV, OGG, M4A, AAC, or WebM audio file. Audio posts use the same description, location, and date fields.</p>
                        </div>
                    </div>

                    @if($isEdit && !empty($lifePost?->audio_url))
                        <div class="mt-6 rounded-lg border border-white/10 bg-white/5 p-4">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <h3 class="font-bold text-white">Current audio</h3>
                                    <p class="mt-1 text-sm leading-6 text-slate-400">{{ $lifePost->audio_name ?: basename($lifePost->audio_url) }}</p>
                                </div>
                                <label class="inline-flex min-h-10 items-center gap-2 rounded-md border border-red-400/30 bg-red-500/10 px-4 text-sm font-bold text-red-100">
                                    <input type="checkbox" name="delete_audio" value="1" class="h-4 w-4 accent-red-400">
                                    Delete current audio
                                </label>
                            </div>
                            <audio controls preload="metadata" src="{{ $lifePost->audio_url }}" class="mt-4 w-full"></audio>
                        </div>
                    @endif

                    <label class="mt-6 grid gap-3 text-sm font-semibold text-slate-300">
                        Upload audio file
                        <input class="admin-input file:mr-4 file:rounded-md file:border-0 file:bg-[#5DF8D8] file:px-4 file:py-2 file:text-sm file:font-black file:text-[#07101f]" type="file" name="new_audio" accept=".mp3,.wav,.ogg,.m4a,.aac,.webm,audio/mpeg,audio/wav,audio/ogg,audio/mp4,audio/aac,audio/webm,video/webm">
                        <span class="text-xs font-normal text-slate-500">MP3, WAV, OGG, M4A, AAC, or WebM. Maximum 20 MB.</span>
                        @error('new_audio')
                            <span class="rounded-md border border-red-400/30 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-100">{{ $message }}</span>
                        @enderror
                    </label>
                </section>
            </form>
        </main>
    </div>
</div>
@endsection
