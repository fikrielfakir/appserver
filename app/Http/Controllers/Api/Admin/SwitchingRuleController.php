<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SwitchingRule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SwitchingRuleController extends Controller
{
    public function show($appId)
    {
        $rule = SwitchingRule::where('app_id', $appId)->first();
        return response()->json(['success' => true, 'rule' => $rule]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'app_id' => 'required|uuid|exists:apps,id|unique:switching_rules',
            'strategy' => 'required|string|in:weighted_random,sequential,geographic,time_based',
            'rotation_interval' => 'required|string|in:hourly,daily,session,random',
            'fallback_enabled' => 'boolean',
            'ab_testing_enabled' => 'boolean',
            'geographic_rules' => 'nullable|array',
        ]);

        $validated['id'] = (string) Str::uuid();
        $rule = SwitchingRule::create($validated);

        return response()->json(['success' => true, 'rule' => $rule], 201);
    }

    public function update(Request $request, $appId)
    {
        $rule = SwitchingRule::where('app_id', $appId)->firstOrFail();
        
        $validated = $request->validate([
            'strategy' => 'sometimes|string|in:weighted_random,sequential,geographic,time_based',
            'rotation_interval' => 'sometimes|string|in:hourly,daily,session,random',
            'fallback_enabled' => 'boolean',
            'ab_testing_enabled' => 'boolean',
            'geographic_rules' => 'nullable|array',
        ]);

        $rule->update($validated);

        return response()->json(['success' => true, 'rule' => $rule]);
    }
}
