<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryZone extends Model
{
    protected $fillable = [
        'name',
        'description',
        'polygon_coordinates',
        'active',
    ];

    protected $casts = [
        'polygon_coordinates' => 'array',
        'active' => 'boolean',
    ];

    public function rates(): BelongsToMany
    {
        return $this->belongsToMany(DeliveryRate::class, 'delivery_rate_zone')
            ->withTimestamps();
    }

    public function ratePrices(): HasMany
    {
        return $this->hasMany(DeliveryRatePrice::class);
    }
}

