# Production Deployment Guide

## Prerequisites

- PHP 8.2+
- MySQL 8.0+ / MariaDB 10.3+
- Redis 6.0+
- Nginx 1.18+ / Apache 2.4+
- Composer 2.x
- Node.js 18+ (for asset compilation)
- Supervisor (for queue workers)

---

## 1. Server Setup

### Install Dependencies

```bash
# Ubuntu/Debian
sudo apt update
sudo apt install -y php8.2-fpm php8.2-mysql php8.2-redis php8.2-mbstring \
    php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd \
    nginx mysql-server redis-server supervisor composer git

# Enable PHP-FPM
sudo systemctl enable php8.2-fpm
sudo systemctl start php8.2-fpm
```

---

## 2. Application Deployment

### Clone & Install

```bash
cd /var/www
sudo git clone <repository-url> new_ai_astro
cd new_ai_astro

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Environment Configuration

```bash
# Copy and edit .env
cp .env.example .env
php artisan key:generate

# Edit .env with production values
nano .env
```

**Critical `.env` Settings:**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_HOST=localhost
DB_DATABASE=astro_prod
DB_USERNAME=astro_user
DB_PASSWORD=<strong-password>

REDIS_HOST=localhost
REDIS_PASSWORD=null
REDIS_PORT=6379

QUEUE_CONNECTION=redis

PHONEPE_MERCHANT_ID=<your-merchant-id>
PHONEPE_SALT_KEY=<your-salt-key>
PHONEPE_SALT_INDEX=<your-salt-index>

FIREBASE_CREDENTIALS=/var/www/new_ai_astro/firebase-credentials.json

ASTROLOGYAPI_USER_ID=<your-user-id>
ASTROLOGYAPI_API_KEY=<your-api-key>
```

### Firebase Setup

```bash
# Upload Firebase service account credentials
sudo nano /var/www/new_ai_astro/firebase-credentials.json
# Paste JSON content

# Secure the file
sudo chmod 600 /var/www/new_ai_astro/firebase-credentials.json
sudo chown www-data:www-data /var/www/new_ai_astro/firebase-credentials.json
```

### Database Migration

```bash
php artisan migrate --force
php artisan db:seed --force  # If needed
```

### Cache & Optimize

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## 3. Nginx Configuration

**File:** `/etc/nginx/sites-available/astro`

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    root /var/www/new_ai_astro/public;
    index index.php index.html;

    # SSL Configuration (use Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Logging
    access_log /var/log/nginx/astro-access.log;
    error_log /var/log/nginx/astro-error.log;

    # Security Headers (redundant with SecureHeaders middleware, but good practice)
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Laravel specific
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Asset caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

**Enable and Test:**

```bash
sudo ln -s /etc/nginx/sites-available/astro /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 4. Supervisor Configuration (Queue Workers)

**File:** `/etc/supervisor/conf.d/astro-worker.conf`

```ini
[program:astro-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/new_ai_astro/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/new_ai_astro/storage/logs/worker.log
stopwaitsecs=3600
```

**Start Workers:**

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start astro-worker:*
sudo supervisorctl status
```

---

## 5. Cron Configuration

**Add to crontab (`sudo crontab -e -u www-data`):**

```cron
* * * * * cd /var/www/new_ai_astro && php artisan schedule:run >> /dev/null 2>&1
```

**Scheduled Tasks (in `app/Console/Kernel.php`):**

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('metrics:daily')->dailyAt('01:00');
    $schedule->command('queue:prune-failed --hours=48')->daily();
}
```

---

## 6. SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
sudo certbot renew --dry-run  # Test auto-renewal
```

---

## 7. Firewall Configuration

```bash
sudo ufw allow 22/tcp   # SSH
sudo ufw allow 80/tcp   # HTTP
sudo ufw allow 443/tcp  # HTTPS
sudo ufw enable
sudo ufw status
```

---

## 8. Monitoring & Health Checks

### Health Endpoints

- **Basic:** `https://yourdomain.com/health`
- **Database:** `https://yourdomain.com/health/db`
- **Queue:** `https://yourdomain.com/health/queue`

### Smoke Test

```bash
php artisan app:smoke-test
```

---

## 9. Backup Strategy

### Database Backup (Daily)

```bash
# Add to cron (root user)
0 2 * * * /usr/bin/mysqldump -u astro_user -p<password> astro_prod | gzip > /backups/db-$(date +\%Y\%m\%d).sql.gz
```

### File Backup

```bash
# Backup critical directories
tar -czf /backups/storage-$(date +%Y%m%d).tar.gz /var/www/new_ai_astro/storage
```

### Firebase Backup

- **Firestore Exports:** Use Firebase Console or CLI
- **Security Rules:** Keep `firestore.rules` in version control

---

## 10. Post-Deployment Verification

```bash
# Run smoke test
php artisan app:smoke-test

# Run test suite
php artisan test

# Check queue workers
sudo supervisorctl status

# Check logs
tail -f storage/logs/laravel.log
tail -f /var/log/nginx/astro-error.log
```

---

## Rollback Procedure

```bash
# If deployment fails
cd /var/www/new_ai_astro
git checkout <previous-commit-hash>
composer install --no-dev
php artisan migrate:rollback  # If needed
php artisan config:cache
sudo supervisorctl restart astro-worker:*
```

---

## Troubleshooting

### Issue: 500 Error
- Check: `storage/logs/laravel.log`
- Check: `/var/log/nginx/astro-error.log`
- Verify: Storage permissions (`chmod -R 775 storage bootstrap/cache`)

### Issue: Queue Not Processing
- Check: `sudo supervisorctl status`
- Restart: `sudo supervisorctl restart astro-worker:*`
- Logs: `tail -f storage/logs/worker.log`

### Issue: Database Connection Failed
- Verify: `.env` credentials
- Test: `php artisan tinker` then `DB::connection()->getPdo();`

### Issue: Redis Connection Failed
- Check: `redis-cli ping`
- Verify: Redis is running (`sudo systemctl status redis`)

---

## Security Checklist

- [ ] HTTPS enforced
- [ ] Firewall configured
- [ ] `.env` file not publicly accessible
- [ ] Firebase credentials secured (chmod 600)
- [ ] Database user has minimal permissions
- [ ] Fail2ban configured (optional)
- [ ] Regular security updates applied

---

## Maintenance

**Weekly:**
- Review logs for errors
- Check disk space
- Verify backups

**Monthly:**
- Update dependencies (`composer update`, `npm update`)
- Security patches (`sudo apt update && sudo apt upgrade`)
- Database optimization

**Quarterly:**
- Review performance metrics
- Optimize indexes
- Prune old data (logs, webhooks)

---

**Support:** For issues, check logs first, then contact dev team.
