# Login System Guide

## Important: Two Different Login Methods

This application has **two separate login systems**:

### 1. Web Login (For Browser Access) ✅ **USE THIS**
- **URL:** `/login`
- **Method:** Form-based POST request
- **Authentication:** Session-based (uses cookies)
- **Redirects to:** `/admin/dashboard` after successful login
- **Use for:** Accessing the admin panel through your web browser

### 2. API Login (For Mobile/External Apps)
- **URL:** `/api/auth/login`
- **Method:** JSON POST request
- **Authentication:** JWT token-based
- **Returns:** JSON with auth token
- **Use for:** Android app or external integrations

---

## How to Login to Dashboard (Web Login)

### Step 1: Clear Your Browser
Since you may have used the API login before, clear your browser:
- Press `Ctrl+Shift+Delete` (or `Cmd+Shift+Delete` on Mac)
- Clear cookies and cache
- Or use Incognito/Private browsing mode

### Step 2: Access the Login Page
- Navigate to: `http://localhost:8000/login` (or your Replit URL)
- You should see the "Admin Login" form

### Step 3: Enter Credentials
- **Username:** `admin`
- **Password:** `admin123`
- Click "Sign In"

### Step 4: Success!
- You should be redirected to `/admin/dashboard`
- You're now logged in with session-based authentication

---

## Why You Were Getting JWT Tokens

If you saw a JWT token like:
```
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

This means you were using the **API login** endpoint (`/api/auth/login`) instead of the web login form. The API login is for external applications (like Android apps), not for browser access to the dashboard.

---

## Troubleshooting

### "I'm stuck on the login page"

1. **Check the URL in the form:**
   - Open your browser's Developer Tools (F12)
   - Go to the Network tab
   - Submit the login form
   - Check if it's posting to `/login` (correct) or `/api/auth/login` (wrong)

2. **Clear browser cache:**
   - The old API-based form might be cached
   - Use Incognito mode to test

3. **Check for errors:**
   - Look in the browser console (F12 → Console tab)
   - Check for any JavaScript errors

### "404 Not Found on /dashboard"

The dashboard is at `/admin/dashboard`, not `/dashboard`. Make sure:
- The redirect after login goes to `/admin/dashboard`
- You're accessing the correct URL

### "Invalid credentials"

- Double-check username: `admin` (lowercase)
- Double-check password: `admin123`
- Try resetting the password (see below)

---

## Resetting Admin Password

```bash
php artisan tinker
```

Then run:
```php
$user = App\Models\AdminUser::where('username', 'admin')->first();
$user->password = bcrypt('newpassword');
$user->save();
exit
```

---

## Testing the Full Flow

### Test 1: Login Form Submission
1. Go to `/login`
2. Enter: `admin` / `admin123`
3. Click "Sign In"
4. Should redirect to `/admin/dashboard`

### Test 2: Protected Routes
1. Try to access `/admin/apps` without logging in
2. Should redirect to `/login`
3. After login, you can access all admin routes

### Test 3: Logout
1. Access `/logout` with POST method
2. Should clear session and redirect to `/login`

---

## Available Admin Routes (After Login)

- `/admin/dashboard` - Main dashboard
- `/admin/apps` - Apps management
- `/admin/notifications` - Notifications
- `/admin/admob-accounts` - AdMob accounts
- `/admin/analytics` - Analytics
- `/admin/settings` - Settings

---

## For API Testing (Mobile Apps)

If you need to test the API login for your Android app:

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'
```

Response:
```json
{
  "success": true,
  "token": "eyJ0eXAi...",
  "user": {
    "id": "...",
    "username": "admin",
    "role": "superadmin"
  }
}
```

Then use this token in subsequent API requests:
```bash
curl http://localhost:8000/api/admin/dashboard/stats \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## Security Notes

⚠️ **Before deploying to production:**

1. Change the default admin password
2. Use strong passwords
3. Enable HTTPS
4. Set `APP_DEBUG=false` in `.env`
5. Update `APP_URL` to your production domain

---

## Current Configuration

- **Database:** SQLite (for development)
- **Session Driver:** database (stored in DB)
- **Auth Guard:** web (session-based for browser)
- **API Guard:** api (JWT-based for external apps)

---

## Need Help?

If login still doesn't work:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check browser console for errors
3. Verify the session driver is working
4. Make sure cookies are enabled in your browser
