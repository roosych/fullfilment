<?php

use App\Enums\DeliveryStatusEnum;
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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id()->from(7001);
            $table->uuid()->unique();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();   // заказ
            $table->foreignId('courier_id')->nullable()->constrained()->nullOnDelete(); // курьер

            $table->foreignId('delivery_zone_id')->nullable()->constrained()->nullOnDelete(); // зона доставки
            $table->foreignId('delivery_rate_id')->nullable()->constrained()->nullOnDelete(); // выбранный тариф

            $table->decimal('weight', 8, 2)->default(0); // вес посылки в кг
            $table->bigInteger('price')->default(0); // цена доставки в копейках

            $table->string('status')->default(DeliveryStatusEnum::CREATED);

            $table->dateTime('delivery_date')->nullable(); // дата доставки
            $table->text('notes')->nullable();             // заметки

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
