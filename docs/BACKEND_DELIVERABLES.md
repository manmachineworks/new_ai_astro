# Backend Deliverables

This document summarizes the backend implementation, core schema, and API surface for the Astrologer Platform.

## 1) Database Schema (Core Tables + Relationships)

- users: primary identity table; roles via spatie/permission; wallet_balance; blocked_at/blocked_until; firebase_uid.
- user_profiles: optional extended profile data; belongs to users.
- astrologer_profiles: belongs to users; skills/languages/experience; verification/visibility; call/chat/appointment toggles.
- astrologer_pricing_histories: captures changes in call/chat pricing for astrologer_profiles.
- availability_rules + availability_exceptions: weekly schedules and exceptions/leaves for astrologer_profiles.
- appointment_slots + appointments + appointment_events: booking flow and status history.
- wallet_transactions + wallet_holds: ledger entries and holds; user_id foreign key.
- payment_orders: PhonePe wallet recharge orders; status and provider ids.
- webhook_events: centralized webhook payload logging, processing status, attempts.
- call_sessions: call lifecycle; references user + astrologer_profile; billing snapshot + settlement metadata.
- chat_sessions + chat_message_charges: chat session metadata + per-message billing.
- ai_chat_sessions + ai_chat_messages + ai_message_charges: AI chat sessions, messages, and billing.
- astrologer_earnings_ledger + earnings_adjustments: earnings tracking and reversals.
- reviews: user reviews for astrologers; moderation status.
- support_tickets + ticket_messages: user support/ticket workflow.
- disputes + dispute_events + refunds: complaints, dispute lifecycle, and wallet refunds.

## 2) API Routes (Key Endpoints)

Authentication
- Uses Firebase ID token (Bearer) via `firebase.auth` middleware.
- Phone login (web) via `POST /auth/phone/verify`.

User Profile
- `GET /api/profile`
- `PUT /api/profile`

Wallet & Payments
- `GET /api/wallet/balance`
- `GET /api/wallet/transactions`
- `POST /api/wallet/recharge`
- `POST /api/webhooks/phonepe`
- `POST /api/withdrawals`

Calls (CallerDesk)
- `POST /api/call/initiate`
- `POST /api/webhooks/callerdesk`

Chat (Firebase)
- `POST /api/chat/initiate`
- `POST /api/chat/end`

AI Chat (AstrologyAPI)
- `POST /api/ai/chat` (expects `session_id`)
- `GET /api/ai/history`

Appointments
- `GET /api/appointments`
- `POST /api/appointments/hold`
- `POST /api/appointments`
- `POST /api/appointments/{id}/cancel`

Reviews
- `GET /api/astrologers/{id}/reviews`
- `POST /api/astrologers/{id}/reviews`
- `PUT /api/reviews/{review}`
- `DELETE /api/reviews/{review}`
- `PUT /api/reviews/{review}/status` (requires `manage_reviews`)

Disputes & Support
- `GET /api/disputes`
- `POST /api/disputes`
- `GET /api/disputes/{id}`
- `GET /api/support`
- `POST /api/support`
- `POST /api/support/{id}/messages`

Admin Reporting APIs (JSON + CSV export)
- `GET /api/admin/reports/dashboard`
- `GET /api/admin/reports/revenue`
- `GET /api/admin/reports/revenue/items`
- `GET /api/admin/reports/recharges`
- `GET /api/admin/reports/calls`
- `GET /api/admin/reports/chats`
- `GET /api/admin/reports/ai`
- `GET /api/admin/reports/astrologers`
- `GET /api/admin/reports/refunds`

## 3) Controllers / Services / Jobs Structure

Controllers (selected)
- `App\Http\Controllers\PaymentController`
- `App\Http\Controllers\CallController`
- `App\Http\Controllers\ChatController`
- `App\Http\Controllers\AiChatController`
- `App\Http\Controllers\AppointmentController`
- `App\Http\Controllers\ReviewController`
- `App\Http\Controllers\DisputeController`
- `App\Http\Controllers\SupportTicketController`
- `App\Http\Controllers\Admin\ReportingController`

Services
- `App\Services\PhonePeService`
- `App\Services\CallerDeskClient`
- `App\Services\AstrologyApiClient`
- `App\Services\FirebaseAuthService`
- `App\Services\WalletService`
- `App\Services\DisputeService`
- `App\Services\RefundService`
- `App\Services\WebhookPayloadMasker`

Jobs
- `App\Jobs\ProcessPhonePeWebhook`
- `App\Jobs\ProcessCallerDeskWebhook`
- `App\Jobs\ChatBillingJob` (legacy per-minute billing)
- `App\Jobs\ChargeActiveChatSessionsJob`
- `App\Console\Commands\ComputeDailyMetrics` (reporting)

## 4) Webhook Handlers

PhonePe
- Endpoint: `POST /api/webhooks/phonepe`
- Signature verification via `PhonePeService::verifyCallback`.
- Webhook payload logged in `webhook_events` with masked headers/payload.
- Processing via `ProcessPhonePeWebhook` job (idempotent wallet credit).

CallerDesk
- Endpoint: `POST /api/webhooks/callerdesk`
- Signature verification via `CallerDeskClient::verifyWebhookSignature`.
- Webhook payload logged in `webhook_events` with masked headers/payload.
- Processing via `ProcessCallerDeskWebhook` job (billing + ledger).

## 5) Validation & Safety Rules (Examples)

- Wallet recharge: `amount` must be numeric >= 1.
- Call initiation: `astrologer_id` required; pricing and min wallet checked.
- Chat initiation: `astrologer_id` required; min wallet check enforced.
- AI chat: `message` max length; daily limit; safety filter; auto-refund on provider failure.
- Reviews: rating 1-5; comment length capped.

## 6) Privacy Enforcement

- `UserResource` masks phone/email for non-admin viewers and removes PII for astrologers.
- Astrologer-facing views use masked identifiers (e.g., `User #1234`).
- Firebase rules in `firestore.rules` block PII fields.
- Webhook payload logs are masked via `WebhookPayloadMasker`.

## 7) Seeders (Roles & Permissions)

- `RolePermissionSeeder` (Super Admin, Finance Admin, Support Admin, Ops Admin, Moderator, Astrologer, User).
- `RolesAndPermissionsSeeder` (legacy roles and defaults).
- `AiChatPricingSeeder` (AI pricing defaults).
- `RoleSeeder` (legacy roles).

## 8) API Examples (cURL)

Wallet Recharge
```bash
curl -X POST "$APP_URL/api/wallet/recharge" \
  -H "Authorization: Bearer <firebase_id_token>" \
  -H "Content-Type: application/json" \
  -d '{"amount": 199}'
```

Initiate Call
```bash
curl -X POST "$APP_URL/api/call/initiate" \
  -H "Authorization: Bearer <firebase_id_token>" \
  -H "Content-Type: application/json" \
  -d '{"astrologer_id": 12}'
```

AI Chat (send message)
```bash
curl -X POST "$APP_URL/api/ai/chat" \
  -H "Authorization: Bearer <firebase_id_token>" \
  -H "Content-Type: application/json" \
  -d '{"session_id":"<uuid>","message":"Hello","client_message_id":"<uuid>"}'
```

Report Review (moderate)
```bash
curl -X PUT "$APP_URL/api/reviews/123/status" \
  -H "Authorization: Bearer <firebase_id_token>" \
  -H "Content-Type: application/json" \
  -d '{"status":"hidden"}'
```
