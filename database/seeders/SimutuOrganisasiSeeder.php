<?php

namespace Database\Seeders;

use App\Services\SimutuOrganisasiImporter;
use Illuminate\Database\Seeder;

class SimutuOrganisasiSeeder extends Seeder
{
    public function run(): void
    {
        $path = (string) config('simutu_sync.organisasi_path');

        if (! is_file($path)) {
            $this->warnIfPossible("Snapshot organisasi Simutu tidak ditemukan ({$path}). Seeder dilewati.");

            return;
        }

        $stats = app(SimutuOrganisasiImporter::class)->import($path);

        $this->warnIfPossible(
            "Organisasi Simutu diimpor: {$stats['units']} unit, {$stats['roles']} jabatan, "
            ."{$stats['users_created']} user dibuat, {$stats['users_updated']} user diperbarui."
        );
    }

    private function warnIfPossible(string $message): void
    {
        if ($this->command) {
            $this->command->warn($message);
        }
    }
}
