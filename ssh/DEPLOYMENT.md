# Jikra - AWS Deployment Guide

## Server Info

| Detail | Value |
|--------|-------|
| **Provider** | AWS EC2 |
| **Host** | 13.205.162.30 |
| **User** | ubuntu |
| **SSH Key** | `d:/projects/Jikra/Dcrayons.pem` |
| **Domain** | jikra.in |
| **App Path** | `/var/www/jikra` |
| **PHP Version** | 8.3 |
| **Web Server** | Nginx |
| **Database** | MySQL 8 |
| **OS** | Ubuntu |

## Quick Connect

```bash
ssh -i d:/projects/Jikra/Dcrayons.pem ubuntu@13.205.162.30
```

## Full Deployment (All Files)

### Step 1: Create archive locally
```bash
cd /d/projects/Jikra

tar -czf /tmp/jikra-full-deploy.tar.gz \
  --exclude=node_modules \
  --exclude=vendor \
  --exclude=.git \
  --exclude=storage \
  --exclude=.env \
  --exclude='*.pem' \
  --exclude='database/*.sqlite*' \
  --exclude=public/hot \
  app/ config/ database/migrations/ database/seeders/ \
  resources/ routes/ public/images/ public/manifest.json public/sw.js
```

### Step 2: Upload to server
```bash
scp -i d:/projects/Jikra/Dcrayons.pem \
  /tmp/jikra-full-deploy.tar.gz \
  ubuntu@13.205.162.30:/tmp/jikra-full-deploy.tar.gz
```

### Step 3: Extract and rebuild caches
```bash
ssh -i d:/projects/Jikra/Dcrayons.pem ubuntu@13.205.162.30 \
  "cd /var/www/jikra && \
   sudo tar -xzf /tmp/jikra-full-deploy.tar.gz && \
   sudo chown -R www-data:www-data . && \
   sudo php artisan config:clear && \
   sudo php artisan route:clear && \
   sudo php artisan view:clear && \
   sudo php artisan config:cache && \
   sudo php artisan route:cache && \
   sudo php artisan migrate --force && \
   echo 'DEPLOY DONE'"
```

## Partial Deployment (Specific Files)

### Step 1: Create archive of changed files only
```bash
cd /d/projects/Jikra

tar -czf /tmp/jikra-deploy.tar.gz \
  app/Http/Controllers/CheckoutController.php \
  routes/web.php \
  resources/views/checkout/index.blade.php
```

### Step 2: Upload and extract
```bash
scp -i d:/projects/Jikra/Dcrayons.pem \
  /tmp/jikra-deploy.tar.gz \
  ubuntu@13.205.162.30:/tmp/jikra-deploy.tar.gz

ssh -i d:/projects/Jikra/Dcrayons.pem ubuntu@13.205.162.30 \
  "cd /var/www/jikra && \
   sudo tar -xzf /tmp/jikra-deploy.tar.gz && \
   sudo chown -R www-data:www-data . && \
   sudo php artisan config:cache && \
   sudo php artisan route:cache && \
   sudo php artisan view:clear && \
   echo 'DEPLOY DONE'"
```

## Common Operations

### Clear all caches
```bash
ssh -i d:/projects/Jikra/Dcrayons.pem ubuntu@13.205.162.30 \
  "cd /var/www/jikra && \
   sudo php artisan config:clear && \
   sudo php artisan route:clear && \
   sudo php artisan view:clear && \
   sudo php artisan cache:clear"
```

### Run migrations
```bash
ssh -i d:/projects/Jikra/Dcrayons.pem ubuntu@13.205.162.30 \
  "cd /var/www/jikra && sudo php artisan migrate --force"
```

### Run seeders
```bash
ssh -i d:/projects/Jikra/Dcrayons.pem ubuntu@13.205.162.30 \
  "cd /var/www/jikra && sudo php artisan db:seed --class=SomeSeeder --force"
```

### Check Laravel logs
```bash
ssh -i d:/projects/Jikra/Dcrayons.pem ubuntu@13.205.162.30 \
  "tail -50 /var/www/jikra/storage/logs/laravel.log"
```

### Restart services
```bash
ssh -i d:/projects/Jikra/Dcrayons.pem ubuntu@13.205.162.30 \
  "sudo systemctl restart nginx && sudo systemctl restart php8.3-fpm"
```

### Check Nginx error log
```bash
ssh -i d:/projects/Jikra/Dcrayons.pem ubuntu@13.205.162.30 \
  "sudo tail -50 /var/log/nginx/error.log"
```

### Storage link (one-time setup)
```bash
ssh -i d:/projects/Jikra/Dcrayons.pem ubuntu@13.205.162.30 \
  "cd /var/www/jikra && sudo php artisan storage:link"
```

## Server Paths

| Path | Description |
|------|-------------|
| `/var/www/jikra/` | Laravel project root |
| `/var/www/jikra/public/` | Web document root |
| `/var/www/jikra/public/images/` | Public images |
| `/var/www/jikra/storage/app/public/` | Storage (symlinked to public/storage) |
| `/var/www/jikra/storage/logs/laravel.log` | Application logs |
| `/etc/nginx/sites-available/` | Nginx site configs |

## Environment Variables (Server .env)

Key environment variables on server (edit with `sudo nano /var/www/jikra/.env`):

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://jikra.in`
- `RAZORPAY_KEY_ID=rzp_live_SA0229lB5Teg0Y`
- `RAZORPAY_KEY_SECRET=` *(set on server)*
- `RAZORPAY_WEBHOOK_SECRET=` *(set on server)*

## Notes

- **No rsync on Windows** - use `tar + scp` workflow instead
- **Always run cache commands** after deploying PHP/config/route changes
- **Run `view:clear`** after deploying blade templates
- **Never deploy `.env`** - credentials are set directly on server
- **Never deploy `vendor/`** - run `composer install` on server if needed
- **File permissions**: `www-data:www-data` ownership required
- Old ForeverKids server (167.88.41.35:65002) is deprecated - do NOT use
