<?php

use App\Enums\Pedals;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedSmallInteger('height')->nullable();
            $table->unsignedSmallInteger('weight')->nullable();
            $table->enum('pedals', array_map(fn($case) => $case->value, Pedals::cases()))->default(Pedals::SHIMANO->value);
        });
    }
    
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['height', 'weight', 'pedals']);
        });
    }
};
