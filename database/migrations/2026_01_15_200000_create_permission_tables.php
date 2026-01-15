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
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('guard_name', 50);
                $table->timestamps();

                $table->unique(['name', 'guard_name']);
            });
        } else {
            DB::statement("ALTER TABLE permissions MODIFY name VARCHAR(100) NOT NULL");
            DB::statement("ALTER TABLE permissions MODIFY guard_name VARCHAR(50) NOT NULL");
            if (!$this->indexExists('permissions', 'permissions_name_guard_name_unique')) {
                DB::statement('CREATE UNIQUE INDEX permissions_name_guard_name_unique ON permissions (name, guard_name)');
            }
        }

        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('guard_name', 50);
                $table->timestamps();

                $table->unique(['name', 'guard_name']);
            });
        } else {
            DB::statement("ALTER TABLE roles MODIFY name VARCHAR(100) NOT NULL");
            DB::statement("ALTER TABLE roles MODIFY guard_name VARCHAR(50) NOT NULL");
            if (!$this->indexExists('roles', 'roles_name_guard_name_unique')) {
                DB::statement('CREATE UNIQUE INDEX roles_name_guard_name_unique ON roles (name, guard_name)');
            }
        }

        if (!Schema::hasTable('model_has_permissions')) {
            Schema::create('model_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->string('model_type', 191);
                $table->unsignedBigInteger('model_id');

                $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');
                $table->foreign('permission_id')
                    ->references('id')
                    ->on('permissions')
                    ->onDelete('cascade');
            });
        } else {
            DB::statement("ALTER TABLE model_has_permissions MODIFY model_type VARCHAR(191) NOT NULL");
            if (!$this->indexExists('model_has_permissions', 'model_has_permissions_model_id_model_type_index')) {
                DB::statement('CREATE INDEX model_has_permissions_model_id_model_type_index ON model_has_permissions (model_id, model_type)');
            }
        }

        if (!Schema::hasTable('model_has_roles')) {
            Schema::create('model_has_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id');
                $table->string('model_type', 191);
                $table->unsignedBigInteger('model_id');

                $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');
            });
        } else {
            DB::statement("ALTER TABLE model_has_roles MODIFY model_type VARCHAR(191) NOT NULL");
            if (!$this->indexExists('model_has_roles', 'model_has_roles_model_id_model_type_index')) {
                DB::statement('CREATE INDEX model_has_roles_model_id_model_type_index ON model_has_roles (model_id, model_type)');
            }
        }

        if (!Schema::hasTable('role_has_permissions')) {
            Schema::create('role_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');

                $table->foreign('permission_id')
                    ->references('id')
                    ->on('permissions')
                    ->onDelete('cascade');

                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');

                $table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
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
