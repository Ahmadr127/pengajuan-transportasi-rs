<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\TransportRequest;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\AclSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverTaskFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(AclSeeder::class);
    }

    private function driverUser(): array
    {
        $user = User::factory()->create(['username' => 'supir1']);
        $user->syncRolesByName(['driver']);

        $driver = Driver::factory()->create(['user_id' => $user->id]);

        return [$user, $driver];
    }

    public function test_driver_can_complete_task(): void
    {
        [$user, $driver] = $this->driverUser();

        $vehicle = Vehicle::factory()->create(['type' => 'umum', 'name' => 'umum_drv', 'last_km' => null]);

        $tr = TransportRequest::factory()->create([
            'jenis' => 'umum',
            'status' => 'digunakan',
            'driver_id' => $driver->id,
            'unit_mobil' => 'umum_drv',
            'km_awal' => 100,
        ]);

        $this->actingAs($user)
            ->post(route('driver.complete', $tr), [
                'km_akhir' => 200,
                'jam_kedatangan' => '12:00',
            ])
            ->assertRedirect(route('driver.dashboard'));

        $this->assertDatabaseHas('transport_requests', ['id' => $tr->id, 'status' => 'selesai']);
        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id, 'last_km' => 200]);
    }

    public function test_driver_can_cancel_task(): void
    {
        [$user, $driver] = $this->driverUser();

        $tr = TransportRequest::factory()->create([
            'jenis' => 'umum',
            'status' => 'digunakan',
            'driver_id' => $driver->id,
        ]);

        $this->actingAs($user)
            ->post(route('driver.cancel', $tr), ['rejection_reason' => 'Kendaraan rusak.'])
            ->assertRedirect(route('driver.dashboard'));

        $this->assertDatabaseHas('transport_requests', [
            'id' => $tr->id,
            'status' => 'tidak_disetujui',
            'rejection_reason' => 'Kendaraan rusak.',
        ]);
    }
}
