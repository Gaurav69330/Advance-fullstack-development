<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class GigPulseSeeder extends Seeder
{
    public function run(): void
    {
        $venue = Venue::firstOrCreate(
            [
                'name' => 'The Sample Music Room',
                'city' => 'Wolverhampton',
            ],
            [
                'latitude' => 52.5860,
                'longitude' => -2.1280,
            ]
        );

        $gigs = [
            [
                'artist_name' => 'Midnight Signals',
                'genre' => 'Indie',
                'event_date' => now()->addDays(7)->setTime(19, 30),
                'description' => 'Fictional sample gig: an evening of indie music.',
            ],
            [
                'artist_name' => 'Velvet Current',
                'genre' => 'Jazz',
                'event_date' => now()->addDays(10)->setTime(20, 0),
                'description' => 'Fictional sample gig: live jazz in an intimate venue.',
            ],
            [
                'artist_name' => 'Sunday Soul Club',
                'genre' => 'Soul',
                'event_date' => now()->subDays(7)->setTime(19, 0),
                'description' => 'Fictional past gig for testing reviews and attendance.',
            ],
        ];

        foreach ($gigs as $gig) {
            Event::firstOrCreate(
                [
                    'venue_id' => $venue->venue_id,
                    'artist_name' => $gig['artist_name'],
                ],
                [
                    'genre' => $gig['genre'],
                    'event_date' => $gig['event_date'],
                    'description' => $gig['description'],
                ]
            );
        }
    }
}