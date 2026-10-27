<?php
namespace Tests\Feature;
use App\Models\User;
use Database\Seeders\AclSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TmpDriverCreateTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(AclSeeder::class);
        $user = User::factory()->create(['username' => 'admin']);
        $user->syncRolesByName(['super-admin']);
        $this->actingAs($user);
    }
    public function test_page_shows_driver_user(): void
    {
        $u = User::factory()->create(['username' => 'driver.bambang']);
        $u->syncRolesByName(['driver']);
        dump('roles:', $u->roles->pluck('name')->toArray());
        dump('perms:', $u->permissionNames());
        dump('driverUsers:', User::withPermission('driver.task.view')->pluck('username')->toArray());
        $response = $this->get(route('admin.drivers.create'));
        $response->assertOk();
        $response->assertSee('driver.bambang');
    }
}
