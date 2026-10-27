<?php

namespace App\Console\Commands;

use App\Services\SimutuOrganisasiImporter;
use Illuminate\Console\Command;

class ImportSimutuOrganisasi extends Command
{
    protected $signature = 'simutu:import-organisasi {--path= : Path ke file JSON snapshot Simutu}';

    protected $description = 'Impor master Unit & Jabatan serta akun user dari snapshot JSON Simutu.';

    public function handle(SimutuOrganisasiImporter $importer): int
    {
        try {
            $stats = $importer->import($this->option('path') ?: null);
        } catch (\Throwable $e) {
            $this->error('Impor gagal: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info("Impor selesai dari {$stats['path']}:");
        $this->line("  Unit    : {$stats['units']}");
        $this->line("  Jabatan : {$stats['roles']}");
        $this->line("  User    : {$stats['users_created']} dibuat, {$stats['users_updated']} diperbarui, {$stats['users_checked']} dicek");

        return self::SUCCESS;
    }
}
