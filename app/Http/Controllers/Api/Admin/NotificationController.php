<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::with('app');
        
        if ($request->has('app_id')) {
            $query->where('app_id', $request->app_id);
        }
        
        $notifications = $query->latest('created_at')->get();
        return response()->json(['success' => true, 'notifications' => $notifications]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'app_id' => 'required|uuid|exists:apps,id',
            'title' => 'required|string',
            'message' => 'required|string',
            'type' => 'required|string|in:popup,toast,banner,fullscreen',
            'priority' => 'required|string|in:low,normal,high',
            'status' => 'nullable|string|in:draft,scheduled,sent,failed',
            'target_countries' => 'nullable|array',
            'target_app_versions' => 'nullable|array',
            'min_android_version' => 'nullable|integer',
            'user_segments' => 'nullable|array',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'recurring' => 'boolean',
            'frequency' => 'nullable|string|in:once,daily,weekly',
            'image_url' => 'nullable|url',
            'action_button_text' => 'nullable|string',
            'action_type' => 'nullable|string|in:deeplink,url,dismiss',
            'action_value' => 'nullable|string',
            'cancelable' => 'boolean',
            'max_displays' => 'nullable|integer',
            'display_interval_hours' => 'nullable|integer',
            'show_on_app_launch' => 'boolean',
        ]);

        $validated['id'] = (string) Str::uuid();
        $notification = Notification::create($validated);

        return response()->json(['success' => true, 'notification' => $notification], 201);
    }

    public function show($id)
    {
        $notification = Notification::with('app', 'events')->findOrFail($id);
        return response()->json(['success' => true, 'notification' => $notification]);
    }

    public function update(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'sometimes|string',
            'message' => 'sometimes|string',
            'type' => 'sometimes|string|in:popup,toast,banner,fullscreen',
            'priority' => 'sometimes|string|in:low,normal,high',
            'status' => 'sometimes|string|in:draft,scheduled,sent,failed',
            'target_countries' => 'nullable|array',
            'target_app_versions' => 'nullable|array',
            'min_android_version' => 'nullable|integer',
            'user_segments' => 'nullable|array',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'recurring' => 'boolean',
            'frequency' => 'nullable|string|in:once,daily,weekly',
            'image_url' => 'nullable|url',
            'action_button_text' => 'nullable|string',
            'action_type' => 'nullable|string|in:deeplink,url,dismiss',
            'action_value' => 'nullable|string',
            'cancelable' => 'boolean',
            'max_displays' => 'nullable|integer',
            'display_interval_hours' => 'nullable|integer',
            'show_on_app_launch' => 'boolean',
        ]);

        $notification->update($validated);

        return response()->json(['success' => true, 'notification' => $notification]);
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true, 'message' => 'Notification deleted']);
    }

    public function send($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['status' => 'sent']);

        return response()->json(['success' => true, 'message' => 'Notification sent']);
    }
}
