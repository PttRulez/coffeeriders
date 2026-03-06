<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->json('race_types')->nullable()->after('race_type');
        });

        DB::table('races')
            ->select(['id', 'race_type'])
            ->orderBy('id')
            ->chunkById(200, function ($races): void {
                foreach ($races as $race) {
                    $type = is_string($race->race_type) && $race->race_type !== ''
                        ? $race->race_type
                        : 'road';

                    DB::table('races')
                        ->where('id', $race->id)
                        ->update([
                            'race_types' => json_encode([$type], JSON_UNESCAPED_UNICODE),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->dropColumn('race_types');
        });
    }
};

