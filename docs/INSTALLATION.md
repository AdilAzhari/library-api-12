# Library Management System - Installation Guide

This guide provides detailed instructions for installing and setting up the Library Management System in different environments. Follow the appropriate section based on your deployment needs.

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Development Installation](#development-installation)
3. [Production Installation](#production-installation)
4. [Docker Installation](#docker-installation)
5. [Configuration](#configuration)
6. [Database Setup](#database-setup)
7. [Search Engine Setup](#search-engine-setup)
8. [Queue Setup](#queue-setup)
9. [Mail Configuration](#mail-configuration)
10. [Troubleshooting](#troubleshooting)

## System Requirements

### Minimum Requirements

- **PHP**: 8.2 or higher
- **Composer**: 2.0 or higher
- **Node.js**: 18.0 or higher
- **NPM**: 9.0 or higher
- **Database**: MySQL 8.0+ or PostgreSQL 13+
- **Memory**: 512MB RAM minimum (2GB recommended)
- **Disk Space**: 1GB free space

### Recommended Requirements

- **PHP**: 8.3 (latest stable)
- **Memory**: 4GB RAM or more
- **CPU**: Multi-core processor
- **SSD**: For better performance

### PHP Extensions Required

- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- BCMath PHP Extension
- Fileinfo PHP Extension
- GD PHP Extension (for image processing)
- Zip PHP Extension
- Curl PHP Extension

### Optional Components

- **Redis**: 6.0+ (for caching and queues)
- **Typesense**: 0.25.0+ (for advanced search)
- **Supervisor**: (for queue workers in production)

## Development Installation

### Step 1: Clone the Repository

```bash
# Clone from GitHub
git clone https://github.com/AdilAzhari/library-api-12.git
cd library-api-12

# Or download and extract ZIP file
wget https://github.com/AdilAzhari/library-api-12/archive/main.zip
unzip main.zip
cd library-api-12-main
```

### Step 2: Install PHP Dependencies

```bash
# Install Composer dependencies
composer install

# If you encounter memory issues:
php -d memory_limit=512M /usr/local/bin/composer install
```

### Step 3: Install Node.js Dependencies

```bash
# Install NPM dependencies
npm install

# Or using Yarn
yarn install
```

### Step 4: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 5: Configure Environment

Edit the `.env` file with your settings:

```env
# Application
APP_NAME="Library Management System"
APP_ENV=local
APP_KEY=base64:your-generated-key
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library_management
DB_USERNAME=root
DB_PASSWORD=your_password

# Redis (optional)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=log
MAIL_HOST=localhost
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@library.local"
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 6: Database Setup

```bash
# Create database (if using MySQL command line)
mysql -u root -p -e "CREATE DATABASE library_management;"

# Run migrations
php artisan migrate

# Seed the database with sample data
php artisan db:seed

# Or run migrations and seeds together
php artisan migrate --seed
```

### Step 7: Build Frontend Assets

```bash
# For development
npm run dev

# For production
npm run build
```

### Step 8: Start Development Server

```bash
# Start Laravel development server
php artisan serve

# In a separate terminal, start Vite dev server
npm run dev
```

The application will be available at `http://localhost:8000`.

### Step 9: Default Admin Account

Use these credentials to log in as an admin:

```
Email: admin@library.com
Password: password
```

## Production Installation

### Step 1: Server Preparation

#### Ubuntu/Debian Server Setup

```bash
# Update package list
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-gd php8.2-curl php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-tokenizer php8.2-json php8.2-ctype -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js and NPM
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y

# Install Nginx
sudo apt install nginx -y

# Install MySQL
sudo apt install mysql-server -y
```

#### CentOS/RHEL Server Setup

```bash
# Enable EPEL repository
sudo yum install epel-release -y

# Install PHP and extensions
sudo yum install php82 php82-fpm php82-mysql php82-xml php82-gd php82-curl php82-mbstring php82-zip php82-bcmath -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://rpm.nodesource.com/setup_18.x | sudo bash -
sudo yum install nodejs -y

# Install Nginx
sudo yum install nginx -y

# Install MySQL
sudo yum install mysql-server -y
```

### Step 2: Clone and Setup Application

```bash
# Clone to web directory
cd /var/www
sudo git clone https://github.com/AdilAzhari/library-api-12.git
cd library-api-12

# Set ownership
sudo chown -R www-data:www-data /var/www/library-api-12

# Install dependencies
sudo -u www-data composer install --optimize-autoloader --no-dev
sudo -u www-data npm ci

# Copy and configure environment
sudo -u www-data cp .env.example .env
sudo -u www-data php artisan key:generate
```

### Step 3: Production Environment Configuration

Edit `/var/www/library-api-12/.env`:

```env
# Application
APP_NAME="Library Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=library_production
DB_USERNAME=library_user
DB_PASSWORD=secure_password

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=secure_redis_password
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
```

### Step 4: Database Setup for Production

```bash
# Create database and user
mysql -u root -p << EOF
CREATE DATABASE library_production;
CREATE USER 'library_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON library_production.* TO 'library_user'@'localhost';
FLUSH PRIVILEGES;
EOF

# Run migrations
sudo -u www-data php artisan migrate --force

# Seed with production data (optional)
sudo -u www-data php artisan db:seed --class=ProductionSeeder
```

### Step 5: Optimize Application

```bash
# Clear and cache configuration
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan event:cache

# Build production assets
sudo -u www-data npm run build

# Set proper permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache
```

### Step 6: Configure Web Server

#### Nginx Configuration

Create `/etc/nginx/sites-available/library`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/library-api-12/public;
    index index.php index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Gzip compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { 
        access_log off; 
        log_not_found off; 
    }

    location = /robots.txt  { 
        access_log off; 
        log_not_found off; 
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/library /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### Apache Configuration

Create `/etc/apache2/sites-available/library.conf`:

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /var/www/library-api-12/public

    <Directory /var/www/library-api-12/public>
        AllowOverride All
        Require all granted
    </Directory>

    # Security headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"

    # Enable compression
    LoadModule deflate_module modules/mod_deflate.so
    <Location />
        SetOutputFilter DEFLATE
    </Location>

    ErrorLog ${APACHE_LOG_DIR}/library_error.log
    CustomLog ${APACHE_LOG_DIR}/library_access.log combined
</VirtualHost>
```

Enable the site:

```bash
sudo a2ensite library
sudo a2enmod rewrite headers deflate
sudo systemctl reload apache2
```

### Step 7: SSL Certificate Setup

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtain SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Verify auto-renewal
sudo certbot renew --dry-run

# Add auto-renewal to crontab
echo "0 12 * * * /usr/bin/certbot renew --quiet" | sudo crontab -
```

## Docker Installation

### Prerequisites

- Docker 20.0+
- Docker Compose 2.0+

### Step 1: Clone Repository

```bash
git clone https://github.com/AdilAzhari/library-api-12.git
cd library-api-12
```

### Step 2: Environment Setup

```bash
cp .env.docker .env
# Edit .env with your specific settings
```

### Step 3: Build and Start Containers

```bash
# Build and start all services
docker-compose up -d --build

# View logs
docker-compose logs -f app

# Check service status
docker-compose ps
```

### Step 4: Application Setup

```bash
# Install dependencies
docker-compose exec app composer install
docker-compose exec app npm install

# Generate key and run migrations
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed

# Build assets
docker-compose exec app npm run build

# Set permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Docker Compose File Example

Create `docker-compose.yml`:

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    ports:
      - "8000:80"
    volumes:
      - .:/var/www/html
    environment:
      - APP_ENV=local
      - APP_DEBUG=true
    depends_on:
      - database
      - redis

  database:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: library_management
      MYSQL_USER: library
      MYSQL_PASSWORD: password
      MYSQL_ROOT_PASSWORD: rootpassword
    volumes:
      - mysql_data:/var/lib/mysql
    ports:
      - "3306:3306"

  redis:
    image: redis:7-alpine
    volumes:
      - redis_data:/data
    ports:
      - "6379:6379"

  typesense:
    image: typesense/typesense:0.25.1
    environment:
      TYPESENSE_API_KEY: library-search-key
    volumes:
      - typesense_data:/data
    ports:
      - "8108:8108"
    command: '--data-dir /data --api-key=library-search-key --enable-cors'

volumes:
  mysql_data:
  redis_data:
  typesense_data:
```

## Configuration

### Database Configuration

#### MySQL Configuration

Optimize MySQL for the application by editing `/etc/mysql/mysql.conf.d/mysqld.cnf`:

```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
max_connections = 200
query_cache_type = 1
query_cache_size = 32M
```

#### PostgreSQL Configuration

For PostgreSQL, edit `/etc/postgresql/13/main/postgresql.conf`:

```ini
shared_buffers = 256MB
effective_cache_size = 1GB
maintenance_work_mem = 64MB
checkpoint_completion_target = 0.9
wal_buffers = 16MB
default_statistics_target = 100
random_page_cost = 1.1
effective_io_concurrency = 200
```

### PHP Configuration

Optimize PHP settings in `/etc/php/8.2/fpm/php.ini`:

```ini
memory_limit = 512M
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
max_input_vars = 3000
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 4000
opcache.revalidate_freq = 2
```

### Application Configuration

Create or edit `config/library.php`:

```php
<?php

return [
    'settings' => [
        'max_loans_per_user' => env('LIBRARY_MAX_LOANS', 5),
        'loan_period_days' => env('LIBRARY_LOAN_PERIOD', 14),
        'max_renewals' => env('LIBRARY_MAX_RENEWALS', 2),
        'grace_period_days' => env('LIBRARY_GRACE_PERIOD', 1),
        'daily_late_fee' => env('LIBRARY_DAILY_LATE_FEE', 0.50),
        'max_reservation_days' => env('LIBRARY_MAX_RESERVATION_DAYS', 7),
        'max_fine_amount' => env('LIBRARY_MAX_FINE_AMOUNT', 50.00),
    ],
    
    'features' => [
        'enable_reservations' => env('LIBRARY_ENABLE_RESERVATIONS', true),
        'enable_fines' => env('LIBRARY_ENABLE_FINES', true),
        'enable_reviews' => env('LIBRARY_ENABLE_REVIEWS', true),
        'enable_reading_lists' => env('LIBRARY_ENABLE_READING_LISTS', true),
        'enable_notifications' => env('LIBRARY_ENABLE_NOTIFICATIONS', true),
        'enable_search' => env('LIBRARY_ENABLE_SEARCH', true),
    ],
    
    'notifications' => [
        'due_reminder_days' => env('LIBRARY_DUE_REMINDER_DAYS', 3),
        'overdue_reminder_frequency' => env('LIBRARY_OVERDUE_REMINDER_FREQ', 7),
        'reservation_expiry_hours' => env('LIBRARY_RESERVATION_EXPIRY_HOURS', 48),
    ],
];
```

## Database Setup

### Creating Database and User

#### MySQL

```sql
-- Create database
CREATE DATABASE library_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user with proper permissions
CREATE USER 'library_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON library_management.* TO 'library_user'@'localhost';
FLUSH PRIVILEGES;

-- For remote connections (if needed)
CREATE USER 'library_user'@'%' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON library_management.* TO 'library_user'@'%';
FLUSH PRIVILEGES;
```

#### PostgreSQL

```sql
-- Create user
CREATE USER library_user WITH PASSWORD 'secure_password';

-- Create database
CREATE DATABASE library_management OWNER library_user;

-- Grant privileges
GRANT ALL PRIVILEGES ON DATABASE library_management TO library_user;
```

### Running Migrations

```bash
# Run all migrations
php artisan migrate

# Run migrations with seeding
php artisan migrate --seed

# Rollback migrations (if needed)
php artisan migrate:rollback

# Reset and re-run migrations
php artisan migrate:fresh --seed
```

### Database Seeding Options

```bash
# Seed with all data
php artisan db:seed

# Seed specific seeders
php artisan db:seed --class=GenreSeeder
php artisan db:seed --class=AdminUserSeeder

# Production seeding (minimal data)
php artisan db:seed --class=ProductionSeeder
```

## Search Engine Setup

### Typesense Installation

#### Using Docker

```bash
# Run Typesense server
docker run -d \
  --name typesense \
  -p 8108:8108 \
  -v $(pwd)/typesense-data:/data \
  typesense/typesense:0.25.1 \
  --data-dir /data \
  --api-key=your-typesense-api-key \
  --enable-cors
```

#### Manual Installation

```bash
# Download and install Typesense
curl -O https://dl.typesense.org/releases/0.25.1/typesense-server-0.25.1-amd64.deb
sudo dpkg -i typesense-server-0.25.1-amd64.deb

# Configure Typesense
sudo mkdir -p /etc/typesense
cat > /etc/typesense/typesense-server.ini << EOF
[server]
api-key = your-typesense-api-key
data-dir = /var/lib/typesense
api-port = 8108
enable-cors = true
EOF

# Start Typesense
sudo systemctl start typesense-server
sudo systemctl enable typesense-server
```

### Configure Search in Application

Add to `.env`:

```env
SCOUT_DRIVER=typesense
TYPESENSE_API_KEY=your-typesense-api-key
TYPESENSE_HOST=localhost
TYPESENSE_PORT=8108
TYPESENSE_PROTOCOL=http
```

### Import Data to Search Engine

```bash
# Import books to Typesense
php artisan scout:import "App\Models\Book"

# Create custom import command
php artisan make:command ImportToTypesense
```

## Queue Setup

### Redis Installation

#### Ubuntu/Debian

```bash
sudo apt install redis-server -y
sudo systemctl start redis-server
sudo systemctl enable redis-server

# Configure Redis
sudo nano /etc/redis/redis.conf
# Set: requirepass your_redis_password
# Set: maxmemory 256mb
# Set: maxmemory-policy allkeys-lru

sudo systemctl restart redis-server
```

### Supervisor Configuration

Install and configure Supervisor for queue workers:

```bash
# Install Supervisor
sudo apt install supervisor -y

# Create worker configuration
sudo nano /etc/supervisor/conf.d/library-worker.conf
```

Add the following configuration:

```ini
[program:library-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/library-api-12/artisan queue:work redis --queue=high,default,low --sleep=3 --tries=3 --max-time=3600 --timeout=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/library-worker.log
stopwaitsecs=3600
```

Start the workers:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start library-worker:*
```

### Cron Jobs

Add to crontab for scheduled tasks:

```bash
sudo crontab -e
# Add the following line:
* * * * * cd /var/www/library-api-12 && php artisan schedule:run >> /dev/null 2>&1
```

## Mail Configuration

### SMTP Configuration

Configure email settings in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Testing Email

```bash
# Test email configuration
php artisan tinker
Mail::raw('Test email', function ($message) {
    $message->to('test@example.com')->subject('Test Email');
});
```

## Troubleshooting

### Common Issues

#### 1. Permission Issues

```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache

# Fix Laravel log permissions
sudo chmod -R 775 storage/logs
```

#### 2. Composer Memory Issues

```bash
# Increase PHP memory limit
php -d memory_limit=512M /usr/local/bin/composer install

# Or set in php.ini
memory_limit = 512M
```

#### 3. Database Connection Issues

```bash
# Test database connection
php artisan tinker
DB::connection()->getPdo();

# Check MySQL is running
sudo systemctl status mysql

# Check connection details
php artisan config:show database.connections.mysql
```

#### 4. Node.js/NPM Issues

```bash
# Clear npm cache
npm cache clean --force

# Delete node_modules and reinstall
rm -rf node_modules package-lock.json
npm install

# Use specific Node version
nvm install 18
nvm use 18
```

#### 5. Application Key Issues

```bash
# Generate new application key
php artisan key:generate

# Clear configuration cache
php artisan config:clear
```

### Log Files

Check these log files for debugging:

```bash
# Laravel application logs
tail -f storage/logs/laravel.log

# Web server logs (Nginx)
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# Web server logs (Apache)
tail -f /var/log/apache2/error.log
tail -f /var/log/apache2/access.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log

# MySQL logs
tail -f /var/log/mysql/error.log

# Redis logs
tail -f /var/log/redis/redis-server.log
```

### Performance Tuning

#### Enable OPcache

Add to `php.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
```

#### Configure Laravel for Production

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Cache events
php artisan event:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### Health Checks

Create a health check endpoint:

```bash
# Add to routes/web.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
        'cache' => Cache::store()->getStore() ? 'connected' : 'disconnected',
        'timestamp' => now()->toISOString(),
    ]);
});
```

### Backup and Restore

#### Database Backup

```bash
# Create backup
mysqldump -u library_user -p library_management > backup.sql

# Restore backup
mysql -u library_user -p library_management < backup.sql
```

#### Application Backup

```bash
# Create complete backup
tar -czf library-backup-$(date +%Y%m%d).tar.gz \
  --exclude='node_modules' \
  --exclude='vendor' \
  --exclude='storage/logs/*' \
  --exclude='.git' \
  /var/www/library-api-12
```

---

For additional help or support, please refer to the [main documentation](../README.md) or create an issue on the [GitHub repository](https://github.com/AdilAzhari/library-api-12/issues).