# Language Learning System - Installation and Setup Guide

## Quick Start Guide

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Apache with mod_rewrite
- Git

### Step 1: Clone the Repository

```bash
git clone https://github.com/ezinwaminiangle2001/php-language-app.git
cd php-language-app
```

### Step 2: Set Up Database

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE language_learning CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import schema
mysql -u root -p language_learning < database/schema.sql
```

### Step 3: Configure Database Connection

Edit `config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'language_learning');
define('DB_USER', 'root');
define('DB_PASSWORD', 'your_password');
```

### Step 4: Set Directory Permissions

```bash
chmod -R 755 public/
chmod -R 755 logs/
```

### Step 5: Configure Apache

Ensure mod_rewrite is enabled:

```bash
a2enmod rewrite
sudo systemctl restart apache2
```

Create `.htaccess` in `public/` directory (already included).

### Step 6: Access the Application

Open your browser and navigate to:

```
http://localhost/php-language-app
```

## Docker Setup (Optional)

If you prefer Docker:

```bash
docker-compose up -d
```

## Troubleshooting

### "Connection refused" Error
- Check if MySQL is running
- Verify database credentials
- Test connection: `mysql -u root -p -h localhost`

### 404 Errors on Routes
- Enable mod_rewrite: `a2enmod rewrite`
- Restart Apache: `sudo systemctl restart apache2`
- Check `.htaccess` file exists in `public/`

### Permission Denied
- Fix permissions: `sudo chown -R www-data:www-data /var/www/php-language-app`
- Set correct permissions: `chmod -R 755 public/`

## Environment Configuration

### Production Setup

Set in `config/config.php`:

```php
define('APP_ENV', 'production');
error_reporting(E_ALL);
ini_set('display_errors', 0);
```

### Development Setup

```php
define('APP_ENV', 'development');
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Performance Tips

1. Enable database query caching
2. Use PHP OpCache
3. Enable Gzip compression in Apache
4. Implement CDN for static assets
5. Use database connection pooling

## Security Checklist

- [ ] Change default admin password
- [ ] Configure HTTPS/SSL
- [ ] Set proper file permissions
- [ ] Enable PHP security settings
- [ ] Regular database backups
- [ ] Keep PHP and MySQL updated
- [ ] Use strong database password
- [ ] Implement rate limiting

## Next Steps

After installation:

1. Create your first language course
2. Add vocabulary words
3. Create quizzes
4. Invite users
5. Monitor progress

For more information, see `README.md`
