# Firebase Cloud Messaging (FCM) Integration Guide

Complete guide for integrating Firebase Cloud Messaging with your Android Platform Control system.

## Part 1: Firebase Console Setup

### Step 1: Create Firebase Project
1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Click **Add Project** or select existing project
3. Enter project name (e.g., "Android Control Platform")
4. Enable Google Analytics (optional)
5. Click **Create Project**

### Step 2: Add Android App to Firebase
1. In Firebase Console, click **Add App** → **Android**
2. Enter your Android app's package name (e.g., `com.moho.wood`)
3. Enter App nickname (optional)
4. Enter SHA-1 certificate (optional, for security)
5. Click **Register App**

### Step 3: Download Configuration File
1. Download `google-services.json` file
2. Place it in your Android project's `app/` directory
3. **Important**: Never commit this file to public repositories

### Step 4: Get Server Key
1. In Firebase Console → Project Settings → Cloud Messaging
2. Copy **Server Key** (Legacy)
3. Also copy **Sender ID** if needed

**Keep this key secure!** This will be used in your Laravel backend.

## Part 2: Android App Configuration

### Step 1: Add Firebase Dependencies
Add to your `app/build.gradle`:

```gradle
dependencies {
    // Firebase BOM (Bill of Materials)
    implementation platform('com.google.firebase:firebase-bom:32.7.0')
    
    // Firebase Cloud Messaging
    implementation 'com.google.firebase:firebase-messaging'
    
    // Firebase Analytics (optional)
    implementation 'com.google.firebase:firebase-analytics'
}
```

Add to your project-level `build.gradle`:

```gradle
buildscript {
    dependencies {
        classpath 'com.google.gms:google-services:4.4.0'
    }
}
```

Add to the bottom of `app/build.gradle`:

```gradle
apply plugin: 'com.google.gms.google-services'
```

### Step 2: Configure AndroidManifest.xml
Add these permissions and service:

```xml
<manifest xmlns:android="http://schemas.android.com/apk/res/android">
    
    <!-- Permissions -->
    <uses-permission android:name="android.permission.INTERNET" />
    <uses-permission android:name="android.permission.POST_NOTIFICATIONS" />
    
    <application>
        <!-- Firebase Messaging Service -->
        <service
            android:name=".MyFirebaseMessagingService"
            android:exported="false">
            <intent-filter>
                <action android:name="com.google.firebase.MESSAGING_EVENT" />
            </intent-filter>
        </service>
        
        <!-- Default notification icon (optional) -->
        <meta-data
            android:name="com.google.firebase.messaging.default_notification_icon"
            android:resource="@drawable/about_icon" />
            
        <!-- Default notification color (optional) -->
        <meta-data
            android:name="com.google.firebase.messaging.default_notification_color"
            android:resource="@color/colorAccent" />
    </application>
</manifest>
```

### Step 3: Update BASE_URL in Java Files
Update the BASE_URL in these files to your Laravel server:

**MainActivity.java:**
```java
private static final String BASE_URL = "https://yourdomain.com";
```

**MyFirebaseMessagingService.java:**
```java
DeviceRegistrationManager deviceManager = 
    new DeviceRegistrationManager(this, "https://yourdomain.com");
```

### Step 4: Request Notification Permission (Android 13+)
In your `MainActivity.onCreate()`:

```java
// For Android 13 (API 33) and above
if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
    if (ContextCompat.checkSelfPermission(this, Manifest.permission.POST_NOTIFICATIONS)
            != PackageManager.PERMISSION_GRANTED) {
        ActivityCompat.requestPermissions(this,
            new String[]{Manifest.permission.POST_NOTIFICATIONS}, 101);
    }
}
```

## Part 3: Laravel Backend Configuration

### Step 1: Install Firebase Admin SDK for PHP
```bash
composer require kreait/firebase-php
```

### Step 2: Add FCM Server Key to .env
```env
# Firebase Cloud Messaging
FCM_SERVER_KEY=your_firebase_server_key_here
FCM_SENDER_ID=your_sender_id_here
```

**For production (Hostinger):**
Never store the actual key in `.env` file. Use environment variables in Hostinger control panel.

### Step 3: Create FCM Service
Create `app/Services/FcmService.php`:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected string $serverKey;
    protected string $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    public function __construct()
    {
        $this->serverKey = config('services.fcm.server_key');
    }

    public function sendToToken(string $fcmToken, array $notification, array $data = []): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, [
                'to' => $fcmToken,
                'notification' => $notification,
                'data' => $data,
                'priority' => 'high',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('FCM send failed: ' . $e->getMessage());
            return false;
        }
    }

    public function sendToMultipleTokens(array $fcmTokens, array $notification, array $data = []): array
    {
        $results = [];
        
        foreach ($fcmTokens as $token) {
            $results[$token] = $this->sendToToken($token, $notification, $data);
        }
        
        return $results;
    }

    public function sendToTopic(string $topic, array $notification, array $data = []): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, [
                'to' => '/topics/' . $topic,
                'notification' => $notification,
                'data' => $data,
                'priority' => 'high',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('FCM topic send failed: ' . $e->getMessage());
            return false;
        }
    }
}
```

### Step 4: Add FCM Config
Create/update `config/services.php`:

```php
return [
    // ... other services
    
    'fcm' => [
        'server_key' => env('FCM_SERVER_KEY'),
        'sender_id' => env('FCM_SENDER_ID'),
    ],
];
```

### Step 5: Create Notification Sending Endpoint
Update `app/Http/Controllers/Api/Admin/NotificationController.php`:

Add this method:

```php
use App\Services\FcmService;
use App\Models\Device;

public function send($id, FcmService $fcmService)
{
    $notification = Notification::findOrFail($id);
    
    // Get devices for this app
    $devices = Device::where('app_id', $notification->app_id)
        ->whereNotNull('fcm_token')
        ->get();
    
    $fcmNotification = [
        'title' => $notification->title,
        'body' => $notification->message,
        'sound' => 'default',
        'badge' => '1',
    ];
    
    $fcmData = [
        'notification_id' => $notification->id,
        'action_type' => $notification->action_type,
        'action_value' => $notification->action_value,
    ];
    
    $sentCount = 0;
    foreach ($devices as $device) {
        if ($fcmService->sendToToken($device->fcm_token, $fcmNotification, $fcmData)) {
            $sentCount++;
        }
    }
    
    // Update notification status
    $notification->update(['status' => 'sent']);
    
    return response()->json([
        'success' => true,
        'message' => "Notification sent to {$sentCount} devices",
        'sent_count' => $sentCount,
        'total_devices' => $devices->count(),
    ]);
}
```

## Part 4: Testing Firebase Integration

### Test 1: Get FCM Token in Android
Add this to your `MainActivity`:

```java
FirebaseMessaging.getInstance().getToken()
    .addOnCompleteListener(new OnCompleteListener<String>() {
        @Override
        public void onComplete(@NonNull Task<String> task) {
            if (task.isSuccessful()) {
                String token = task.getResult();
                Log.d("FCM Token", token);
                Toast.makeText(MainActivity.this, "Token: " + token, Toast.LENGTH_LONG).show();
            }
        }
    });
```

### Test 2: Test Notification from Firebase Console
1. Go to Firebase Console → Cloud Messaging
2. Click **Send your first message**
3. Enter notification title and text
4. Click **Send test message**
5. Enter your FCM token from Test 1
6. Click **Test**

### Test 3: Send from Laravel Backend
```bash
curl -X POST https://yourdomain.com/api/admin/notifications \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "app_id": "your-app-uuid",
    "title": "Test Notification",
    "message": "This is a test from Laravel!",
    "type": "popup",
    "status": "active"
  }'

# Then send it
curl -X POST https://yourdomain.com/api/admin/notifications/{id}/send \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

## Part 5: Common Issues & Solutions

### Issue: Notifications not received
**Solutions:**
1. Check FCM token is correctly registered in database
2. Verify FCM_SERVER_KEY in `.env` is correct
3. Check Android app has notification permission
4. Verify `google-services.json` is in correct location
5. Check Laravel logs: `tail -f storage/logs/laravel.log`

### Issue: Token registration fails
**Solutions:**
1. Verify BASE_URL is correct in Android code
2. Check network connectivity
3. Verify API endpoint `/api/v1/device/register` is working

### Issue: Background notifications not showing
**Solutions:**
1. Add notification channel for Android 8+
2. Check device battery optimization settings
3. Verify notification permission is granted

## Part 6: Advanced Features

### Topic-based Messaging
Subscribe devices to topics for targeted messaging:

**Android (subscribe to topic):**
```java
FirebaseMessaging.getInstance().subscribeToTopic("all_users");
FirebaseMessaging.getInstance().subscribeToTopic("country_US");
```

**Laravel (send to topic):**
```php
$fcmService->sendToTopic('country_US', [
    'title' => 'US Only Notification',
    'body' => 'This goes to US users only'
]);
```

### Scheduled Notifications
Use Laravel's scheduler in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        // Send daily notifications
        $notifications = Notification::where('status', 'active')
            ->where('frequency', 'daily')
            ->get();
            
        foreach ($notifications as $notification) {
            dispatch(new SendNotificationJob($notification));
        }
    })->daily();
}
```

## Security Best Practices

1. **Never commit** `google-services.json` or FCM keys to version control
2. Use environment variables for all sensitive data
3. Validate FCM tokens before using
4. Rate limit notification sending
5. Log all notification attempts for auditing
6. Use HTTPS only for API communication

## Monitoring

Monitor your FCM integration:
1. Firebase Console → Analytics → Events
2. Check delivery rates in Firebase Console
3. Monitor Laravel logs for errors
4. Track notification events in your database

## Cost Considerations

Firebase Cloud Messaging is **FREE** for:
- Unlimited messages
- Unlimited devices
- All features

Only Firebase Analytics and other premium features may have costs.
