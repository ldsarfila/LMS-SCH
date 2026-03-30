# Deployment Guide - Enterprise LMS

## Prerequisites

### Server Requirements
- PHP >= 8.5
- MySQL 8.0+ or MariaDB 10.6+
- Redis 7.0+
- Node.js 20.x+
- Composer 2.6+
- Nginx/Apache

## Backend Installation (Laravel)

### 1. Install Dependencies
```bash
cd backend
composer install --optimize-autoloader --no-dev
```

### 2. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure Database
Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_school
DB_USERNAME=root
DB_PASSWORD=your_password

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls

AWS_BUCKET=your-bucket-name
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=ap-southeast-1
```

### 4. Run Migrations & Seeders
```bash
php artisan migrate --seed
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=SchoolSeeder
```

### 5. Storage Setup
```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 6. Queue Worker (Redis)
```bash
php artisan queue:work --daemon --queue=high,default,low
```

### 7. Schedule (Cron)
Add to crontab:
```bash
* * * * * cd /path/to/backend && php artisan schedule:run >> /dev/null 2>&1
```

### 8. Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

## Frontend Installation (Next.js)

### 1. Install Dependencies
```bash
cd frontend
npm install
```

### 2. Environment Configuration
Create `.env.local`:
```env
NEXT_PUBLIC_API_URL=http://localhost:8000/api/v1
NEXT_PUBLIC_APP_NAME=LMS School
NEXT_PUBLIC_APP_URL=http://localhost:3000
```

### 3. Build for Production
```bash
npm run build
```

### 4. Start Production Server
```bash
npm start
```

## Nginx Configuration

### Backend (API)
```nginx
server {
    listen 80;
    server_name api.yourlms.com;
    root /var/www/lms/backend/public;

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
        fastcgi_pass unix:/var/run/php/php8.5-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Frontend (Next.js)
```nginx
server {
    listen 80;
    server_name yourlms.com www.yourlms.com;

    location / {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## SSL Configuration (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourlms.com -d www.yourlms.com -d api.yourlms.com
```

## Supervisor Configuration (Queue Worker)
`/etc/supervisor/conf.d/lms-worker.conf`:
```ini
[program:lms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/lms/backend/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/lms/backend/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start lms-worker:*
```

## Monitoring & Logging

### Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

### Setup Log Rotation
`/etc/logrotate.d/lms`:
```
/var/www/lms/backend/storage/logs/*.log {
    daily
    rotate 30
    compress
    delaycompress
    missingok
    notifempty
    create 0664 www-data www-data
}
```

## Backup Strategy

### Database Backup (Daily)
```bash
#!/bin/bash
mysqldump -u root -p lms_school | gzip > /backups/lms_$(date +%Y%m%d).sql.gz
find /backups -name "*.sql.gz" -mtime +30 -delete
```

### Storage Backup (Weekly)
```bash
tar -czf /backups/storage_$(date +%Y%m%d).tar.gz /var/www/lms/backend/storage/app
```

## Performance Optimization

### Redis Caching
```bash
php artisan cache:clear
php artisan config:cache
php artisan route:cache
```

### OPcache Configuration
In `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

## Security Checklist

- [ ] Enable HTTPS
- [ ] Set secure file permissions
- [ ] Configure firewall (UFW)
- [ ] Enable fail2ban
- [ ] Set up database backups
- [ ] Configure rate limiting
- [ ] Enable CORS properly
- [ ] Use environment variables for secrets
- [ ] Keep dependencies updated
- [ ] Monitor logs regularly

## Troubleshooting

### Common Issues

**Permission Denied:**
```bash
chown -R www-data:www-data /var/www/lms
chmod -R 775 storage bootstrap/cache
```

**Queue Not Processing:**
```bash
sudo supervisorctl status
sudo supervisorctl restart lms-worker:*
```

**Cache Issues:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Support

For issues and support, contact the development team.
