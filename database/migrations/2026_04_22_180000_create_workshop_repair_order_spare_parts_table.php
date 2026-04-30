<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshop_repair_order_spare_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_repair_order_id')
                ->constrained('workshop_repair_orders')
                ->cascadeOnDelete();
            $table->foreignId('workshop_spare_part_id')
                ->nullable()
                ->constrained('workshop_spare_parts')
                ->nullOnDelete();
            $table->boolean('external')->default(false);
            $table->string('name', 150)->nullable();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('purchase_price_rub')->default(0);
            $table->unsignedInteger('sale_price_rub')->default(0);
            $table->unsignedInteger('workshop_income_rub')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_repair_order_spare_parts');
    }
};
