<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserMembership;
use App\Models\MembershipEvent;

class ExpireMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'memberships:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire memberships that have passed their end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired memberships...');

        $expired = UserMembership::where('status', 'active')
            ->where('ends_at_utc', '<', now())
            ->get();

        $count = 0;
        foreach ($expired as $membership) {
            $membership->update(['status' => 'expired']);
            MembershipEvent::create([
                'user_membership_id' => $membership->id,
                'event_type' => 'expired',
                'meta_json' => ['reason' => 'auto_expire_cron']
            ]);
            $count++;
        }

        $this->info("Expired {$count} memberships.");
    }
}
