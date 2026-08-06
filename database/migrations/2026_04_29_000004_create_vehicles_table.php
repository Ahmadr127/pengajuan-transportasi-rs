<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // nama unit: mobil_umum_1, ambulance_igd
            $table->string('type'); // umum, ambulance
            $table->string('plate_number')->unique(); // nomor polisi
            $table->string('brand')->nullable(); // merk mobil
            $table->string('model')->nullable(); // model/tipe
            $table->year('year')->nullable(); // tahun pembuatan
            $table->integer('capacity')->nullable(); // kapasitas penumpang
            $table->integer('last_km')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};