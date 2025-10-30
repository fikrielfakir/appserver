# Local Setup Instructions

This guide will help you run this Laravel application on your local computer.

## Prerequisites

Before you begin, make sure you have the following installed on your computer:

1. **PHP 8.2 or higher** - [Download PHP](https://www.php.net/downloads.php)
2. **Composer** - [Download Composer](https://getcomposer.org/download/)
3. **Node.js and npm** (optional, for frontend assets) - [Download Node.js](https://nodejs.org/)

## Step-by-Step Setup

### 1. Download the Project

First, download all the project files to your local computer.

### 2. Install PHP Dependencies

Open your terminal/command prompt, navigate to the project folder, and run:

```bash
composer install
```

This will install all the required PHP packages.

### 3. Set Up Environment File

Copy the `.env.example` file to create your `.env` file:

```bash
# On Mac/Linux:
cp .env.example .env

# On Windows:
copy .env.example .env
```

### 4. Generate Application Key

Run this command to generate a unique application key:

```bash
php artisan key:generate
```

### 5. Set Up Database

The app is currently configured to use SQLite (a simple file-based database). Create the database file:

```bash
# On Mac/Linux:
touch database/database.sqlite

# On Windows:
type nul > database\database.sqlite
```

Make sure your `.env` file has this line:
```
DB_CONNECTION=sqlite
```

### 6. Run Database Migrations

Create all the necessary database tables:

```bash
php artisan migrate
```

### 7. Start the Development Server

Now you can start the Laravel development server:

```bash
php artisan serve
```

By default, the server will start at `http://localhost:8000`

### 8. Access the Application

Open your web browser and go to:
```
http://localhost:8000
```

You should see the Admin Login page.

## Optional: Frontend Assets

If the project has frontend assets (CSS/JS that need building), you may also need to run:

```bash
npm install
npm run dev
```

## Troubleshooting

### Port Already in Use

If port 8000 is already in use, you can specify a different port:

```bash
php artisan serve --port=8080
```

Then access the app at `http://localhost:8080`

### Database Errors

If you see database errors, make sure:
- The `database/database.sqlite` file exists
- Your `.env` file has `DB_CONNECTION=sqlite`
- You've run `php artisan migrate`

### Permission Errors

On Mac/Linux, you may need to give write permissions to storage folders:

```bash
chmod -R 775 storage bootstrap/cache
```

## For Render Deployment

When you're ready to deploy to Render, you'll need to:
1. Use PostgreSQL instead of SQLite
2. Set up environment variables in Render dashboard
3. Configure build and start commands

Let me know when you're ready to set up Render deployment!
