<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class App extends Model
{
    use HasUuids;

    protected $table = 'apps';
    
    protected $fillable = [
        'package_name',
        'app_name',
        'description',
        'status',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function admobAccounts(): HasMany
    {
        return $this->hasMany(AdmobAccount::class);
    }

    public function switchingRule(): HasOne
    {
        return $this->hasOne(SwitchingRule::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function analyticsEvents(): HasMany
    {
        return $this->hasMany(AnalyticsEvent::class);
    }
}
