# Deployment Guide - Hostinger MySQL

This guide explains how to deploy the Android Platform Control Laravel backend to Hostinger with MySQL database.

## Prerequisites

- Hostinger account with PHP hosting and MySQL database
- Git installed locally
- Composer installed locally
- SSH access to your Hostinger account

## Step 1: Prepare Your Local Environment

### 1.1 Clone the Repository
```bash
git clone <your-repository-url>
cd <project-directory>
```

### 1.2 Install Dependencies
```bash
composer install
npm install
```

### 1.3 Configure Environment Variables
Copy the example environment file:
```bash
cp .env.example .env
```

Generate application key:
```bash
php artisan key:generate
```

Generate JWT secret:
```bash
php artisan jwt:secret
```

## Step 2: Configure MySQL Database on Hostinger

### 2.1 Create MySQL Database
1. Log in to your Hostinger control panel (hPanel)
2. Navigate to **Databases** → **MySQL Databases**
3. Click **Create Database**
4. Database name: `android_control` (or your preferred name)
5. Create a database user with a strong password
6. Grant all privileges to the user for this database
7. Note down:
   - Database name
   - Database username
   - Database password
   - Database host (usually: `localhost` or specific host like `mysql.yourdomain.com`)

### 2.2 Update .env File with Hostinger MySQL Details
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_strong_password
```

### 2.3 Update Other Environment Variables
```env
APP_NAME="Android Control Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Your domain or Hostinger URL
BASE_URL=https://yourdomain.com

# Firebase Cloud Messaging Server Key (for push notifications)
FCM_SERVER_KEY=your_fcm_server_key_here

# JWT Configuration (auto-generated with php artisan jwt:secret)
JWT_SECRET=your_jwt_secret_here
JWT_TTL=60
```

## Step 3: Deploy to Hostinger

### 3.1 Upload Files via SSH/FTP
**Option A: Using SSH and Git**
```bash
# SSH into your Hostinger account
ssh username@yourdomain.com

# Navigate to public_html or your domain's root
cd public_html

# Clone your repository
git clone <your-repository-url> .

# Install dependencies
composer install --optimize-autoloader --no-dev

# Set proper permissions
chmod -R 755 storage bootstrap/cache
```

**Option B: Using FTP**
1. Use FileZilla or Hostinger File Manager
2. Upload all files except `node_modules`, `vendor`, `.git`
3. SSH in and run `composer install --no-dev`

### 3.2 Configure Web Root
1. In Hostinger hPanel, navigate to your domain settings
2. Set the document root to: `/public_html/public` (or wherever your Laravel `public` folder is)
3. This ensures Laravel's public folder is the web root

### 3.3 Run Database Migrations
```bash
# SSH into your server
ssh username@yourdomain.com
cd public_html

# Run migrations
php artisan migrate --force

# Seed initial admin user
php artisan db:seed --class=AdminUserSeeder
```

Default admin credentials will be:
- Username: `admin`
- Password: `admin123` (CHANGE THIS IMMEDIATELY after first login!)

### 3.4 Optimize Laravel for Production
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

## Step 4: Configure .htaccess (if needed)

If Laravel routing isn't working, ensure your `.htaccess` file in the `public` folder contains:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## Step 5: Test Your Deployment

### 5.1 Test Health Endpoint
```bash
curl https://yourdomain.com/api/health
```

### 5.2 Test Android API Endpoints

**Get Config**
```bash
curl https://yourdomain.com/api/v1/config/com.example.app
```

**Register Device**
```bash
curl -X POST https://yourdomain.com/api/v1/device/register \
  -H "Content-Type: application/json" \
  -d '{
    "package_name": "com.example.app",
    "fcm_token": "test_token_123",
    "country": "US",
    "app_version": "1.0.0",
    "android_version": 33
  }'
```

### 5.3 Test Admin Login
```bash
curl -X POST https://yourdomain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "admin123"
  }'
```

## Step 6: Android App Integration

Update your Android app's `BASE_URL` constant to point to your Hostinger domain:

```java
private static final String BASE_URL = "https://yourdomain.com";
```

Then rebuild and test your Android app.

## Step 7: Security Best Practices

### 7.1 Change Default Admin Password
```bash
php artisan tinker
$admin = App\Models\AdminUser::where('username', 'admin')->first();
$admin->password = 'your_new_secure_password';
$admin->save();
```

### 7.2 Set Proper File Permissions
```bash
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache
```

### 7.3 Enable HTTPS
1. In Hostinger hPanel, enable SSL certificate (usually free with Let's Encrypt)
2. Force HTTPS in `.env`:
```env
APP_URL=https://yourdomain.com
```

3. Add to `app/Providers/AppServiceProvider.php` boot method:
```php
if (config('app.env') === 'production') {
    \URL::forceScheme('https');
}
```

## Step 8: Monitoring and Maintenance

### 8.1 View Logs
```bash
tail -f storage/logs/laravel.log
```

### 8.2 Clear Cache (when needed)
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 8.3 Database Backup
Set up regular MySQL backups in Hostinger hPanel:
- Navigate to **Databases** → **phpMyAdmin**
- Export your database regularly
- Or use Hostinger's automatic backup feature

## API Endpoints Summary

### Android Integration (No Auth Required)
- `GET /api/v1/config/{packageName}` - Get AdMob configuration
- `POST /api/v1/device/register` - Register device
- `POST /api/v1/analytics/admob` - Track AdMob events
- `GET /api/v1/notifications/pending` - Get pending notifications
- `POST /api/v1/notifications/track` - Track notification events

### Admin API (Auth Required - JWT Token)
- `POST /api/auth/login` - Admin login
- `GET /api/admin/dashboard/stats` - Dashboard statistics
- `GET|POST|PUT|DELETE /api/admin/apps` - Manage apps
- `GET|POST|PUT|DELETE /api/admin/admob-accounts` - Manage AdMob accounts
- `GET|POST|PUT|DELETE /api/admin/notifications` - Manage notifications
- `GET|POST|PUT /api/admin/switching-rules` - Manage switching rules

## Troubleshooting

### Issue: 500 Internal Server Error
- Check `storage/logs/laravel.log`
- Ensure proper file permissions
- Clear cache with `php artisan cache:clear`
- Check `.env` configuration

### Issue: Database Connection Failed
- Verify MySQL credentials in `.env`
- Check database host (might be `localhost` or specific hostname)
- Ensure database user has proper privileges

### Issue: Routes Not Working
- Check that web root is set to `/public` folder
- Verify `.htaccess` file exists in public folder
- Clear route cache: `php artisan route:clear`
- Recache routes: `php artisan route:cache`

### Issue: JWT Token Invalid
- Regenerate JWT secret: `php artisan jwt:secret`
- Clear config cache: `php artisan config:clear`
- Recache config: `php artisan config:cache`

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Review Hostinger documentation: https://support.hostinger.com
3. Laravel documentation: https://laravel.com/docs

## Database Schema

The application uses these tables:
- `admin_users` - Admin authentication
- `apps` - Android applications
- `admob_accounts` - AdMob account configurations
- `switching_rules` - AdMob switching strategies
- `notifications` - Push notifications
- `devices` - Registered Android devices
- `analytics_events` - AdMob analytics tracking
- `notification_events` - Notification event tracking
