<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->string('race_type')->default('road')->after('description');
            $table->boolean('in_our_studio')->default(false)->after('race_type');
            $table->string('organizer_name')->nullable()->after('in_our_studio');
            $table->string('organizer_website_url')->nullable()->after('organizer_name');
            $table->string('registration_url')->nullable()->after('organizer_website_url');
            $table->string('yandex_map_url')->nullable()->after('registration_url');
            $table->string('cover_img_url')->nullable()->after('yandex_map_url');
        });
    }

    public function down(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->dropColumn([
                'race_type',
                'in_our_studio',
                'organizer_name',
                'organizer_website_url',
                'registration_url',
                'yandex_map_url',
                'cover_img_url',
            ]);
        });
    }
};
