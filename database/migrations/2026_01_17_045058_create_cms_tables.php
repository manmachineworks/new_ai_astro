<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. CMS Pages
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug', 190);
            $table->string('title');
            $table->longText('content_html')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('locale', 10)->default('en');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('created_by_admin_id')->nullable();
            $table->timestamps();

            $table->unique(['slug', 'locale']);
        });

        // 2. CMS Banners
        Schema::create('cms_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('image_path');
            $table->string('link_url')->nullable();
            $table->string('position')->default('home_top'); // home_top, home_mid, etc.
            $table->string('locale', 10)->default('en');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamp('start_at_utc')->nullable();
            $table->timestamp('end_at_utc')->nullable();
            $table->timestamps();
        });

        // 3. FAQs
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->longText('answer_html')->nullable();
            $table->string('locale', 10)->default('en');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 4. Featured Astrologers
        Schema::create('featured_astrologers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('astrologer_profile_id');
            $table->string('locale', 10)->default('en');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['astrologer_profile_id', 'locale']);
            // Assuming astrologer_profile_id references astrologer_profiles.id (but that table might not have foreign key constraint enforced strictly in this context, but good to index)
            // Ideally we should add FK constraint if astrologer_profiles table exists.
            // I'll add index for performance.
            $table->index('astrologer_profile_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_astrologers');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('cms_banners');
        Schema::dropIfExists('cms_pages');
    }
};
