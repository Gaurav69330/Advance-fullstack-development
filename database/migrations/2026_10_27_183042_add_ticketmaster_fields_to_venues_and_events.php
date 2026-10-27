<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->string('ticketmaster_id', 100)
                ->nullable()
                ->unique();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('ticketmaster_id', 100)
                ->nullable()
                ->unique();

            $table->text('ticket_url')->nullable();

            $table->boolean('is_demo')->default(false);
        });

        // Label the fictional events created by our sample seeder.
        \Illuminate\Support\Facades\DB::table('events')
            ->where('description', 'like', 'Fictional %')
            ->update(['is_demo' => true]);
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropUnique(['ticketmaster_id']);

            $table->dropColumn([
                'ticketmaster_id',
                'ticket_url',
                'is_demo',
            ]);
        });

        Schema::table('venues', function (Blueprint $table) {
            $table->dropUnique(['ticketmaster_id']);
            $table->dropColumn('ticketmaster_id');
        });
    }
};