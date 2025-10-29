# Hostinger Deployment Guide

Complete step-by-step guide to deploy your Android Platform Control Dashboard to Hostinger.

## Prerequisites

- Hostinger account with Node.js hosting
- MySQL database access
- FTP/SSH access to your hosting

## Part 1: Database Setup

### Step 1: Create MySQL Database in Hostinger

1. Login to Hostinger control panel (hPanel)
2. Go to **Databases** → **MySQL Databases**
3. Click **Create New Database**
4. Fill in:
   - Database Name: `u123456789_android_control` (use your prefix)
   - Database User: `u123456789_admin`
   - Password: Create a strong password
5. Click **Create**
6. Note down:
   - Database Name
   - Database User
   - Database Password
   - Database Host (usually: `localhost` or specific hostname)

### Step 2: Import Database Schema

1. In hPanel, go to **Databases** → **phpMyAdmin**
2. Select your database from left sidebar
3. Click **Import** tab
4. Click **Choose File** and select `database_mysql.sql`
5. Click **Go** to import
6. Verify tables are created:
   - admin_users
   - apps
   - admob_accounts
   - switching_rules
   - notifications
   - devices
   - analytics_events
   - notification_events

### Step 3: Verify Admin User

1. In phpMyAdmin, click on **admin_users** table
2. Click **Browse** to see the default admin user
3. Username: `admin`
4. Password (hashed): For first login use `admin123`
5. **IMPORTANT**: Change password after first login!

---

## Part 2: Prepare Application Files

### Step 1: Build the Application Locally

On your development machine or in Replit shell:

```bash
# Install dependencies (if not already installed)
npm install

# Build the frontend
npm run build

# This creates:
# - dist/public/ (frontend files)
# - dist/index.js (backend bundle)
```

### Step 2: Create Environment File

Create a file named `.env` with your database credentials:

```env
# Database Configuration
DATABASE_HOST=localhost
DATABASE_USER=u123456789_admin
DATABASE_PASSWORD=your_strong_password
DATABASE_NAME=u123456789_android_control
DATABASE_URL=mysql://u123456789_admin:your_strong_password@localhost:3306/u123456789_android_control

# Application Configuration
NODE_ENV=production
PORT=3000

# JWT Secret (generate a random string)
JWT_SECRET=your_very_secret_random_string_here_change_this

# Optional: If using custom domain
# ALLOWED_ORIGINS=https://yourdomain.com
```

**IMPORTANT**: Replace placeholders with your actual credentials!

### Step 3: Prepare Files for Upload

Create a deployment package with these files/folders:

```
your-app/
├── dist/               # Built files
│   ├── public/        # Frontend static files
│   └── index.js       # Backend bundle
├── node_modules/      # Dependencies
├── package.json
├── package-lock.json
├── .env              # Your environment variables
└── ecosystem.config.js  # PM2 config (we'll create this)
```

---

## Part 3: Upload to Hostinger

### Method 1: Using File Manager (Easier)

1. Login to hPanel
2. Go to **Files** → **File Manager**
3. Navigate to `public_html` or your domain folder
4. Create a new folder: `android-dashboard`
5. Upload all files from your deployment package
6. Extract if uploaded as ZIP

### Method 2: Using FTP/SFTP (Recommended)

1. Get FTP credentials from hPanel → **Files** → **FTP Accounts**
2. Use FileZilla or similar FTP client
3. Connect to your hosting
4. Navigate to `public_html/android-dashboard`
5. Upload all files

### Method 3: Using SSH (Advanced)

```bash
# Connect via SSH
ssh u123456789@yourdomain.com

# Navigate to web directory
cd public_html

# Create app directory
mkdir android-dashboard
cd android-dashboard

# Upload using rsync or scp from your local machine
# scp -r dist/ node_modules/ package*.json .env u123456789@yourdomain.com:~/public_html/android-dashboard/
```

---

## Part 4: Configure Node.js in Hostinger

### Step 1: Setup Node.js Application

1. In hPanel, go to **Advanced** → **Node.js**
2. Click **Create Application**
3. Fill in:
   - **Node.js Version**: Select latest (18.x or 20.x)
   - **Application Mode**: Production
   - **Application Root**: `public_html/android-dashboard`
   - **Application URL**: Your domain or subdomain
   - **Application Startup File**: `dist/index.js`
   - **Port**: Leave default or use 3000
4. Click **Create**

### Step 2: Install Dependencies

1. In the Node.js application section, click on your app
2. Click **Run NPM Install** or use terminal:

```bash
cd ~/public_html/android-dashboard
npm install --production
```

### Step 3: Start the Application

1. Click **Start Application** in hPanel
2. Or via terminal:

```bash
npm start
```

---

## Part 5: Configure Web Server (Apache/NGINX)

### For Apache (Most Hostinger plans use Apache)

Create `.htaccess` file in your domain root:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Redirect to Node.js app
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ http://localhost:3000/$1 [P,L]
</IfModule>

# Enable CORS if needed
Header set Access-Control-Allow-Origin "*"
Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
Header set Access-Control-Allow-Headers "Content-Type, Authorization"
```

### For NGINX (If available)

Add to your NGINX configuration:

```nginx
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
```

---

## Part 6: Update Android App

Update the BASE_URL in your Android app to point to your Hostinger URL:

```java
// In MainActivity.java, UtilsAdmob.java, MyFirebaseMessagingService.java
private static final String BASE_URL = "https://yourdomain.com";
```

---

## Part 7: Testing

### Test Backend API

```bash
# Test if server is running
curl https://yourdomain.com/api/v1/config/com.example.app

# Should return 404 or app config if app exists
```

### Test Admin Dashboard

1. Open browser: `https://yourdomain.com`
2. Login with:
   - Username: `admin`
   - Password: `admin123`
3. You should see the dashboard

### Test from Android App

1. Build and install your Android app
2. Check Logcat for:
   ```
   AdMobConfigManager: AdMob config saved successfully
   DeviceRegistration: Device registered successfully
   ```

---

## Part 8: Security & Maintenance

### Change Default Admin Password

1. Login to dashboard
2. Go to Settings (when implemented) or use phpMyAdmin
3. Update admin password immediately

### Set Up SSL Certificate

1. In hPanel, go to **Security** → **SSL**
2. Enable **Let's Encrypt SSL** for your domain
3. Update BASE_URL in Android app to use `https://`

### Enable Firewall (Optional)

1. Configure Hostinger firewall if available
2. Allow only necessary ports: 80, 443, 3000

### Regular Backups

1. In hPanel, go to **Files** → **Backups**
2. Enable automatic backups
3. Download database backups regularly from phpMyAdmin

### Monitor Application

Use PM2 for process management (if SSH access available):

```bash
# Install PM2
npm install -g pm2

# Start app with PM2
pm2 start dist/index.js --name android-dashboard

# Save PM2 process list
pm2 save

# Setup startup script
pm2 startup
```

---

## Troubleshooting

### Application Won't Start

**Check logs:**
```bash
# View application logs in hPanel or:
pm2 logs android-dashboard
```

**Common issues:**
- Missing dependencies: Run `npm install`
- Wrong NODE_ENV: Check `.env` file
- Database connection: Verify credentials in `.env`
- Port conflicts: Change PORT in `.env`

### Database Connection Error

1. Verify credentials in `.env` file
2. Check if database exists in phpMyAdmin
3. Ensure database user has all privileges
4. Try using `127.0.0.1` instead of `localhost`

### 500 Internal Server Error

1. Check application logs
2. Verify `.env` file exists and is readable
3. Check file permissions: `chmod 644 .env`
4. Verify Node.js version matches requirement

### Cannot Access from Android App

1. Verify BASE_URL is correct in Android code
2. Check SSL certificate is valid
3. Test API endpoints with curl or Postman
4. Check CORS headers are set correctly

### Analytics Not Working

1. Verify app exists in database with correct package_name
2. Check API endpoint is accessible
3. Verify Android app is sending correct format
4. Check database INSERT privileges

---

## Performance Optimization

### Enable Caching

Add to `.htaccess`:

```apache
# Cache static files
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### Enable Gzip Compression

```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>
```

### Database Optimization

Run periodically in phpMyAdmin:

```sql
-- Optimize tables
OPTIMIZE TABLE admin_users, apps, admob_accounts, switching_rules, notifications, devices, analytics_events, notification_events;

-- Analyze tables for query optimization
ANALYZE TABLE analytics_events, notification_events;
```

---

## Updating the Application

### Update Code

1. Build new version locally: `npm run build`
2. Upload new `dist/` folder via FTP
3. Restart Node.js application:

```bash
pm2 restart android-dashboard
# OR in hPanel: Stop → Start
```

### Update Dependencies

```bash
cd ~/public_html/android-dashboard
npm install
pm2 restart android-dashboard
```

### Database Migrations

1. Create migration SQL file
2. Import via phpMyAdmin
3. Test thoroughly before applying to production

---

## Support

If you encounter issues:

1. Check Hostinger documentation
2. Review application logs
3. Test API endpoints with Postman
4. Verify database connection
5. Contact Hostinger support if server-related

---

## Quick Reference

### Important URLs

- Admin Dashboard: `https://yourdomain.com`
- API Base: `https://yourdomain.com/api/v1`
- phpMyAdmin: Access via hPanel → Databases

### Default Credentials

- Admin Username: `admin`
- Admin Password: `admin123` (CHANGE THIS!)

### File Locations

- Application Root: `~/public_html/android-dashboard`
- Config File: `~/public_html/android-dashboard/.env`
- Logs: Check in hPanel or `pm2 logs`

---

🎉 **Congratulations!** Your Android Platform Control Dashboard is now deployed to Hostinger!

Remember to:
- ✅ Change default admin password
- ✅ Enable SSL certificate
- ✅ Set up regular backups
- ✅ Monitor application logs
- ✅ Update Android app BASE_URL
