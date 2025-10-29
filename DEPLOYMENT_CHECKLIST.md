# 🚀 Hostinger Deployment Checklist

Quick reference checklist for deploying to Hostinger with MySQL.

## ✅ Pre-Deployment Checklist

### Local Preparation
- [ ] All code is tested and working
- [ ] Dependencies are up to date (`npm install`)
- [ ] Build completes successfully (`npm run build`)
- [ ] MySQL schema file is ready (`database_mysql.sql`)

### Hostinger Account Setup
- [ ] Hostinger hosting account is active
- [ ] Node.js support is enabled in your plan
- [ ] SSH/FTP access credentials are available
- [ ] Domain or subdomain is configured

---

## 📦 Step 1: Create Deployment Package

Run the packaging script:
```bash
chmod +x package-for-hostinger.sh
./package-for-hostinger.sh
```

This creates:
- ✅ `hostinger-deployment-package.zip` - Ready to upload
- ✅ `hostinger-package/` - Unzipped version

What's included:
- Built application files (`dist/`)
- Dependencies (`node_modules/`)
- Database SQL (`database_mysql.sql`)
- Configuration files (`.env`, `ecosystem.config.js`, `.htaccess`)
- Documentation

---

## 🗄️ Step 2: Setup MySQL Database

### 2.1 Create Database
- [ ] Login to Hostinger hPanel
- [ ] Navigate to **Databases** → **MySQL Databases**
- [ ] Click **Create New Database**
- [ ] Fill in database details:
  - Name: `u123456789_android_control`
  - User: `u123456789_admin`
  - Password: (strong password)
- [ ] Note down credentials:
  ```
  Host: _________________
  Database: _________________
  User: _________________
  Password: _________________
  ```

### 2.2 Import Database Schema
- [ ] Go to **Databases** → **phpMyAdmin**
- [ ] Select your database from sidebar
- [ ] Click **Import** tab
- [ ] Upload `database_mysql.sql`
- [ ] Click **Go** to import
- [ ] Verify 8 tables were created:
  - admin_users
  - apps
  - admob_accounts
  - switching_rules
  - notifications
  - devices
  - analytics_events
  - notification_events

### 2.3 Verify Default Admin
- [ ] Click on `admin_users` table
- [ ] Verify admin user exists
- [ ] Username: `admin`
- [ ] Password will be: `admin123` (first login)

---

## 📤 Step 3: Upload Files to Hostinger

### Choose Upload Method:

#### Option A: File Manager (Easiest)
- [ ] Go to **Files** → **File Manager**
- [ ] Navigate to `public_html` or your domain folder
- [ ] Create folder: `android-dashboard`
- [ ] Upload `hostinger-deployment-package.zip`
- [ ] Extract the ZIP file
- [ ] Verify all files are extracted

#### Option B: FTP/SFTP (Recommended)
- [ ] Get FTP credentials from hPanel
- [ ] Connect with FileZilla or similar
- [ ] Navigate to `public_html/android-dashboard`
- [ ] Upload entire `hostinger-package/` folder contents
- [ ] Verify upload completed (check file count)

#### Option C: SSH (Advanced)
```bash
# Connect to server
ssh u123456789@yourdomain.com

# Create directory
mkdir -p ~/public_html/android-dashboard
cd ~/public_html/android-dashboard

# Upload via scp from local machine
scp -r hostinger-package/* u123456789@yourdomain.com:~/public_html/android-dashboard/
```

---

## ⚙️ Step 4: Configure Environment Variables

### 4.1 Edit .env File
- [ ] Open `.env` file in File Manager or FTP
- [ ] Update with your actual credentials:

```env
DATABASE_HOST=localhost
DATABASE_USER=u123456789_admin
DATABASE_PASSWORD=YOUR_ACTUAL_PASSWORD
DATABASE_NAME=u123456789_android_control
DATABASE_URL=mysql://u123456789_admin:YOUR_PASSWORD@localhost:3306/u123456789_android_control

NODE_ENV=production
PORT=3000

JWT_SECRET=GENERATE_A_RANDOM_LONG_STRING_HERE
```

### 4.2 Generate JWT Secret
Use one of these:
```bash
# Method 1: OpenSSL
openssl rand -base64 32

# Method 2: Node.js
node -e "console.log(require('crypto').randomBytes(32).toString('base64'))"

# Method 3: Online
# Visit: https://randomkeygen.com/
```

- [ ] JWT_SECRET is set to a random, secure value
- [ ] All DATABASE_* values match your Hostinger database
- [ ] Save `.env` file

---

## 🖥️ Step 5: Configure Node.js Application

### 5.1 Setup in hPanel
- [ ] Go to **Advanced** → **Node.js**
- [ ] Click **Create Application**
- [ ] Configure:
  - **Node.js Version**: 18.x or 20.x
  - **Application Mode**: Production
  - **Application Root**: `public_html/android-dashboard`
  - **Application URL**: `yourdomain.com` or `subdomain.yourdomain.com`
  - **Application Startup File**: `dist/index.js`
  - **Port**: 3000 (or leave default)
- [ ] Click **Create**

### 5.2 Install Dependencies
- [ ] In Node.js app settings, click **Run NPM Install**
- OR via SSH:
  ```bash
  cd ~/public_html/android-dashboard
  npm install --production
  ```
- [ ] Wait for installation to complete

### 5.3 Start Application
- [ ] Click **Start Application** in hPanel
- [ ] Check status shows "Running"
- [ ] Note the application URL

---

## 🌐 Step 6: Configure Web Server

### 6.1 Apache Configuration (Most common)
- [ ] Verify `.htaccess` exists in your domain root
- [ ] If not, upload the provided `.htaccess` file
- [ ] Verify it contains proxy rules to port 3000

### 6.2 Test Configuration
```bash
# From your local machine
curl https://yourdomain.com/api/v1/config/test

# Should return 404 or JSON (not connection refused)
```

---

## 🔒 Step 7: Security Setup

### 7.1 Enable SSL Certificate
- [ ] Go to **Security** → **SSL** in hPanel
- [ ] Enable **Let's Encrypt SSL** for your domain
- [ ] Wait for certificate to activate
- [ ] Test: `https://yourdomain.com` (should show green lock)

### 7.2 Update File Permissions
Via SSH or File Manager:
```bash
chmod 644 .env
chmod 644 .htaccess
chmod 755 dist
```

### 7.3 Verify Security Headers
```bash
curl -I https://yourdomain.com

# Should see:
# X-Content-Type-Options: nosniff
# X-Frame-Options: SAMEORIGIN
# X-XSS-Protection: 1; mode=block
```

---

## 🧪 Step 8: Testing

### 8.1 Test Admin Dashboard
- [ ] Open: `https://yourdomain.com`
- [ ] Login page appears
- [ ] Login with:
  - Username: `admin`
  - Password: `admin123`
- [ ] Dashboard loads successfully
- [ ] Navigate to all sections:
  - [ ] Dashboard
  - [ ] Apps
  - [ ] AdMob Accounts
  - [ ] Switching Rules
  - [ ] Notifications
  - [ ] Analytics

### 8.2 Test API Endpoints
```bash
# Test config endpoint
curl https://yourdomain.com/api/v1/config/com.example.app

# Test analytics endpoint (should reject without data)
curl -X POST https://yourdomain.com/api/v1/analytics/admob \
  -H "Content-Type: application/json"
```

### 8.3 Create Test App
- [ ] In dashboard, go to **Apps**
- [ ] Click **Create App**
- [ ] Enter:
  - Package Name: `com.test.app`
  - App Name: `Test Application`
  - Status: Active
- [ ] Click **Create**
- [ ] Verify app appears in list

### 8.4 Test from Android
- [ ] Update Android app BASE_URL to `https://yourdomain.com`
- [ ] Build and install Android app
- [ ] Launch app and check Logcat:
  ```
  AdMobConfigManager: AdMob config saved successfully
  DeviceRegistration: Device registered successfully
  ```
- [ ] Check dashboard shows:
  - [ ] Device registered
  - [ ] Analytics events tracked

---

## 🔄 Step 9: Post-Deployment

### 9.1 Change Default Password
- [ ] Login to dashboard
- [ ] Change admin password from `admin123` to strong password
- [ ] (Feature may need to be implemented)

### 9.2 Setup Monitoring
If SSH access available:
```bash
# Install PM2
npm install -g pm2

# Start with PM2
cd ~/public_html/android-dashboard
pm2 start ecosystem.config.js

# Save process list
pm2 save

# Setup auto-start
pm2 startup
```

### 9.3 Enable Backups
- [ ] In hPanel, go to **Files** → **Backups**
- [ ] Enable automatic backups
- [ ] Set backup frequency
- [ ] Test restore process

### 9.4 Database Backup
- [ ] In phpMyAdmin, select database
- [ ] Click **Export**
- [ ] Export as SQL
- [ ] Save backup file locally
- [ ] Schedule regular exports

---

## 📱 Step 10: Update Android App

### 10.1 Update BASE_URL
Update in these 3 files:
- [ ] `MainActivity.java` (line ~42)
- [ ] `UtilsAdmob.java` (line ~59)
- [ ] `MyFirebaseMessagingService.java` (line ~27)

```java
// Change from:
private static final String BASE_URL = "https://your-replit-app.replit.app";

// To:
private static final String BASE_URL = "https://yourdomain.com";
```

### 10.2 Rebuild and Test
- [ ] Clean and rebuild project
- [ ] Install on test device
- [ ] Verify connection to new server
- [ ] Test all features:
  - [ ] AdMob config loading
  - [ ] Analytics tracking
  - [ ] Device registration
  - [ ] Notifications

### 10.3 Production Release
- [ ] Update version code and name
- [ ] Generate signed APK/AAB
- [ ] Test on multiple devices
- [ ] Upload to Play Store

---

## 🎉 Deployment Complete!

### Final Verification
- [ ] ✅ Database is created and populated
- [ ] ✅ Files uploaded to Hostinger
- [ ] ✅ Environment variables configured
- [ ] ✅ Node.js application running
- [ ] ✅ SSL certificate enabled
- [ ] ✅ Admin dashboard accessible
- [ ] ✅ API endpoints responding
- [ ] ✅ Android app connected
- [ ] ✅ Default password changed
- [ ] ✅ Backups configured

### Monitoring
Daily/Weekly tasks:
- [ ] Check application logs
- [ ] Monitor disk space usage
- [ ] Review analytics data
- [ ] Check for errors in phpMyAdmin
- [ ] Verify backups are running

### Support Resources
- Deployment Guide: `HOSTINGER_DEPLOYMENT.md`
- Android Integration: `ANDROID_INTEGRATION.md`
- Integration Review: `INTEGRATION_REVIEW.md`
- Hostinger Docs: https://www.hostinger.com/tutorials
- Support: Hostinger ticket system

---

## 🐛 Troubleshooting Quick Reference

**App won't start:**
```bash
pm2 logs android-dashboard
# Check for errors in logs
```

**Database connection failed:**
- Verify credentials in `.env`
- Check database exists in phpMyAdmin
- Try `127.0.0.1` instead of `localhost`

**502 Bad Gateway:**
- Node.js app not running - restart it
- Wrong port in Apache config
- Check application logs

**Android app can't connect:**
- Verify SSL certificate is valid
- Check CORS headers in `.htaccess`
- Test API with curl/Postman first

---

## 📞 Getting Help

1. Check application logs first
2. Review Hostinger documentation
3. Test API endpoints with Postman
4. Check database connection
5. Contact Hostinger support for server issues

---

**Ready to deploy? Start from Step 1!** 🚀
