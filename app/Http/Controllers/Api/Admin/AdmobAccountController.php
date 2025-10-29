<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmobAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdmobAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = AdmobAccount::with('app');
        
        if ($request->has('app_id')) {
            $query->where('app_id', $request->app_id);
        }
        
        $accounts = $query->orderBy('priority')->get();
        return response()->json(['success' => true, 'accounts' => $accounts]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'app_id' => 'required|uuid|exists:apps,id',
            'account_name' => 'required|string',
            'status' => 'nullable|string|in:active,paused,disabled',
            'priority' => 'nullable|integer',
            'weight' => 'nullable|integer|min:0|max:100',
            'banner_id' => 'nullable|string',
            'interstitial_id' => 'nullable|string',
            'rewarded_id' => 'nullable|string',
            'app_open_id' => 'nullable|string',
            'native_id' => 'nullable|string',
        ]);

        $validated['id'] = (string) Str::uuid();
        $account = AdmobAccount::create($validated);

        return response()->json(['success' => true, 'account' => $account], 201);
    }

    public function show($id)
    {
        $account = AdmobAccount::with('app')->findOrFail($id);
        return response()->json(['success' => true, 'account' => $account]);
    }

    public function update(Request $request, $id)
    {
        $account = AdmobAccount::findOrFail($id);
        
        $validated = $request->validate([
            'account_name' => 'sometimes|string',
            'status' => 'sometimes|string|in:active,paused,disabled',
            'priority' => 'sometimes|integer',
            'weight' => 'sometimes|integer|min:0|max:100',
            'banner_id' => 'nullable|string',
            'interstitial_id' => 'nullable|string',
            'rewarded_id' => 'nullable|string',
            'app_open_id' => 'nullable|string',
            'native_id' => 'nullable|string',
        ]);

        $account->update($validated);

        return response()->json(['success' => true, 'account' => $account]);
    }

    public function destroy($id)
    {
        $account = AdmobAccount::findOrFail($id);
        $account->delete();

        return response()->json(['success' => true, 'message' => 'Account deleted']);
    }
}
