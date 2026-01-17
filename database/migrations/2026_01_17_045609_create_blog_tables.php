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
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 190);
            $table->string('locale', 10)->default('en');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['slug', 'locale']);
        });

        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 190)->unique();
            $table->timestamps();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug', 190);
            $table->text('excerpt')->nullable();
            $table->longText('content_html')->nullable();
            $table->string('featured_image_path')->nullable();
            $table->string('locale', 10)->default('en');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('author_admin_id')->nullable(); // Assuming admin link
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->unsignedBigInteger('blog_category_id')->nullable();
            $table->timestamps();

            $table->unique(['slug', 'locale']);
        });

        Schema::create('blog_post_tags', function (Blueprint $table) {
            $table->foreignUuid('blog_post_id')->constrained('blog_posts')->onDelete('cascade');
            $table->foreignId('blog_tag_id')->constrained('blog_tags')->onDelete('cascade');
            $table->primary(['blog_post_id', 'blog_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_post_tags');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_categories');
    }
};
