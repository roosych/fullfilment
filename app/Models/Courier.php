<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Courier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'vehicle_type',
        'phone',
        'id_card',
        'avatar',
        'active',
        'notes',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // Связь с пользователем
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Связь с доставками
//    public function deliveries(): HasMany
//    {
//        return $this->hasMany(Delivery::class, 'courier_id');
//    }
}
