<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded and run [the permission migration] first before running this script.');
        }

        // Roles table
        Schema::create($tableNames['roles'], function (Blueprint $table) use ($teams, $columnNames) {
            $table->uuid('id')->primary();
            if ($teams || config('permission.testing')) {
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->foreignUuid('school_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->timestamps();
            $table->unique([$columnNames['team_foreign_key'] ?? 'school_id', 'name', 'guard_name']);
        });

        // Permissions table
        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        // Role has permissions
        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($pivotRole, $pivotPermission) {
            $table->uuid($pivotPermission);
            $table->uuid($pivotRole);
            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
            $table->foreign($pivotPermission)->references('id')->on($tableNames['permissions'])->cascadeOnDelete();
            $table->foreign($pivotRole)->references('id')->on($tableNames['roles'])->cascadeOnDelete();
        });

        // Model has roles
        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($teams, $columnNames, $pivotRole) {
            $table->uuid($pivotRole);
            if ($teams || config('permission.testing')) {
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->default(1);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');
            }
            $table->morphs('model');
            $table->primary([$pivotRole, 'model_id', 'model_type'], 'model_has_roles_role_id_model_id_model_type_primary');
            $table->foreign($pivotRole)->references('id')->on($tableNames['roles'])->cascadeOnDelete();
        });

        // Model has permissions
        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($teams, $columnNames, $pivotPermission) {
            $table->uuid($pivotPermission);
            if ($teams || config('permission.testing')) {
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->default(1);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');
            }
            $table->morphs('model');
            $table->primary([$pivotPermission, 'model_id', 'model_type'], 'model_has_permissions_permission_id_model_id_model_type_primary');
            $table->foreign($pivotPermission)->references('id')->on($tableNames['permissions'])->cascadeOnDelete();
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');
        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not found and run [the permission migrations] first before running this script.');
        }
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['permissions']);
        Schema::drop($tableNames['roles']);
    }
};
