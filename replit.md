# Android Platform Control Dashboard

A comprehensive admin dashboard for managing multi-account AdMob switching and push notifications across multiple Android applications.

## Overview

This is a full-stack web application built with React, Express, and PostgreSQL that provides centralized control and management for Android app monetization and user engagement.

## Key Features

### 1. Multi-Account AdMob Management
- Support for unlimited AdMob accounts per app
- Multiple switching strategies:
  - Weighted Random: Distribute traffic based on percentage weights
  - Sequential Rotation: Rotate accounts in priority order
  - Geographic Targeting: Route by country/region
  - Time-Based: Switch based on time of day
- A/B testing capabilities to compare account performance
- Real-time analytics showing revenue, fill rates, and distribution

### 2. Push Notification System
- Multiple notification types: popup, toast, banner, fullscreen
- Advanced targeting:
  - Geographic (by country)
  - App version specific
  - Android version specific
  - User segments (new users, active users)
- Flexible scheduling (immediate, scheduled, recurring)
- Display rules (max displays, intervals, triggers)
- Comprehensive analytics (delivery rate, open rate, conversions)

### 3. Admin Dashboard
- Secure JWT-based authentication with role-based access control
- Real-time metrics and analytics visualization
- Intuitive Material Design UI with dark mode support
- Comprehensive app management (create, edit, delete)

### 4. RESTful API for Android Integration
- Configuration endpoints for fetching AdMob accounts and switching rules
- Analytics tracking endpoints for impressions, clicks, and revenue
- Device registration for push notifications
- Notification delivery and tracking

## Tech Stack

### Frontend
- **Framework**: React 18 with TypeScript
- **Routing**: Wouter
- **State Management**: React Query (TanStack Query)
- **UI Components**: shadcn/ui + Radix UI
- **Styling**: Tailwind CSS
- **Forms**: React Hook Form + Zod validation
- **Charts**: Recharts
- **Icons**: Lucide React

### Backend
- **Runtime**: Node.js 20
- **Framework**: Express.js
- **Database**: PostgreSQL (Neon)
- **ORM**: Drizzle ORM
- **Authentication**: JWT (jsonwebtoken)
- **Password Hashing**: bcrypt
- **Validation**: Zod

## Android Integration

This backend provides API endpoints for Android apps to fetch AdMob configurations, track analytics, and receive push notifications. See the integration guides:

- **[ANDROID_INTEGRATION.md](./ANDROID_INTEGRATION.md)** - Complete integration guide with examples
- **[ANDROID_QUICK_START.md](./ANDROID_QUICK_START.md)** - 5-minute quick start guide

Key API endpoints:
- `GET /api/v1/config/{packageName}` - Fetch AdMob accounts and configurations
- `POST /api/v1/analytics/admob` - Track ad impressions, clicks, and revenue
- `POST /api/v1/device/register` - Register devices for push notifications

## Project Structure

```
├── client/                 # Frontend React application
│   ├── src/
│   │   ├── components/    # Reusable UI components
│   │   ├── contexts/      # React contexts (Auth, Theme)
│   │   ├── pages/         # Page components
│   │   ├── lib/           # Utilities and configurations
│   │   └── hooks/         # Custom React hooks
│   └── index.html
├── server/                 # Backend Express application
│   ├── middleware/        # Auth middleware
│   ├── routes.ts          # API route definitions
│   ├── storage.ts         # Data access layer
│   └── db.ts              # Database connection
├── shared/                 # Shared code between frontend/backend
│   └── schema.ts          # Database schema and types
└── design_guidelines.md   # UI/UX design specifications
```

## Database Schema

### Core Tables
- **admin_users**: Dashboard users with role-based access
- **apps**: Android applications being managed
- **admob_accounts**: AdMob account configurations
- **switching_rules**: Account switching strategies per app
- **notifications**: Push notification campaigns
- **devices**: Registered user devices for FCM
- **analytics_events**: AdMob impression/click/revenue tracking
- **notification_events**: Notification delivery tracking

## API Endpoints

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

## Getting Started

### Default Credentials
- **Username**: admin
- **Password**: admin123

The default admin account is auto-created on first login.

### Development
The app automatically runs on port 5000 via the "Start application" workflow, which runs `npm run dev`.

### Environment Variables
- `DATABASE_URL` - PostgreSQL connection string (automatically configured)
- `SESSION_SECRET` - JWT secret key (automatically configured)

## Android Integration Guide

### 1. Fetch App Configuration
```java
GET /api/v1/config/com.example.myapp
```

Response includes:
- AdMob account configurations
- Switching strategy rules
- Active notifications

### 2. Select AdMob Account
Implement the switching logic based on strategy:
- `weighted_random`: Select based on weight percentages
- `sequential`: Use priority order
- `geographic`: Match user's country
- `time_based`: Match current time

### 3. Track Analytics
```java
POST /api/v1/analytics/admob
{
  "package_name": "com.example.myapp",
  "account_id": "account-id",
  "event": "impression",
  "ad_type": "banner"
}
```

### 4. Register for Push Notifications
```java
POST /api/v1/device/register
{
  "package_name": "com.example.myapp",
  "fcm_token": "...",
  "device_info": {
    "country": "US",
    "app_version": "1.0.0",
    "android_version": 30
  }
}
```

### 5. Handle Notifications
Fetch and display notifications based on targeting rules and display conditions.

## Security Features

- JWT-based authentication with 7-day expiration
- Password hashing with bcrypt
- Protected API routes with auth middleware
- Input validation with Zod schemas
- SQL injection prevention via Drizzle ORM
- XSS protection via React's built-in sanitization

## Recent Updates

- **2025-10-29**: Initial MVP implementation with complete frontend, backend, and database
- Implemented JWT authentication and protected routes
- Created comprehensive admin dashboard with all management features
- Built complete REST API for Android integration
- Set up PostgreSQL database with Drizzle ORM
- Designed beautiful Material Design UI with dark mode support

## Future Enhancements

- Two-factor authentication for admin users
- Real-time WebSocket updates for analytics
- Advanced performance-based AdMob switching
- Bulk operations for managing multiple apps
- Custom notification template builder
- Detailed analytics reports with export functionality
- IP whitelisting and enhanced security features
- Notification A/B testing with automatic winner selection
- Mobile app for dashboard access
