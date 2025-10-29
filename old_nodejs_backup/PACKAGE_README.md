# 📦 Android Platform Control Dashboard - Deployment Package

Welcome! This package contains everything needed to deploy your Android Platform Control Dashboard to Hostinger with MySQL.

## 📂 Package Contents

```
📦 Deployment Package
├── 📁 dist/                      # Built application
│   ├── 📁 public/               # Frontend static files (HTML, CSS, JS)
│   └── 📄 index.js              # Backend server bundle
│
├── 📁 node_modules/             # Node.js dependencies
│
├── 📄 database_mysql.sql        # MySQL database schema
├── 📄 .env                      # Environment configuration ⚠️ EDIT THIS!
├── 📄 ecosystem.config.js       # PM2 process manager config
├── 📄 .htaccess                 # Apache web server config
├── 📄 package.json              # Node.js package definition
├── 📄 package-lock.json         # Locked dependency versions
│
└── 📁 Documentation
    ├── 📄 README.md             # Complete deployment guide
    ├── 📄 ANDROID_INTEGRATION.md  # Android app integration
    └── 📄 INTEGRATION_REVIEW.md   # Your Android code review
```

## 🚀 Quick Deployment (5 Minutes)

### Step 1: Setup MySQL Database
1. Login to Hostinger hPanel
2. Create new MySQL database
3. Import `database_mysql.sql` via phpMyAdmin
4. Note your database credentials

### Step 2: Configure Application
1. Open `.env` file
2. Replace placeholders with your actual database credentials:
   ```env
   DATABASE_HOST=localhost
   DATABASE_USER=u123456789_admin        # ← Your user
   DATABASE_PASSWORD=your_password        # ← Your password
   DATABASE_NAME=u123456789_android_db    # ← Your database
   JWT_SECRET=random_secure_string        # ← Generate new!
   ```

### Step 3: Upload to Hostinger
1. Upload this entire folder to `public_html/android-dashboard`
2. Or extract this ZIP in the target directory

### Step 4: Configure Node.js
1. In hPanel, go to **Advanced** → **Node.js**
2. Create new application:
   - Root: `public_html/android-dashboard`
   - Entry file: `dist/index.js`
   - Port: 3000
3. Click **Run NPM Install**
4. Click **Start Application**

### Step 5: Test
1. Open: `https://yourdomain.com`
2. Login: `admin` / `admin123`
3. ✅ You're live!

---

## ⚙️ Configuration Required

### 🔴 CRITICAL: Edit .env File

You **MUST** edit the `.env` file before deployment:

```env
# Replace with YOUR actual database credentials from Hostinger
DATABASE_HOST=localhost
DATABASE_USER=u123456789_admin
DATABASE_PASSWORD=YOUR_ACTUAL_PASSWORD_HERE
DATABASE_NAME=u123456789_android_control
DATABASE_URL=mysql://u123456789_admin:YOUR_PASSWORD@localhost:3306/u123456789_android_control

# Generate a secure random string for JWT_SECRET
JWT_SECRET=GENERATE_RANDOM_STRING_HERE

# Keep these as-is
NODE_ENV=production
PORT=3000
```

**Generate JWT Secret:**
```bash
openssl rand -base64 32
```

---

## 🗄️ Database Setup

### Import Database Schema

1. Login to hPanel
2. Go to **Databases** → **phpMyAdmin**
3. Select your database
4. Click **Import** tab
5. Choose file: `database_mysql.sql`
6. Click **Go**

### Verify Tables Created

You should see 8 tables:
- ✅ admin_users
- ✅ apps
- ✅ admob_accounts
- ✅ switching_rules
- ✅ notifications
- ✅ devices
- ✅ analytics_events
- ✅ notification_events

### Default Admin Account

- **Username:** `admin`
- **Password:** `admin123`
- ⚠️ **IMPORTANT:** Change this password after first login!

---

## 🌐 Web Server Configuration

### Apache (.htaccess)

The `.htaccess` file is already configured to:
- Proxy requests to Node.js (port 3000)
- Enable CORS for API access
- Enable compression
- Set security headers
- Cache static files

**No changes needed** - it's ready to use!

---

## 📱 Update Your Android App

After deployment, update the BASE_URL in your Android app:

**Files to update (3 files):**
1. `MainActivity.java` (around line 42)
2. `UtilsAdmob.java` (around line 59)
3. `MyFirebaseMessagingService.java` (around line 27)

**Change this:**
```java
private static final String BASE_URL = "https://your-replit-app.replit.app";
```

**To your actual URL:**
```java
private static final String BASE_URL = "https://yourdomain.com";
```

Then rebuild and install your Android app.

---

## 🧪 Testing Your Deployment

### Test 1: Check Backend
```bash
curl https://yourdomain.com/api/v1/config/com.example.app
```
Should return JSON (or 404 if app doesn't exist)

### Test 2: Access Dashboard
1. Open browser: `https://yourdomain.com`
2. Should see login page
3. Login with: `admin` / `admin123`
4. Dashboard should load

### Test 3: Create Test App
1. In dashboard, go to **Apps**
2. Click **Create App**
3. Fill in details and save
4. Should appear in apps list

### Test 4: Test from Android
1. Update BASE_URL in Android code
2. Build and install app
3. Check Logcat for success messages
4. Verify device appears in dashboard

---

## 🔒 Security Checklist

After deployment:

- [ ] Change default admin password from `admin123`
- [ ] Enable SSL certificate (Let's Encrypt) in Hostinger
- [ ] Verify `.env` is not publicly accessible
- [ ] Generate new JWT_SECRET (don't use example)
- [ ] Set up regular database backups
- [ ] Review and update CORS settings if needed
- [ ] Monitor application logs regularly

---

## 📊 What This Application Does

### For Administrators (Dashboard)
- Manage multiple Android apps
- Configure AdMob accounts per app
- Set up account switching strategies
- Create and send push notifications
- View analytics (impressions, clicks, revenue)
- Monitor registered devices

### For Android Apps (API)
- Fetch AdMob configuration dynamically
- Track ad impressions and clicks
- Register devices for push notifications
- Receive targeted notifications
- Report analytics data

---

## 📚 Documentation

### Included Documentation Files

1. **README.md** (This is also HOSTINGER_DEPLOYMENT.md)
   - Complete deployment guide with 67 steps
   - Troubleshooting section
   - Security best practices

2. **ANDROID_INTEGRATION.md**
   - Full API documentation
   - Android implementation examples
   - Code samples

3. **INTEGRATION_REVIEW.md**
   - Review of your Android implementation
   - Integration checklist
   - Testing guide

### Need Help?

**Deployment Issues:**
- Check README.md troubleshooting section
- Review Hostinger documentation
- Contact Hostinger support

**Android Integration:**
- See ANDROID_INTEGRATION.md
- Review your code in INTEGRATION_REVIEW.md
- Test API endpoints with Postman

---

## 🔄 Updating Your Application

### Update Code
1. Build new version: `npm run build`
2. Upload new `dist/` folder
3. Restart Node.js app in hPanel

### Update Dependencies
```bash
cd ~/public_html/android-dashboard
npm install
# Restart application
```

### Update Database
1. Create migration SQL file
2. Import via phpMyAdmin
3. Test changes

---

## 💡 Tips & Best Practices

### Performance
- Enable caching (already configured in .htaccess)
- Optimize database queries regularly
- Monitor memory usage
- Use PM2 for process management

### Monitoring
```bash
# Install PM2 (if you have SSH access)
npm install -g pm2

# Start with PM2
pm2 start ecosystem.config.js

# Monitor
pm2 status
pm2 logs android-dashboard
```

### Backups
- Enable automatic backups in Hostinger
- Export database weekly via phpMyAdmin
- Keep backup of `.env` file (securely!)
- Test restore process

---

## 🐛 Common Issues & Solutions

### Issue: "Database connection failed"
**Solution:**
- Verify credentials in `.env`
- Check database exists in phpMyAdmin
- Try `127.0.0.1` instead of `localhost`

### Issue: "502 Bad Gateway"
**Solution:**
- Node.js app not running - restart it
- Check application logs
- Verify port 3000 is correct

### Issue: "Admin login fails"
**Solution:**
- Default password is `admin123`
- Check `admin_users` table in database
- Verify JWT_SECRET is set in `.env`

### Issue: "Android app can't connect"
**Solution:**
- Verify BASE_URL in Android code
- Check SSL certificate is active
- Test API with curl/Postman first
- Check CORS headers

---

## 📈 After Deployment

### Immediate Tasks
1. ✅ Login and change admin password
2. ✅ Create your first app
3. ✅ Add your AdMob accounts
4. ✅ Update Android app BASE_URL
5. ✅ Test complete flow

### Setup Monitoring
1. Enable backups
2. Set up PM2 (if SSH available)
3. Monitor logs regularly
4. Track analytics data

### Go to Production
1. Test thoroughly
2. Update Android app
3. Release to Play Store
4. Monitor performance

---

## 🎯 System Requirements

**Hostinger Plan:**
- Node.js support (Premium or Business)
- MySQL database
- 1+ GB storage
- SSH access (recommended)

**Software Versions:**
- Node.js: 18.x or 20.x
- MySQL: 5.7+ or 8.0+
- Apache: 2.4+

---

## 🆘 Support Resources

**Hostinger:**
- Documentation: https://www.hostinger.com/tutorials
- Support tickets via hPanel
- Live chat (depending on plan)

**This Application:**
- Check included documentation
- Review troubleshooting guides
- Test API endpoints

---

## ✅ Deployment Checklist

Before you start:
- [ ] Hostinger account is ready
- [ ] MySQL database access confirmed
- [ ] Node.js support is available
- [ ] Domain/subdomain is configured

Deployment steps:
- [ ] Database created in Hostinger
- [ ] `database_mysql.sql` imported
- [ ] `.env` file edited with credentials
- [ ] Files uploaded to server
- [ ] Node.js app configured in hPanel
- [ ] Dependencies installed
- [ ] Application started
- [ ] SSL certificate enabled
- [ ] Dashboard accessible
- [ ] Admin login works
- [ ] Android app updated
- [ ] Complete flow tested

Post-deployment:
- [ ] Default password changed
- [ ] Backups configured
- [ ] Monitoring set up
- [ ] Documentation reviewed

---

## 🎉 You're All Set!

This package has everything you need for a successful deployment. Follow the README.md for detailed instructions, or use the quick start above to get running in 5 minutes.

**Default Admin Credentials:**
- Username: `admin`
- Password: `admin123`

**Remember to change the password!**

Good luck with your deployment! 🚀

---

**Questions?** Check the included documentation files for detailed guides and troubleshooting help.
