<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchSuggestionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $search = trim($validated['q'] ?? '');

        if (mb_strlen($search) < 2) {
            return response()->json([
                'suggestions' => [],
            ]);
        }

        $events = Event::query()
            ->with('venue')
            ->where('event_date', '>=', now())
            ->where(function ($query) use ($search) {
                $query
                    ->where(
                        'artist_name',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhereHas(
                        'venue',
                        function ($venueQuery) use ($search) {
                            $venueQuery->where(function ($query) use ($search) {
                                $query
                                    ->where(
                                        'name',
                                        'like',
                                        '%' . $search . '%'
                                    )
                                    ->orWhere(
                                        'city',
                                        'like',
                                        '%' . $search . '%'
                                    );
                            });
                        }
                    );
            })
            ->orderBy('event_date')
            ->limit(8)
            ->get();

        $suggestions = $events->map(function (Event $event): array {
            return [
                'id' => $event->event_id,
                'title' => $event->artist_name,
                'venue' => $event->venue?->name
                    ?? 'Venue to be confirmed',
                'city' => $event->venue?->city
                    ?? 'City to be confirmed',
                'date' => $event->event_date->format('j M Y'),
                'url' => route('events.show', $event),
            ];
        });

        return response()
            ->json([
                'suggestions' => $suggestions->all(),
            ])
            ->header('Cache-Control', 'no-store');
    }
}