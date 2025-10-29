<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\Notification;
use App\Models\NotificationEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function pending(Request $request)
    {
        $validated = $request->validate([
            'package_name' => 'required|string',
            'country' => 'nullable|string',
            'app_version' => 'nullable|string',
            'android_version' => 'nullable|integer',
        ]);

        $app = App::where('package_name', $validated['package_name'])->first();
        
        if (!$app) {
            return response()->json([
                'success' => false,
                'message' => 'App not found'
            ], 404);
        }

        $query = Notification::where('app_id', $app->id)
            ->where('status', 'sent')
            ->where(function($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });

        if ($validated['country'] ?? null) {
            $query->where(function($q) use ($validated) {
                $q->whereJsonContains('target_countries', $validated['country'])
                    ->orWhereNull('target_countries');
            });
        }

        if ($validated['app_version'] ?? null) {
            $query->where(function($q) use ($validated) {
                $q->whereJsonContains('target_app_versions', $validated['app_version'])
                    ->orWhereNull('target_app_versions');
            });
        }

        if ($validated['android_version'] ?? null) {
            $query->where(function($q) use ($validated) {
                $q->where('min_android_version', '<=', $validated['android_version'])
                    ->orWhereNull('min_android_version');
            });
        }

        $notifications = $query->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    public function track(Request $request)
    {
        $validated = $request->validate([
            'notification_id' => 'required|uuid|exists:notifications,id',
            'device_id' => 'nullable|uuid|exists:devices,id',
            'event_type' => 'required|string|in:displayed,clicked,dismissed',
        ]);

        $event = NotificationEvent::create([
            'id' => (string) Str::uuid(),
            'notification_id' => $validated['notification_id'],
            'device_id' => $validated['device_id'] ?? null,
            'event_type' => $validated['event_type'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event tracked successfully',
            'event' => $event
        ], 201);
    }
}
