# Astrologer Marketplace (Laravel 11)

A production-ready Astrologer Marketplace web application built with Laravel, featuring Wallet, Realtime Chat (Firebase), Call Logic (CallerDesk), and Role Management.

## Stack
- **Framework**: Laravel 11 / PHP 8.2+
- **Database**: MySQL 8.0
- **Auth**: Laravel Sanctum (API), Spatie Permissions (RBAC)
- **Realtime**: Firebase Firestore + FCM
- **Payments**: PhonePe + Internal Wallet Ledger
- **Telephony**: CallerDesk

## Setup Instructions

### 1. Prerequisites
- PHP 8.2+
- MySQL
- Composer
- Node.js & NPM

### 2. Installation
1.  Clone the repository.
2.  Install PHP dependencies:
    ```bash
    composer install
    ```
3.  Install JS dependencies:
    ```bash
    npm install && npm run build
    ```
4.  Configure Environment:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    Update `.env` with your DB credentials.

### 3. Database Setup
Run migrations and seeders to set up tables and default roles/users.
```bash
php artisan migrate --seed
```
This will create:
- Roles: `Super Admin`, `Admin`, `Astrologer`, `User`
- Default Admin: `admin@example.com` / `password`

### 4. Running the App
```bash
php artisan serve
```

## API Documentation (Core)

### Authentication
- `POST /api/login` (Use `sanctum:token` command for testing now)

### Wallet
- `GET /api/wallet/balance`
- `GET /api/wallet/transactions`
- `POST /api/wallet/recharge` (Amount -> PhonePe Redirect)

### Astrologers
- `GET /api/astrologers?search=name&skill=Vedic`
- `PUT /api/astrologer/profile` (Update Bio/Pricing)
- `PUT /api/astrologer/status` (Toggle Online/Offline)

### Services
- **Call**: `POST /api/call/initiate` (Requires `astrologer_id`)
- **Chat**: `POST /api/chat/initiate` (Requires `astrologer_id`)
- **AI**: `POST /api/ai/chat` (Message -> Response)

### Withdrawals
- `POST /api/withdrawals` (Astrologer requests payout)
- `GET /api/withdrawals` (Admin list)
- `PUT /api/withdrawals/{id}` (Admin approve/reject)

## Running Tests
Run all feature tests to verify system integrity:
```bash
php artisan test
```

## Architecture
See `brain/design.md` for detailed Schema and Architecture.
