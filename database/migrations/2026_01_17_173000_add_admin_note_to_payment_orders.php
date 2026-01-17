<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_orders', 'admin_note')) {
                $table->text('admin_note')->nullable();
            }
            if (!Schema::hasColumn('payment_orders', 'admin_note_status')) {
                $table->string('admin_note_status', 30)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            if (Schema::hasColumn('payment_orders', 'admin_note_status')) {
                $table->dropColumn('admin_note_status');
            }
            if (Schema::hasColumn('payment_orders', 'admin_note')) {
                $table->dropColumn('admin_note');
            }
        });
    }
};
