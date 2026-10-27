<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id('venue_id');
            $table->string('name');
            $table->string('city');
            $table->decimal('latitude', 9, 6);
            $table->decimal('longitude', 9, 6);
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id');

            $table->foreignId('venue_id')
                ->constrained('venues', 'venue_id')
                ->onDelete('cascade');

            $table->string('artist_name');
            $table->dateTime('event_date');
            $table->string('genre')->nullable();
            $table->string('spotify_artist_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('user_attendances', function (Blueprint $table) {
            $table->id('attendance_id');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('event_id')
                ->constrained('events', 'event_id')
                ->onDelete('cascade');

            $table->enum('status', [
                'Interested',
                'Attending',
                'Went',
            ])->default('Interested');

            $table->string('ticket_photo_path')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'event_id']);
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id('review_id');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('event_id')
                ->constrained('events', 'event_id')
                ->onDelete('cascade');

            $table->integer('rating');
            $table->text('comment');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('user_attendances');
        Schema::dropIfExists('events');
        Schema::dropIfExists('venues');
    }
};