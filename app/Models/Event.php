<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $table = 'events';

    protected $primaryKey = 'event_id';

    protected $fillable = [
        'venue_id',
        'artist_name',
        'event_date',
        'genre',
        'spotify_artist_id',
        'description',
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(
            'App\\Models\\Venue',
            'venue_id',
            'venue_id'
        );
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(
            Attendance::class,
            'event_id',
            'event_id'
        );
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(
            Review::class,
            'event_id',
            'event_id'
        );
    }
}