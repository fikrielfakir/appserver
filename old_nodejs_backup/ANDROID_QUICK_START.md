# Android Integration - Quick Start

## 🚀 5-Minute Setup

### Step 1: Update AdMobConfigManager

Replace the config URL in `AdMobConfigManager.java`:

```java
private static final String CONFIG_URL = "https://your-replit-app.replit.app/api/v1/config/";
```

### Step 2: Update JSON Parsing

Replace the `parseAndSaveConfig` method to handle the new API response:

```java
private void parseAndSaveConfig(String jsonResponse) {
    try {
        JSONObject config = new JSONObject(jsonResponse);
        JSONArray accounts = config.getJSONArray("admob_accounts");
        
        if (accounts.length() > 0) {
            JSONObject account = accounts.getJSONObject(0);
            SharedPreferences.Editor editor = preferences.edit();
            
            editor.putString(PREF_BANNER_ID, account.optString("banner_id"));
            editor.putString(PREF_INTERSTITIAL_ID, account.optString("interstitial_id"));
            editor.putString(PREF_REWARDED_ID, account.optString("rewarded_id"));
            editor.putLong(PREF_LAST_UPDATE, System.currentTimeMillis());
            editor.apply();
        }
    } catch (JSONException e) {
        Log.e(TAG, "Error parsing config: " + e.getMessage());
    }
}
```

### Step 3: Dashboard Setup

1. **Login:** Navigate to your Replit app URL
   - Username: `admin`
   - Password: `admin123`

2. **Create App:**
   - Go to **Apps** → **Create App**
   - Enter your Android package name (e.g., `com.moho.wood`)
   - Set status to **Active**

3. **Add AdMob Account:**
   - Go to **AdMob** → **Create Account**
   - Select your app
   - Enter your AdMob ad unit IDs
   - Save

### Step 4: Test

Run your Android app. It will automatically fetch the AdMob IDs from your dashboard!

## 📊 View Analytics

Analytics are tracked automatically when you integrate the tracking code. View them in the **Analytics** section of the dashboard.

## 🔄 Switch AdMob Accounts

Add multiple AdMob accounts and configure switching rules in **Settings**:
- **Weighted Random**: Distribute traffic by percentage
- **Sequential**: Rotate accounts in order
- **Geographic**: Route by country

## 📱 Push Notifications

Configure push notifications in the **Notifications** section and they'll automatically reach your users.

## 📖 Full Documentation

See [ANDROID_INTEGRATION.md](./ANDROID_INTEGRATION.md) for complete integration details, analytics tracking, and advanced features.

## ✅ Checklist

- [ ] Updated CONFIG_URL in AdMobConfigManager
- [ ] Updated parseAndSaveConfig method
- [ ] Created app in dashboard with correct package name
- [ ] Added AdMob account with ad unit IDs
- [ ] Tested configuration fetch in Android app
- [ ] AdMob ads loading with dashboard IDs

## 🆘 Need Help?

1. Check that package names match exactly
2. Verify app is **Active** in dashboard
3. Check Android logs for connection errors
4. Test the API endpoint directly:
   ```bash
   curl https://your-app.replit.app/api/v1/config/your.package.name
   ```
