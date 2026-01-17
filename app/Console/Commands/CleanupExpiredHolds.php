<?php

namespace App\Console\Commands;

use App\Services\AppointmentService;
use Illuminate\Console\Command;

class CleanupExpiredHolds extends Command
{
    protected $signature = 'appointments:cleanup-holds';
    protected $description = 'Release expired appointment slot holds';

    public function handle(AppointmentService $appointments)
    {
        $count = $appointments->expireStaleHolds();

        $this->info("Released {$count} expired holds");
        return 0;
    }
}
