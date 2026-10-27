<?php

namespace Database\Factories;

use App\Models\TransportRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransportRequest>
 */
class TransportRequestFactory extends Factory
{
    protected $model = TransportRequest::class;

    public function definition(): array
    {
        $tanggal = now()->addDay()->toDateString();

        return [
            'user_id' => User::factory(),
            'jenis' => 'umum',
            'keperluan' => fake()->sentence(3),
            'prioritas' => 'biasa',
            'tanggal' => $tanggal,
            'jam' => '08:00',
            'tanggal_sampai' => $tanggal,
            'jam_sampai' => '10:00',
            'kontak' => '081234567890',
            'pemohon_nama' => fake()->name(),
            'pemohon_unit' => 'Unit Kerja',
            'alamat_tujuan' => fake()->address(),
            'status' => 'diajukan',
            'nomor_pengajuan' => now()->format('ymd').fake()->unique()->numerify('####'),
        ];
    }

    public function ambulance(): static
    {
        return $this->state(fn () => [
            'jenis' => 'ambulance',
            'keperluan' => 'antar',
            'pasien_nama' => fake()->name(),
            'pasien_no_rm' => fake()->numerify('######'),
        ]);
    }
}
