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
            'cv_pdf.uploaded' => 'The CV PDF could not be uploaded. This usually means the file exceeded the server upload limit; the CV must be a PDF and at most 10 MB.',
            'cv_pdf.file' => 'The uploaded CV could not be read as a file. Please select the PDF again.',
            'cv_pdf.mimes' => 'The CV file must be a PDF. DOC, DOCX, JPG, or other formats are not accepted.',
            'cv_pdf.max' => 'The CV PDF is too large. You can upload at most 10 MB.',
            'profile_image.uploaded' => 'The profile photo could not be uploaded. This usually means the file exceeded the server upload limit; the profile photo must be at most 4 MB and JPG, PNG, or WebP.',
            'profile_image.image' => 'The profile photo is not a valid image file.',
            'profile_image.mimes' => 'The profile photo must be JPG, PNG, or WebP.',
            'profile_image.max' => 'The profile photo is too large. You can upload at most 4 MB.',
            'delete_cv_pdf.boolean' => 'The CV delete option was submitted incorrectly. Please refresh the page and try again.',
            'delete_profile_image.boolean' => 'The profile photo delete option was submitted incorrectly. Please refresh the page and try again.',
        ]);

        $settings = SiteSetting::current();
        $site = $settings->site;
        $home = $settings->home;
        $about = $settings->about;

        $site['name'] = $request->string('site_name')->trim()->toString();
        $site['role'] = $request->string('site_role')->trim()->toString();
        $site['location'] = $request->string('site_location')->trim()->toString();
        $email = $request->string('site_email')->trim()->toString();
        $site['links']['email'] = $email === '' ? '' : 'mailto:'.preg_replace('/^mailto:/i', '', $email);
        $site['links']['github'] = normalize_external_url($request->string('github')->trim()->toString());
        $site['links']['linkedin'] = normalize_external_url($request->string('linkedin')->trim()->toString());
        $site['links']['telegram'] = normalize_telegram_url($request->string('telegram')->trim()->toString());
        $site['links']['cv'] = '/cv/pdf';

        if ($request->boolean('delete_cv_pdf')) {
            $this->deleteStoredCv($site['cv_pdf_url'] ?? null);
            unset($site['cv_pdf_url'], $site['cv_pdf_name']);
        }

        if ($request->hasFile('cv_pdf')) {
            $this->deleteStoredCv($site['cv_pdf_url'] ?? null);
            $file = $request->file('cv_pdf');
            $path = $file->store('cv', 'public');
            $site['cv_pdf_url'] = '/storage/'.ltrim($path, '/');
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
            $site['profile_image'] = '/storage/'.ltrim($path, '/');
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

        $about['stats'] = collect($request->input('about_stats', []))
            ->map(fn ($item) => [
                'value' => trim((string) ($item['value'] ?? '')),
                'label' => trim((string) ($item['label'] ?? '')),
            ])
            ->filter(fn ($item) => $item['value'] !== '' || $item['label'] !== '')
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

        return back()->with('status', 'General settings saved.');
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
