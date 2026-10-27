<?php

namespace Tests\Feature\Api;

use App\Models\Driver;
use App\Models\TransportRequest;
use App\Models\User;
use Database\Seeders\AclSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverApiTest extends TestCase
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
        $user = User::factory()->create(['username' => 'apidriver']);
        $user->syncRolesByName(['driver']);
        $driver = Driver::factory()->create(['user_id' => $user->id]);

        return [$user, $driver];
    }

    public function test_driver_index_only_returns_assigned_tasks(): void
    {
        [$user, $driver] = $this->driverUser();

        $mine = TransportRequest::factory()->create([
            'jenis' => 'umum',
            'status' => 'digunakan',
            'driver_id' => $driver->id,
        ]);

        // milestone untuk driver lain
        $otherDriver = Driver::factory()->create();
        TransportRequest::factory()->create([
            'jenis' => 'umum',
            'status' => 'digunakan',
            'driver_id' => $otherDriver->id,
        ]);

        $token = $user->createToken('api')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/transport-requests?status=digunakan')
            ->assertOk()
            ->assertJsonPath('data.0.id', $mine->id)
            ->assertJsonCount(1, 'data');
    }

    public function test_driver_dashboard_returns_driver_summary(): void
    {
        [$user, $driver] = $this->driverUser();

        TransportRequest::factory()->create([
            'jenis' => 'umum',
            'status' => 'digunakan',
            'driver_id' => $driver->id,
        ]);
        TransportRequest::factory()->create([
            'jenis' => 'umum',
            'status' => 'selesai',
            'driver_id' => $driver->id,
        ]);

        $token = $user->createToken('api')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonPath('data.role', 'driver')
            ->assertJsonPath('data.tugas_aktif', 1)
            ->assertJsonPath('data.tugas_selesai', 1)
            ->assertJsonPath('data.total_tugas', 2);
    }
}
