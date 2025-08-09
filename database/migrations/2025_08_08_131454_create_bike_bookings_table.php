<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bike_bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bike_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('customer_name');
            $table->string('telegram_username')->nullable();
            $table->string('phone')->nullable();
            $table->text('comment')->nullable();

            $table->date('starts_at');
            $table->date('ends_at');

            $table->string('status')->default('booked');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bike_bookings');
    }
};