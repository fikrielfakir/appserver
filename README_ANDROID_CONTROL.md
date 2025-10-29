# Android Platform Control - Laravel Backend

A comprehensive Laravel backend system for managing Android applications with dynamic AdMob account switching, push notifications, and analytics tracking.

## Features

### 🎯 Core Features
- **Multi-App Management**: Manage multiple Android applications from a single dashboard
- **Dynamic AdMob Switching**: Switch between multiple AdMob accounts using various strategies
- **Push Notifications**: Send targeted notifications to Android devices
- **Analytics Tracking**: Track AdMob impressions, clicks, and revenue
- **Device Management**: Register and track Android devices with FCM integration

### 📊 AdMob Switching Strategies
- **Weighted Random**: Distribute traffic based on account weights
- **Rotation**: Rotate accounts on hourly/daily/weekly/monthly intervals  
- **Priority**: Select accounts based on priority levels
- **A/B Testing**: Randomly distribute traffic for testing

### 🎯 Notification Targeting
- Target by country, app version, and Android version
- Schedule notifications with start/end dates
- Control display frequency and limits
- Track notification events (displayed, clicked, dismissed)

## Technology Stack

- **Framework**: Laravel 12.x
- **Database**: MySQL 8.0+
- **Authentication**: JWT (tymon/jwt-auth)
- **PHP Version**: 8.2+
- **Node.js**: For assets compilation

## Installation

### 1. Clone Repository
```bash
git clone <your-repository-url>
cd <project-directory>
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

### 4. Configure Database
Update `.env` with your MySQL credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=android_control
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Run Migrations
```bash
php artisan migrate
php artisan db:seed --class=AdminUserSeeder
```

Default admin credentials:
- Username: `admin`
- Password: `admin123` (Change immediately!)

### 6. Start Development Server
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## API Documentation

### Android Integration Endpoints

#### Get AdMob Configuration
```http
GET /api/v1/config/{packageName}
```
Returns active AdMob account configuration based on switching rules.

#### Register Device
```http
POST /api/v1/device/register
Content-Type: application/json

{
  "package_name": "com.example.app",
  "fcm_token": "device_fcm_token",
  "country": "US",
  "app_version": "1.0.0",
  "android_version": 33,
  "device_manufacturer": "Samsung",
  "device_model": "Galaxy S21"
}
```

#### Track AdMob Analytics
```http
POST /api/v1/analytics/admob
Content-Type: application/json

{
  "package_name": "com.example.app",
  "account_id": "uuid",
  "event": "impression",
  "ad_type": "banner",
  "value": 100
}
```

#### Get Pending Notifications
```http
GET /api/v1/notifications/pending?package_name=com.example.app&country=US
```

#### Track Notification Events
```http
POST /api/v1/notifications/track
Content-Type: application/json

{
  "notification_id": "uuid",
  "device_id": "uuid",
  "event_type": "displayed"
}
```

### Admin API Endpoints (Requires JWT Auth)

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "username": "admin",
  "password": "admin123"
}
```

Returns JWT token for subsequent requests.

#### Manage Apps
```http
GET /api/admin/apps              # List all apps
POST /api/admin/apps             # Create new app
GET /api/admin/apps/{id}         # Get app details
PUT /api/admin/apps/{id}         # Update app
DELETE /api/admin/apps/{id}      # Delete app
```

#### Manage AdMob Accounts
```http
GET /api/admin/admob-accounts           # List all accounts
POST /api/admin/admob-accounts          # Create new account
PUT /api/admin/admob-accounts/{id}      # Update account
DELETE /api/admin/admob-accounts/{id}   # Delete account
```

#### Manage Notifications
```http
GET /api/admin/notifications              # List all notifications
POST /api/admin/notifications             # Create notification
PUT /api/admin/notifications/{id}         # Update notification
POST /api/admin/notifications/{id}/send   # Send notification
DELETE /api/admin/notifications/{id}      # Delete notification
```

#### Dashboard Statistics
```http
GET /api/admin/dashboard/stats
```

## Database Schema

### Main Tables
- `admin_users` - Admin user accounts
- `apps` - Android applications
- `admob_accounts` - AdMob account configurations
- `switching_rules` - Account switching strategies
- `notifications` - Push notification definitions
- `devices` - Registered Android devices
- `analytics_events` - AdMob event tracking
- `notification_events` - Notification interaction tracking

## Android Integration

### Java Integration Files
Use the provided Java integration files in `attached_assets/`:
- `AdMobConfigManager.java` - Manages AdMob configuration
- `DeviceRegistrationManager.java` - Handles device registration
- `NotificationManager.java` - Manages notifications
- `MyFirebaseMessagingService.java` - FCM service

### Setup in Android
1. Update `BASE_URL` in Java files to your server URL
2. Add Firebase dependencies to your `build.gradle`
3. Configure FCM in your Android app
4. Initialize managers in your `MainActivity`

```java
private static final String BASE_URL = "https://your-domain.com";

// Initialize managers
AdMobConfigManager configManager = new AdMobConfigManager(this, BASE_URL);
DeviceRegistrationManager deviceManager = new DeviceRegistrationManager(this, BASE_URL);
NotificationManager notificationManager = new NotificationManager(this, BASE_URL);

// Fetch configuration
configManager.fetchConfig(new AdMobConfigManager.ConfigCallback() {
    @Override
    public void onSuccess() {
        String bannerId = configManager.getBannerId();
        // Use the banner ID
    }

    @Override
    public void onError(String error) {
        // Handle error
    }
});
```

## Deployment

See [DEPLOYMENT_HOSTINGER.md](DEPLOYMENT_HOSTINGER.md) for detailed deployment instructions for Hostinger with MySQL.

### Quick Deployment Checklist
- [ ] Configure MySQL database
- [ ] Update `.env` with production settings
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed admin user: `php artisan db:seed`
- [ ] Optimize for production: `php artisan config:cache`
- [ ] Set web root to `/public` folder
- [ ] Enable HTTPS/SSL
- [ ] Change default admin password
- [ ] Test all endpoints

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

### Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Project Structure

```
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/
│   │   │   ├── Admin/          # Admin dashboard controllers
│   │   │   └── V1/             # Android API controllers
│   ├── Models/                 # Eloquent models
│   └── Services/               # Business logic services
├── database/
│   ├── migrations/             # Database migrations
│   └── seeders/                # Database seeders
├── routes/
│   └── api.php                 # API routes
└── config/
    └── database.php            # Database configuration
```

## Services

### AdMobStrategyService
Handles AdMob account selection based on configured strategies.

### NotificationTargetingService
Evaluates notification targeting rules and display limits.

### AnalyticsService
Tracks and aggregates AdMob analytics events.

### DeviceService
Manages device registration and tracking.

## Security

- JWT authentication for admin endpoints
- Password hashing using bcrypt
- CORS configuration for API access
- Environment variable protection
- SQL injection protection via Eloquent ORM

## License

Proprietary - All rights reserved

## Support

For deployment issues, refer to:
- `DEPLOYMENT_HOSTINGER.md` - Hostinger deployment guide
- Laravel documentation: https://laravel.com/docs
- Laravel logs: `storage/logs/laravel.log`
