<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\App;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppController extends Controller
{
    public function index()
    {
        $apps = App::withCount(['admobAccounts', 'devices', 'notifications'])->get();
        return response()->json(['success' => true, 'apps' => $apps]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_name' => 'required|string|unique:apps',
            'app_name' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:active,paused,disabled',
        ]);

        $validated['id'] = (string) Str::uuid();
        $app = App::create($validated);

        return response()->json(['success' => true, 'app' => $app], 201);
    }

    public function show($id)
    {
        $app = App::with(['admobAccounts', 'switchingRule'])->findOrFail($id);
        return response()->json(['success' => true, 'app' => $app]);
    }

    public function update(Request $request, $id)
    {
        $app = App::findOrFail($id);
        
        $validated = $request->validate([
            'app_name' => 'sometimes|string',
            'description' => 'nullable|string',
            'status' => 'sometimes|string|in:active,paused,disabled',
        ]);

        $app->update($validated);

        return response()->json(['success' => true, 'app' => $app]);
    }

    public function destroy($id)
    {
        $app = App::findOrFail($id);
        $app->delete();

        return response()->json(['success' => true, 'message' => 'App deleted']);
    }
}
