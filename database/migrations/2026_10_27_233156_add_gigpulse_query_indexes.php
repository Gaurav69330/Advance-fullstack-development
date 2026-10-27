<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->index(
                'event_date',
                'gp_events_date_index'
            );
        });

        Schema::table('user_attendances', function (Blueprint $table) {
            $table->index(
                ['event_id', 'status'],
                'gp_attendance_event_status_index'
            );
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index(
                ['event_id', 'updated_at'],
                'gp_reviews_event_updated_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('gp_reviews_event_updated_index');
        });

        Schema::table('user_attendances', function (Blueprint $table) {
            $table->dropIndex('gp_attendance_event_status_index');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('gp_events_date_index');
        });
    }
};