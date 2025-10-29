<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationEvent extends Model
{
    use HasUuids;

    protected $table = 'notification_events';
    public $timestamps = false;
    const CREATED_AT = 'timestamp';
    
    protected $fillable = [
        'notification_id',
        'device_id',
        'event_type',
    ];
    
    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
