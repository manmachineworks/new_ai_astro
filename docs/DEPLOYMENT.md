# Deployment Guide

This guide outlines the steps to deploy the Astrologer Marketplace application to a production environment (e.g., DigitalOcean, AWS EC2, or a VPS).

## 1. Server Requirements
*   **OS**: Ubuntu 22.04 LTS (Recommended)
*   **Web Server**: Nginx or Apache
*   **PHP**: 8.2 or 8.3
*   **Extensions**: `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `curl`
*   **Database**: MySQL 8.0 or MariaDB 10.6
*   **Composer**: Latest version
*   **Node.js**: v18+ (for frontend assets)

## 2. Environment Setup

### Clone Repository
```bash
git clone https://github.com/your-repo/new_ai_astro.git
cd new_ai_astro
```

### Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### Environment Configuration
Copy `.env.example` to `.env` and update the following:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=astro_db
DB_USERNAME=astro_user
DB_PASSWORD=secure_password

# PhonePe (Production Credentials)
PHONEPE_ENV=PROD
PHONEPE_MERCHANT_ID=...
PHONEPE_SALT_KEY=...

# Firebase (Production Key)
FIREBASE_PROJECT_ID=...
# Ensure the private key is properly formatted with \n
FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\n..."
```

### Key Generation & Storage Link
```bash
php artisan key:generate
php artisan storage:link
```

## 3. Database Migration
```bash
php artisan migrate --force
```
*Note: Ensure your production database is created and empty before running this.*

## 4. Web Server Configuration (Nginx Example)

Create a configuration file at `/etc/nginx/sites-available/astro`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/new_ai_astro/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site and restart Nginx:
```bash
ln -s /etc/nginx/sites-available/astro /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx
```

## 5. SSL Certificate (Certbot)
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

## 6. Supervisor (Queue Workers)
For sending emails and handling webhooks (if queued):

Install Supervisor:
```bash
sudo apt install supervisor
```

Create config `/etc/supervisor/conf.d/astro-worker.conf`:
```ini
[program:astro-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/new_ai_astro/artisan queue:work sqs --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/new_ai_astro/storage/logs/worker.log
stopwaitsecs=3600
```

Start Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start astro-worker:*
```

## 7. Scheduler (Cron)
Add the following to your server's crontab (`crontab -e`):

```bash
* * * * * cd /var/www/new_ai_astro && php artisan schedule:run >> /dev/null 2>&1
```
*This is critical for `billing:reconcile` ensuring wallet safety.*

## 8. Verifying Production
1.  **PhonePe**: Test a ₹1 transaction in Production mode.
2.  **Firebase**: Login and ensure Chat connects.
3.  **CallerDesk**: Verify the webhook URL is reachable from outside.
