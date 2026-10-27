<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\TransportRequest;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\AclSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransportStatusFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(AclSeeder::class);
    }

    private function admin(): User
    {
        $user = User::factory()->create(['username' => 'admin1']);
        $user->syncRolesByName(['admin']);

        return $user;
    }

    public function test_admin_can_approve_request(): void
    {
        Vehicle::factory()->create(['type' => 'umum', 'is_active' => true]);
        $tr = TransportRequest::factory()->create(['jenis' => 'umum', 'status' => 'diajukan']);

        $this->actingAs($this->admin())
            ->put(route('admin.transport.update', $tr), ['status' => 'diproses'])
            ->assertRedirect(route('admin.transport.show', $tr));

        $tr->refresh();
        $this->assertSame('diproses', $tr->status);
        $this->assertNotNull($tr->signature_pengelola_1);
        $this->assertNotNull($tr->signature_pengelola_1_at);
    }

    public function test_admin_cannot_approve_when_all_units_used(): void
    {
        Vehicle::factory()->create(['type' => 'umum', 'is_active' => true]);

        $tr = TransportRequest::factory()->create([
            'jenis' => 'umum',
            'status' => 'diajukan',
            'tanggal' => '2026-03-01',
            'jam' => '08:00',
            'tanggal_sampai' => '2026-03-01',
            'jam_sampai' => '10:00',
        ]);

        TransportRequest::factory()->create([
            'jenis' => 'umum',
            'status' => 'diproses',
            'tanggal' => '2026-03-01',
            'jam' => '08:00',
            'tanggal_sampai' => '2026-03-01',
            'jam_sampai' => '10:00',
        ]);

        $this->actingAs($this->admin())
            ->put(route('admin.transport.update', $tr), ['status' => 'diproses'])
            ->assertSessionHasErrors('status');
    }

    public function test_admin_can_reject_request(): void
    {
        $tr = TransportRequest::factory()->create(['status' => 'diajukan']);

        $this->actingAs($this->admin())
            ->put(route('admin.transport.update', $tr), [
                'status' => 'tidak_disetujui',
                'rejection_reason' => 'Tidak diperlukan.',
            ])
            ->assertRedirect(route('admin.transport.show', $tr));

        $this->assertDatabaseHas('transport_requests', [
            'id' => $tr->id,
            'status' => 'tidak_disetujui',
            'rejection_reason' => 'Tidak diperlukan.',
        ]);
    }

    public function test_admin_can_assign_unit_and_driver(): void
    {
        $vehicle = Vehicle::factory()->create([
            'type' => 'umum',
            'name' => 'umum_test',
            'plate_number' => 'B 9999 ZZ',
            'last_km' => null,
        ]);
        $driver = Driver::factory()->create();
        $tr = TransportRequest::factory()->create(['jenis' => 'umum', 'status' => 'diproses']);

        $this->actingAs($this->admin())
            ->put(route('admin.transport.update', $tr), [
                'status' => 'digunakan',
                'unit_mobil' => 'umum_test',
                'driver_id' => $driver->id,
                'km_awal' => 100,
            ])
            ->assertRedirect(route('admin.transport.show', $tr));

        $tr->refresh();
        $this->assertSame('digunakan', $tr->status);
        $this->assertSame('umum_test', $tr->unit_mobil);
        $this->assertSame('B 9999 ZZ', $tr->plat_nomor);
        $this->assertNotNull($tr->signature_driver);
    }

    public function test_admin_can_complete_request_and_updates_vehicle_km(): void
    {
        $vehicle = Vehicle::factory()->create([
            'type' => 'umum',
            'name' => 'umum_test2',
            'last_km' => 90,
        ]);
        $tr = TransportRequest::factory()->create([
            'jenis' => 'umum',
            'status' => 'digunakan',
            'unit_mobil' => 'umum_test2',
            'km_awal' => 100,
        ]);

        $this->actingAs($this->admin())
            ->put(route('admin.transport.update', $tr), [
                'status' => 'selesai',
                'km_akhir' => 150,
                'jam_kedatangan' => '10:30',
            ])
            ->assertRedirect(route('admin.transport.show', $tr));

        $this->assertDatabaseHas('transport_requests', ['id' => $tr->id, 'status' => 'selesai']);
        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id, 'last_km' => 150]);
    }

    public function test_admin_km_akhir_must_exceed_km_awal(): void
    {
        $tr = TransportRequest::factory()->create([
            'jenis' => 'umum',
            'status' => 'digunakan',
            'unit_mobil' => 'umum_x',
            'km_awal' => 100,
        ]);

        $this->actingAs($this->admin())
            ->put(route('admin.transport.update', $tr), [
                'status' => 'selesai',
                'km_akhir' => 50,
                'jam_kedatangan' => '10:30',
            ])
            ->assertSessionHasErrors('km_akhir');
    }
}
