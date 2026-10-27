<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->char('country_code', 2)
                ->nullable()
                ->index();
        });

        // The existing sample venue is in the United Kingdom.
        \Illuminate\Support\Facades\DB::table('venues')
            ->where('name', 'The Sample Music Room')
            ->where('city', 'Wolverhampton')
            ->update(['country_code' => 'GB']);
    }

    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropIndex(['country_code']);
            $table->dropColumn('country_code');
        });
    }
};