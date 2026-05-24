<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LifePostController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\PublicPageController;
use App\Http\Middleware\AdminSecurityHeaders;
use App\Http\Middleware\EnforceAdminSessionTimeout;
use App\Http\Middleware\EnsureAdminUser;
use Illuminate\Support\Facades\Route;

Route::get('/favicon.ico', fn () => response('', 204)->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0'));

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/about', [PublicPageController::class, 'about'])->name('about');
Route::get('/blog', [PublicPageController::class, 'blogIndex'])->name('blog.index');
Route::get('/blog/{slug}', [PublicPageController::class, 'blogShow'])->name('blog.show');
Route::get('/projects', [PublicPageController::class, 'projectsIndex'])->name('projects.index');
Route::get('/projects/{slug}', [PublicPageController::class, 'projectShow'])->name('projects.show');
Route::get('/life', [PublicPageController::class, 'life'])->name('life');
Route::get('/cv', [PublicPageController::class, 'cv'])->name('cv');
Route::get('/cv/pdf', [PublicPageController::class, 'cvPdf'])->name('cv.pdf');
Route::get('/tags/{slug}', [PublicPageController::class, 'tagShow'])->name('tags.show');

Route::middleware('guest')->group(function (): void {
    Route::middleware(AdminSecurityHeaders::class)->group(function (): void {
        Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
        Route::post('/admin/login', [AuthController::class, 'login'])->middleware('throttle:admin-login')->name('admin.login.submit');
    });
});

Route::middleware([
    'auth',
    'auth.session',
    'throttle:admin',
    EnsureAdminUser::class,
    EnforceAdminSessionTimeout::class,
    AdminSecurityHeaders::class,
])->group(function (): void {
    Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::put('/admin', [DashboardController::class, 'update'])->name('admin.dashboard.update');

    Route::get('/admin/projects', [ProjectController::class, 'index'])->name('admin.projects.index');
    Route::get('/admin/projects/create', [ProjectController::class, 'create'])->name('admin.projects.create');
    Route::post('/admin/projects', [ProjectController::class, 'store'])->name('admin.projects.store');
    Route::get('/admin/projects/{slug}/edit', [ProjectController::class, 'edit'])->name('admin.projects.edit');
    Route::put('/admin/projects/{slug}', [ProjectController::class, 'update'])->name('admin.projects.update');

    Route::get('/admin/blog', [PostController::class, 'index'])->name('admin.blog.index');
    Route::get('/admin/blog/create', [PostController::class, 'create'])->name('admin.blog.create');
    Route::post('/admin/blog', [PostController::class, 'store'])->name('admin.blog.store');
    Route::get('/admin/blog/{slug}/edit', [PostController::class, 'edit'])->name('admin.blog.edit');
    Route::put('/admin/blog/{slug}', [PostController::class, 'update'])->name('admin.blog.update');

    Route::get('/admin/life', [LifePostController::class, 'index'])->name('admin.life.index');
    Route::get('/admin/life/create', [LifePostController::class, 'create'])->name('admin.life.create');
    Route::post('/admin/life', [LifePostController::class, 'store'])->name('admin.life.store');
    Route::get('/admin/life/{lifePost}/edit', [LifePostController::class, 'edit'])->name('admin.life.edit');
    Route::put('/admin/life/{lifePost}', [LifePostController::class, 'update'])->name('admin.life.update');

    Route::post('/admin/uploads/images', [UploadController::class, 'image'])->name('admin.uploads.images');
});
