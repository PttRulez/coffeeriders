<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshop_spare_part_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users');
            $table->foreignId('workshop_spare_part_id')
                ->constrained('workshop_spare_parts')
                ->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('purchase_price_rub');
            $table->date('purchased_at');
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_spare_part_purchases');
    }
};
