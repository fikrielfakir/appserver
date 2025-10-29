<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
    use HasUuids;

    protected $table = 'notifications';
    
    protected $fillable = [
        'app_id',
        'title',
        'message',
        'type',
        'priority',
        'status',
        'target_countries',
        'target_app_versions',
        'min_android_version',
        'user_segments',
        'start_date',
        'end_date',
        'recurring',
        'frequency',
        'image_url',
        'action_button_text',
        'action_type',
        'action_value',
        'cancelable',
        'max_displays',
        'display_interval_hours',
        'show_on_app_launch',
    ];
    
    protected $casts = [
        'target_countries' => 'array',
        'target_app_versions' => 'array',
        'user_segments' => 'array',
        'min_android_version' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'recurring' => 'boolean',
        'cancelable' => 'boolean',
        'max_displays' => 'integer',
        'display_interval_hours' => 'integer',
        'show_on_app_launch' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(NotificationEvent::class);
    }
}
