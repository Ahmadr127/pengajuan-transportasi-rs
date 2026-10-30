<?php

namespace Tests\Feature\Api;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\AclSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(AclSeeder::class);
    }

    private function tokenFor(string $roleName): array
    {
        $user = User::factory()->create(['username' => 'nav'.str_replace('-', '', $roleName)]);
        $user->syncRolesByName([$roleName]);

        return [$user, $user->createToken('api')->plainTextToken];
    }

    public function test_admin_navigation_contains_dynamic_bottom_nav_with_master_hub(): void
    {
        [$user, $token] = $this->tokenFor('admin');

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/me/navigation')
            ->assertOk()
            ->assertJsonPath('data.context', 'admin')
            ->assertJsonPath('data.landing', 'admin.dashboard');

        $keys = collect($response->json('data.bottom_nav'))->pluck('key')->all();

        $this->assertSame(
            ['admin.dashboard', 'admin.transport', 'admin.report', 'admin.master', 'admin.profile'],
            $keys
        );

        $master = collect($response->json('data.bottom_nav'))->firstWhere('key', 'admin.master');

        $this->assertNotNull($master);
        $this->assertContains('admin.vehicles', collect($master['children'])->pluck('key')->all());
        // Role admin tidak punya user.manage/unit.manage/jabatan.manage, jadi anak menu
        // tersebut tidak boleh bocor ke navigasinya.
        $this->assertNotContains('admin.users', collect($master['children'])->pluck('key')->all());
        $this->assertContains('profile.view', $response->json('data.permissions'));
    }

    public function test_user_navigation_only_contains_user_menus(): void
    {
        [, $token] = $this->tokenFor('user');

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/me/navigation')
            ->assertOk()
            ->assertJsonPath('data.context', 'user');

        $keys = collect($response->json('data.bottom_nav'))->pluck('key')->all();

        $this->assertSame([
            'user.dashboard',
            'user.transport.create',
            'user.transport.history',
            'user.profile',
        ], $keys);

        $this->assertNotContains('admin.dashboard', $keys);
    }

    public function test_custom_role_receives_menu_matching_its_permission_only(): void
    {
        $role = Role::create(['name' => 'operator', 'label' => 'Operator']);

        $permissionId = \App\Models\Permission::query()->where('name', 'transport.request.view')->value('id');
        $role->permissions()->sync([$permissionId]);

        $user = User::factory()->create(['username' => 'navoperator']);
        $user->syncRolesByName(['operator']);
        $token = $user->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/me/navigation')
            ->assertOk();

        $keys = collect($response->json('data.bottom_nav'))->pluck('key')->all();

        $this->assertSame(['admin.transport'], $keys);
        $this->assertSame(['transport.request.view'], $response->json('data.permissions'));
    }

    public function test_navigation_requires_authentication(): void
    {
        $this->getJson('/api/me/navigation')->assertUnauthorized();
    }
}
