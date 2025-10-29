<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'package_name' => 'required|string',
            'fcm_token' => 'required|string',
            'country' => 'nullable|string',
            'app_version' => 'nullable|string',
            'android_version' => 'nullable|integer',
            'device_manufacturer' => 'nullable|string',
            'device_model' => 'nullable|string',
        ]);

        $app = App::where('package_name', $validated['package_name'])->first();
        
        if (!$app) {
            return response()->json([
                'success' => false,
                'message' => 'App not found'
            ], 404);
        }

        $device = Device::where('fcm_token', $validated['fcm_token'])->first();

        if ($device) {
            $device->update([
                'last_seen' => now(),
                'country' => $validated['country'] ?? $device->country,
                'app_version' => $validated['app_version'] ?? $device->app_version,
                'android_version' => $validated['android_version'] ?? $device->android_version,
                'device_manufacturer' => $validated['device_manufacturer'] ?? $device->device_manufacturer,
                'device_model' => $validated['device_model'] ?? $device->device_model,
            ]);
        } else {
            $device = Device::create([
                'id' => (string) Str::uuid(),
                'app_id' => $app->id,
                'fcm_token' => $validated['fcm_token'],
                'country' => $validated['country'] ?? null,
                'app_version' => $validated['app_version'] ?? null,
                'android_version' => $validated['android_version'] ?? null,
                'device_manufacturer' => $validated['device_manufacturer'] ?? null,
                'device_model' => $validated['device_model'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Device registered successfully',
            'device' => $device
        ], 201);
    }
}
