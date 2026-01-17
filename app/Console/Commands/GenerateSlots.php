<?php

namespace App\Console\Commands;

use App\Services\SlotGeneratorService;
use App\Models\AstrologerProfile;
use Illuminate\Console\Command;

class GenerateSlots extends Command
{
    protected $signature = 'slots:generate {--days=14} {--astrologer=}';
    protected $description = 'Generate appointment slots from availability rules';

    public function handle(SlotGeneratorService $slotGenerator)
    {
        $days = (int) $this->option('days');
        $astrologerId = $this->option('astrologer');

        $this->info("Generating slots for next {$days} days...");

        if ($astrologerId) {
            // Generate for specific astrologer
            $profile = AstrologerProfile::findOrFail($astrologerId);
            $count = $slotGenerator->generateForAstrologer($profile, $days);
            $this->info("Generated {$count} slots for {$profile->display_name}");
        } else {
            // Generate for all astrologers
            $results = $slotGenerator->generateForAllAstrologers($days);
            $totalSlots = array_sum($results);
            $astrologerCount = count($results);

            $this->info("Generated {$totalSlots} slots for {$astrologerCount} astrologers");

            // Show breakdown
            if ($this->output->isVerbose()) {
                foreach ($results as $profileId => $count) {
                    $profile = AstrologerProfile::find($profileId);
                    $this->line("  - {$profile->display_name}: {$count} slots");
                }
            }
        }

        $this->info('Slot generation complete!');
        return 0;
    }
}
