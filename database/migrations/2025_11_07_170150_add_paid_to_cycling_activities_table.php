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
        Schema::table('cycling_activities', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->after('note');
            $table->foreignId('coupon_id')->nullable()->after('is_paid')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cycling_activities', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['is_paid', 'coupon_id']);
        });
    }
};
