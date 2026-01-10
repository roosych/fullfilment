<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_entries', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('stock_batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->integer('remaining_quantity')->default(0);
            $table->integer('reserved_quantity')->default(0); // сколько зарезервировано под заказы
            $table->unsignedBigInteger('purchase_price')->nullable(); // в копейках
            $table->timestamp('expires_at')->nullable(); // для товаров с ограниченным сроком
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_entries');
    }
};
