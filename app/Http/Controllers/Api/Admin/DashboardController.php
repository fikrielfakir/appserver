<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\AdmobAccount;
use App\Models\Device;
use App\Models\Notification;
use App\Models\AnalyticsEvent;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats()
    {
        $totalApps = App::count();
        $totalAccounts = AdmobAccount::count();
        $totalDevices = Device::count();
        $activeNotifications = Notification::where('status', 'sent')->count();
        
        $todayRevenue = AnalyticsEvent::where('event_type', 'revenue')
            ->whereDate('timestamp', today())
            ->sum('value');
        
        $todayImpressions = AnalyticsEvent::where('event_type', 'impression')
            ->whereDate('timestamp', today())
            ->count();
        
        return response()->json([
            'success' => true,
            'stats' => [
                'totalApps' => $totalApps,
                'totalAccounts' => $totalAccounts,
                'totalDevices' => $totalDevices,
                'activeNotifications' => $activeNotifications,
                'todayRevenue' => $todayRevenue / 100,
                'todayImpressions' => $todayImpressions,
            ]
        ]);
    }
}
