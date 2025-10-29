# Android App Integration Guide

This guide shows how to integrate your Android application with the Android Platform Control Dashboard backend.

## Overview

The backend provides a RESTful API that allows your Android app to:
- Fetch AdMob account configurations dynamically
- Track AdMob analytics (impressions, clicks, revenue)
- Register devices for push notifications
- Receive and track push notifications
- Report analytics events

## Base URL Configuration

Set your backend URL in the Android app. For development:
```java
private static final String BASE_URL = "https://your-replit-app.replit.app";
```

For production, use your published domain.

## API Endpoints

### 1. Get App Configuration

Fetches AdMob accounts, switching rules, and active notifications for your app.

**Endpoint:** `GET /api/v1/config/{packageName}`

**Example:**
```java
String url = BASE_URL + "/api/v1/config/com.yourapp.package";
```

**Response:**
```json
{
  "app_id": "com.yourapp.package",
  "version": "1.0.0",
  "last_update": "2024-01-29T12:00:00.000Z",
  "admob_accounts": [
    {
      "account_id": "uuid-123",
      "account_name": "Primary Account",
      "status": "active",
      "priority": 1,
      "weight": 70,
      "banner_id": "ca-app-pub-xxxxx/banner",
      "interstitial_id": "ca-app-pub-xxxxx/interstitial",
      "rewarded_id": "ca-app-pub-xxxxx/rewarded",
      "app_open_id": "ca-app-pub-xxxxx/appopen",
      "native_id": "ca-app-pub-xxxxx/native"
    }
  ],
  "switching_rules": {
    "strategy": "weighted_random",
    "rotation_interval": "daily",
    "fallback_enabled": true,
    "ab_testing_enabled": false,
    "geographic_rules": null
  },
  "notifications": []
}
```

### 2. Track AdMob Analytics

Report AdMob events like impressions, clicks, and revenue.

**Endpoint:** `POST /api/v1/analytics/admob`

**Request Body:**
```json
{
  "package_name": "com.yourapp.package",
  "account_id": "uuid-123",
  "event": "impression",
  "ad_type": "banner",
  "value": 150,
  "timestamp": "2024-01-29T12:00:00.000Z"
}
```

**Events:**
- `impression` - Ad was shown
- `click` - Ad was clicked
- `revenue` - Revenue earned (value in cents)

**Ad Types:**
- `banner`
- `interstitial`
- `rewarded`
- `app_open`
- `native`

### 3. Register Device for Push Notifications

Register a device to receive push notifications.

**Endpoint:** `POST /api/v1/device/register`

**Request Body:**
```json
{
  "package_name": "com.yourapp.package",
  "fcm_token": "device-fcm-token",
  "device_info": {
    "country": "US",
    "app_version": "1.0.0",
    "android_version": 33,
    "manufacturer": "Samsung",
    "model": "Galaxy S21"
  }
}
```

**Response:**
```json
{
  "message": "Device registered successfully",
  "device_id": "device-uuid"
}
```

### 4. Get Pending Notifications

Fetch notifications that should be displayed to the user.

**Endpoint:** `GET /api/v1/notifications/pending?package_name={packageName}`

**Response:**
```json
{
  "notifications": [
    {
      "notification_id": "notif-uuid",
      "title": "Special Offer",
      "message": "Get 50% off premium features!",
      "type": "popup",
      "priority": "high",
      "targeting": {
        "countries": ["US", "CA"],
        "app_versions": ["1.0.0"],
        "min_android_version": 21,
        "user_segments": ["active_users"]
      },
      "content": {
        "image_url": "https://example.com/image.png",
        "action_button_text": "Claim Now",
        "action_type": "url",
        "action_value": "https://example.com/offer",
        "cancelable": true
      },
      "display_rules": {
        "max_displays": 3,
        "display_interval_hours": 24,
        "show_on_app_launch": true
      }
    }
  ]
}
```

### 5. Track Notification Events

Track when notifications are displayed, clicked, or dismissed.

**Endpoint:** `POST /api/v1/notifications/track`

**Request Body:**
```json
{
  "notification_id": "notif-uuid",
  "device_id": "device-uuid",
  "event": "displayed",
  "timestamp": "2024-01-29T12:00:00.000Z"
}
```

**Events:**
- `displayed` - Notification was shown
- `clicked` - User clicked the notification
- `dismissed` - User dismissed the notification

## Integration with AdMobConfigManager

Update your `AdMobConfigManager.java` to use the backend API:

### 1. Update the Config URL

```java
public class AdMobConfigManager {
    private static final String CONFIG_URL = "https://your-replit-app.replit.app/api/v1/config/";
    
    public AdMobConfigManager(Context context, String packageName) {
        this.context = context;
        this.configUrl = CONFIG_URL + packageName;
        this.preferences = PreferenceManager.getDefaultSharedPreferences(context);
    }
}
```

### 2. Parse the Response

The current implementation expects:
```json
{
  "banner_id": "ca-app-pub-xxxxx/banner",
  "interstitial_id": "ca-app-pub-xxxxx/interstitial",
  "rewarded_id": "ca-app-pub-xxxxx/rewarded"
}
```

Update the parsing to use the first active account from `admob_accounts`:

```java
private void parseAndSaveConfig(String jsonResponse) {
    try {
        JSONObject config = new JSONObject(jsonResponse);
        SharedPreferences.Editor editor = preferences.edit();

        // Get the first active AdMob account
        JSONArray accounts = config.getJSONArray("admob_accounts");
        if (accounts.length() > 0) {
            JSONObject account = accounts.getJSONObject(0);
            
            if (account.has("banner_id")) {
                editor.putString(PREF_BANNER_ID, account.getString("banner_id"));
            }
            
            if (account.has("interstitial_id")) {
                editor.putString(PREF_INTERSTITIAL_ID, account.getString("interstitial_id"));
            }
            
            if (account.has("rewarded_id")) {
                editor.putString(PREF_REWARDED_ID, account.getString("rewarded_id"));
            }
            
            // Store account ID for analytics tracking
            if (account.has("account_id")) {
                editor.putString("current_account_id", account.getString("account_id"));
            }
        }

        editor.putLong(PREF_LAST_UPDATE, System.currentTimeMillis());
        editor.apply();

        Log.d(TAG, "AdMob config saved successfully");

    } catch (JSONException e) {
        Log.e(TAG, "Error parsing config: " + e.getMessage());
    }
}
```

### 3. Implement Analytics Tracking

Add a method to track AdMob events:

```java
public void trackAdEvent(String event, String adType, int value) {
    new Thread(new Runnable() {
        @Override
        public void run() {
            try {
                String accountId = preferences.getString("current_account_id", "");
                String packageName = context.getPackageName();
                
                URL url = new URL(BASE_URL + "/api/v1/analytics/admob");
                HttpURLConnection connection = (HttpURLConnection) url.openConnection();
                connection.setRequestMethod("POST");
                connection.setRequestProperty("Content-Type", "application/json");
                connection.setDoOutput(true);

                JSONObject payload = new JSONObject();
                payload.put("package_name", packageName);
                payload.put("account_id", accountId);
                payload.put("event", event);
                payload.put("ad_type", adType);
                payload.put("value", value);

                OutputStream os = connection.getOutputStream();
                os.write(payload.toString().getBytes());
                os.close();

                int responseCode = connection.getResponseCode();
                Log.d(TAG, "Analytics tracked: " + responseCode);

                connection.disconnect();

            } catch (Exception e) {
                Log.e(TAG, "Error tracking analytics: " + e.getMessage());
            }
        }
    }).start();
}
```

### 4. Update UtilsAdmob to Track Events

In your `UtilsAdmob.java`, add tracking to ad listeners:

```java
mAdView.setAdListener(new AdListener() {
    @Override
    public void onAdLoaded() {
        Log.d("Jacob", "Banner loaded successfully");
        // Track impression
        configManager.trackAdEvent("impression", "banner", 0);
    }

    @Override
    public void onAdClicked() {
        // Track click
        configManager.trackAdEvent("click", "banner", 0);
    }
});
```

For interstitial ads:

```java
mInterstitialAd.setFullScreenContentCallback(new FullScreenContentCallback(){
    @Override
    public void onAdShowedFullScreenContent() {
        mInterstitialAd = null;
        Log.d("Jacob", "Interstitial shown");
        // Track impression
        configManager.trackAdEvent("impression", "interstitial", 0);
    }
});
```

For rewarded ads:

```java
mRewardedAd.setFullScreenContentCallback(new FullScreenContentCallback() {
    @Override
    public void onAdShowedFullScreenContent() {
        Log.d("Jacob Reward", "Ad shown");
        // Track impression
        configManager.trackAdEvent("impression", "rewarded", 0);
    }
});
```

## Device Registration for Push Notifications

Add device registration in your `MainActivity.onCreate()`:

```java
private void registerDevice() {
    new Thread(new Runnable() {
        @Override
        public void run() {
            try {
                // Get FCM token (implement this based on your FCM setup)
                String fcmToken = getFCMToken();
                String packageName = getPackageName();
                
                URL url = new URL(BASE_URL + "/api/v1/device/register");
                HttpURLConnection connection = (HttpURLConnection) url.openConnection();
                connection.setRequestMethod("POST");
                connection.setRequestProperty("Content-Type", "application/json");
                connection.setDoOutput(true);

                JSONObject payload = new JSONObject();
                payload.put("package_name", packageName);
                payload.put("fcm_token", fcmToken);
                
                JSONObject deviceInfo = new JSONObject();
                deviceInfo.put("country", Locale.getDefault().getCountry());
                deviceInfo.put("app_version", getAppVersion());
                deviceInfo.put("android_version", Build.VERSION.SDK_INT);
                deviceInfo.put("manufacturer", Build.MANUFACTURER);
                deviceInfo.put("model", Build.MODEL);
                
                payload.put("device_info", deviceInfo);

                OutputStream os = connection.getOutputStream();
                os.write(payload.toString().getBytes());
                os.close();

                int responseCode = connection.getResponseCode();
                Log.d("DeviceReg", "Device registered: " + responseCode);

                connection.disconnect();

            } catch (Exception e) {
                Log.e("DeviceReg", "Error registering device: " + e.getMessage());
            }
        }
    }).start();
}
```

## Admin Dashboard Setup

### 1. Login to Dashboard

Navigate to `https://your-replit-app.replit.app` and login with:
- Username: `admin`
- Password: `admin123`

**Important:** Change these credentials after first login!

### 2. Create Your App

1. Go to the **Apps** section
2. Click **Create App**
3. Fill in:
   - **Package Name:** com.yourapp.package (must match your Android app)
   - **App Name:** Your App Name
   - **Description:** Optional description
   - **Status:** Active

### 3. Add AdMob Accounts

1. Go to the **AdMob** section
2. Click **Create Account**
3. Fill in:
   - **App:** Select your app
   - **Account Name:** Primary Account
   - **Status:** Active
   - **Priority:** 1 (lower number = higher priority)
   - **Weight:** 100 (percentage for weighted distribution)
   - **Banner ID:** Your AdMob banner ad unit ID
   - **Interstitial ID:** Your AdMob interstitial ad unit ID
   - **Rewarded ID:** Your AdMob rewarded ad unit ID

You can add multiple accounts and configure switching strategies in the **Settings** section.

### 4. Configure Switching Rules (Optional)

1. Go to **Settings**
2. Select your app
3. Choose a strategy:
   - **Weighted Random:** Distribute based on account weights
   - **Sequential:** Rotate in priority order
   - **Geographic:** Route by country
   - **Time-Based:** Switch based on time

### 5. Create Push Notifications (Optional)

1. Go to **Notifications**
2. Click **Create Notification**
3. Configure targeting, scheduling, and content
4. Click **Send** when ready

## Testing the Integration

### 1. Test Configuration Fetch

```bash
curl https://your-replit-app.replit.app/api/v1/config/com.yourapp.package
```

### 2. Test Analytics Tracking

```bash
curl -X POST https://your-replit-app.replit.app/api/v1/analytics/admob \
  -H "Content-Type: application/json" \
  -d '{
    "package_name": "com.yourapp.package",
    "event": "impression",
    "ad_type": "banner"
  }'
```

### 3. Monitor in Dashboard

- View analytics in the **Analytics** section
- Monitor devices in the **Dashboard**
- Track notification performance

## Security Best Practices

1. **Use HTTPS:** Always use HTTPS in production
2. **Rate Limiting:** Implement rate limiting on the Android side to avoid excessive API calls
3. **Error Handling:** Implement proper error handling and fallback mechanisms
4. **Cache Configuration:** Cache the configuration locally and only update when needed
5. **Validate Responses:** Always validate API responses before using the data

## Troubleshooting

### Configuration Not Loading

- Verify the package name matches exactly
- Check that the app exists and is active in the dashboard
- Verify the URL is correct and accessible
- Check Android logs for HTTP errors

### Analytics Not Appearing

- Verify you're sending the correct package_name
- Check that events are being sent after ad loads/clicks
- Monitor network requests in Android Studio

### Notifications Not Showing

- Verify FCM is properly configured
- Check device registration was successful
- Verify notification targeting matches your device

## Support

For issues or questions:
1. Check the dashboard logs
2. Review Android logcat output
3. Verify API responses match expected format
4. Ensure all endpoints are accessible from your device
