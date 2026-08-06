<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recurring_transport_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('jenis');
            $table->string('keperluan');
            $table->string('prioritas');
            $table->string('pemohon_nama')->nullable();
            $table->string('pemohon_unit')->nullable();
            $table->integer('jumlah_penumpang')->nullable();
            $table->time('jam');
            $table->time('jam_sampai')->nullable();
            $table->json('hari'); // json array of days
            $table->date('start_date');
            $table->date('end_date');
            $table->text('alamat_asal')->nullable();
            $table->text('alamat_tujuan')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('pasien_nama')->nullable();
            $table->string('pasien_no_rm')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_transport_templates');
    }
};
