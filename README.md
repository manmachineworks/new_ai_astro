# AstroTalk Clone - Astrologer Marketplace Platform

A premium, scalable marketplace for Astrology services featuring Real-time Chat, Video/Audio Calls, and Wallet Payments.

## 🚀 Key Features
*   **User & Astrologer Dashboards**: Glassmorphism UI, Responsive.
*   **Wallet System**: PhonePe Integration, Withdrawals, Transaction Ledger.
*   **Real-time Chat**: WhatsApp-style, Firebase Synced, Per-minute/Per-message billing.
*   **Telephony**: Click-to-Call via CallerDesk, Masked Numbers.
*   **Security**: Role-Based Access (Admin/Astrologer/User), PII Masking.

## 📚 Documentation
For detailed developer guides, see the `/docs` directory:
*   [Deployment Guide](docs/DEPLOYMENT.md): Setup for Production (Ubuntu/Nginx).
*   [Technical Specs](docs/TECHNICAL_SPECS.md): billing logic, API flows, and security rules.
*   [Architecture](docs/ARCHITECTURE.md): System design and component interaction.
*   [Completion Report](docs/COMPLETION_REPORT.md): Summary of implemented features.

## 🛠 Tech Stack
*   **Backend**: Laravel 10
*   **Frontend**: Blade + Bootstrap 5 + Vanilla JS
*   **Realtime**: Firebase Firestore
*   **Database**: MySQL
*   **Services**: PhonePe (Payments), CallerDesk (IVR)

## ⚡ Quick Start
```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## 🔐 Credentials (Development)
*   **Admin**: `admin@astrotalk.com` / `password`
*   **Astrologer**: `astro@test.com` / `password`
*   **User**: `user@test.com` / `password`
