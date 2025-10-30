# Test Login Credentials

## Admin Login

To test the application, use these credentials:

- **Username:** `admin`
- **Password:** `admin123`

## Access the Application

### On Replit:
- The app is running at: `http://0.0.0.0:5000`
- Click the "Open in new tab" button in Replit to view the app

### On Your Local Computer:
1. Start the server: `php artisan serve`
2. Open browser: `http://localhost:8000`
3. Log in with the credentials above

### After Login:
You will be redirected to: `/admin/dashboard`

## Changing the Password

To change the admin password, run this in your terminal:

```bash
php artisan tinker
```

Then execute:

```php
$user = App\Models\AdminUser::where('username', 'admin')->first();
$user->password = bcrypt('your_new_password');
$user->save();
exit
```

## Creating Additional Users

```bash
php artisan tinker
```

Then execute:

```php
$user = new App\Models\AdminUser();
$user->username = 'newuser';
$user->password = bcrypt('password123');
$user->role = 'admin'; // or 'superadmin'
$user->save();
exit
```

## Available Roles

- `superadmin` - Full access to all features
- `admin` - Standard administrative access

## Important Notes

- **Current Database:** SQLite (for Replit/local testing)
- **For Production (Render):** Switch to MySQL or PostgreSQL
- **Security:** Change the default password before deploying to production!
