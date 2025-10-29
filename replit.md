# Android Platform Control Dashboard - PHP Laravel Rebuild

## Project Overview

A complete rebuild of the Android Platform Control Dashboard using PHP full-stack technology with Laravel framework. This dashboard manages multi-account AdMob switching, push notifications, and analytics across multiple Android applications.

**Original Stack:** Node.js, Express, React, PostgreSQL  
**New Stack:** Laravel 12.x, Blade/Livewire, SQLite (temporary)

## Current Status

### ✅ Infrastructure Setup Complete
- **PHP Version:** 8.2
- **Laravel Version:** 12.36.1
- **Composer:** Installed with all Laravel dependencies
- **Node.js:** Installed for Laravel's Vite frontend tooling
- **Database:** SQLite configured (PostgreSQL planned for production)
- **Server:** Laravel development server running on port 5000
- **Workflow:** Configured and running successfully

### 📁 Project Structure
```
├── app/                    # Laravel application code
│   ├── Http/              # Controllers, Middleware
│   ├── Models/            # Eloquent models
│   └── Providers/         # Service providers
├── database/              
│   ├── migrations/        # Database migrations
│   └── database.sqlite    # SQLite database file
├── resources/
│   ├── views/             # Blade templates
│   ├── css/              # Stylesheets
│   └── js/               # JavaScript files
├── routes/
│   ├── web.php           # Web routes
│   └── api.php           # API routes
├── old_nodejs_backup/     # Original Node.js project (reference)
└── .env                   # Environment configuration
```

### 🔄 Backup
Original Node.js/React project backed up to `old_nodejs_backup/` directory for reference.

## Features to Rebuild

### 1. Multi-Account AdMob Management
- Unlimited AdMob accounts per app
- Multiple switching strategies:
  - Weighted Random: Distribution by percentage weights
  - Sequential Rotation: Priority order rotation
  - Geographic Targeting: Route by country/region
  - Time-Based: Switch by time of day
- A/B testing capabilities
- Real-time analytics (revenue, fill rates, distribution)

### 2. Push Notification System
- Multiple notification types (popup, toast, banner, fullscreen)
- Advanced targeting:
  - Geographic (by country)
  - App version specific
  - Android version specific
  - User segments (new/active users)
- Flexible scheduling (immediate, scheduled, recurring)
- Display rules (max displays, intervals, triggers)
- Comprehensive analytics (delivery, open rates, conversions)

### 3. Admin Dashboard
- JWT-based authentication with RBAC
- Real-time metrics and analytics visualization
- Modern UI with dark mode support
- Comprehensive app management (CRUD operations)

### 4. RESTful API for Android Integration
- Configuration endpoints for AdMob accounts and switching rules
- Analytics tracking endpoints (impressions, clicks, revenue)
- Device registration for push notifications
- Notification delivery and tracking

## Database Schema (8 Tables)

### Core Tables
1. **admin_users**: Dashboard users with role-based access
2. **apps**: Android applications being managed
3. **admob_accounts**: AdMob account configurations
4. **switching_rules**: Account switching strategies per app
5. **notifications**: Push notification campaigns
6. **devices**: Registered user devices for FCM
7. **analytics_events**: AdMob impression/click/revenue tracking
8. **notification_events**: Notification delivery tracking

## API Endpoints to Implement

### Authentication
- `POST /api/auth/login` - Admin login with JWT token

### Admin Dashboard (Protected)
- `GET /api/admin/dashboard/stats` - Dashboard statistics
- `GET|POST|PUT|DELETE /api/admin/apps` - App management
- `GET|POST|PUT|DELETE /api/admin/admob-accounts` - AdMob account management
- `GET|POST|PUT|DELETE /api/admin/switching-rules` - Switching rules management
- `GET|POST|PUT|DELETE /api/admin/notifications` - Notification management
- `POST /api/admin/notifications/:id/send` - Send notification

### Public API (For Android Apps)
- `GET /api/v1/config/:packageName` - Get app configuration
- `POST /api/v1/analytics/admob` - Track analytics events
- `POST /api/v1/device/register` - Register device for notifications
- `GET /api/v1/notifications/pending` - Get pending notifications
- `POST /api/v1/notifications/track` - Track notification events

## Technology Stack

### Backend
- **Framework:** Laravel 12.x
- **Language:** PHP 8.2
- **Database:** SQLite (development), PostgreSQL (production)
- **ORM:** Eloquent
- **Authentication:** Laravel Sanctum + JWT
- **Queue:** Laravel Queue workers
- **Validation:** Laravel Form Requests

### Frontend
- **Template Engine:** Blade templates
- **Interactivity:** Laravel Livewire
- **Styling:** Tailwind CSS (via Vite)
- **Build Tool:** Vite 7
- **Icons:** To be determined

### Deployment
- **Development Server:** PHP built-in server (port 5000)
- **Production:** To be configured for Hostinger or similar

## Environment Configuration

Current `.env` settings:
- **APP_NAME:** Laravel
- **APP_ENV:** local
- **APP_DEBUG:** true
- **DB_CONNECTION:** sqlite
- **SESSION_DRIVER:** database
- **QUEUE_CONNECTION:** database
- **CACHE_STORE:** database

## Default Credentials (To Be Created)
- **Username:** admin
- **Password:** admin123

## Android Integration Reference

The original project includes comprehensive Android integration guides:
- `old_nodejs_backup/ANDROID_INTEGRATION.md` - Complete integration guide
- `old_nodejs_backup/ANDROID_QUICK_START.md` - Quick start guide

These will serve as references for the API endpoints and Android communication patterns.

## Development Workflow

1. **Start Server:** Workflow "Laravel Server" runs automatically
2. **Access App:** http://0.0.0.0:5000
3. **View Logs:** Available through workflow logs
4. **Database:** SQLite file at `database/database.sqlite`

## Next Steps

### Phase 1: Database & Models
- [ ] Create all 8 database migration files
- [ ] Create Eloquent models with relationships
- [ ] Set up seeders for default admin user

### Phase 2: Authentication
- [ ] Install Laravel Sanctum
- [ ] Set up JWT authentication
- [ ] Create login API endpoint
- [ ] Add auth middleware

### Phase 3: Admin Dashboard
- [ ] Create dashboard layout with Blade/Livewire
- [ ] Implement app management (CRUD)
- [ ] Implement AdMob account management
- [ ] Implement switching rules management
- [ ] Implement notification management
- [ ] Add analytics visualization

### Phase 4: Public API
- [ ] Create Android API routes
- [ ] Implement configuration endpoint
- [ ] Implement analytics tracking
- [ ] Implement device registration
- [ ] Implement notification endpoints

### Phase 5: Testing & Deployment
- [ ] Test all endpoints
- [ ] Configure for production deployment
- [ ] Create deployment documentation
- [ ] Update Android app integration guide

## Recent Changes

- **2025-10-29**: Laravel 12.x project initialized
- PHP 8.2 and Composer installed
- Node.js dependencies installed for Vite
- Laravel development server configured on port 5000
- SQLite database configured and migrated
- Original Node.js project backed up for reference

## Notes

- Original project had comprehensive Hostinger deployment documentation
- All original features and API endpoints must be replicated
- UI should maintain modern, clean design principles
- Security features (JWT, password hashing, input validation) are critical
- Android integration must be seamless and well-documented
