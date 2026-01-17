# System Architecture & Implementation Plan

## 1. High-Level Architecture
The platform is a monolithic Laravel application with significant real-time components offloaded to external services.

- **Backend**: Laravel 10+ (API + Blade Views)
- **Database**: MySQL (Primary Data)
- **Real-time DB**: Firebase Firestore (Chat Messages, Presence)
- **Notifications**: Firebase Cloud Messaging (FCM)
- **Payments**: PhonePe (PG)
- **Telephony**: CallerDesk (Cloud Telephony)
- **AI**: Gemini/OpenAI via AstrologyAPI wrapper

## 2. Core Modules

### 2.1 Auth & Identity
- **Users**: Phone Authentication via Firebase → Laravel User Sync.
  - Table: `users` (`firebase_uid`, `phone`, `role`, `wallet_balance`)
- **Astrologers**: Admin-verified profiles.
  - Table: `astrologer_profiles` (`user_id`, `expertise`, `rates`, `is_verified`)

### 2.2 Wallet & Payments
- **Ledger System**: Double-entry bookkeeping for safety.
- **Tables**:
  - `wallets` (user_id, balance, currency)
  - `wallet_transactions` (wallet_id, type, amount, reference_id, description)
  - `phonepe_orders` (merchant_txn_id, amount, status, provider_ref_id)
- **Flow**:
  1. User initiates Recharge → creates `phonepe_order`.
  2. Redirect to PhonePe.
  3. Webhook/Callback → Verify Signature → Update Order → Credit Wallet (Idempotent).

### 2.3 Call System (CallerDesk)
- **Workflow**:
  1. User clicks "Call" → Backend checks Wallet > Min Balance.
  2. Backend calls CallerDesk API to bridge User & Astrologer.
  3. Webhook (Call Start) → Mark status "Connected".
  4. Webhook (Call End) → Calculate Duration * Rate → Debit User Wallet → Credit Astrologer (minus commission).

### 2.4 Chat System (Firebase + Laravel)
- **Data Model (Firestore)**:
  - `chats/{chatId}`: participants, lastMessage, status (active/ended).
  - `messages/{chatId}/{msgId}`: text, sender, timestamp, readReceipt.
- **Billing**:
  - **Per Message**: Backend API `authorizeSend` deducts balance → specifices "allow" signature → Client writes to Firestore.
  - **Per Minute**: Client starts timer → Backend job runs every minute to deduct balance.
- **Access Control**:
  - Laravel acts as the gatekeeper. Users trade Laravel Auth Token for Firebase Custom Token to authenticate SDK.

## 3. Database Schema

```sql
users: id, role, phone, name, email, firebase_uid, is_active, created_at
astrologer_profiles: id, user_id, display_name, slug, call_rate, chat_rate, dashboard_settings (json)
wallet_transactions: id, user_id, amount, type (credit/debit), description, reference_type (App\Models\CallSession), reference_id
phonepe_orders: id, user_id, amount, status (pending/success/failed), transaction_id
call_sessions: id, user_id, astrologer_id, duration, cost, status, external_call_id
chat_sessions: id, user_id, astrologer_id, duration, cost, status, pricing_mode
```

## 4. Webhook Strategy

### PhonePe Webhook
```php
Route::post('/webhooks/phonepe', [PaymentController::class, 'handleWebhook']);
// 1. Verify X-VERIFY header using salt.
// 2. Decode payload.
// 3. Find Order by MerchantTxnId.
// 4. If success and order is pending -> Credit Wallet.
```

### CallerDesk Webhook
```php
Route::post('/webhooks/callerdesk', [WebhookController::class, 'handleCallerDesk']);
// Events: dial_answer, dial_disconnect
// On Disconnect:
// 1. Find CallSession by CallUUID.
// 2. Update duration.
// 3. Calculate Cost = ceil(duration/60) * astrologer_rate.
// 4. Debit User Wallet.
```

## 5. Security & Compliance
- **PII Masking**: Astrologers see `User #8821`, not `+919876543210`.
- **Signature Verification**: All webhooks must be verified against secrets.
- **Idempotency**: Webhooks process same ID only once to prevent double-crediting.
- **Rate Limiting**: `throttle:60,1` on sensitive APIs (OTP, Payment Init).

## 6. Implementation Milestones

### Phase 1: Core Foundation (Completed)
- [x] Laravel Setup & Auth
- [x] Basic Admin Panel
- [x] Astrologer Dashboard Layout

### Phase 2: Wallet & Payments (Current Focus)
- [ ] PhonePe Integration Service.
- [ ] Wallet Transaction Logic (Credit/Debit atomic ops).
- [ ] Recharge Flow UI.

### Phase 3: Real-time Communication
- [ ] Firebase Integration (Wait for User Config).
- [ ] Chat UI (WhatsApp Style).
- [ ] Call Logic (CallerDesk Mock Integration first).

### Phase 4: AI & Reporting
- [ ] OpenAI/Gemini integration for "Ask AI".
- [ ] PDF Reports for Astrologers.
