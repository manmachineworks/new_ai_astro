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
        if (!Schema::hasTable('admin_activity_logs')) {
            Schema::create('admin_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('causer_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action');
                $table->string('target_type', 191)->nullable();
                $table->unsignedBigInteger('target_id')->nullable();
                $table->json('metadata')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->index(['target_type', 'target_id']);
            });
        } else {
            DB::statement("ALTER TABLE admin_activity_logs MODIFY target_type VARCHAR(191) NULL");
            if (!$this->indexExists('admin_activity_logs', 'admin_activity_logs_target_type_target_id_index')) {
                DB::statement('CREATE INDEX admin_activity_logs_target_type_target_id_index ON admin_activity_logs (target_type, target_id)');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
    }

    private function indexExists(string $table, string $index): bool
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            $indexes = Schema::getConnection()->select("PRAGMA index_list('{$table}')");
            foreach ($indexes as $idx) {
                if (($idx->name ?? null) === $index) {
                    return true;
                }
            }
            return false;
        }

        $results = DB::select(
            'SELECT COUNT(1) AS count FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?',
            [$table, $index]
        );

        return (int) ($results[0]->count ?? 0) > 0;
    }
};
