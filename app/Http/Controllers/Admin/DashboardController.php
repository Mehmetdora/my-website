<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::current();

        return view('admin.dashboard', [
            'admin' => true,
            'noindex' => true,
            'title' => 'Admin Panel',
            'site' => $settings->site,
            'home' => $settings->home,
            'about' => $settings->about,
            'tags' => Tag::query()->orderBy('name')->get()->map->toViewArray()->all(),
            'posts' => Post::query()->with('tags')->latest()->get()->map->toViewArray()->all(),
            'projects' => Project::query()->with('tags')->latest()->get()->map->toViewArray()->all(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'cv_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'delete_cv_pdf' => ['nullable', 'boolean'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'delete_profile_image' => ['nullable', 'boolean'],
        ], [
            'cv_pdf.uploaded' => 'CV PDF sunucuya yüklenemedi. Bu genelde dosya boyutu sunucunun upload limitini aştığında olur; CV dosyası en fazla 10 MB ve PDF formatında olmalı.',
            'cv_pdf.file' => 'CV alanına yüklenen içerik dosya olarak okunamadı. Lütfen tekrar PDF seç.',
            'cv_pdf.mimes' => 'CV dosyası PDF olmalı. DOC, DOCX, JPG veya başka formatlar kabul edilmez.',
            'cv_pdf.max' => 'CV PDF dosyası çok büyük. Maksimum 10 MB yükleyebilirsin.',
            'profile_image.uploaded' => 'Profil fotoğrafı sunucuya yüklenemedi. Bu genelde dosya boyutu sunucunun upload limitini aştığında olur; profil fotoğrafı en fazla 4 MB ve JPG, PNG veya WebP olmalı.',
            'profile_image.image' => 'Profil fotoğrafı geçerli bir görsel dosyası değil.',
            'profile_image.mimes' => 'Profil fotoğrafı JPG, PNG veya WebP formatında olmalı.',
            'profile_image.max' => 'Profil fotoğrafı çok büyük. Maksimum 4 MB yükleyebilirsin.',
            'delete_cv_pdf.boolean' => 'CV silme seçimi geçersiz gönderildi. Sayfayı yenileyip tekrar dene.',
            'delete_profile_image.boolean' => 'Profil fotoğrafı silme seçimi geçersiz gönderildi. Sayfayı yenileyip tekrar dene.',
        ]);

        $settings = SiteSetting::current();
        $site = $settings->site;
        $home = $settings->home;
        $about = $settings->about;

        $site['name'] = $request->string('site_name')->trim()->toString();
        $site['role'] = $request->string('site_role')->trim()->toString();
        $site['location'] = $request->string('site_location')->trim()->toString();
        $site['links']['email'] = 'mailto:'.$request->string('site_email')->trim()->toString();
        $site['links']['github'] = $request->string('github')->trim()->toString();
        $site['links']['linkedin'] = $request->string('linkedin')->trim()->toString();
        $site['links']['telegram'] = $request->string('telegram')->trim()->toString();
        $site['links']['cv'] = '/cv/pdf';

        if ($request->boolean('delete_cv_pdf')) {
            $this->deleteStoredCv($site['cv_pdf_url'] ?? null);
            unset($site['cv_pdf_url'], $site['cv_pdf_name']);
        }

        if ($request->hasFile('cv_pdf')) {
            $this->deleteStoredCv($site['cv_pdf_url'] ?? null);
            $file = $request->file('cv_pdf');
            $path = $file->store('cv', 'public');
            $site['cv_pdf_url'] = Storage::url($path);
            $site['cv_pdf_name'] = $file->getClientOriginalName();
        }

        if ($request->boolean('delete_profile_image')) {
            $this->deleteStoredProfileImage($site['profile_image'] ?? null);
            $site['profile_image'] = config('content.site.profile_image');
        }

        if ($request->hasFile('profile_image')) {
            $this->deleteStoredProfileImage($site['profile_image'] ?? null);
            $file = $request->file('profile_image');
            $path = $file->store('profile', 'public');
            $site['profile_image'] = Storage::url($path);
        }

        $home['top_skills'] = collect($request->input('top_skills', []))
            ->map(fn ($skill) => trim((string) $skill))
            ->filter()
            ->values()
            ->all();
        $home['expertise'] = collect($request->input('expertise', []))
            ->map(fn ($item) => [
                'title' => trim((string) ($item['title'] ?? '')),
                'description' => trim((string) ($item['description'] ?? '')),
                'icon' => $item['icon'] ?? 'code',
            ])
            ->filter(fn ($item) => $item['title'] !== '' || $item['description'] !== '')
            ->values()
            ->all();

        $about['hobbies'] = collect($request->input('hobbies', []))
            ->map(fn ($item) => [
                'title' => trim((string) ($item['title'] ?? '')),
                'description' => trim((string) ($item['description'] ?? '')),
                'icon' => $item['icon'] ?? 'code',
            ])
            ->filter(fn ($item) => $item['title'] !== '' || $item['description'] !== '')
            ->values()
            ->all();
        $about['education'] = collect($request->input('education', []))
            ->map(fn ($item) => [
                'degree' => trim((string) ($item['degree'] ?? '')),
                'period' => trim((string) ($item['period'] ?? '')),
                'org' => trim((string) ($item['org'] ?? '')),
            ])
            ->filter(fn ($item) => $item['degree'] !== '' || $item['org'] !== '')
            ->values()
            ->all();

        $settings->update(compact('site', 'home', 'about'));

        $submittedTagSlugs = [];
        foreach ($request->input('tags', []) as $tag) {
            $name = trim((string) ($tag['name'] ?? ''));
            $slug = Str::slug(strtolower((string) ($tag['slug'] ?? $name)));

            if ($name === '' || $slug === '') {
                continue;
            }

            $submittedTagSlugs[] = $slug;
            Tag::query()->updateOrCreate(['slug' => $slug], ['name' => $name]);
        }

        if ($submittedTagSlugs !== []) {
            Tag::query()->whereNotIn('slug', $submittedTagSlugs)->delete();
        }

        return back()->with('status', 'Genel ayarlar kaydedildi.');
    }

    private function deleteStoredCv(?string $url): void
    {
        if (! is_string($url) || ! str_starts_with($url, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(substr($url, strlen('/storage/')));
    }

    private function deleteStoredProfileImage(?string $url): void
    {
        if (! is_string($url) || ! str_starts_with($url, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(substr($url, strlen('/storage/')));
    }
}
