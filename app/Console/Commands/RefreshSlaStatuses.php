<?php

namespace App\Console\Commands;

use App\Services\SlaService;
use Illuminate\Console\Command;

class RefreshSlaStatuses extends Command
{
    protected $signature = 'bank:sla-refresh';

    protected $description = 'Refreshes active case SLA status and creates required alerts.';

    public function handle(SlaService $sla): int
    {
        $sla->refreshOpenCases();
        $this->info('SLA status berhasil diperbarui.');

        return self::SUCCESS;
    }
}
