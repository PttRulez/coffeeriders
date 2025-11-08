<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            
            $table->enum('discount_type', ['percent', 'fixed'])->default('percent');
            $table->unsignedInteger('discount_value');
            
            $table->enum('service_type', ['cycling', 'bike_rent'])->default('cycling');
            
            $table->boolean('is_active')->default(true);
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('max_uses_per_user')->nullable();
            
            $table->timestamps();
            
            $table->unique(['code', 'service_type']);
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};