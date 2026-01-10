<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('delivery_rate_zone', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_zone_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_rate_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['delivery_zone_id', 'delivery_rate_id'], 'unique_zone_rate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_rate_zone');
    }
};

