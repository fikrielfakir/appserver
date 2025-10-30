# MySQL Setup Guide

This guide explains how to set up MySQL for this Laravel application in different environments.

## Option 1: Local Development (Your Computer)

### Install MySQL

**On Windows:**
1. Download MySQL from [https://dev.mysql.com/downloads/installer/](https://dev.mysql.com/downloads/installer/)
2. Run the installer and follow the setup wizard
3. Remember the root password you set during installation

**On Mac:**
```bash
brew install mysql
brew services start mysql
```

**On Linux (Ubuntu/Debian):**
```bash
sudo apt update
sudo apt install mysql-server
sudo systemctl start mysql
```

### Create Database

1. Open MySQL command line:
```bash
mysql -u root -p
```

2. Create the database:
```sql
CREATE DATABASE android_control;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON android_control.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Update .env File

Edit your `.env` file with these settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=android_control
DB_USERNAME=laravel_user
DB_PASSWORD=your_password
```

### Run Migrations

```bash
php artisan migrate
```

### Start the Server

```bash
php artisan serve
```

---

## Option 2: Render Deployment

Render provides managed MySQL databases.

### Steps:

1. **Log in to Render** at [https://render.com](https://render.com)

2. **Create a MySQL Database:**
   - Click "New +" → "MySQL"
   - Choose a name (e.g., "android-control-db")
   - Select a region close to your users
   - Choose a plan (Free tier available)
   - Click "Create Database"

3. **Get Database Credentials:**
   - After creation, you'll see connection details:
     - Host
     - Port
     - Database name
     - Username
     - Password

4. **Create a Web Service:**
   - Click "New +" → "Web Service"
   - Connect your Git repository
   - Configure:
     - **Name:** android-control
     - **Environment:** PHP
     - **Build Command:** `composer install --no-dev && php artisan migrate --force`
     - **Start Command:** `php artisan serve --host=0.0.0.0 --port=$PORT`

5. **Set Environment Variables in Render:**
   - Go to your web service → Environment
   - Add these variables:
     - `APP_KEY` (copy from your local .env or generate with `php artisan key:generate --show`)
     - `APP_ENV=production`
     - `APP_DEBUG=false`
     - `DB_CONNECTION=mysql`
     - `DB_HOST` (from MySQL database connection info)
     - `DB_PORT` (from MySQL database connection info)
     - `DB_DATABASE` (from MySQL database connection info)
     - `DB_USERNAME` (from MySQL database connection info)
     - `DB_PASSWORD` (from MySQL database connection info)

6. **Deploy:**
   - Click "Manual Deploy" → "Deploy latest commit"
   - Wait for build to complete

---

## Option 3: Replit Environment (PostgreSQL Alternative)

Since Replit provides PostgreSQL (not MySQL), you have two options:

### A. Use PostgreSQL on Replit (Recommended for Replit)

Replit offers managed PostgreSQL which works seamlessly. To use it:

1. I can create a PostgreSQL database for you
2. Update `.env` to use PostgreSQL
3. It will work automatically on Replit

### B. Keep MySQL for Local/Render Only

- Use MySQL for local development and Render deployment
- Use PostgreSQL or SQLite when running on Replit
- Switch `.env` based on environment

---

## Current Configuration

Your `.env` is now configured for MySQL with these settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=android_control
DB_USERNAME=root
DB_PASSWORD=
```

**For local development:** Install MySQL and create the database as shown above.

**For Render deployment:** Follow the Render setup steps to create a managed MySQL database.

---

## Quick Commands Reference

```bash
# Create database (MySQL CLI)
mysql -u root -p -e "CREATE DATABASE android_control;"

# Run migrations
php artisan migrate

# Reset database (fresh start)
php artisan migrate:fresh

# Start local server
php artisan serve
```

---

## Need Help?

Let me know which environment you want to set up:
- Local MySQL on your computer
- Render deployment with MySQL
- PostgreSQL on Replit (alternative to MySQL)
