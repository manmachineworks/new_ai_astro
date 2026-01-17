<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Models\NotificationJob;
use App\Services\FirebaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAppointmentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $notificationJobId;

    public function __construct(int $notificationJobId)
    {
        $this->notificationJobId = $notificationJobId;
    }

    public function handle(FirebaseService $firebase): void
    {
        $notification = NotificationJob::with('recipientUser')->find($this->notificationJobId);
        if (!$notification || $notification->status !== 'pending') {
            return;
        }

        $appointment = $this->resolveAppointment($notification);
        if (!$appointment) {
            $notification->markAsFailed('Appointment not found');
            return;
        }

        $recipient = $notification->recipientUser;
        if (!$recipient) {
            $notification->markAsFailed('Recipient not found');
            return;
        }

        $tokens = $recipient->deviceTokens()->pluck('token')->toArray();
        if (empty($tokens)) {
            $notification->markAsFailed('No device tokens');
            return;
        }

        $timezone = config('appointments.default_timezone', 'Asia/Kolkata');
        $startAt = $appointment->start_at_utc->copy()->tz($timezone)->format('M d, h:i A');

        $isAstrologer = $appointment->astrologerProfile?->user_id === $recipient->id;
        $title = 'Appointment Reminder';
        $body = $isAstrologer
            ? "Upcoming appointment at {$startAt}."
            : "Your appointment starts at {$startAt}.";

        $sent = false;
        foreach ($tokens as $token) {
            $sent = $firebase->sendNotification($token, $title, $body, [
                'type' => 'appointment_reminder',
                'appointment_id' => $appointment->id,
            ]) || $sent;
        }

        if ($sent) {
            $notification->markAsSent();
            $appointment->logEvent('reminder_sent', 'system', null, [
                'notification_id' => $notification->id,
                'recipient_user_id' => $recipient->id,
            ]);
        } else {
            $notification->markAsFailed('FCM send failed');
        }
    }

    protected function resolveAppointment(NotificationJob $notification): ?Appointment
    {
        if ($notification->reference_type && class_exists($notification->reference_type)) {
            $model = $notification->reference_type;
            $record = $model::find($notification->reference_id);
            return $record instanceof Appointment ? $record : null;
        }

        return Appointment::find($notification->reference_id);
    }
}
