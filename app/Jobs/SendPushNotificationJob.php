<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\FCMNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $type;
    protected $payload;
    protected $title;
    protected $body;

    public function __construct(int $userId, string $type, array $payload, ?string $title = null, ?string $body = null)
    {
        $this->userId = $userId;
        $this->type = $type;
        $this->payload = $payload;
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array
     */
    public function backoff()
    {
        return [10, 30, 60];
    }

    public function handle(FCMNotificationService $fcmService)
    {
        $user = User::find($this->userId);

        if (!$user) {
            \Log::warning("Skipping Push: User {$this->userId} not found.");
            return;
        }

        try {
            $fcmService->sendToUser($user, $this->type, $this->payload, $this->title, $this->body);
        } catch (\Exception $e) {
            \Log::error("Push Job Failed: " . $e->getMessage());
            $this->release(10); // Retry in 10s if service fails
        }
    }
}
