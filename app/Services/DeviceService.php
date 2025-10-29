<?php

namespace App\Services;

use App\Models\Device;
use App\Models\App;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DeviceService
{
    public function registerOrUpdate(string $packageName, string $fcmToken, array $deviceInfo): ?Device
    {
        try {
            $app = App::where('package_name', $packageName)->first();

            if (!$app) {
                return null;
            }

            $device = Device::where('app_id', $app->id)
                ->where('fcm_token', $fcmToken)
                ->first();

            if ($device) {
                $device->update([
                    'country' => $deviceInfo['country'] ?? $device->country,
                    'app_version' => $deviceInfo['app_version'] ?? $device->app_version,
                    'android_version' => $deviceInfo['android_version'] ?? $device->android_version,
                    'device_manufacturer' => $deviceInfo['manufacturer'] ?? $device->device_manufacturer,
                    'device_model' => $deviceInfo['model'] ?? $device->device_model,
                    'last_seen' => Carbon::now(),
                ]);
            } else {
                $device = Device::create([
                    'id' => Str::uuid(),
                    'app_id' => $app->id,
                    'fcm_token' => $fcmToken,
                    'country' => $deviceInfo['country'] ?? null,
                    'app_version' => $deviceInfo['app_version'] ?? null,
                    'android_version' => $deviceInfo['android_version'] ?? null,
                    'device_manufacturer' => $deviceInfo['manufacturer'] ?? null,
                    'device_model' => $deviceInfo['model'] ?? null,
                    'last_seen' => Carbon::now(),
                ]);
            }

            return $device;
        } catch (\Exception $e) {
            logger()->error('Device registration failed: ' . $e->getMessage());
            return null;
        }
    }

    public function updateLastSeen(string $deviceId): void
    {
        Device::where('id', $deviceId)->update([
            'last_seen' => Carbon::now(),
        ]);
    }

    public function getActiveDevicesCount(string $appId, int $daysActive = 30): int
    {
        return Device::where('app_id', $appId)
            ->where('last_seen', '>=', Carbon::now()->subDays($daysActive))
            ->count();
    }
}
