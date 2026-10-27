<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            // Backfill pivot role_user dari kolom role (jika ada user yang belum punya pivot).
            $roleIds = DB::table('roles')->pluck('id', 'name');
            DB::table('users')->select('id', 'role')->orderBy('id')->chunkById(100, function ($users) use ($roleIds) {
                $rows = [];
                foreach ($users as $user) {
                    $roleId = $roleIds[$user->role] ?? ($roleIds['user'] ?? null);
                    if ($roleId) {
                        $rows[] = ['role_id' => $roleId, 'user_id' => $user->id];
                    }
                }
                if ($rows) {
                    DB::table('role_user')->insertOrIgnore($rows);
                }
            });

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('user')->after('password');
            });
        }
    }
};
