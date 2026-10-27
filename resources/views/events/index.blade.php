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

        header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 20px 6%;
            border-bottom: 1px solid #d9dccf;
        }

        .brand {
            font-size: 28px;
            font-weight: bold;
            text-decoration: none;
        }

        .brand span {
            color: #637c26;
        }

        nav {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 18px;
        }

        nav form {
            margin: 0;
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
            font: inherit;
        }

        button,
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

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .card {
            padding: 24px;
            border: 1px solid #d9dccf;
            border-radius: 8px;
            background: #fffef9;
        }

        .card h2 {
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            background: #d8f36a;
            font-size: 12px;
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
        }
    </style>
</head>

<body>
    <header>
        <a class="brand" href="{{ route('events.index') }}">
            Gig<span>Pulse</span>
        </a>

        <nav aria-label="Main navigation">
            <a href="{{ route('events.index') }}">Discover</a>

            <a
                href="{{ route('saved-events.index') }}"
                aria-current="page"
            >
                Saved listings
            </a>

            @auth
                <a href="{{ route('my-gigs.index') }}">My gigs</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Sign out</button>
                </form>
            @else
                <a href="{{ route('login') }}">Sign in</a>
            @endauth
        </nav>
    </header>

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

            <button type="submit">Apply filters</button>

            <a href="{{ route('saved-events.index') }}">
                Clear filters
            </a>
        </form>

        <p>{{ $events->total() }} matching saved gigs</p>

        @if ($events->isEmpty())
            <section class="empty">
                <h2>No matching gigs are stored yet.</h2>

                <p>
                    Try All gigs or clear your search and genre filters.
                    Past events must exist in the database to appear here.
                </p>
            </section>
        @else
            <div class="cards">
                @foreach ($events as $event)
                    <article class="card">
                        <span class="badge">
                            {{ $event->genre ?: 'Live music' }}
                        </span>

                        @if ($event->is_demo)
                            <p><small>Fictional sample gig</small></p>
                        @endif

                        <h2>{{ $event->artist_name }}</h2>

                        <p>
                            <strong>Date:</strong>
                            {{ $event->event_date->format('D, j M Y · H:i') }}
                            ({{ config('app.timezone', 'UTC') }})
                        </p>

                        <p>
                            <strong>Venue:</strong>
                            {{ $event->venue?->name ?? 'To be confirmed' }}
                        </p>

                        <p>
                            <strong>City:</strong>
                            {{ $event->venue?->city ?? 'To be confirmed' }}
                        </p>

                        <p>
                            {{ $event->event_date->isPast()
                                ? 'Past gig — reviews are open.'
                                : 'Upcoming gig.' }}
                        </p>

                        <a
                            class="button"
                            href="{{ route('events.show', $event) }}"
                        >
                            View gig →
                        </a>
                    </article>
                @endforeach
            </div>
        @endif

        @if ($events->hasPages())
            <nav class="pagination" aria-label="Event pages">
                @if (!$events->onFirstPage())
                    <a class="button" href="{{ $events->previousPageUrl() }}">
                        ← Previous
                    </a>
                @endif

                <span>
                    Page {{ $events->currentPage() }}
                    of {{ $events->lastPage() }}
                </span>

                @if ($events->hasMorePages())
                    <a class="button" href="{{ $events->nextPageUrl() }}">
                        Next →
                    </a>
                @endif
            </nav>
        @endif
    </main>

    <footer>
        GigPulse — Less scrolling. More live music.
    </footer>
</body>
</html>