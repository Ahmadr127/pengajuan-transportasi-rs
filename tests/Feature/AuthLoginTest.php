<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AclSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(AclSeeder::class);

        // Hindari koneksi DB Simutu saat pengujian.
        config(['simutu_sync.enabled' => false]);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['username' => 'pemohon']);
        $user->syncRolesByName(['user']);

        $this->post(route('login.store'), [
            'username' => 'pemohon',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['username' => 'pemohon']);
        $user->syncRolesByName(['user']);

        $this->post(route('login.store'), [
            'username' => 'pemohon',
            'password' => 'salah',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_disabled_simutu_account_cannot_login(): void
    {
        $user = User::factory()->create([
            'username' => 'nonaktif',
            'simutu_status' => 'non-aktif',
        ]);
        $user->syncRolesByName(['user']);

        $this->post(route('login.store'), [
            'username' => 'nonaktif',
            'password' => 'password',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }
}
