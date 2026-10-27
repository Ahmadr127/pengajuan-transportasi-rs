<?php

namespace Tests\Feature;

use App\Models\Unit;
use App\Models\User;
use Database\Seeders\AclSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMasterDataCrudTest extends TestCase
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
        $user->syncRolesByName(['super-admin']);

        return $user;
    }

    public function test_admin_can_view_and_create_unit(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.units.index'))->assertOk();

        $this->actingAs($admin)->post(route('admin.units.store'), [
            'kode_unit' => 'UNIT900',
            'nama_unit' => 'Unit Uji',
            'status_unit' => 'aktif',
        ])->assertRedirect(route('admin.units.index'));

        $this->assertDatabaseHas('units', ['kode_unit' => 'UNIT900', 'nama_unit' => 'Unit Uji']);
    }

    public function test_admin_can_update_and_delete_unit(): void
    {
        $admin = $this->admin();
        $unit = Unit::create(['kode_unit' => 'UNIT901', 'nama_unit' => 'Unit Lama', 'status_unit' => 'aktif']);

        $this->actingAs($admin)->put(route('admin.units.update', $unit), [
            'kode_unit' => 'UNIT901',
            'nama_unit' => 'Unit Baru',
            'status_unit' => 'non-aktif',
        ])->assertRedirect(route('admin.units.index'));

        $this->assertDatabaseHas('units', [
            'id' => $unit->id,
            'nama_unit' => 'Unit Baru',
            'status_unit' => 'non-aktif',
        ]);

        $this->actingAs($admin)->delete(route('admin.units.destroy', $unit))
            ->assertRedirect(route('admin.units.index'));

        $this->assertDatabaseMissing('units', ['id' => $unit->id]);
    }

    public function test_admin_can_create_job_role(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.job-roles.index'))->assertOk();

        $this->actingAs($admin)->post(route('admin.job-roles.store'), [
            'nama_role' => 'Supervisor Uji',
        ])->assertRedirect(route('admin.job-roles.index'));

        $this->assertDatabaseHas('job_roles', ['nama_role' => 'Supervisor Uji']);
    }

    public function test_user_without_permission_is_forbidden(): void
    {
        $user = User::factory()->create(['username' => 'pemohon']);
        $user->syncRolesByName(['user']);

        $this->actingAs($user)->get(route('admin.units.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.job-roles.index'))->assertForbidden();
    }
}
