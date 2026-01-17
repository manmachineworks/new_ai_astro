<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class NotificationDispatcher
{
    protected $templateService;
    protected $throttleService;
    protected $fcmService;

    public function __construct(
        NotificationTemplateService $templateService,
        NotificationThrottleService $throttleService,
        FCMNotificationService $fcmService
    ) {
        $this->templateService = $templateService;
        $this->throttleService = $throttleService;
        $this->fcmService = $fcmService;
    }

    /**
     * Dispatch a notification to a specific user.
     *
     * @param string $type Template Key (e.g. 'wallet_low')
     * @param User $recipient
     * @param array $variables Variables for template
     * @param array $options ['throttle_key', 'throttle_ttl', 'priority', 'deeplink']
     */
    public function dispatch(string $type, User $recipient, array $variables = [], array $options = [])
    {
        // 1. Privacy Check
        PrivacyGuard::assertSafe($variables, $recipient->getRoleNames()->first() ?? 'user');

        // 2. Throttling
        if (isset($options['throttle_key']) && isset($options['throttle_ttl'])) {
            if (!$this->throttleService->attempt($options['throttle_key'], $options['throttle_ttl'])) {
                \Log::info("Notification throttled: {$type} for User {$recipient->id}");
                return;
            }
        }

        // 3. Render Template
        // Default locale from user or 'en'
        $dbLocale = $recipient->deviceTokens()->latest()->value('locale'); // best effort?
        $locale = $dbLocale ?? 'en';

        $content = $this->templateService->render($type, $locale, $variables);

        if (!$content) {
            \Log::warning("Skipping notification: Template {$type} render failed.");
            return;
        }

        $title = $content['title'];
        $body = $content['body'];
        $channels = $content['channels'];

        $notificationId = (string) Str::uuid();

        // 4. Inbox Channel
        if (in_array('inbox', $channels)) {
            Notification::create([
                'id' => $notificationId,
                'recipient_user_id' => $recipient->id,
                'type' => $type,
                'title' => $title,
                'body' => $body,
                'data_json' => $variables, // safe vars
                'status' => 'unread',
                'priority' => $options['priority'] ?? 'normal',
                'created_at' => now(),
            ]);
        }

        // 5. Push Channel
        if (in_array('push', $channels)) {
            // Prepare payload
            $payload = array_merge($variables, [
                'type' => $type,
                'notification_id' => $notificationId,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]);

            if (isset($options['deeplink'])) {
                $payload['deeplink'] = $options['deeplink'];
            }

            // Using existing FCM Service to send
            // Ensure SendPushNotificationJob is compliant or call service directly?
            // "Everything push-related must be queued."
            // Existing FCMService->sendToUser sends directly (synchronous).
            // We should dispatch job. Or FCMService internally queues?
            // Actually, we should dispatch `SendPushNotificationJob`.

            \App\Jobs\SendPushNotificationJob::dispatch(
                $recipient->id,
                $type,
                $payload,
                $title,
                $body
            );

            // Log Event (Queued)
            $this->logEvent($notificationId, $recipient->id, 'push', 'queued');
        }
    }

    protected function logEvent($notifId, $userId, $channel, $status)
    {
        DB::table('notification_delivery_events')->insert([
            'id' => (string) Str::uuid(),
            'notification_id' => $notifId,
            'recipient_user_id' => $userId,
            'channel' => $channel,
            'event' => $status,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
