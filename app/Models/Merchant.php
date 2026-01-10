<?php

namespace App\Models;

use App\Traits\HasBalance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Merchant extends Model
{
    use HasBalance, SoftDeletes, HasRelationships;

    protected $fillable = [
        'uuid',
        'user_id',
        'company',
        'address',
        'phone',
        'avatar',
        'id_card',
        'balance',
        'reserved_balance',
        'notes',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    // Связи
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(BalanceTransaction::class)->orderByDesc('created_at');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function deliveries(): HasManyThrough
    {
        return $this->hasManyThrough(
            Delivery::class,
            Order::class,
            'merchant_id',
            'order_id',
            'id',
            'id'
        );
    }

    public function getStockByWarehouse(): array
    {
        $this->loadMissing([
            'stock_batches.entries.product',
            'stock_batches.warehouse',
        ]);

        $productsData = [];

        foreach ($this->stock_batches as $batch) {
            $warehouse = $batch->warehouse;
            foreach ($batch->entries as $entry) {
                if ($entry->remaining_quantity > 0 && $entry->product) {
                    $product = $entry->product;
                    $productId = $product->id;
                    $warehouseName = $warehouse->name ?? '—';

                    $productsData[$productId]['product'] = $product;
                    $productsData[$productId]['warehouses'][$warehouseName] =
                        ($productsData[$productId]['warehouses'][$warehouseName] ?? 0)
                        + $entry->remaining_quantity;
                }
            }
        }

        return $productsData;
    }





    /**
     * Батчи (партии поставок).
     */
    public function stock_batches(): HasMany
    {
        return $this->hasMany(StockBatch::class)->orderByDesc('received_at');
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
