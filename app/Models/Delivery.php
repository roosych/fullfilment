<?php

namespace App\Models;

use App\Enums\DeliveryStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Delivery extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'courier_id',
        'delivery_zone_id',
        'delivery_rate_id',
        'weight',
        'price',
        'status',
        'delivery_date',
        'notes',
    ];

    protected $casts = [
        'status' => DeliveryStatusEnum::class,
        'delivery_date' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // Связь с заказом
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Связь с курьером
    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class);
    }

    // Связь с зоной доставки
    public function zone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class, 'delivery_zone_id');
    }

    // Связь с тарифом
    public function rate(): BelongsTo
    {
        return $this->belongsTo(DeliveryRate::class, 'delivery_rate_id');
    }
}
