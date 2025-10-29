<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SwitchingRule extends Model
{
    use HasUuids;

    protected $table = 'switching_rules';
    public $timestamps = false;
    
    protected $fillable = [
        'app_id',
        'strategy',
        'rotation_interval',
        'fallback_enabled',
        'ab_testing_enabled',
        'geographic_rules',
    ];
    
    protected $casts = [
        'fallback_enabled' => 'boolean',
        'ab_testing_enabled' => 'boolean',
        'geographic_rules' => 'array',
        'updated_at' => 'datetime',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }
}
