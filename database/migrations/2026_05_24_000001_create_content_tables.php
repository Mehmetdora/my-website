<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->json('site');
            $table->json('home');
            $table->json('about');
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->string('cover_url')->nullable();
            $table->string('cover_alt')->nullable();
            $table->string('status')->default('draft')->index();
            $table->string('visibility')->default('public')->index();
            $table->unsignedSmallInteger('reading_time')->default(1);
            $table->timestamp('published_at')->nullable()->index();
            $table->longText('content_html')->nullable();
            $table->timestamps();
        });

        Schema::create('post_tag', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['post_id', 'tag_id']);
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->string('cover_url')->nullable();
            $table->string('cover_alt')->nullable();
            $table->string('status')->default('planned')->index();
            $table->string('visibility')->default('public')->index();
            $table->boolean('featured')->default(false)->index();
            $table->string('github')->nullable();
            $table->json('technologies')->nullable();
            $table->longText('content_html')->nullable();
            $table->timestamps();
        });

        Schema::create('project_tag', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['project_id', 'tag_id']);
        });

        Schema::create('life_posts', function (Blueprint $table) {
            $table->id();
            $table->text('excerpt');
            $table->string('location')->nullable();
            $table->string('visibility')->default('public')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('life_post_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('life_post_id')->constrained()->cascadeOnDelete();
            $table->text('url');
            $table->string('alt')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('life_post_images');
        Schema::dropIfExists('life_posts');
        Schema::dropIfExists('project_tag');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('site_settings');
    }
};
