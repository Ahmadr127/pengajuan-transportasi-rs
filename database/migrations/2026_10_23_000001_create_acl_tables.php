<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->string('description')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_super')->default(false);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('group')->default('general');
            $table->string('label');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'user_id']);
            $table->timestamps();
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->string('key')->unique();
            $table->string('context', 20); // admin | user | driver
            $table->string('section')->nullable();
            $table->string('label');
            $table->string('route_name')->nullable();
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->string('permission_name')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_landing')->default(false);
            $table->boolean('show_sidebar')->default(true);
            $table->boolean('show_bottom_nav')->default(false);
            $table->string('active_pattern')->nullable();
            $table->timestamps();

            $table->index(['context', 'is_active', 'sort_order']);
        });

        // Seed role inti agar user lama bisa langsung dipetakan.
        $now = now();
        $roles = [
            ['name' => 'super-admin', 'label' => 'Super Admin', 'description' => 'Akses penuh termasuk pengaturan role & menu.', 'is_locked' => true, 'is_super' => true],
            ['name' => 'admin', 'label' => 'Admin', 'description' => 'Pengelola transportasi.', 'is_locked' => true, 'is_super' => false],
            ['name' => 'driver', 'label' => 'Driver', 'description' => 'Pengemudi.', 'is_locked' => true, 'is_super' => false],
            ['name' => 'user', 'label' => 'User', 'description' => 'Pemohon transportasi.', 'is_locked' => true, 'is_super' => false],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                $role + ['created_at' => $now, 'updated_at' => $now]
            );
        }

        $roleIds = DB::table('roles')->pluck('id', 'name');

        DB::table('users')->select('id', 'role')->orderBy('id')->chunkById(100, function ($users) use ($roleIds) {
            $rows = [];
            foreach ($users as $user) {
                $roleId = $roleIds[$user->role] ?? $roleIds['user'];
                $rows[] = ['role_id' => $roleId, 'user_id' => $user->id];
            }
            if ($rows) {
                DB::table('role_user')->insertOrIgnore($rows);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
