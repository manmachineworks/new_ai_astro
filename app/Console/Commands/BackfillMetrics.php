<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;

class BackfillMetrics extends Command
{
    protected $signature = 'metrics:backfill {start? : Start date YYYY-MM-DD} {end? : End date YYYY-MM-DD} {--from= : Start date YYYY-MM-DD} {--to= : End date YYYY-MM-DD}';
    protected $description = 'Backfill daily metrics for a date range (IST)';

    public function handle()
    {
        $start = $this->option('from') ?: $this->argument('start');
        $end = $this->option('to') ?: $this->argument('end');

        if (!$start || !$end) {
            $this->error('Provide --from and --to (or start/end arguments).');
            return self::FAILURE;
        }

        $period = CarbonPeriod::create($start, $end);

        $this->info("Backfilling metrics from {$start} to {$end}");

        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $this->comment("Processing {$dateStr}...");
            $this->call('metrics:daily', ['date' => $dateStr]);
        }

        $this->info("Backfill completed for " . $period->count() . " days.");
    }
}
