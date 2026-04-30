<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshop_repair_order_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_repair_order_id')
                ->constrained('workshop_repair_orders')
                ->cascadeOnDelete();
            $table->string('photo_url');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_repair_order_photos');
    }
};
