<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Saved Listings | GigPulse</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f6f4ec;
            color: #20231e;
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        a {
            color: inherit;
        }

        main {
            max-width: 1200px;
            margin: auto;
            padding: 35px 24px 60px;
        }

        h1 {
            font-size: clamp(32px, 5vw, 52px);
            line-height: 1.2;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            align-items: end;
            gap: 16px;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
            background: #e8ecdf;
        }

        .search-field {
            flex: 1 1 240px;
        }

        label {
            display: block;
            font-weight: bold;
        }

        input,
        select {
            display: block;
            width: 100%;
            min-height: 46px;
            margin-top: 6px;
            padding: 10px;
            border: 1px solid #bbc5a8;
            border-radius: 5px;
            background: white;
            color: #20231e;
            font: inherit;
        }

        .filter-button,
        .button {
            display: inline-block;
            min-height: 44px;
            padding: 10px 18px;
            border: 1px solid #20231e;
            border-radius: 5px;
            background: #20231e;
            color: white;
            font: inherit;
            text-decoration: none;
            cursor: pointer;
        }

        .filter-button:hover,
        .button:hover {
            background: #344b38;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .card {
            display: flex;
            flex-direction: column;
            align-items: start;
            padding: 24px;
            border: 1px solid #d9dccf;
            border-radius: 8px;
            background: #fffef9;
        }

        .card h2 {
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        .card p {
            margin: 6px 0;
        }

        .card-details {
            margin-bottom: 22px;
        }

        .card .button {
            margin-top: auto;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            background: #d8f36a;
            font-size: 12px;
        }

        .hint {
            color: #686b60;
            font-size: 13px;
        }

        .empty,
        .message {
            padding: 24px;
            border: 1px solid #d9dccf;
            border-radius: 8px;
            background: #fffef9;
        }

        .error {
            color: #8b2727;
        }

        .pagination {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-top: 30px;
        }

        footer {
            padding: 24px;
            border-top: 1px solid #d9dccf;
            text-align: center;
            color: #686b60;
        }

        :focus-visible {
            outline: 3px solid #637c26;
            outline-offset: 4px;
        }

        @media (max-width: 900px) {
            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 600px) {
            .cards {
                grid-template-columns: 1fr;
            }

            .filters > * {
                width: 100%;
            }

            main {
                padding: 30px 18px;
            }
        }
    </style>
</head>

<body>
    @include('partials.header')

    @php
        $validTimezones = \DateTimeZone::listIdentifiers(
            \DateTimeZone::ALL_WITH_BC
        );
    @endphp

    <main>
        <h1>Saved event listings</h1>

        <p>
            Browse events stored in GigPulse.
            Choose Past gigs to find events you can review.
        </p>

        @if (session('success'))
            <p class="message" role="status">
                {{ session('success') }}
            </p>
        @endif

        @if (session('error'))
            <p class="message error" role="alert">
                {{ session('error') }}
            </p>
        @endif

        @if ($errors->any())
            <div class="message error" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            class="filters"
            method="GET"
            action="{{ route('saved-events.index') }}"
            role="search"
        >
            <div class="search-field">
                <label for="search">Artist, venue, or city</label>

                <input
                    id="search"
                    name="search"
                    type="search"
                    value="{{ $search }}"
                    maxlength="100"
                    placeholder="Search saved events"
                >
            </div>

            <div>
                <label for="when">When</label>

                <select id="when" name="when">
                    <option
                        value="upcoming"
                        @selected($when === 'upcoming')
                    >
                        Upcoming gigs
                    </option>

                    <option
                        value="past"
                        @selected($when === 'past')
                    >
                        Past gigs
                    </option>

                    <option
                        value="all"
                        @selected($when === 'all')
                    >
                        All gigs
                    </option>
                </select>
            </div>

            <div>
                <label for="genre">Genre</label>

                <select id="genre" name="genre">
                    <option value="">All genres</option>

                    @foreach ($genres as $availableGenre)
                        <option
                            value="{{ $availableGenre }}"
                            @selected($genre === $availableGenre)
                        >
                            {{ $availableGenre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button class="filter-button" type="submit">
                Apply filters
            </button>

            <a href="{{ route('saved-events.index') }}">
                Clear filters
            </a>
        </form>

        <p>
            {{ $events->total() }}
            {{ $events->total() === 1
                ? 'matching saved gig'
                : 'matching saved gigs' }}
        </p>

        @if ($events->isEmpty())
            <section class="empty">
                <h2>No matching gigs are stored yet.</h2>

                <p>
                    Try All gigs, clear your filters,
                    or save an event from Discover.
                </p>

                <a class="button" href="{{ route('events.index') }}">
                    Discover gigs →
                </a>
            </section>
        @else
            <div class="cards">
                @foreach ($events as $event)
                    @php
                        $venueTimezone = $event->venue?->timezone;

                        $hasVenueTimezone = is_string($venueTimezone)
                            && in_array(
                                $venueTimezone,
                                $validTimezones,
                                true
                            );

                        $displayTimezone = $hasVenueTimezone
                            ? $venueTimezone
                            : 'UTC';

                        $localEventDate = $event->event_date
                            ->copy()
                            ->setTimezone($displayTimezone);
                    @endphp

                    <article class="card">
                        <span class="badge">
                            {{ $event->genre ?: 'Live music' }}
                        </span>

                        <h2>{{ $event->artist_name }}</h2>

                        <div class="card-details">
                            @if ($event->is_demo)
                                <p class="hint">
                                    Fictional sample gig
                                </p>
                            @endif

                            <p>
                                <strong>Date:</strong>

                                <time datetime="{{ $localEventDate->toIso8601String() }}">
                                    {{ $localEventDate->format('D, j M Y') }}
                                </time>
                            </p>

                            <p>
                                <strong>
                                    {{ $hasVenueTimezone
                                        ? 'Venue local time:'
                                        : 'Time (UTC):' }}
                                </strong>

                                {{ $localEventDate->format('H:i') }}
                            </p>

                            <p class="hint">
                                {{ $displayTimezone }}
                            </p>

                            <p>
                                <strong>Venue:</strong>
                                {{ $event->venue?->name ?? 'To be confirmed' }}
                            </p>

                            <p>
                                <strong>City:</strong>
                                {{ $event->venue?->city ?? 'To be confirmed' }}

                                @if ($event->venue?->country_code)
                                    · {{ $event->venue->country_code }}
                                @endif
                            </p>

                            <p class="hint">
                                {{ $event->event_date->isPast()
                                    ? 'Past gig — reviews are open.'
                                    : 'Upcoming gig.' }}
                            </p>
                        </div>

                        <a
                            class="button"
                            href="{{ route('events.show', $event) }}"
                            aria-label="View gig: {{ $event->artist_name }}"
                        >
                            View gig →
                        </a>
                    </article>
                @endforeach
            </div>
        @endif

        @if ($events->hasPages())
            <nav class="pagination" aria-label="Event pages">
                @if ($events->onFirstPage())
                    <span aria-disabled="true">← Previous</span>
                @else
                    <a
                        class="button"
                        href="{{ $events->previousPageUrl() }}"
                        rel="prev"
                    >
                        ← Previous
                    </a>
                @endif

                <span>
                    Page {{ $events->currentPage() }}
                    of {{ $events->lastPage() }}
                </span>

                @if ($events->hasMorePages())
                    <a
                        class="button"
                        href="{{ $events->nextPageUrl() }}"
                        rel="next"
                    >
                        Next →
                    </a>
                @else
                    <span aria-disabled="true">Next →</span>
                @endif
            </nav>
        @endif
    </main>

    <footer>
        GigPulse — Less scrolling. More live music.
    </footer>
</body>
</html>