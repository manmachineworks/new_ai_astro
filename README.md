# AstroTalk Clone - Astrologer Marketplace Platform

A premium, scalable marketplace for Astrology services featuring Real-time Chat, Video/Audio Calls, and Wallet Payments.

## ? Key Features
* **User & Astrologer Dashboards**: Modern UI with role-aware navigation.
* **Wallet System**: Integrated PhonePe wallets, transactions, and audit logs.
* **Real-time Chat**: Firebase-powered WhatsApp-style conversations with masked data.
* **Telephony**: Click-to-call via CallerDesk with concealed numbers and status tracking.
* **Security**: Role-Based Access Control plus tracing via policy enforcement.

## 📚 Documentation
* [`docs/DEPLOYMENT.md`](docs/DEPLOYMENT.md) - Production deployment guidelines.
* [`docs/TECHNICAL_SPECS.md`](docs/TECHNICAL_SPECS.md) - Billing logic, services, and integrations.
* [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) - System components and data flows.
* [`docs/COMPLETION_REPORT.md`](docs/COMPLETION_REPORT.md) - Implementation highlights.

## ⚙️ Tech Stack
* Backend: Laravel 12+
* Frontend: Inertia + Vue 3 + Tailwind + Vite
* Real-time: Firebase Auth + Firestore + Storage
* Telephony: CallerDesk
* Payments: PhonePe + WalletService
* AI & Horoscope: AstrologyAPI

## 🛠 Quick Start
```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

## 🌠 Credentials (Development)
* **Admin**: `admin@astrotalk.com` / `password`
* **Astrologer**: `astro@test.com` / `password`
* **User**: `user@test.com` / `password`

## Astrologer Dashboard Module
The new Inertia/Vue-based Astrologer Dashboard lives inside `/astrologer/*`:
* Overview with call/chat stats, masked calls, Firebase chat links, and earnings.
* Service toggles, schedule/time-off editors, and pricing updates (logged in `astrologer_pricing_audits`).
* Call logs/chats with privacy masks, quick moderation actions (close/block), and appointment controls.
* PhonePe & CallerDesk webhook consumers backed by queued jobs, idempotent writes, and wallet charges.

## Environment Variables
Add or update the following before booting the dashboard:
```
CALLERDESK_BASE_URL=https://api.callerdesk.io/v1
CALLERDESK_API_KEY=
CALLERDESK_WEBHOOK_SECRET=

PHONEPE_MERCHANT_ID=
PHONEPE_SALT_KEY=
PHONEPE_SALT_INDEX=1
PHONEPE_BASE_URL=https://api-preprod.phonepe.com/apis/pg-sandbox
PHONEPE_WEBHOOK_SECRET=

ASTROLOGYAPI_BASE_URL=https://json.astrologyapi.com/v1
ASTROLOGYAPI_KEY=

FIREBASE_PROJECT_ID=
FIREBASE_CLIENT_EMAIL=
FIREBASE_PRIVATE_KEY=
FIREBASE_DATABASE_URL=
FIREBASE_STORAGE_BUCKET=

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=
```

## Firebase Rules
* `firebase/firestore.rules` and `firebase/storage.rules` guard chat threads, message writes, and attachments so only the authorized astrologer/user pair can interact.

## Webhooks & Jobs
* `/api/webhooks/phonepe` → `ProcessPhonePeWebhook` (signature verification, wallet credit, idempotency via `webhook_events`).
* `/api/webhooks/callerdesk` → `ProcessCallerDeskWebhook` (call log upsert, wallet debit, earnings record, insufficient balance flag).
* Both jobs rely on the existing `WalletService` and new ledger models.

## Testing
* `php artisan test` covers:
  1. Astrologers cannot see other astrologers' call/chat records.
  2. Pricing updates insert audit records.
  3. Webhooks are idempotent via the `webhook_events` table.
  4. Wallet debits guard against negative balances.
