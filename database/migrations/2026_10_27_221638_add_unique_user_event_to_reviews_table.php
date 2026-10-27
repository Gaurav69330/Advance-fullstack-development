<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicate = DB::table('reviews')
            ->select('user_id', 'event_id')
            ->groupBy('user_id', 'event_id')
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicate) {
            throw new \RuntimeException(
                'Duplicate reviews already exist. '
                . 'Review them before adding the unique constraint. '
                . 'No reviews have been deleted.'
            );
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->unique(
                ['user_id', 'event_id'],
                'reviews_user_event_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique('reviews_user_event_unique');
        });
    }
};