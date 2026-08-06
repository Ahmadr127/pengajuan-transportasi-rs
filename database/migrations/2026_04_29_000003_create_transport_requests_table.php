<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transport_requests', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan', 20)->nullable()->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('jenis'); // umum | ambulance
            $table->string('keperluan')->nullable(); // antar | jemput
            $table->string('prioritas')->default('biasa'); // biasa | segera

            $table->date('tanggal');
            $table->time('jam');
            $table->date('tanggal_sampai')->nullable();
            $table->time('jam_sampai')->nullable();
            $table->string('jam_kedatangan', 10)->nullable();
            $table->unsignedInteger('biaya_tol')->nullable();
            $table->string('kontak');

            $table->text('alamat_asal')->nullable();
            $table->text('alamat_tujuan')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('unit_mobil')->nullable();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();

            // Umum
            $table->string('pemohon_nama')->nullable();
            $table->string('pemohon_unit')->nullable();
            $table->unsignedInteger('jumlah_penumpang')->nullable();

            // Ambulance
            $table->string('pasien_nama')->nullable();
            $table->string('pasien_no_rm')->nullable();
            $table->string('ruangan')->nullable();
            $table->string('kondisi_pasien')->nullable();
            $table->text('alamat_pasien')->nullable();
            $table->string('pendamping_nama')->nullable();

            // Admin fields
            $table->string('plat_nomor')->nullable();
            $table->integer('km_awal')->nullable();
            $table->integer('km_akhir')->nullable();

            $table->string('status')->default('diajukan'); // diajukan | diproses | selesai | tidak_disetujui
            $table->text('rejection_reason')->nullable();

            // Signature fields
            $table->string('signature_pemohon')->nullable();
            $table->timestamp('signature_pemohon_at')->nullable();
            $table->string('signature_pengelola_1')->nullable();
            $table->timestamp('signature_pengelola_1_at')->nullable();
            $table->string('signature_pengelola_1_name')->nullable();
            $table->string('signature_driver')->nullable();
            $table->timestamp('signature_driver_at')->nullable();
            $table->string('signature_pengelola_2')->nullable();
            $table->timestamp('signature_pengelola_2_at')->nullable();
            $table->string('signature_pengelola_2_name')->nullable();

            $table->timestamps();
        });

        // Backfill nomor_pengajuan
        $requests = DB::table('transport_requests')->orderBy('created_at')->get();
        $counter = [];

        foreach ($requests as $req) {
            $dateKey = date('ymd', strtotime($req->created_at));
            $counter[$dateKey] = ($counter[$dateKey] ?? 0) + 1;
            $nomor = $dateKey . $counter[$dateKey];

            DB::table('transport_requests')
                ->where('id', $req->id)
                ->update(['nomor_pengajuan' => $nomor]);
        }

        // Update old statuses
        DB::table('transport_requests')
            ->whereIn('status', ['ditolak', 'kadaluarsa'])
            ->update(['status' => 'tidak_disetujui']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_requests');
    }
};