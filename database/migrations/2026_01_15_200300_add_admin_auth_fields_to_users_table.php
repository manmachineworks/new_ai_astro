<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email')) {
                $table->string('email', 191)->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
        });

        if (Schema::hasColumn('users', 'email')) {
            DB::statement('ALTER TABLE users MODIFY email VARCHAR(191) NULL');
            if (!$this->indexExists('users', 'users_email_unique')) {
                DB::statement('CREATE UNIQUE INDEX users_email_unique ON users (email)');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email', 'password', 'remember_token']);
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        $results = DB::select(
            'SELECT COUNT(1) AS count FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?',
            [$table, $index]
        );

        return (int) ($results[0]->count ?? 0) > 0;
    }
};
