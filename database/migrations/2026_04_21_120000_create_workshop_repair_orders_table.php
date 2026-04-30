<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshop_repair_orders', function (Blueprint $table) {
            $table->id();
            $table->string('bike_name', 150);
            $table->string('comment', 255)->nullable();
            $table->string('client_phone', 40)->nullable();
            $table->string('client_telegram', 120)->nullable();
            $table->foreignId('mechanic_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('status', 20)->default('pending');
            $table->dateTime('finished_at')->nullable();
            $table->integer('mechanic_income_rub')->default(0);
            $table->integer('workshop_works_income_rub')->default(0);
            $table->integer('workshop_spare_parts_income_rub')->default(0);
            $table->integer('workshop_income_rub')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_repair_orders');
    }
};
