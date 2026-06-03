<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('life_posts', function (Blueprint $table): void {
            $table->string('post_type')->default('image')->after('id')->index();
            $table->text('audio_url')->nullable()->after('visibility');
            $table->string('audio_name')->nullable()->after('audio_url');
            $table->string('audio_mime')->nullable()->after('audio_name');
            $table->unsignedBigInteger('audio_size')->nullable()->after('audio_mime');
        });
    }

    public function down(): void
    {
        Schema::table('life_posts', function (Blueprint $table): void {
            $table->dropColumn([
                'post_type',
                'audio_url',
                'audio_name',
                'audio_mime',
                'audio_size',
            ]);
        });
    }
};
