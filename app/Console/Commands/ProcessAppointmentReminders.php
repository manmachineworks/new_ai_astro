<?php

namespace App\Console\Commands;

use App\Models\NotificationJob;
use App\Services\AppointmentService;
use Illuminate\Console\Command;

class ProcessAppointmentReminders extends Command
{
    protected $signature = 'appointments:reminders';
    protected $description = 'Dispatch appointment reminders and release expired holds';

    public function handle(AppointmentService $appointments)
    {
        $expired = $appointments->expireStaleHolds();

        $jobs = NotificationJob::pending()
            ->orderBy('scheduled_at')
            ->limit(100)
            ->get();

        foreach ($jobs as $job) {
            \App\Jobs\SendAppointmentReminderJob::dispatch($job->id);
        }

        $this->info("Expired holds released: {$expired}. Reminders queued: {$jobs->count()}");

        return self::SUCCESS;
    }
}
