<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Discover Gigs | GigPulse</title>

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

        main {
            max-width: 1200px;
            margin: auto;
            padding: 45px 24px;
        }

        h1 {
            margin-bottom: 10px;
            font-size: clamp(36px, 6vw, 64px);
            line-height: 1.1;
        }

        .intro {
            color: #686b60;
            margin-bottom: 30px;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            align-items: end;
            gap: 16px;
            padding: 20px;
            background: #fffef9;
            border: 1px solid #d9dccf;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: bold;
        }

        .search-field {
            flex: 1 1 250px;
        }

        input,
        select,
        button {
            font: inherit;
            min-height: 46px;
            border-radius: 5px;
        }

        input,
        select {
            display: block;
            width: 100%;
            margin-top: 6px;
            padding: 10px;
            border: 1px solid #c8cdbd;
            background: white;
        }

        button,
        .button {
            display: inline-block;
            padding: 11px 20px;
            border: 1px solid #20231e;
            background: #20231e;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }

        .clear-link {
            padding: 11px 0;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .card {
            padding: 24px;
            background: #fffef9;
            border: 1px solid #d9dccf;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: start;
        }

        .genre {
            display: inline-block;
            padding: 4px 10px;
            background: #d8f36a;
            border-radius: 20px;
            font-size: 12px;
        }

        .card h2 {
            margin: 18px 0 10px;
            line-height: 1.2;
        }

        .card p {
            color: #686b60;
            margin: 6px 0;
        }

        .card .description {
            margin: 16px 0 22px;
        }

        .card .button {
            margin-top: auto;
            border-radius: 5px;
        }

        .empty {
            padding: 35px;
            border: 1px dashed #a6b18d;
            border-radius: 8px;
            text-align: center;
        }

        .errors {
            color: #8b2727;
            background: #fff0ec;
            padding: 16px 32px;
            border-radius: 5px;
        }

        .pagination {
            margin-top: 30px;
        }

        .pagination nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 16px;
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
    <header>
        <a class="brand" href="{{ route('events.index') }}">
            Gig<span>Pulse</span>
        </a>
    </header>

    <main>
        <h1>Find your next good night.</h1>

        <p class="intro">
            Discover upcoming live music, explore local venues,
            and find your next favourite artist.
        </p>

        @if ($errors->any())
            <ul class="errors" role="alert">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form
            class="filters"
            method="GET"
            action="{{ route('events.index') }}"
            role="search"
        >
            <div class="search-field">
                <label for="search">Artist, venue, or city</label>

                <input
                    id="search"
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    maxlength="100"
                    placeholder="Search for a gig..."
                >
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

            <button type="submit">Find gigs</button>

            <a class="clear-link" href="{{ route('events.index') }}">
                Clear filters
            </a>
        </form>

        <p>
            {{ $events->total() }}
            {{ $events->total() === 1 ? 'upcoming gig' : 'upcoming gigs' }}
            found
        </p>

        @if ($events->isEmpty())
            <div class="empty">
                <h2>No gigs found.</h2>
                <p>Try another search or clear your filters.</p>
            </div>
        @else
            <div class="cards">
                @foreach ($events as $event)
                    <article class="card">
                        <span class="genre">
                            {{ $event->genre ?: 'Live music' }}
                        </span>

                        <h2>{{ $event->artist_name }}</h2>

                        <p>
                            <strong>When:</strong>

                            <time datetime="{{ $event->event_date->toIso8601String() }}">
                                {{ $event->event_date->format('D, j M Y · H:i') }}
                            </time>
                        </p>

                        <p>
                            <strong>Venue:</strong>
                            {{ $event->venue?->name ?? 'Venue to be confirmed' }}
                        </p>

                        <p>
                            <strong>City:</strong>
                            {{ $event->venue?->city ?? 'To be confirmed' }}
                        </p>

                        <p class="description">
                            {{ \Illuminate\Support\Str::limit($event->description ?? '', 140) }}
                        </p>

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

        <div class="pagination">
            {{ $events->links('pagination::simple-tailwind') }}
        </div>
    </main>

    <footer>
        GigPulse — Less scrolling. More live music.
    </footer>
</body>
</html>