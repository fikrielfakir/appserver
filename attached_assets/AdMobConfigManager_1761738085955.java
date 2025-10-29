package com.moho.wood;

import android.content.Context;
import android.content.SharedPreferences;
import android.util.Log;

import androidx.preference.PreferenceManager;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;

public class AdMobConfigManager {
    private static final String TAG = "AdMobConfigManager";
    private static final String PREF_BANNER_ID = "admob_banner_id";
    private static final String PREF_INTERSTITIAL_ID = "admob_interstitial_id";
    private static final String PREF_REWARDED_ID = "admob_rewarded_id";
    private static final String PREF_LAST_UPDATE = "admob_last_update";
    private static final long UPDATE_INTERVAL = 24 * 60 * 60 * 1000; // 24 hours

    private Context context;
    private SharedPreferences preferences;
    private String configUrl;

    // Default IDs (fallback)
    private String defaultBannerId;
    private String defaultInterstitialId;
    private String defaultRewardedId;

    public AdMobConfigManager(Context context, String configUrl) {
        this.context = context;
        this.configUrl = configUrl;
        this.preferences = PreferenceManager.getDefaultSharedPreferences(context);
    }

    /**
     * Set default AdMob IDs as fallback
     */
    public void setDefaultIds(String bannerId, String interstitialId, String rewardedId) {
        this.defaultBannerId = bannerId;
        this.defaultInterstitialId = interstitialId;
        this.defaultRewardedId = rewardedId;
    }

    /**
     * Fetch AdMob configuration from server
     */
    public void fetchConfig(final ConfigCallback callback) {
        new Thread(new Runnable() {
            @Override
            public void run() {
                try {
                    URL url = new URL(configUrl);
                    HttpURLConnection connection = (HttpURLConnection) url.openConnection();
                    connection.setRequestMethod("GET");
                    connection.setConnectTimeout(10000);
                    connection.setReadTimeout(10000);

                    int responseCode = connection.getResponseCode();
                    if (responseCode == HttpURLConnection.HTTP_OK) {
                        BufferedReader reader = new BufferedReader(
                                new InputStreamReader(connection.getInputStream()));
                        StringBuilder response = new StringBuilder();
                        String line;

                        while ((line = reader.readLine()) != null) {
                            response.append(line);
                        }
                        reader.close();

                        parseAndSaveConfig(response.toString());

                        if (callback != null) {
                            callback.onSuccess();
                        }
                    } else {
                        Log.e(TAG, "Server returned error: " + responseCode);
                        if (callback != null) {
                            callback.onError("Server error: " + responseCode);
                        }
                    }
                    connection.disconnect();

                } catch (Exception e) {
                    Log.e(TAG, "Error fetching config: " + e.getMessage());
                    if (callback != null) {
                        callback.onError(e.getMessage());
                    }
                }
            }
        }).start();
    }

    /**
     * Parse JSON config and save to SharedPreferences
     */
    private void parseAndSaveConfig(String jsonResponse) {
        try {
            JSONObject config = new JSONObject(jsonResponse);

            SharedPreferences.Editor editor = preferences.edit();

            if (config.has("banner_id")) {
                editor.putString(PREF_BANNER_ID, config.getString("banner_id"));
            }

            if (config.has("interstitial_id")) {
                editor.putString(PREF_INTERSTITIAL_ID, config.getString("interstitial_id"));
            }

            if (config.has("rewarded_id")) {
                editor.putString(PREF_REWARDED_ID, config.getString("rewarded_id"));
            }

            editor.putLong(PREF_LAST_UPDATE, System.currentTimeMillis());
            editor.apply();

            Log.d(TAG, "AdMob config saved successfully");

        } catch (JSONException e) {
            Log.e(TAG, "Error parsing config: " + e.getMessage());
        }
    }

    /**
     * Check if config needs update
     */
    public boolean needsUpdate() {
        long lastUpdate = preferences.getLong(PREF_LAST_UPDATE, 0);
        return (System.currentTimeMillis() - lastUpdate) > UPDATE_INTERVAL;
    }

    /**
     * Get banner ad unit ID
     */
    public String getBannerId() {
        return preferences.getString(PREF_BANNER_ID, defaultBannerId);
    }

    /**
     * Get interstitial ad unit ID
     */
    public String getInterstitialId() {
        return preferences.getString(PREF_INTERSTITIAL_ID, defaultInterstitialId);
    }

    /**
     * Get rewarded ad unit ID
     */
    public String getRewardedId() {
        return preferences.getString(PREF_REWARDED_ID, defaultRewardedId);
    }

    /**
     * Force update config from server
     */
    public void forceUpdate(ConfigCallback callback) {
        fetchConfig(callback);
    }

    /**
     * Clear saved config (for testing)
     */
    public void clearConfig() {
        SharedPreferences.Editor editor = preferences.edit();
        editor.remove(PREF_BANNER_ID);
        editor.remove(PREF_INTERSTITIAL_ID);
        editor.remove(PREF_REWARDED_ID);
        editor.remove(PREF_LAST_UPDATE);
        editor.apply();
    }

    /**
     * Callback interface for async config fetch
     */
    public interface ConfigCallback {
        void onSuccess();
        void onError(String error);
    }
}