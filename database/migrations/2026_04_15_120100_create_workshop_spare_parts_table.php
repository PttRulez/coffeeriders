<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshop_spare_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_spare_part_category_id')
                ->constrained('workshop_spare_part_categories')
                ->restrictOnDelete();
            $table->string('name');
            $table->string('comment')->nullable();
            $table->unsignedInteger('purchase_price_rub');
            $table->unsignedInteger('sale_price_rub');
            $table->unsignedInteger('quantity')->default(0);
            $table->string('photo_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_spare_parts');
    }
};
