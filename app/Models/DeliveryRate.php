<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryRate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

//    public function zones(): BelongsToMany
//    {
//        return $this->belongsToMany(DeliveryZone::class, 'delivery_rate_zone')
//            ->withTimestamps();
//    }

    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(
            DeliveryZone::class,
            'delivery_rate_prices',   // ← правильная таблица
            'delivery_rate_id',       // ← FK на тариф
            'delivery_zone_id'        // ← FK на зону
        )->withPivot(['min_weight', 'max_weight', 'price', 'active']);
    }


    public function ratePrices(): HasMany
    {
        return $this->hasMany(DeliveryRatePrice::class);
    }

    public function zonesWithPrices()
    {
        return $this->hasManyThrough(
            DeliveryRatePrice::class,
            DeliveryZone::class,
            'id',               // ключ зоны в DeliveryZone
            'delivery_rate_id', // ключ тарифа в DeliveryRatePrice
            'id',               // локальный ключ тарифа
            'id'                // локальный ключ зоны
        )->with('zone');
    }
}

