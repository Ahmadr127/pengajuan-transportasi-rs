<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('simutu_id')->nullable()->unique();
            $table->string('kode_unit')->unique();
            $table->string('nama_unit');
            $table->text('deskripsi_unit')->nullable();
            $table->string('status_unit')->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
