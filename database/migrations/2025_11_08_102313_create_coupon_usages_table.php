<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('orderable');
            $table->string('coupon_code');
            $table->enum('discount_type', ['percent', 'fixed']);
            $table->unsignedInteger('discount_value');
            $table->unsignedInteger('base_price');
            $table->unsignedInteger('applied_discount');
            $table->unsignedInteger('final_price');
            $table->timestamps();
            $table->index(['coupon_id', 'user_id']);
            $table->unique(['orderable_type', 'orderable_id'], 'coupon_usages_unique_orderable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
    }
};