<?php

namespace App\Models;

use App\Enums\DeliveryStatusEnum;
use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'created_by_id',
        'merchant_id',
        'delivery_zone_id',
        'delivery_rate_id',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'notes',
        'status',
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
    ];

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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(DeliveryRate::class, 'delivery_rate_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class, 'delivery_zone_id');
    }

    public function activeDelivery(): HasOne
    {
        return $this->hasOne(Delivery::class)->whereIn('status', [
            DeliveryStatusEnum::CREATED,
            DeliveryStatusEnum::ON_THE_WAY
        ]);
    }
}
