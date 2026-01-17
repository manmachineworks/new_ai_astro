# Technical Specifications & Flows

This document outlines the implemented logic and required specifications for the critical billing and communication flows.

## A) Firebase Security (Implemented in `firestore.rules`)
*   **Privacy**: PII (phone, email) is explicitly blocked from being written to Firestore via rules `function noPII()`.
*   **Access Control**: Strictly enforced `isParticipant(chatId)` for reading/writing messages.
*   **Validation**: Message types restricted to `['text', 'image', 'file', 'voice']`.

## B) Payment Flows

### 1. PhonePe Wallet Recharge
*   **Init**: User -> `POST /api/wallet/phonepe/initiate` -> Redirect.
*   **Webhook**: PhonePe -> `POST /api/webhooks/phonepe`
    *   **Logic**: Verify X-VERIFY signature -> Check Idempotency (`merchant_txn_id`) -> Credit Wallet.
    *   **Status**: SUCCESS / FAILED / PENDING.

### 2. CallerDesk Telephony
*   **Init**: User (Click Call) -> Laravel Check Balance -> `CallerDeskService::initiateCall`.
*   **Webhook**: CallerDesk -> `POST /api/webhooks/callerdesk`
    *   **Event**: `call_ended` (Source of Billing Truth).
    *   **Logic**: Calculate Duration -> Debit Wallet -> Credit Astrologer.
    *   **Rate Logic**: `duration_minutes * astrologer_rate`.

### 3. Chat Billing (Per Message model)
*   **Logic**:
    1.  Client sends `POST /api/chat/messages` (Laravel).
    2.  Laravel checks Balance > Rate.
    3.  Transaction: Debit Wallet -> Credit Astrologer (Ledger).
    4.  Response: `200 OK` + `firebase_token` (custom) or success signal.
    5.  Client writes message to Firestore.
*   **Source of Truth**: Laravel Database (`chat_sessions` + `wallet_transactions`). Firebase is only for Realtime Sync.

## C) Data Privacy (PII)
*   **API Responses**: All User endpoints exposed to Astrologers return `user_masked_identifier` (e.g., "User #8821").
*   **Dashboard**: Phone numbers and Emails are never rendered in the Astrologer Dashboard HTML.
*   **Firestore**: Rules physically prevent writing `phone` or `email` keys.

## D) System Integrity
*   **Reconciliation**: `php artisan billing:reconcile` runs daily to release "stuck holds" from failed calls.
*   **Idempotency**: All webhooks use `merchant_transaction_id` or `call_id` as a unique key to prevent double-billing.
