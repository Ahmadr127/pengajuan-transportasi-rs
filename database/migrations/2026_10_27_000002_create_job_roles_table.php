<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel jabatan dari Simutu (tbl_role).
     *
     * PENTING: ini BUKAN tabel `roles` ACL (super-admin/admin/driver/user).
     * Jabatan Simutu (Administrator, Koordinator, …) disimpan terpisah.
     */
    public function up(): void
    {
        Schema::create('job_roles', function (Blueprint $table) {
            $table->id();
            $table->string('simutu_id')->nullable()->unique();
            $table->string('nama_role');
            $table->text('deskripsi_role')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_roles');
    }
};
