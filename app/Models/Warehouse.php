<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Warehouse extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'address',
        'active',
        'is_primary',
        'manager_id',
        'phone',
        'notes',
        'location_lat',
        'location_lng',
    ];

    /**
     * Связь с менеджером склада (User)
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }



    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
