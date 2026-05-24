<?php

namespace Tests\Feature;

use App\Models\LifePost;
use App\Models\Post;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminBackendTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_uses_session_auth_and_protects_dashboard(): void
    {
        $this->seed();

        $this->get('/admin')->assertRedirect('/admin/login');

        $this->post('/admin/login', [
            'email' => 'admin@personal-website.local',
            'password' => 'password',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs(User::query()->where('email', 'admin@personal-website.local')->first());
    }

    public function test_non_allowlisted_authenticated_user_cannot_access_admin(): void
    {
        $this->seed();

        $user = User::query()->create([
            'name' => 'Regular User',
            'email' => 'regular@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();

        $this->assertGuest();
    }

    public function test_admin_session_expires_after_idle_timeout(): void
    {
        $this->seed();
        config(['admin_security.idle_timeout_minutes' => 1]);
        $this->actingAs(User::query()->where('email', 'admin@personal-website.local')->first());

        session(['admin_last_activity' => now()->subMinutes(2)->timestamp]);

        $this->get('/admin')
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_admin_pages_send_no_store_and_hardening_headers(): void
    {
        $this->seed();
        $this->actingAs(User::query()->where('email', 'admin@personal-website.local')->first());

        $response = $this->get('/admin')->assertOk();

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $response
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'no-referrer');
        $this->assertStringContainsString("frame-ancestors 'none'", $response->headers->get('Content-Security-Policy'));
    }

    public function test_login_is_rate_limited(): void
    {
        $this->seed();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', [
                'email' => 'admin@personal-website.local',
                'password' => 'wrong-password',
            ])->assertRedirect();
        }

        $this->post('/admin/login', [
            'email' => 'admin@personal-website.local',
            'password' => 'wrong-password',
        ])->assertTooManyRequests();
    }

    public function test_admin_can_create_blog_with_shared_tags_and_sanitized_quill_html(): void
    {
        $this->seed();
        $this->actingAs(User::query()->first());

        $this->post('/admin/blog', [
            'title' => 'Backend Sanitize Denemesi',
            'slug' => 'backend-sanitize-denemesi',
            'summary' => 'Quill HTML güvenli şekilde kaydedilir.',
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => '2026-05-24',
            'reading_time' => 3,
            'cover_url' => '',
            'tags' => ['STM32', 'c'],
            'content_html' => '<h2 onclick="alert(1)">Giriş</h2><script>alert(1)</script><p><a href="javascript:alert(1)">kötü link</a><img src="/storage/uploads/demo.png" onerror="alert(1)"></p><pre>int main(void) {}</pre>',
        ])->assertRedirect('/admin/blog/backend-sanitize-denemesi/edit');

        $post = Post::query()->where('slug', 'backend-sanitize-denemesi')->firstOrFail();

        $this->assertSame(['c', 'stm32'], $post->tags()->orderBy('slug')->pluck('slug')->all());
        $this->assertStringContainsString('<h2 id="giris">Giriş</h2>', $post->content_html);
        $this->assertStringContainsString('<pre>int main(void) {}</pre>', $post->content_html);
        $this->assertStringNotContainsString('<script', $post->content_html);
        $this->assertStringNotContainsString('onclick', $post->content_html);
        $this->assertStringNotContainsString('javascript:', $post->content_html);
        $this->assertStringNotContainsString('onerror', $post->content_html);

        $content = $this->get('/blog/backend-sanitize-denemesi')->assertOk()->getContent();
        $this->assertStringContainsString('Backend Sanitize Denemesi', $content);
        $this->assertStringNotContainsString('<script>alert', $content);
        $this->assertStringNotContainsString('alert(1)', $content);
        $this->assertStringNotContainsString('javascript:', $content);
    }

    public function test_admin_can_upload_and_crop_blog_cover_image(): void
    {
        $this->seed();
        Storage::fake('public');
        $this->actingAs(User::query()->first());

        $this->post('/admin/blog', [
            'title' => 'Crop Edilen Blog Kapağı',
            'slug' => 'crop-edilen-blog-kapagi',
            'summary' => 'Kapak görseli 2:1 oranında kırpılır.',
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => '2026-05-24',
            'reading_time' => 3,
            'cover_image' => UploadedFile::fake()->image('cover.jpg', 800, 600)->size(128),
            'cover_crop' => json_encode(['x' => 100, 'y' => 120, 'width' => 600, 'height' => 300]),
            'tags' => ['stm32'],
            'content_html' => '<p>Kapak crop test.</p>',
        ])->assertRedirect('/admin/blog/crop-edilen-blog-kapagi/edit');

        $post = Post::query()->where('slug', 'crop-edilen-blog-kapagi')->firstOrFail();
        $path = Str::after($post->cover_url, '/storage/');

        $this->assertStringStartsWith('/storage/blog/', $post->cover_url);
        Storage::disk('public')->assertExists($path);

        [$width, $height] = getimagesize(Storage::disk('public')->path($path));
        $this->assertSame(600, $width);
        $this->assertSame(300, $height);
    }

    public function test_admin_can_create_project_and_feature_it_from_project_form(): void
    {
        $this->seed();
        $this->actingAs(User::query()->first());

        $this->post('/admin/projects', [
            'title' => 'STM32 Telemetry Logger',
            'slug' => 'stm32-telemetry-logger',
            'summary' => 'UART tabanlı küçük telemetry logger.',
            'status' => 'completed',
            'visibility' => 'public',
            'featured' => '1',
            'github' => 'https://github.com/example/logger',
            'technologies' => "STM32\nUART\nDMA\nLogic analyzer",
            'tags' => ['stm32', 'uart'],
            'content_html' => '<h2>Proje amacı</h2><p>Telemetry verisini kaydetmek.</p>',
        ])->assertRedirect('/admin/projects/stm32-telemetry-logger/edit');

        $project = Project::query()->where('slug', 'stm32-telemetry-logger')->firstOrFail();

        $this->assertTrue($project->featured);
        $this->assertSame(['STM32', 'UART', 'DMA', 'Logic analyzer'], $project->technologies);
        $this->assertSame(['stm32', 'uart'], $project->tags()->orderBy('slug')->pluck('slug')->all());
        $this->get('/projects/stm32-telemetry-logger')->assertOk()->assertSee('Telemetry verisini kaydetmek.', false);
    }

    public function test_admin_can_create_life_post_with_multiple_images(): void
    {
        $this->seed();
        Storage::fake('public');
        $this->actingAs(User::query()->first());

        $this->post('/admin/life', [
            'excerpt' => 'Hafta sonu kısa bir yürüyüş ve müzik molası.',
            'location' => 'Istanbul',
            'visibility' => 'public',
            'published_at' => '2026-05-24',
            'new_images' => [
                UploadedFile::fake()->image('one.jpg', 800, 600)->size(128),
                UploadedFile::fake()->image('two.webp', 800, 600)->size(128),
            ],
            'new_image_crops' => json_encode([
                ['x' => 100, 'y' => 120, 'width' => 600, 'height' => 300],
                ['x' => 80, 'y' => 140, 'width' => 600, 'height' => 300],
            ]),
        ])->assertRedirect();

        $lifePost = LifePost::query()->where('excerpt', 'Hafta sonu kısa bir yürüyüş ve müzik molası.')->firstOrFail();

        $this->assertCount(2, $lifePost->images);
        $this->assertStringStartsWith('/storage/life/', $lifePost->images->first()->url);
        Storage::disk('public')->assertExists(Str::after($lifePost->images->first()->url, '/storage/'));

        [$width, $height] = getimagesize(Storage::disk('public')->path(Str::after($lifePost->images->first()->url, '/storage/')));
        $this->assertSame(600, $width);
        $this->assertSame(300, $height);

        $this->get('/life')->assertOk()->assertSee('Hafta sonu kısa bir yürüyüş ve müzik molası.');
    }

    public function test_life_image_upload_reports_specific_file_errors(): void
    {
        $this->seed();
        $this->actingAs(User::query()->first());

        $this->from('/admin/life/create')->post('/admin/life', [
            'excerpt' => 'Dosya validasyon denemesi.',
            'location' => 'Istanbul',
            'visibility' => 'public',
            'published_at' => '2026-05-24',
            'new_images' => [
                UploadedFile::fake()->create('document.pdf', 2, 'application/pdf'),
            ],
        ])
            ->assertRedirect('/admin/life/create')
            ->assertSessionHasErrors([
                'new_images.0' => 'Seçilen dosyalardan biri geçerli bir fotoğraf değil.',
            ]);
    }

    public function test_life_image_upload_failure_is_explained_in_plain_turkish(): void
    {
        $this->seed();
        $this->actingAs(User::query()->first());

        $path = tempnam(sys_get_temp_dir(), 'failed-upload');
        file_put_contents($path, 'not actually uploaded');

        $this->from('/admin/life/create')->post('/admin/life', [
            'excerpt' => 'Upload limit açıklaması.',
            'location' => 'Istanbul',
            'visibility' => 'public',
            'published_at' => '2026-05-24',
            'new_images' => [
                new UploadedFile($path, 'large-photo.jpg', 'image/jpeg', UPLOAD_ERR_INI_SIZE, true),
            ],
        ])
            ->assertRedirect('/admin/life/create')
            ->assertSessionHasErrors([
                'new_images.0' => 'Fotoğraflardan biri sunucuya yüklenemedi. Bu genelde fotoğraf boyutu 4 MB sınırını veya sunucunun upload limitini aştığında olur; lütfen daha küçük bir JPG, PNG, GIF veya WebP dosya seç.',
            ]);
    }

    public function test_quill_image_upload_is_auth_protected_and_stores_public_file(): void
    {
        $this->seed();
        Storage::fake('public');

        $this->postJson('/admin/uploads/images', [
            'image' => UploadedFile::fake()->image('demo.png', 80, 80)->size(64),
        ])->assertUnauthorized();

        $this->actingAs(User::query()->first());

        $response = $this->postJson('/admin/uploads/images', [
            'image' => UploadedFile::fake()->image('demo.png', 80, 80)->size(64),
        ])->assertOk()->assertJsonStructure(['url']);

        $url = $response->json('url');

        $this->assertStringStartsWith('/storage/uploads/', $url);
        Storage::disk('public')->assertExists(Str::after($url, '/storage/'));
    }

    public function test_quill_image_upload_returns_specific_validation_message(): void
    {
        $this->seed();
        $this->actingAs(User::query()->first());

        $this->postJson('/admin/uploads/images', [
            'image' => UploadedFile::fake()->create('not-image.txt', 2, 'text/plain'),
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['image'])
            ->assertJsonPath('errors.image.0', 'Seçilen dosya geçerli bir görsel değil. PNG, JPG, GIF veya WebP yüklemelisin.');
    }

    public function test_quill_image_upload_failure_is_explained_in_plain_turkish(): void
    {
        $this->seed();
        $this->actingAs(User::query()->first());

        $path = tempnam(sys_get_temp_dir(), 'failed-quill-upload');
        file_put_contents($path, 'not actually uploaded');

        $this->postJson('/admin/uploads/images', [
            'image' => new UploadedFile($path, 'large-editor-image.jpg', 'image/jpeg', UPLOAD_ERR_INI_SIZE, true),
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['image'])
            ->assertJsonPath('errors.image.0', 'Görsel sunucuya yüklenemedi. Bu genelde dosya boyutu sunucunun upload limitini aştığında olur; editör içi görseller için 2 MB altında PNG, JPG, GIF veya WebP dosya seç.');
    }

    public function test_public_pages_hide_unpublished_and_private_content(): void
    {
        $this->seed();

        $tag = Tag::query()->firstOrFail();
        $privatePost = Post::query()->create([
            'title' => 'Private Draft',
            'slug' => 'private-draft',
            'summary' => 'Görünmemeli',
            'status' => 'published',
            'visibility' => 'private',
            'reading_time' => 1,
            'published_at' => '2026-05-24',
            'content_html' => '<p>Gizli</p>',
        ]);
        $privatePost->tags()->sync([$tag->id]);

        $this->get('/blog/private-draft')->assertNotFound();
        $this->get('/blog')->assertOk()->assertDontSee('Private Draft');
    }

    public function test_public_page_titles_use_dora_prefix_without_favicon(): void
    {
        $this->seed();

        $this->get('/')
            ->assertOk()
            ->assertSee('<title>DORA | Home</title>', false)
            ->assertDontSee('rel="icon"', false);

        $this->get('/blog')
            ->assertOk()
            ->assertSee('<title>DORA | Blog</title>', false);
    }

    public function test_favicon_request_returns_empty_no_content_response(): void
    {
        $response = $this->get('/favicon.ico')->assertNoContent();

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_admin_can_upload_view_and_delete_cv_pdf(): void
    {
        $this->seed();
        Storage::fake('public');
        $this->actingAs(User::query()->first());

        $this->put('/admin', [
            'site_name' => 'Mehmet Dora',
            'site_role' => 'Embedded Developer',
            'site_location' => 'Istanbul',
            'site_email' => 'mehmet@example.com',
            'github' => 'https://github.com/example',
            'linkedin' => 'https://linkedin.com/in/example',
            'telegram' => 'https://t.me/example',
            'cv_pdf' => UploadedFile::fake()->create('mehmet-dora-cv.pdf', 128, 'application/pdf'),
        ])->assertRedirect();

        $settings = \App\Models\SiteSetting::current()->fresh();
        $site = $settings->site;

        $this->assertSame('/cv/pdf', $site['links']['cv']);
        $this->assertStringStartsWith('/storage/cv/', $site['cv_pdf_url']);
        Storage::disk('public')->assertExists(Str::after($site['cv_pdf_url'], '/storage/'));

        $this->get('/cv')->assertOk()->assertSee('Resume');

        $this->get('/cv/pdf')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->put('/admin', [
            'site_name' => 'Mehmet Dora',
            'site_role' => 'Embedded Developer',
            'site_location' => 'Istanbul',
            'site_email' => 'mehmet@example.com',
            'github' => 'https://github.com/example',
            'linkedin' => 'https://linkedin.com/in/example',
            'telegram' => 'https://t.me/example',
            'delete_cv_pdf' => '1',
        ])->assertRedirect();

        Storage::disk('public')->assertMissing(Str::after($site['cv_pdf_url'], '/storage/'));

        $siteAfterDelete = \App\Models\SiteSetting::current()->fresh()->site;
        $this->assertArrayNotHasKey('cv_pdf_url', $siteAfterDelete);
        $this->get('/cv')->assertOk()->assertSee('Resume');
        $this->get('/cv/pdf')->assertRedirect('/cv');
    }

    public function test_admin_cv_upload_reports_specific_file_errors(): void
    {
        $this->seed();
        $this->actingAs(User::query()->first());

        $response = $this->from('/admin')->put('/admin', [
            'site_name' => 'Mehmet Dora',
            'site_role' => 'Embedded Developer',
            'site_location' => 'Istanbul',
            'site_email' => 'mehmet@example.com',
            'github' => 'https://github.com/example',
            'linkedin' => 'https://linkedin.com/in/example',
            'telegram' => 'https://t.me/example',
            'cv_pdf' => UploadedFile::fake()->create('cv.txt', 2, 'text/plain'),
        ]);

        $response
            ->assertRedirect('/admin')
            ->assertSessionHasErrors([
                'cv_pdf' => 'CV dosyası PDF olmalı. DOC, DOCX, JPG veya başka formatlar kabul edilmez.',
            ]);
    }
}
