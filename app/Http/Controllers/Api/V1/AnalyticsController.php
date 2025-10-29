<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnalyticsController extends Controller
{
    public function track(Request $request)
    {
        $validated = $request->validate([
            'package_name' => 'required|string',
            'account_id' => 'nullable|uuid',
            'event_type' => 'required|string|in:impression,click,revenue',
            'ad_type' => 'nullable|string|in:banner,interstitial,rewarded,app_open,native',
            'value' => 'nullable|integer',
            'country' => 'nullable|string',
        ]);

        $app = App::where('package_name', $validated['package_name'])->first();
        
        if (!$app) {
            return response()->json([
                'success' => false,
                'message' => 'App not found'
            ], 404);
        }

        $event = AnalyticsEvent::create([
            'id' => (string) Str::uuid(),
            'app_id' => $app->id,
            'account_id' => $validated['account_id'] ?? null,
            'event_type' => $validated['event_type'],
            'ad_type' => $validated['ad_type'] ?? null,
            'value' => $validated['value'] ?? null,
            'country' => $validated['country'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event tracked successfully',
            'event' => $event
        ], 201);
    }
}
