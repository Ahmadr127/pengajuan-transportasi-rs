<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\LandingResolver;
use App\Services\MenuService;
use Database\Seeders\AclSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(AclSeeder::class);
    }

    public function test_super_admin_can_open_role_and_menu_management(): void
    {
        $user = User::factory()->create(['username' => 'super']);
        $user->syncRolesByName(['super-admin']);

        $this->actingAs($user)
            ->get(route('admin.roles.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('admin.menus.index'))
            ->assertOk();
    }

    public function test_operator_only_gets_permitted_menu_and_buttons(): void
    {
        $role = Role::create([
            'name' => 'operator',
            'label' => 'Operator',
        ]);

        $role->permissions()->sync([
            Permission::where('name', 'transport.request.view')->value('id'),
        ]);

        $user = User::factory()->create(['username' => 'operator']);
        $user->syncRolesByName(['operator']);

        $this->actingAs($user)
            ->get(route('admin.transport.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('admin.roles.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->assertTrue($user->hasPermission('transport.request.view'));
        $this->assertFalse($user->hasPermission('transport.request.approve'));

        $menus = app(MenuService::class)->forUser($user, 'admin');
        $this->assertTrue($menus->contains('key', 'admin.transport'));
        $this->assertFalse($menus->contains('key', 'admin.roles'));
        $this->assertFalse($menus->contains('key', 'admin.users'));
    }

    public function test_regular_user_cannot_access_admin_or_driver_routes(): void
    {
        $user = User::factory()->create(['username' => 'pemohon']);
        $user->syncRolesByName(['user']);

        $this->actingAs($user)
            ->get(route('admin.transport.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('driver.dashboard'))
            ->assertForbidden();
    }

    public function test_landing_route_follows_permission_not_role_name(): void
    {
        $user = User::factory()->create(['username' => 'pemohon2']);
        $user->syncRolesByName(['user']);

        $this->assertSame('dashboard', app(LandingResolver::class)->routeNameFor($user));
    }

    public function test_role_based_menu_can_be_added_without_code_change(): void
    {
        $role = Role::create([
            'name' => 'auditor',
            'label' => 'Auditor',
        ]);

        $role->permissions()->sync([
            Permission::where('name', 'report.view')->value('id'),
        ]);

        $user = User::factory()->create(['username' => 'auditor']);
        $user->syncRolesByName(['auditor']);

        $menus = app(MenuService::class)->forUser($user, 'admin');

        $this->assertTrue($menus->contains('key', 'admin.report'));
        $this->assertFalse($menus->contains('key', 'admin.transport'));
        $this->assertSame('admin', app(MenuService::class)->contextFor($user));
    }

    public function test_landing_falls_back_to_first_permitted_menu(): void
    {
        $role = Role::create([
            'name' => 'reporter',
            'label' => 'Reporter',
        ]);

        $role->permissions()->sync([
            Permission::where('name', 'report.view')->value('id'),
        ]);

        $user = User::factory()->create(['username' => 'reporter']);
        $user->syncRolesByName(['reporter']);

        $this->assertSame(
            'admin.laporan',
            app(LandingResolver::class)->routeNameFor($user)
        );
    }

    public function test_user_without_any_permission_gets_forbidden(): void
    {
        $user = User::factory()->create(['username' => 'tanpa.akses']);

        $this->assertNull(app(LandingResolver::class)->routeNameFor($user));

        $this->actingAs($user)
            ->get(route('home'))
            ->assertForbidden();
    }
}
