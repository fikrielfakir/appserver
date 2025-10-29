<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    use HasUuids;

    protected $table = 'devices';
    public $timestamps = false;
    
    protected $fillable = [
        'app_id',
        'fcm_token',
        'country',
        'app_version',
        'android_version',
        'device_manufacturer',
        'device_model',
    ];
    
    protected $casts = [
        'android_version' => 'integer',
        'last_seen' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }
}
