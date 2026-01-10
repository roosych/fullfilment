<?php

namespace App\Models;

use App\Enums\BalanceTransactionTypeEnum;
use App\Traits\HasBalance;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BalanceTransaction extends Model
{
    use HasUuids, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'merchant_id',
        'type',
        'amount',
        'source_id',
        'source_type',
    ];

    protected $casts = [
        'type' => BalanceTransactionTypeEnum::class,
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
