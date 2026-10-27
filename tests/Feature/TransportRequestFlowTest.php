<?php

namespace Tests\Feature;

use App\Models\TransportRequest;
use App\Models\User;
use Database\Seeders\AclSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransportRequestFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(AclSeeder::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function pemohon(): User
    {
        $user = User::factory()->create(['username' => 'pemohon', 'unit_kerja' => 'IGD']);
        $user->syncRolesByName(['user']);

        return $user;
    }

    public function test_user_can_create_umum_request(): void
    {
        $user = $this->pemohon();

        $response = $this->actingAs($user)->post(route('pengajuan.umum.store'), [
            'tanggal' => '2026-02-10',
            'jam' => '09:00',
            'tanggal_sampai' => '2026-02-10',
            'jam_sampai' => '11:00',
            'prioritas' => 'biasa',
            'alamat_tujuan' => 'Jl. Merdeka No. 1',
            'keperluan' => 'Rapat koordinasi',
            'keterangan' => 'Bawa berkas',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transport_requests', [
            'user_id' => $user->id,
            'jenis' => 'umum',
            'keperluan' => 'Rapat koordinasi',
            'status' => 'diajukan',
        ]);
    }

    public function test_duplicate_umum_submission_is_blocked(): void
    {
        $user = $this->pemohon();

        // Simulasikan baris tersimpan: kolom `date` berisi 'Y-m-d' (seperti di Postgres),
        // karena cast `date` pada sqlite menyimpan 'Y-m-d H:i:s'.
        DB::table('transport_requests')->insert([
            'user_id' => $user->id,
            'jenis' => 'umum',
            'prioritas' => 'biasa',
            'tanggal' => '2026-02-10',
            'jam' => '09:00',
            'kontak' => '',
            'keperluan' => 'Rapat koordinasi',
            'status' => 'diajukan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('pengajuan.umum.store'), [
                'tanggal' => '2026-02-10',
                'jam' => '09:00',
                'tanggal_sampai' => '2026-02-10',
                'jam_sampai' => '11:00',
                'prioritas' => 'biasa',
                'alamat_tujuan' => 'Jl. Merdeka No. 1',
                'keperluan' => 'Rapat koordinasi',
            ])
            ->assertSessionHasErrors('duplicate');
    }

    public function test_user_can_create_ambulance_request(): void
    {
        $user = $this->pemohon();

        $response = $this->actingAs($user)->post(route('pengajuan.ambulance.store'), [
            'purpose' => 'antar',
            'pasien_nama' => 'Budi Santoso',
            'pasien_no_rm' => '123456',
            'alamat_pasien' => 'Jl. Pasien No. 2',
            'tanggal' => '2026-02-10',
            'jam' => '09:00',
            'tanggal_sampai' => '2026-02-10',
            'jam_sampai' => '11:00',
            'prioritas' => 'segera',
            'alamat_tujuan' => 'RS Umum Daerah',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transport_requests', [
            'user_id' => $user->id,
            'jenis' => 'ambulance',
            'keperluan' => 'antar',
            'pasien_nama' => 'Budi Santoso',
            'alamat_asal' => 'RS',
            'alamat_tujuan' => 'RS Umum Daerah',
        ]);
    }

    public function test_availability_check_returns_json(): void
    {
        $user = $this->pemohon();

        $this->actingAs($user)
            ->get(route('pengajuan.umum.check', [
                'tanggal' => '2026-02-10',
                'jam' => '09:00',
                'tanggal_sampai' => '2026-02-10',
                'jam_sampai' => '11:00',
            ]))
            ->assertOk()
            ->assertJsonStructure(['available', 'total_units', 'used_units', 'available_units']);

        $this->actingAs($user)
            ->get(route('pengajuan.ambulance.check', [
                'tanggal' => '2026-02-10',
                'jam' => '09:00',
                'tanggal_sampai' => '2026-02-10',
                'jam_sampai' => '11:00',
            ]))
            ->assertOk()
            ->assertJsonStructure(['available', 'total_units', 'used_units', 'available_units']);
    }

    public function test_recurring_umum_creates_template_and_today_request(): void
    {
        Carbon::setTestNow('2026-01-05 08:00:00'); // Senin (N=1)

        $user = $this->pemohon();

        $response = $this->actingAs($user)->post(route('pengajuan.umum.store'), [
            'is_recurring' => 1,
            'tanggal' => null,
            'jam' => '09:00',
            'tanggal_sampai' => null,
            'jam_sampai' => '11:00',
            'prioritas' => 'biasa',
            'alamat_tujuan' => 'Jl. Merdeka No. 1',
            'keperluan' => 'Rapat rutin',
            'recurring_hari' => [1],
        ]);

        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas('recurring_success');

        $this->assertDatabaseCount('recurring_transport_templates', 1);

        $created = TransportRequest::where('user_id', $user->id)->first();
        $this->assertNotNull($created);
        $this->assertSame('umum', $created->jenis);
        $this->assertSame('2026-01-05', $created->tanggal->format('Y-m-d'));
    }
}
