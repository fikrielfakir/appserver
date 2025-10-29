<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\Notification;

class ConfigController extends Controller
{
    public function getConfig($packageName)
    {
        $app = App::where('package_name', $packageName)->first();
        
        if (!$app) {
            return response()->json([
                'success' => false,
                'message' => 'App not found'
            ], 404);
        }

        $admobAccounts = $app->admobAccounts()
            ->where('status', 'active')
            ->orderBy('priority')
            ->get();

        $switchingRule = $app->switchingRule;

        $activeNotifications = Notification::where('app_id', $app->id)
            ->where('status', 'sent')
            ->where(function($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->get();

        return response()->json([
            'success' => true,
            'config' => [
                'app' => [
                    'id' => $app->id,
                    'name' => $app->app_name,
                    'status' => $app->status,
                ],
                'admob_accounts' => $admobAccounts,
                'switching_rule' => $switchingRule,
                'notifications' => $activeNotifications,
            ]
        ]);
    }
}
