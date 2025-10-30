# Deploying Laravel to Render

This guide will help you deploy your Laravel application to Render.com.

## Prerequisites

- A Render account (sign up at https://render.com)
- Your code in a Git repository (GitHub, GitLab, or Bitbucket)

## Step 1: Prepare Your Application

### 1.1 Create a Build Script

Create a file named `build.sh` in your project root:

```bash
#!/usr/bin/env bash
set -o errexit

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Generate application key if not set
php artisan key:generate --force --no-interaction

# Run database migrations
php artisan migrate --force --no-interaction

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create symbolic link for storage
php artisan storage:link
```

Make it executable:
```bash
chmod +x build.sh
```

### 1.2 Create a Start Script

Create a file named `start.sh` in your project root:

```bash
#!/usr/bin/env bash
php artisan serve --host=0.0.0.0 --port=$PORT
```

Make it executable:
```bash
chmod +x start.sh
```

### 1.3 Update .gitignore

Make sure your `.gitignore` includes:
```
/vendor
.env
/storage/*.key
/storage/logs/*
/storage/framework/cache/*
/storage/framework/sessions/*
/storage/framework/views/*
```

## Step 2: Set Up Database on Render

1. Go to your Render Dashboard
2. Click "New +" and select "PostgreSQL"
3. Configure your database:
   - Name: `android-control-db` (or your preferred name)
   - Database: `android_control`
   - User: (auto-generated)
   - Region: Choose closest to your users
   - Plan: Free or paid based on your needs
4. Click "Create Database"
5. Save the connection details (you'll need them for environment variables)

## Step 3: Create Web Service on Render

1. Go to your Render Dashboard
2. Click "New +" and select "Web Service"
3. Connect your Git repository
4. Configure your service:
   - **Name**: `android-control-app` (or your preferred name)
   - **Region**: Same as your database
   - **Branch**: `main` (or your deployment branch)
   - **Root Directory**: Leave empty (unless your Laravel app is in a subdirectory)
   - **Runtime**: `PHP`
   - **Build Command**: `./build.sh`
   - **Start Command**: `./start.sh`
   - **Plan**: Free or paid based on your needs

## Step 4: Configure Environment Variables

In your Render web service settings, go to "Environment" and add these variables:

### Required Variables:
```
APP_NAME=Android Control Platform
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.onrender.com

# Database - Get these from your Render PostgreSQL database
DB_CONNECTION=pgsql
DB_HOST=<your-postgres-host>
DB_PORT=5432
DB_DATABASE=android_control
DB_USERNAME=<your-postgres-username>
DB_PASSWORD=<your-postgres-password>

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail (update with your mail service)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS=hello@example.com

# JWT & FCM (add your actual keys)
JWT_SECRET=<your-jwt-secret>
FCM_SERVER_KEY=<your-fcm-server-key>
```

### Generate APP_KEY:
Run this locally to generate an app key:
```bash
php artisan key:generate --show
```
Copy the output and add it as:
```
APP_KEY=base64:your-generated-key-here
```

### Generate JWT_SECRET:
Run this locally:
```bash
php artisan jwt:secret --show
```
Copy the output and add it to your environment variables.

## Step 5: Update Database Configuration

Since Render provides PostgreSQL, make sure your `config/database.php` has the PostgreSQL configuration (it already does).

The app will automatically use PostgreSQL when `DB_CONNECTION=pgsql` is set in environment variables.

## Step 6: Deploy

1. Click "Create Web Service" or "Manual Deploy"
2. Render will:
   - Clone your repository
   - Run the build script
   - Install dependencies
   - Run migrations
   - Start your application
3. Wait for deployment to complete (usually 5-10 minutes)
4. Visit your app at: `https://your-app-name.onrender.com`

## Step 7: Verify Deployment

1. Open your app URL
2. You should see the Admin Login page
3. Check the Render logs for any errors:
   - Go to your service dashboard
   - Click "Logs" tab

## Important Notes

### Free Tier Limitations:
- Render's free tier spins down after 15 minutes of inactivity
- First request after spin-down may take 30-60 seconds
- Consider upgrading to paid tier for production use

### Database Backups:
- Free PostgreSQL databases are deleted after 90 days
- Paid plans include automatic backups
- Export your database regularly

### File Storage:
- Render's filesystem is ephemeral
- Use external storage (AWS S3, Cloudinary) for user uploads
- Configure Laravel filesystem in `config/filesystems.php`

### Custom Domain:
1. Go to your web service settings
2. Click "Custom Domains"
3. Add your domain and follow DNS configuration instructions

## Troubleshooting

### Build Fails:
- Check Render logs for specific errors
- Ensure `build.sh` has correct permissions
- Verify all dependencies are in `composer.json`

### App Crashes:
- Check environment variables are set correctly
- Verify database connection settings
- Review application logs in Render dashboard

### 500 Errors:
- Set `APP_DEBUG=true` temporarily to see detailed errors
- Check storage directory permissions
- Verify `.env` variables match production settings

### Database Connection Issues:
- Double-check database credentials
- Ensure database is in same region as web service
- Verify `DB_CONNECTION=pgsql` is set

## Next Steps

1. Set up proper FCM credentials for push notifications
2. Configure JWT for authentication
3. Set up email service (SendGrid, Mailgun, etc.)
4. Configure external file storage
5. Set up monitoring and error tracking (Sentry, Bugsnag)
6. Enable SSL (automatic with Render)
7. Set up CI/CD for automatic deployments

## Support

For Render-specific issues, check:
- Render Documentation: https://render.com/docs
- Render Community: https://community.render.com

For Laravel issues:
- Laravel Documentation: https://laravel.com/docs
- Laravel Forums: https://laracasts.com/discuss
