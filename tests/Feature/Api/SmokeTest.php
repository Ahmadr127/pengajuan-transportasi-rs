<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(\Database\Seeders\AclSeeder::class);
    }

    private function makeUser(string $roleName, array $attrs = []): User
    {
        $user = User::factory()->create(array_merge(['password' => bcrypt('secret123')], $attrs));
        $role = \App\Models\Role::where('name', $roleName)->first();
        if ($role) {
            $user->roles()->sync([$role->id]);
        }
        return $user;
    }

    private function tokenFor(User $user): string
    {
        return $user->createToken('api')->plainTextToken;
    }

    public function test_login_returns_token(): void
    {
        $user = $this->makeUser('user', ['username' => 'apitest']);
        $response = $this->postJson('/api/auth/login', [
            'username' => 'apitest',
            'password' => 'secret123',
        ]);
        $response->assertOk()->assertJsonStructure(['success', 'data' => ['token', 'user', 'permissions']]);
        $token = $response->json('data.token');
        $this->withHeader('Authorization', 'Bearer '.$token)->getJson('/api/auth/me')
            ->assertOk()->assertJsonPath('data.user.username', 'apitest');
    }

    public function test_unauthenticated_returns_401(): void
    {
        $this->getJson('/api/users')->assertStatus(401);
    }

    public function test_forbidden_returns_403(): void
    {
        $user = $this->makeUser('user', ['username' => 'regular']);
        $token = $this->tokenFor($user);
        $this->withHeader('Authorization', 'Bearer '.$token)->getJson('/api/users')
            ->assertStatus(403);
    }

    public function test_super_admin_can_list_users(): void
    {
        $admin = $this->makeUser('super-admin', ['username' => 'superadmin']);
        $token = $this->tokenFor($admin);
        $this->withHeader('Authorization', 'Bearer '.$token)->getJson('/api/users')
            ->assertOk()->assertJsonStructure(['success', 'data', 'meta']);
    }

    public function test_create_vehicle(): void
    {
        $admin = $this->makeUser('super-admin', ['username' => 'superadmin2']);
        $token = $this->tokenFor($admin);
        $this->withHeader('Authorization', 'Bearer '.$token)->postJson('/api/vehicles', [
            'name' => 'Mobil Test',
            'type' => 'umum',
            'plate_number' => 'B 1234 TEST',
        ])->assertStatus(201);
    }
}