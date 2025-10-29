<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdmobAccount extends Model
{
    use HasUuids;

    protected $table = 'admob_accounts';
    
    protected $fillable = [
        'app_id',
        'account_name',
        'status',
        'priority',
        'weight',
        'banner_id',
        'interstitial_id',
        'rewarded_id',
        'app_open_id',
        'native_id',
    ];
    
    protected $casts = [
        'priority' => 'integer',
        'weight' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    public function analyticsEvents(): HasMany
    {
        return $this->hasMany(AnalyticsEvent::class, 'account_id');
    }
}
