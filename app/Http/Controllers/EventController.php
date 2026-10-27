<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'genre' => ['nullable', 'string', 'max:100'],
            'when' => ['nullable', 'in:upcoming,past,all'],
        ]);

        $search = trim($validated['search'] ?? '');
        $genre = $validated['genre'] ?? '';
        $when = $validated['when'] ?? 'upcoming';

        $events = Event::query()
            ->with('venue')
            ->when($when === 'upcoming', function ($query) {
                $query->where('event_date', '>=', now());
            })
            ->when($when === 'past', function ($query) {
                $query->where('event_date', '<', now());
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
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
                });
            })
            ->when($genre !== '', function ($query) use ($genre) {
                $query->where('genre', $genre);
            })
            ->orderBy(
                'event_date',
                $when === 'past' ? 'desc' : 'asc'
            )
            ->paginate(9)
            ->withQueryString();

        $genres = Event::query()
            ->whereNotNull('genre')
            ->where('genre', '!=', '')
            ->distinct()
            ->orderBy('genre')
            ->pluck('genre');

        return view('events.index', [
            'events' => $events,
            'genres' => $genres,
            'search' => $search,
            'genre' => $genre,
            'when' => $when,
        ]);
    }

    public function show(Event $event): View
    {
        $event->load('venue');

        return view('events.show', compact('event'));
    }
}