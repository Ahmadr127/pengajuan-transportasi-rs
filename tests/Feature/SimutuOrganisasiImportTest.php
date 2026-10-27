<?php

namespace Tests\Feature;

use App\Models\JobRole;
use App\Models\Unit;
use App\Models\User;
use App\Services\SimutuOrganisasiImporter;
use Database\Seeders\AclSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\Concerns\BuildsSimutuSnapshot;
use Tests\TestCase;

class SimutuOrganisasiImportTest extends TestCase
{
    use BuildsSimutuSnapshot, RefreshDatabase;

    private string $snapshotPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AclSeeder::class);
        $this->snapshotPath = $this->makeSnapshotFixture();
    }

    protected function tearDown(): void
    {
        if (is_file($this->snapshotPath)) {
            @unlink($this->snapshotPath);
        }

        parent::tearDown();
    }

    public function test_imports_units_job_roles_and_users_from_snapshot(): void
    {
        $stats = app(SimutuOrganisasiImporter::class)->import($this->snapshotPath);

        $this->assertSame(2, $stats['units']);
        $this->assertSame(2, $stats['roles']);
        $this->assertSame(2, $stats['users_created']);

        $this->assertDatabaseHas('units', [
            'simutu_id' => '9',
            'kode_unit' => 'UNIT009',
            'nama_unit' => 'Transportasi & kurir',
            'status_unit' => 'aktif',
        ]);
        $this->assertDatabaseHas('job_roles', [
            'simutu_id' => '5',
            'nama_role' => 'Koordinator',
        ]);
        $this->assertDatabaseHas('users', [
            'username' => 'aditya.pratama',
            'unit_kerja' => 'IT',
            'jabatan' => 'Koordinator',
            'simutu_status' => 'aktif',
        ]);
    }

    public function test_import_is_idempotent(): void
    {
        $importer = app(SimutuOrganisasiImporter::class);
        $importer->import($this->snapshotPath);

        $stats = $importer->import($this->snapshotPath);

        $this->assertSame(2, Unit::count());
        $this->assertSame(2, JobRole::count());
        $this->assertSame(2, User::count());
        $this->assertSame(0, $stats['users_created']);
        $this->assertSame(2, $stats['users_updated']);
    }

    public function test_missing_file_throws(): void
    {
        $this->expectException(RuntimeException::class);

        app(SimutuOrganisasiImporter::class)->import(base_path('tests/Fixtures/tidak-ada.json'));
    }
}
