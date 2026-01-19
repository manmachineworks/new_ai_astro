<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g. wallet_low

            // Templates (JSON for multi-lang OR simple columns if single table approach)
            // User requested: notification_template_translations or cols with default_locale.
            // Let's use simple columns for default, and allow translations table later if needed using key.
            // Actually, prompts says: "Provide models.. notification_templates... Optional: translations table".
            // I will implement simple JSON columns for translations or just sticking to one locale for now but with structure for more?
            // "Include examples for English + Hindi".
            // Let's use JSON column for templates: {"en": "...", "hi": "..."} OR just standard text and rely on app localizer?
            // The prompt implies a DB-driven template engine.
            // Let's use `title_template` and `body_template` as JSON to store locales directly: { "en": "Hello", "hi": "Namaste" }
            // Or Keep it simple: one row per key, default text, and use validation.
            // I will use JSON columns for `title_templates` and `body_templates` to support multi-language in one table easily.

            $table->json('title_templates'); // {"en": "Alert", "hi": "Savdhaan"}
            $table->json('body_templates');
            $table->string('default_locale')->default('en');

            $table->json('variables_schema')->nullable(); // ["balance", "currency"]
            $table->json('channels_enabled')->nullable(); // Default handled in app for MySQL < 8.0.13

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
