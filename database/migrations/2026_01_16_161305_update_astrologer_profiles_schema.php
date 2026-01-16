<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('astrologer_profiles', function (Blueprint $table) {
            // New Fields
            $table->string('display_name')->nullable()->after('user_id');
            $table->string('gender')->nullable()->after('bio');
            $table->date('dob')->nullable()->after('gender');
            $table->string('profile_photo_path')->nullable()->after('dob');

            // Stats
            $table->decimal('rating_avg', 3, 2)->default(0)->after('experience_years');
            $table->integer('rating_count')->default(0)->after('rating_avg');

            // Extended JSON
            $table->json('specialties')->nullable()->after('skills');

            // Admin Controls
            $table->boolean('show_on_front')->default(false)->after('visibility');
            $table->boolean('is_enabled')->default(true)->after('show_on_front');
            $table->foreignId('verified_by_admin_id')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Indexing
            $table->index(['show_on_front', 'is_enabled']);
            $table->index('rating_avg');
        });
    }

    public function down(): void
    {
        Schema::table('astrologer_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'display_name',
                'gender',
                'dob',
                'profile_photo_path',
                'rating_avg',
                'rating_count',
                'specialties',
                'show_on_front',
                'is_enabled',
                'verified_by_admin_id',
                'verified_at',
                'rejection_reason'
            ]);
        });
    }
};
