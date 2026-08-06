<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create users table with initial columns
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Add unit_kerja
        Schema::table('users', function (Blueprint $table) {
            $table->string('unit_kerja')->nullable()->after('name');
        });

        // Add profesi and jabatan
        Schema::table('users', function (Blueprint $table) {
            $table->string('profesi')->nullable()->after('unit_kerja');
            $table->string('jabatan')->nullable()->after('profesi');
        });

        // Add posisi_pekerjaan
        Schema::table('users', function (Blueprint $table) {
            $table->string('posisi_pekerjaan')->nullable()->after('unit_kerja');
        });

        // Add role
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('password');
        });

        // Add priority_level
        Schema::table('users', function (Blueprint $table) {
            $table->integer('priority_level')->default(0)->after('role');
        });

        // Update name structure: rename name to first_name, add last_name, drop email
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'first_name');
            $table->string('last_name')->after('first_name')->nullable();
            $table->dropColumn('email');
        });

        // Add nip
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 50)->nullable()->after('last_name');
        });

        // Add username with backfill
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('last_name');
        });

        // Backfill username
        foreach (User::all() as $user) {
            $parts = preg_split('/\s+/', trim($user->first_name . ' ' . $user->last_name));
            $w1 = strtolower(preg_replace('/[^a-z0-9.]/i', '', $parts[0] ?? ''));
            $w2raw = $parts[1] ?? null;
            $w2 = ($w2raw && strpos($w2raw, '.') === false)
                ? strtolower(preg_replace('/[^a-z0-9]/i', '', $w2raw))
                : null;
            $base = rtrim($w1, '.');
            $username = ($w2 && $w2 !== '') ? "{$base}.{$w2}" : $base;

            $final = $username;
            $i = 1;
            while (User::where('username', $final)->where('id', '!=', $user->id)->exists()) {
                $final = $username . $i++;
            }
            $user->update(['username' => $final]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false)->unique()->change();
        });

        // Create password_reset_tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Create sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};