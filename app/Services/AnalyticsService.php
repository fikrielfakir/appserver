<?php

namespace App\Services;

use App\Models\AnalyticsEvent;
use App\Models\App;
use Illuminate\Support\Str;

class AnalyticsService
{
    public function trackAdMobEvent(string $packageName, array $data): bool
    {
        try {
            $app = App::where('package_name', $packageName)->first();

            if (!$app) {
                return false;
            }

            AnalyticsEvent::create([
                'id' => Str::uuid(),
                'app_id' => $app->id,
                'account_id' => $data['account_id'] ?? null,
                'event_type' => $data['event'] ?? 'unknown',
                'ad_type' => $data['ad_type'] ?? null,
                'value' => $data['value'] ?? 0,
                'country' => $data['country'] ?? null,
            ]);

            return true;
        } catch (\Exception $e) {
            logger()->error('Analytics tracking failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getAppAnalytics(string $appId, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = AnalyticsEvent::where('app_id', $appId);

        if ($startDate) {
            $query->where('timestamp', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('timestamp', '<=', $endDate);
        }

        $events = $query->get();

        return [
            'total_events' => $events->count(),
            'impressions' => $events->where('event_type', 'impression')->count(),
            'clicks' => $events->where('event_type', 'click')->count(),
            'by_ad_type' => [
                'banner' => $events->where('ad_type', 'banner')->count(),
                'interstitial' => $events->where('ad_type', 'interstitial')->count(),
                'rewarded' => $events->where('ad_type', 'rewarded')->count(),
            ],
            'by_country' => $events->groupBy('country')
                ->map(fn($group) => $group->count())
                ->toArray(),
        ];
    }
}
