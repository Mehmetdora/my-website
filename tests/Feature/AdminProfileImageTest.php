<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProfileImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_profile_image()
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->put('/admin', [
            'site_name' => 'Mehmet',
            'site_role' => 'Engineer',
            'site_location' => 'Istanbul',
            'site_email' => 'me@example.com',
            'profile_image' => $file,
        ]);

        $response->assertStatus(302);

        $settings = SiteSetting::current();
        $this->assertStringStartsWith('/storage/profile/', $settings->site['profile_image']);
        Storage::disk('public')->assertExists(\Illuminate\Support\Str::after($settings->site['profile_image'], '/storage/'));
    }

    public function test_admin_can_delete_profile_image()
    {
        Storage::fake('public');

        // create a fake stored profile image
        Storage::disk('public')->put('profile/profile.jpg', 'contents');

        $this->actingAsAdmin();

        // ensure SiteSetting has profile_image set
        $settings = SiteSetting::current();
        $siteArray = $settings->site;
        $siteArray['profile_image'] = Storage::url('profile/profile.jpg');
        $settings->site = $siteArray;
        $settings->save();

        $response = $this->put('/admin', [
            'site_name' => 'Mehmet',
            'site_role' => 'Engineer',
            'site_location' => 'Istanbul',
            'site_email' => 'me@example.com',
            'delete_profile_image' => '1',
        ]);

        $response->assertStatus(302);

        Storage::disk('public')->assertMissing('profile/profile.jpg');

        $settings = SiteSetting::current();
        $this->assertEquals(config('content.site.profile_image'), $settings->site['profile_image']);
    }

    public function test_admin_profile_image_upload_reports_specific_file_errors()
    {
        $this->actingAsAdmin();

        $response = $this->from('/admin')->put('/admin', [
            'site_name' => 'Mehmet',
            'site_role' => 'Engineer',
            'site_location' => 'Istanbul',
            'site_email' => 'me@example.com',
            'profile_image' => UploadedFile::fake()->create('avatar.pdf', 2, 'application/pdf'),
        ]);

        $response
            ->assertRedirect('/admin')
            ->assertSessionHasErrors([
                'profile_image' => 'Profil fotoğrafı geçerli bir görsel dosyası değil.',
            ]);
    }

    public function test_admin_profile_image_upload_failure_is_explained_in_plain_turkish()
    {
        $this->actingAsAdmin();

        $path = tempnam(sys_get_temp_dir(), 'failed-profile-upload');
        file_put_contents($path, 'not actually uploaded');

        $response = $this->from('/admin')->put('/admin', [
            'site_name' => 'Mehmet',
            'site_role' => 'Engineer',
            'site_location' => 'Istanbul',
            'site_email' => 'me@example.com',
            'profile_image' => new UploadedFile($path, 'large-avatar.jpg', 'image/jpeg', UPLOAD_ERR_INI_SIZE, true),
        ]);

        $response
            ->assertRedirect('/admin')
            ->assertSessionHasErrors([
                'profile_image' => 'Profil fotoğrafı sunucuya yüklenemedi. Bu genelde dosya boyutu sunucunun upload limitini aştığında olur; profil fotoğrafı en fazla 4 MB ve JPG, PNG veya WebP olmalı.',
            ]);
    }

    private function actingAsAdmin(): void
    {
        $this->actingAs(User::factory()->create([
            'email' => 'admin@personal-website.local',
        ]));
    }
}
