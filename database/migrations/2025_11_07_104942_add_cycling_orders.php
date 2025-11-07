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
         Schema::create('cycling_orders', function (Blueprint $table) {
             $table->id();
             $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
             $table->unsignedSmallInteger('quantity');
             $table->unsignedInteger('amount_paid')->default(0);
             $table->timestamps();
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cycling_orders');
    }
};
