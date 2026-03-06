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
            $table->dropColumn('race_type');
        });
    }

    public function down(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->string('race_type')->default('road')->after('description');
        });

        DB::table('races')
            ->select(['id', 'race_types'])
            ->orderBy('id')
            ->chunkById(200, function ($races): void {
                foreach ($races as $race) {
                    $raceTypes = json_decode((string) $race->race_types, true);
                    $primaryType = is_array($raceTypes) && isset($raceTypes[0]) && is_string($raceTypes[0])
                        ? $raceTypes[0]
                        : 'road';

                    DB::table('races')
                        ->where('id', $race->id)
                        ->update([
                            'race_type' => $primaryType,
                        ]);
                }
            });
    }
};

