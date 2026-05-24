<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Models\Tag;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        if (app()->runningInConsole() || ! Schema::hasTable('site_settings')) {
            return;
        }

        $settings = SiteSetting::current();

        Config::set('content.site', $settings->site);
        Config::set('content.home', $settings->home);
        Config::set('content.about', $settings->about);

        if (Schema::hasTable('tags')) {
            Config::set('content.tags', Tag::query()->orderBy('name')->get()->map->toViewArray()->all());
        }
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('admin-login', function (Request $request): array {
            $email = Str::lower((string) $request->input('email', 'unknown'));

            return [
                Limit::perMinute(5)->by('admin-login-email:'.$email.'|'.$request->ip()),
                Limit::perHour(20)->by('admin-login-ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('admin', function (Request $request): array {
            $actor = $request->user()?->id
                ? 'user:'.$request->user()->id
                : 'ip:'.$request->ip();

            return [
                Limit::perMinute(120)->by('admin:'.$actor),
                Limit::perMinute(60)->by('admin-ip:'.$request->ip()),
            ];
        });
    }
}
