<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends Model
{
    use HasUuids;

    protected $table = 'analytics_events';
    public $timestamps = false;
    const CREATED_AT = 'timestamp';
    
    protected $fillable = [
        'app_id',
        'account_id',
        'event_type',
        'ad_type',
        'value',
        'country',
    ];
    
    protected $casts = [
        'value' => 'integer',
        'timestamp' => 'datetime',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(AdmobAccount::class, 'account_id');
    }
}
