# Android Platform Control Dashboard - Laravel Classic

## Project Overview

A complete rebuild of the Android Platform Control Dashboard using **pure PHP Laravel** with a simple, classic architecture. This dashboard manages multi-account AdMob switching, push notifications, and analytics across multiple Android applications.

**Original Stack:** Node.js, Express, React, PostgreSQL  
**New Stack (Simple & Classic):** Laravel 12.x, Blade Templates, Bootstrap 5, SQLite

## Current Status

### ✅ Infrastructure Setup Complete
- **PHP Version:** 8.2
- **Laravel Version:** 12.36.1
- **Composer:** Installed with all Laravel dependencies
- **Database:** SQLite (file-based, simple, no server required)
- **Frontend:** Bootstrap 5 via CDN (no build tools, no npm)
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
│   └── database.sqlite    # SQLite database file (224KB)
├── resources/
│   └── views/             # Blade templates with Bootstrap 5
├── routes/
│   ├── web.php           # Web routes
│   └── api.php           # API routes
├── old_nodejs_backup/     # Original Node.js project (reference)
└── .env                   # Environment configuration
```

### 🔄 Backup
Original Node.js/React project backed up to `old_nodejs_backup/` directory for reference.

## Technology Stack (Simple & Classic)

### Backend
- **Framework:** Laravel 12.x
- **Language:** PHP 8.2
- **Database:** SQLite (single file, zero config)
- **ORM:** Eloquent
- **Authentication:** Laravel Sanctum + JWT
- **Queue:** Laravel Queue workers
- **Validation:** Laravel Form Requests

### Frontend
- **Template Engine:** Blade templates
- **CSS Framework:** Bootstrap 5.3.2 (via CDN)
- **Icons:** Bootstrap Icons (via CDN)
- **JavaScript:** Vanilla JavaScript only
- **Build Tools:** NONE (no npm, no Vite, no bundlers)

### Deployment
- **Development Server:** PHP built-in server (port 5000)
- **Production:** Hostinger or any PHP hosting

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
- Modern UI with Bootstrap 5
- Comprehensive app management (CRUD operations)

### 4. RESTful API for Android Integration
- Configuration endpoints for AdMob accounts and switching rules
- Analytics tracking endpoints (impressions, clicks, revenue)
- Device registration for push notifications
- Notification delivery and tracking

## Database Schema (11 Tables)

### Core Tables (Already Migrated)
1. **users**: Laravel default users table
2. **cache**: Laravel cache storage
3. **jobs**: Laravel queue jobs
4. **admin_users**: Dashboard users with role-based access
5. **apps**: Android applications being managed
6. **admob_accounts**: AdMob account configurations
7. **switching_rules**: Account switching strategies per app
8. **notifications**: Push notification campaigns
9. **devices**: Registered user devices for FCM
10. **analytics_events**: AdMob impression/click/revenue tracking
11. **notification_events**: Notification delivery tracking

## API Endpoints to Implement

### Authentication
- `POST /api/auth/login` - Admin login with JWT token
- `POST /api/auth/logout` - Admin logout

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

## Why This Simple Classic Stack?

### Advantages
✅ **No Build Process** - No npm, Vite, or webpack needed  
✅ **Pure PHP** - Classic server-side rendering  
✅ **Bootstrap 5 CDN** - No CSS compilation required  
✅ **SQLite** - Zero database server configuration  
✅ **Fast Development** - Edit and refresh, no rebuilds  
✅ **Easy Deployment** - Works on any PHP hosting  
✅ **Small Footprint** - Minimal dependencies  

### Perfect For
- Classic web applications
- Server-side rendered dashboards
- Simple, maintainable architecture
- Easy hosting and deployment

## Next Steps

### Phase 1: Authentication & Admin Panel
- [ ] Create admin user seeder
- [ ] Implement login functionality
- [ ] Build dashboard homepage with Bootstrap 5
- [ ] Add navigation and layout

### Phase 2: App Management
- [ ] Create app management UI
- [ ] Implement CRUD operations
- [ ] Add validation and error handling

### Phase 3: AdMob Account Management
- [ ] Create AdMob account management UI
- [ ] Implement switching rules configuration
- [ ] Add analytics visualization

### Phase 4: Notification System
- [ ] Build notification management UI
- [ ] Implement scheduling system
- [ ] Add targeting rules

### Phase 5: Public API
- [ ] Create Android API routes
- [ ] Implement configuration endpoint
- [ ] Implement analytics tracking
- [ ] Implement device registration
- [ ] Implement notification endpoints

### Phase 6: Testing & Deployment
- [ ] Test all endpoints
- [ ] Configure for production deployment
- [ ] Create deployment documentation
- [ ] Update Android app integration guide

## Recent Changes

- **2025-10-30**: Complete rebuild to Laravel + Bootstrap 5
  - ✅ Removed all Node.js, npm, Vite dependencies
  - ✅ Converted all views from Tailwind to Bootstrap 5 CDN
  - ✅ Switched from PostgreSQL to SQLite for simplicity
  - ✅ Implemented JWT authentication with token-based auth
  - ✅ Created admin user seeder (admin/admin123)
  - ✅ Built all admin pages with Bootstrap 5:
    - Dashboard with Chart.js revenue charts
    - Apps management with CRUD modals
    - AdMob accounts with all ad unit IDs
    - Notifications with targeting & scheduling
    - Analytics with Chart.js visualizations
    - Settings for switching rules configuration
  - ✅ All API endpoints implemented (admin + Android)
  - ✅ Full CRUD operations working
  - ✅ No build process - pure Laravel + Bootstrap CDN

## Notes

- **No npm/Node.js required** - Pure PHP/Laravel stack
- **No build process** - Direct file editing, instant changes
- **Bootstrap 5 via CDN** - Modern UI without compilation
- **SQLite database** - Simple file-based storage
- Original project had comprehensive Hostinger deployment documentation
- All original features and API endpoints must be replicated
- UI maintains modern, clean design with Bootstrap 5
- Security features (JWT, password hashing, input validation) are critical
- Android integration must be seamless and well-documented
