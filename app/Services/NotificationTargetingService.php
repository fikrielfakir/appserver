<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Notification;
use App\Models\NotificationEvent;
use Carbon\Carbon;

class NotificationTargetingService
{
    public function shouldShowNotification(Notification $notification, Device $device): bool
    {
        if ($notification->status !== 'active') {
            return false;
        }

        if (!$this->isWithinSchedule($notification)) {
            return false;
        }

        if (!$this->matchesTargetCountry($notification, $device)) {
            return false;
        }

        if (!$this->matchesAppVersion($notification, $device)) {
            return false;
        }

        if (!$this->matchesAndroidVersion($notification, $device)) {
            return false;
        }

        if (!$this->respectsDisplayLimits($notification, $device)) {
            return false;
        }

        return true;
    }

    protected function isWithinSchedule(Notification $notification): bool
    {
        $now = Carbon::now();

        if ($notification->start_date && $now->lt($notification->start_date)) {
            return false;
        }

        if ($notification->end_date && $now->gt($notification->end_date)) {
            return false;
        }

        return true;
    }

    protected function matchesTargetCountry(Notification $notification, Device $device): bool
    {
        if (empty($notification->target_countries)) {
            return true;
        }

        if (!$device->country) {
            return false;
        }

        return in_array($device->country, $notification->target_countries);
    }

    protected function matchesAppVersion(Notification $notification, Device $device): bool
    {
        if (empty($notification->target_app_versions)) {
            return true;
        }

        if (!$device->app_version) {
            return false;
        }

        return in_array($device->app_version, $notification->target_app_versions);
    }

    protected function matchesAndroidVersion(Notification $notification, Device $device): bool
    {
        if (!$notification->min_android_version) {
            return true;
        }

        if (!$device->android_version) {
            return false;
        }

        return $device->android_version >= $notification->min_android_version;
    }

    protected function respectsDisplayLimits(Notification $notification, Device $device): bool
    {
        $displayedCount = NotificationEvent::where('notification_id', $notification->id)
            ->where('device_id', $device->id)
            ->where('event_type', 'displayed')
            ->count();

        if ($displayedCount >= $notification->max_displays) {
            return false;
        }

        if ($displayedCount > 0 && $notification->display_interval_hours > 0) {
            $lastDisplay = NotificationEvent::where('notification_id', $notification->id)
                ->where('device_id', $device->id)
                ->where('event_type', 'displayed')
                ->latest('timestamp')
                ->first();

            if ($lastDisplay) {
                $hoursSinceLastDisplay = Carbon::parse($lastDisplay->timestamp)
                    ->diffInHours(Carbon::now());

                if ($hoursSinceLastDisplay < $notification->display_interval_hours) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getPendingNotifications(Device $device): array
    {
        $notifications = Notification::where('app_id', $device->app_id)
            ->where('status', 'active')
            ->get();

        $pending = [];

        foreach ($notifications as $notification) {
            if ($this->shouldShowNotification($notification, $device)) {
                $pending[] = $this->formatNotification($notification);
            }
        }

        return $pending;
    }

    protected function formatNotification(Notification $notification): array
    {
        return [
            'notification_id' => $notification->id,
            'title' => $notification->title,
            'message' => $notification->message,
            'type' => $notification->type,
            'priority' => $notification->priority,
            'content' => [
                'image_url' => $notification->image_url,
                'action_button_text' => $notification->action_button_text,
                'action_type' => $notification->action_type,
                'action_value' => $notification->action_value,
                'cancelable' => $notification->cancelable,
            ],
            'display_rules' => [
                'max_displays' => $notification->max_displays,
                'display_interval_hours' => $notification->display_interval_hours,
                'show_on_app_launch' => $notification->show_on_app_launch,
            ],
        ];
    }
}
