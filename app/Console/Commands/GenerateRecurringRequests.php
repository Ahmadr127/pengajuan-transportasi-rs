<?php

namespace App\Console\Commands;

use App\Models\RecurringTransportTemplate;
use App\Services\RecurringRequestService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateRecurringRequests extends Command
{
    protected $signature = 'app:generate-recurring-requests';

    protected $description = 'Generate daily transport requests from active recurring templates';

    public function __construct(private readonly RecurringRequestService $recurring)
    {
        parent::__construct();
    }

    public function handle()
    {
        // Gunakan timezone aplikasi agar tidak meleset 1 hari di server UTC
        $today = Carbon::today(config('app.timezone'));

        $activeTemplates = RecurringTransportTemplate::where('is_active', true)
            ->whereDate('start_date', '<=', $today->toDateString())
            ->whereDate('end_date', '>=', $today->toDateString())
            ->with('user')
            ->get();

        $count = 0;

        foreach ($activeTemplates as $template) {
            $result = $this->recurring->generateDailyFromTemplate($template);

            switch ($result['status']) {
                case 'skipped_duplicate':
                    $skipNama = $template->user ? $template->user->full_name : 'user';
                    $this->line("Skip (duplikat) template #{$template->id} — {$skipNama} jam {$template->jam}");
                    break;

                case 'skipped_no_user':
                    $this->warn("Skip template #{$template->id} — user tidak ditemukan.");
                    break;

                case 'created':
                    $count++;
                    $nama = $template->user->full_name ?? "template #{$template->id}";
                    $this->info("Dibuat #{$count} — {$nama} ({$template->jenis}, jam {$template->jam})");
                    break;

                case 'skipped_day':
                default:
                    break;
            }
        }

        $this->info("✓ Total {$count} pengajuan berulang dibuat untuk hari ini ({$today->format('d/m/Y')}).");

        return Command::SUCCESS;
    }
}
