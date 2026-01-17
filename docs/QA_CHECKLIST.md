# QA Checklist - Production Readiness

## Pre-Deployment Verification

### 1. Environment & Configuration
- [ ] `.env` file configured with production values
- [ ] `APP_DEBUG=false` in production
- [ ] `APP_ENV=production`
- [ ] All required environment variables present (run `php artisan app:smoke-test`)
- [ ] Firebase credentials file exists and is valid
- [ ] Storage directories writable (`storage/logs`, `storage/framework/*`)

### 2. Database & Migrations
- [ ] All migrations executed (`php artisan migrate:status`)
- [ ] Database indexes verified (wallet_transactions, payment_orders, sessions)
- [ ] Seed data loaded if needed (`php artisan db:seed`)

### 3. Security
- [ ] HTTPS enforced
- [ ] Secure headers middleware active
- [ ] CSRF protection enabled for web routes
- [ ] Rate limiting configured
- [ ] PII leak tests passing (`php artisan test tests/Feature/Security/PIIAuditTest.php`)

---

## User Flows Testing

### A. Authentication & Registration
- [ ] **Phone OTP Login**
  - User can request OTP
  - OTP is received (check SMS logs)
  - User can verify OTP and login
  - Session persists correctly
  - Logout works

### B. Wallet & Payments
- [ ] **Wallet Recharge**
  - User initiates recharge
  - PhonePe payment page loads
  - Successful payment credits wallet
  - Failed payment does NOT credit wallet
  - Webhook idempotency: duplicate webhook doesn't double-credit
- [ ] **Wallet Balance Display**
  - Balance updates in real-time after transactions
  - Negative balance triggers safeguards

### C. Voice Calls
- [ ] **Call Session**
  - User can initiate call to verified astrologer
  - CallerDesk webhook received on call end
  - Wallet debited correctly (per-minute billing)
  - Astrologer earnings calculated with commission
  - Call history visible to both parties (with PII masking for astrologer)

### D. Human Chat
- [ ] **Chat Session Start**
  - User can start chat with astrologer
  - Firebase conversation created
  - Pricing gate enforced (wallet check)
- [ ] **Per-Message Billing**
  - User sends message → Firebase write → confirm-sent API call
  - Wallet debited per message
  - Idempotency: duplicate confirm doesn't double-charge
  - Astrologer sees masked user identifier (NOT phone/email)

### E. AI Chat
- [ ] **AI Chat Session**
  - User can start AI chat
  - Per-message or per-session pricing enforced
  - AstrologyAPI integration works
  - Failed API call triggers auto-refund
  - Refund idempotency works

### F. Horoscopes & Kundli
- [ ] **Daily/Weekly Horoscope**
  - User can select zodiac sign
  - Horoscopes fetched and cached
  - Cache invalidation works (12-24h)
- [ ] **Kundli Generation**
  - Free kundli generation works
  - Birth details validated

---

## Admin Flows Testing

### A. Astrologer Management
- [ ] **Verification**
  - Admin can view pending astrologers
  - Verification status updates correctly
  - Only verified astrologers appear in directory
- [ ] **Visibility Toggle**
  - `show_on_front` toggle works
  - Hidden astrologers not in public directory

### B. Reporting & Analytics
- [ ] **Dashboard**
  - KPIs display correct data
  - Date range filters work
  - Charts render properly
- [ ] **Detailed Reports**
  - Revenue, Recharges, Calls, Chats, AI reports accessible
  - Pagination works
  - CSV export triggers download
- [ ] **Commission Control**
  - Granular commission % settings saveable
  - Commission snapshots preserved in sessions

### C. Pricing & Settings
- [ ] **Pricing Settings**
  - Wallet gates, AI chat prices, commission % all editable
  - Changes reflected immediately in new sessions

---

## Failure & Edge Cases

### A. Payment Failures
- [ ] **Webhook Replay Protection**
  - Duplicate PhonePe webhook ignored
  - Signature verification failures logged
- [ ] **Payment Failure**
  - Failed payment does not credit wallet
  - User sees appropriate error message

### B. Insufficient Wallet
- [ ] **Mid-Call Insufficiency**
  - Call terminates gracefully if wallet depleted
  - User notified to recharge
- [ ] **Chat Message Block**
  - Message send blocked if wallet < price_per_message
  - User prompted to recharge

### C. Provider Failures
- [ ] **AstrologyAPI Failure**
  - AI chat message fails gracefully
  - Auto-refund triggered
  - Idempotency prevents duplicate refunds
- [ ] **CallerDesk Failure**
  - Webhook timeout/failure logged
  - Retryable from admin panel (dead-letter queue)

### D. PII Protection
- [ ] **Astrologer Views**
  - Chat list does NOT show user email
  - Chat list does NOT show user phone
  - Call history shows masked identifiers only
- [ ] **Automated Tests**
  - `PIIAuditTest` passes
  - No email/phone patterns in astrologer endpoint responses

---

## Performance & Scalability

- [ ] **Query Optimization**
  - No N+1 queries in critical paths
  - Eager loading used where appropriate
- [ ] **Caching**
  - Horoscope requests cached
  - Report aggregates cached
  - Cache invalidation on admin changes
- [ ] **Background Jobs**
  - Queue workers running (`supervisor` configured)
  - Daily metrics command scheduled in cron
  - FCM notifications dispatched asynchronously

---

## Monitoring & Observability

- [ ] **Health Endpoints**
  - `/health` returns 200 OK
  - `/health/db` verifies database connectivity
  - `/health/queue` verifies Redis connectivity
- [ ] **Logs**
  - Structured JSON logs enabled
  - Request ID included in logs
  - Critical errors logged (negative wallet, failed webhooks)
- [ ] **Error Reporting**
  - Sentry/Bugsnag configured (optional)
  - Exception notifications working

---

## Final Checks

- [ ] **Smoke Test Passes**
  - Run: `php artisan app:smoke-test`
  - All checks pass
- [ ] **Test Suite Passes**
  - Run: `php artisan test`
  - All tests green
- [ ] **Code Quality**
  - PHPStan/Larastan: `vendor/bin/phpstan analyze` (if installed)
  - Laravel Pint: `vendor/bin/pint --test` (if installed)
- [ ] **Firebase Security Rules**
  - `firestore.rules` deployed to Firebase
  - Rules enforce participant-only access
- [ ] **Documentation**
  - README.md updated with deployment steps
  - `.env.example` complete
  - Deployment guides (Nginx, Supervisor) available

---

## Sign-Off

**Tested By:** _______________  
**Date:** _______________  
**Environment:** [ ] Staging [ ] Production  
**Status:** [ ] PASS [ ] FAIL  
**Notes:** _______________________________________________
