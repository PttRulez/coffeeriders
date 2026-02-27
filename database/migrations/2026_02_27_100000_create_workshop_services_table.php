<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshop_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_category_id')
                ->constrained('workshop_categories');
            $table->string('name');
            $table->unsignedInteger('price_rub');
            $table->string('additional_info')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_services');
    }
};
