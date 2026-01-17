<?php

namespace App\Jobs;

use App\Services\TopicSubscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncFCMTopicsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $token;
    protected $userId;
    protected $role;
    protected $action; // 'subscribe' or 'unsubscribe'

    public function __construct(string $token, int $userId, string $role, string $action = 'subscribe')
    {
        $this->token = $token;
        $this->userId = $userId;
        $this->role = $role;
        $this->action = $action;
    }

    public function handle(TopicSubscriptionService $subscriptionService): void
    {
        if ($this->action === 'subscribe') {
            // Subscribe to User Topic
            $subscriptionService->subscribeToUserTopic($this->token, $this->userId);

            // Subscribe to Role-specific Topic (Astrologer only per requirements)
            if ($this->role === 'Astrologer') {
                $subscriptionService->subscribeToAstrologerTopic($this->token, $this->userId);
            }

            // Optional: Subscribe to global announcements?
            // $subscriptionService->subscribeToTopic($this->token, 'announcements');

        } else {
            // Unsubscribe
            $subscriptionService->unsubscribeFromUserTopic($this->token, $this->userId);

            if ($this->role === 'Astrologer') {
                $subscriptionService->unsubscribeFromAstrologerTopic($this->token, $this->userId);
            }
        }
    }
}
