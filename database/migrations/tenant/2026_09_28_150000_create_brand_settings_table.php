<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brand_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('brand_name')->nullable();
            $table->string('primary_color', 30)->default('#6366f1');
            $table->string('secondary_color', 30)->default('#8b5cf6');
            $table->string('accent_color', 30)->default('#f59e0b');
            $table->string('background_color', 30)->default('#0f172a');
            $table->string('logo_url')->nullable();
            $table->string('favicon_url')->nullable();
            $table->string('banner_url')->nullable();
            $table->string('font_family', 100)->default('Inter');
            $table->json('social_links')->nullable();
            $table->text('custom_css')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_settings');
    }
};
