<?php

namespace App\Services;

use App\Models\AdmobAccount;
use App\Models\App;
use App\Models\SwitchingRule;
use Illuminate\Support\Collection;

class AdMobStrategyService
{
    public function selectAccount(App $app): ?AdmobAccount
    {
        $switchingRule = $app->switchingRule;
        $accounts = $app->admobAccounts()->where('status', 'active')->get();

        if ($accounts->isEmpty()) {
            return null;
        }

        if (!$switchingRule) {
            return $accounts->first();
        }

        return match ($switchingRule->strategy) {
            'weighted_random' => $this->weightedRandomSelection($accounts),
            'rotation' => $this->rotationSelection($accounts, $switchingRule->rotation_interval),
            'priority' => $this->prioritySelection($accounts),
            'ab_testing' => $this->abTestingSelection($accounts),
            default => $accounts->first(),
        };
    }

    protected function weightedRandomSelection(Collection $accounts): ?AdmobAccount
    {
        $totalWeight = $accounts->sum('weight');
        
        if ($totalWeight <= 0) {
            return $accounts->first();
        }

        $random = rand(1, $totalWeight);
        $currentWeight = 0;

        foreach ($accounts as $account) {
            $currentWeight += $account->weight;
            if ($random <= $currentWeight) {
                return $account;
            }
        }

        return $accounts->first();
    }

    protected function rotationSelection(Collection $accounts, string $interval): ?AdmobAccount
    {
        $index = match ($interval) {
            'hourly' => (int)(date('YmdH') % $accounts->count()),
            'daily' => (int)(date('Ymd') % $accounts->count()),
            'weekly' => (int)(date('YW') % $accounts->count()),
            'monthly' => (int)(date('Ym') % $accounts->count()),
            default => (int)(date('Ymd') % $accounts->count()),
        };

        return $accounts->values()->get($index) ?? $accounts->first();
    }

    protected function prioritySelection(Collection $accounts): ?AdmobAccount
    {
        return $accounts->sortByDesc('priority')->first();
    }

    protected function abTestingSelection(Collection $accounts): ?AdmobAccount
    {
        if ($accounts->count() <= 1) {
            return $accounts->first();
        }

        $index = rand(0, $accounts->count() - 1);
        return $accounts->values()->get($index);
    }

    public function getAccountConfig(App $app): array
    {
        $account = $this->selectAccount($app);

        if (!$account) {
            return [
                'error' => 'No active AdMob accounts found',
                'admob_accounts' => [],
            ];
        }

        return [
            'admob_accounts' => [
                [
                    'account_id' => $account->id,
                    'status' => $account->status,
                    'banner_id' => $account->banner_id,
                    'interstitial_id' => $account->interstitial_id,
                    'rewarded_id' => $account->rewarded_id,
                    'app_open_id' => $account->app_open_id,
                    'native_id' => $account->native_id,
                ],
            ],
        ];
    }
}
