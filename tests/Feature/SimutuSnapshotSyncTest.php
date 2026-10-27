<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SimutuUserSyncService;
use Database\Seeders\AclSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\BuildsSimutuSnapshot;
use Tests\TestCase;

class SimutuSnapshotSyncTest extends TestCase
{
    use BuildsSimutuSnapshot, RefreshDatabase;

    private ?string $snapshotPath = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AclSeeder::class);

        $this->snapshotPath = $this->makeSnapshotFixture();

        config([
            'simutu_sync.enabled' => true,
            'simutu_sync.source' => 'snapshot',
            'simutu_sync.organisasi_path' => $this->snapshotPath,
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->snapshotPath && is_file($this->snapshotPath)) {
            @unlink($this->snapshotPath);
        }

        parent::tearDown();
    }

    public function test_sync_creates_local_users_from_snapshot(): void
    {
        $stats = app(SimutuUserSyncService::class)->sync();

        $this->assertSame(2, $stats['created']);

        $adit = User::where('username', 'aditya.pratama')->first();
        $this->assertNotNull($adit);
        $this->assertSame('IT', $adit->unit_kerja);
        $this->assertSame('Koordinator', $adit->jabatan);
        $this->assertSame('aktif', $adit->simutu_status);
        $this->assertSame('Non Medis', $adit->profesi);
        $this->assertSame('20263114', $adit->nip);
        $this->assertEquals('101', $adit->simutu_id);
        $this->assertSame(1, $adit->priority_level); // Koordinator => prioritas tinggi
        $this->assertTrue(Hash::check('password', $adit->password));
    }

    public function test_sync_for_username_resolves_unit_and_role_from_snapshot(): void
    {
        $budi = app(SimutuUserSyncService::class)->syncForUsername('budi.santoso');

        $this->assertNotNull($budi);
        $this->assertSame('Transportasi & kurir', $budi->unit_kerja);
        $this->assertSame('Administrator', $budi->jabatan);
        $this->assertSame('non-aktif', $budi->simutu_status);
        $this->assertSame(0, $budi->priority_level);
    }
}
