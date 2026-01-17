# FCM Notification System Architecture

## Architecture Overview: Hybrid Pattern
This system uses a **Hybrid Architecture** to balance low latency for real-time chats with robust business logic for calls and wallet events.

### 1. Chat Notifications (Real-time Flow)
- **Source**: Firestore Trigger (Cloud Functions)
- **Event**: `onCreate` of `chats/{chatId}/messages/{messageId}`
- **Logic**:
  1.  Cloud Function triggers.
  2.  Identifies Receiver ID from `chats/{chatId}` document.
  3.  Calls Internal Laravel API (`/api/internal/fcm-tokens`) to get valid/enabled FCM tokens (and check Mute status).
  4.  Sends Multicast Message via Firebase Admin SDK.
- **Why?**: Bypasses Laravel for sending, ensuring sub-second delivery for chat apps.

### 2. Call & Wallet Notifications (Business Logic Flow)
- **Source**: Laravel Event/Service
- **Event**: `CallerDesk Webhook`, `WalletTransaction` (Debit/Credit)
- **Logic**:
  1.  Service (e.g., `WalletService`, `CallController`) detects event.
  2.  Dispatches `SendPushNotificationJob` to Queue (Redis/Database).
  3.  Job processes in background:
      - Validates Preferences (DND, Mute).
      - Fetches Tokens from MySQL `device_tokens`.
      - Sends via `kreait/firebase-php` (Admin SDK).
- **Why?**: Avoids webhook timeouts and ensures transactional integrity.

---

## Database Schema

### `device_tokens`
Stores FCM tokens for multi-device support.
- `user_id`: FK to users.
- `fcm_token`: The actual token.
- `platform`: 'android', 'ios', 'web'.
- `is_enabled`: Soft disable.

### `notification_preferences`
User-controlled settings.
- `mute_chat`, `mute_calls`, `mute_wallet`: Booleans.
- `dnd_start`, `dnd_end`: Time range (User's timezone).

---

## Notification Types & Payloads

### 1. Chat Messages
- **Type**: `chat_message`
- **Title**: "New Message"
- **Body**: Truncated text preview (No PII).
- **Deep Link**: `app://chat/{chatId}`
- **FCM Data**: `chatId`, `click_action` (Flutter).

### 2. Calls
- **Type**: `call_incoming` (Astrologer only)
- **Type**: `call_missed` (Astrologer)
- **Type**: `call_ended` (User)
- **Payload**: `call_session_id`, `cost`, `duration`.
- **Deep Link**: `app://calls/{id}`

### 3. Wallet
- **Type**: `wallet_low` (< 100 INR)
- **Type**: `wallet_exhausted` (<= 0)
- **Type**: `wallet_recharge_success`
- **Payload**: `balance`, `amount`.
- **Deep Link**: `app://wallet/recharge`

### 4. System
- **Type**: `chat_session_paused` (Astrologer)
- **Title**: "Chat Paused"
- **Reason**: User funds exhausted.

---

## Integration Guide

### Mobile App (Flutter/React Native)
1.  **On Login**: Call `POST /api/devices/register` with FCM token.
2.  **On Logout**: Call `POST /api/devices/unregister`.
3.  **Notification Channels (Android)**: Create channels `chat_channel`, `calls_channel`, `wallet_channel`.
4.  **Click Handling**: Listen for `data.deeplink` and route accordingly.

### Deployment
1.  **Laravel**: Run `php artisan migrate`. Ensure Queue Worker is running (`php artisan queue:work`).
2.  **Firebase**: Deploy Cloud Function (`firebase deploy --only functions`).
    - Config: `firebase functions:config:set laravel.url="https://api.yoursite.com" laravel.secret="YOUR_SECRET"`
