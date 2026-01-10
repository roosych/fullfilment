<?php

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Таблица заказов
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->from(1001);
            $table->uuid()->unique();

            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete(); // кто создал заказ
            $table->foreignId('merchant_id')->nullable()->constrained('merchants')->nullOnDelete(); // чей заказ (мерчант)

            $table->foreignId('delivery_zone_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('delivery_rate_id')->nullable()->constrained()->nullOnDelete();

            $table->string('recipient_name');
            $table->string('recipient_phone')->nullable();
            $table->text('recipient_address');
            $table->text('notes')->nullable();

            $table->string('status')->default(OrderStatusEnum::CREATED);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
