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

        .header-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .brand {
            font-size: 28px;
            font-weight: bold;
            text-decoration: none;
        }

        .brand span {
            color: #637c26;
        }

        .account-nav {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 18px;
            font-size: 14px;
        }

        .account-nav a {
            text-decoration: none;
        }

        .account-nav a:hover {
            text-decoration: underline;
        }

        .account-nav form {
            margin: 0;
        }

        main {
            max-width: 1200px;
            margin: auto;
            padding: 45px 24px;
        }

        h1 {
            margin: 0 0 12px;
            font-size: clamp(36px, 6vw, 64px);
            line-height: 1.1;
        }

        h2 {
            line-height: 1.2;
        }

        .intro,
        .hint {
            color: #686b60;
        }

        .intro {
            margin-bottom: 30px;
        }

        .hint {
            font-size: 13px;
        }

        .success-message {
            padding: 14px 18px;
            background: #e8ecdf;
            border: 1px solid #bbc9a4;
            border-radius: 5px;
        }

        .errors {
            padding: 16px 24px;
            background: #fff0ec;
            border: 1px solid #e1b1a7;
            border-radius: 5px;
            color: #8b2727;
        }

        .worldwide-panel {
            padding: 26px;
            margin-bottom: 35px;
            background: #e8ecdf;
            border: 1px solid #c9d2ba;
            border-radius: 8px;
        }

        .worldwide-panel h2 {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .worldwide-fields {
            display: grid;
            grid-template-columns: 1fr 1fr 1.5fr;
            gap: 16px;
            margin-top: 20px;
        }

        .worldwide-panel button {
            margin-top: 18px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: bold;
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
            padding: 10px 12px;
            border: 1px solid #c8cdbd;
            background: white;
            color: #20231e;
        }

        #country_code {
            text-transform: uppercase;
        }

        button,
        .button {
            display: inline-block;
            padding: 11px 20px;
            border: 1px solid #20231e;
            border-radius: 5px;
            background: #20231e;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }

        button:hover,
        .button:hover {
            background: #344b38;
        }

        button:disabled {
            opacity: 0.6;
            cursor: wait;
        }

        .account-nav .logout-button {
            min-height: 44px;
            margin: 0;
            padding: 8px 16px;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            align-items: end;
            gap: 16px;
            padding: 20px;
            margin-bottom: 25px;
            background: #fffef9;
            border: 1px solid #d9dccf;
            border-radius: 8px;
        }

        .search-field {
            flex: 1 1 250px;
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
            display: flex;
            flex-direction: column;
            align-items: start;
            padding: 24px;
            background: #fffef9;
            border: 1px solid #d9dccf;
            border-radius: 8px;
        }

        .badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .genre,
        .source {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .genre {
            background: #d8f36a;
        }

        .source {
            background: #e8ecdf;
        }

        .card h3 {
            margin: 18px 0 10px;
            font-size: 23px;
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        .card p {
            margin: 6px 0;
            color: #686b60;
        }

        .card .description {
            margin: 16px 0 22px;
        }

        .card .button {
            margin-top: auto;
        }

        .empty {
            padding: 35px;
            border: 1px dashed #a6b18d;
            border-radius: 8px;
            text-align: center;
        }

        .pagination {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 30px;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            padding: 10px 16px;
            border: 1px solid #d9dccf;
            border-radius: 5px;
            text-decoration: none;
        }

        .pagination a {
            background: #fffef9;
        }

        .pagination a:hover {
            background: #e8ecdf;
        }

        footer {
            padding: 24px;
            border-top: 1px solid #d9dccf;
            color: #686b60;
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

            .worldwide-fields {
                grid-template-columns: 1fr 1fr;
            }

            .keyword-field {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 600px) {
            main {
                padding: 30px 18px;
            }

            .header-content {
                flex-direction: column;
                align-items: start;
            }

            .account-nav {
                width: 100%;
                gap: 14px;
            }

            .cards,
            .worldwide-fields {
                grid-template-columns: 1fr;
            }

            .keyword-field {
                grid-column: auto;
            }

            .filters > * {
                width: 100%;
            }

            .worldwide-panel {
                padding: 20px;
            }

            .worldwide-panel button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="header-content">
            <a class="brand" href="{{ route('events.index') }}">
                Gig<span>Pulse</span>
            </a>

            <nav class="account-nav" aria-label="Main navigation">
                <a
                    href="{{ route('events.index') }}"
                    aria-current="page"
                >
                    Discover
                </a>

                @auth
                    <a href="{{ route('my-gigs.index') }}">My gigs</a>

                    <span>Hello, {{ auth()->user()->name }}</span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button class="logout-button" type="submit">
                            Sign out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Sign in</a>
                    <a href="{{ route('register') }}">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        @if (session('success'))
            <p class="success-message" role="status">
                {{ session('success') }}
            </p>
        @endif

        @if (session('error'))
            <p class="errors" role="alert">
                {{ session('error') }}
            </p>
        @endif

        @if ($errors->any())
            <div class="errors" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h1>Find your next good night.</h1>

        <p class="intro">
            Explore live music across the world.
            Find your favourite artists, discover new venues,
            and save the gigs you want to attend.
        </p>

        <section
            class="worldwide-panel"
            aria-labelledby="worldwide-heading"
        >
            <h2 id="worldwide-heading">Discover music worldwide</h2>

            <p>
                Import live listings from Ticketmaster.
                Leave country and city empty to search across
                its available countries.
            </p>

            @auth
                <form
                    id="discovery-form"
                    method="POST"
                    action="{{ route('discovery.store') }}"
                >
                    @csrf

                    <div class="worldwide-fields">
                        <div>
                            <label for="country_code">
                                Country code (optional)
                            </label>

                            <input
                                id="country_code"
                                name="country_code"
                                type="text"
                                value="{{ old('country_code') }}"
                                minlength="2"
                                maxlength="2"
                                pattern="[A-Za-z]{2}"
                                placeholder="GB, US, AU..."
                                aria-describedby="country-hint"
                            >
                        </div>

                        <div>
                            <label for="discovery_city">
                                City (optional)
                            </label>

                            <input
                                id="discovery_city"
                                name="city"
                                type="text"
                                value="{{ old('city') }}"
                                maxlength="100"
                                placeholder="London or New York"
                            >
                        </div>

                        <div class="keyword-field">
                            <label for="keyword">
                                Artist or event (optional)
                            </label>

                            <input
                                id="keyword"
                                name="keyword"
                                type="search"
                                value="{{ old('keyword') }}"
                                maxlength="100"
                                placeholder="Search an artist or event"
                            >
                        </div>
                    </div>

                    <p class="hint" id="country-hint">
                        Use a two-letter country code.
                        Coverage varies by country. Each search imports
                        up to 20 listings with confirmed dates and coordinates.
                    </p>

                    <button id="discovery-button" type="submit">
                        Discover live events →
                    </button>

                    <p
                        id="discovery-status"
                        class="hint"
                        role="status"
                        aria-live="polite"
                    ></p>
                </form>
            @else
                <p>
                    <a href="{{ route('login') }}">Sign in</a>
                    or
                    <a href="{{ route('register') }}">create an account</a>
                    to discover live events.
                </p>
            @endauth
        </section>

        <section aria-labelledby="saved-listings-heading">
            <h2 id="saved-listings-heading">Explore available gigs</h2>

            <p class="hint">
                These filters search listings already saved in GigPulse.
                Use the worldwide form above to import more.
            </p>

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
                        name="search"
                        type="search"
                        value="{{ $search }}"
                        maxlength="100"
                        placeholder="Search saved listings..."
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

                <a
                    class="clear-link"
                    href="{{ route('events.index') }}"
                >
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
                    <h3>No gigs found.</h3>

                    <p>
                        Try another search, clear your filters,
                        or import live listings above.
                    </p>
                </div>
            @else
                <div class="cards">
                    @foreach ($events as $event)
                        <article class="card">
                            <div class="badges">
                                <span class="genre">
                                    {{ $event->genre ?: 'Live music' }}
                                </span>

                                @if ($event->is_demo)
                                    <span class="source">Sample gig</span>
                                @elseif ($event->ticketmaster_id)
                                    <span class="source">Ticketmaster</span>
                                @endif
                            </div>

                            <h3>{{ $event->artist_name }}</h3>

                            <p>
                                <strong>When:</strong>

                                <time datetime="{{ $event->event_date->toIso8601String() }}">
                                    {{ $event->event_date->format('D, j M Y · H:i') }}
                                </time>

                                <span>
                                    ({{ config('app.timezone', 'UTC') }})
                                </span>
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
                                <strong>Country:</strong>
                                {{ $event->venue?->country_code ?? 'To be confirmed' }}
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

            @if ($events->hasPages())
                <nav class="pagination" aria-label="Event pages">
                    @if ($events->onFirstPage())
                        <span aria-disabled="true">← Previous</span>
                    @else
                        <a
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
                        <a href="{{ $events->nextPageUrl() }}" rel="next">
                            Next →
                        </a>
                    @else
                        <span aria-disabled="true">Next →</span>
                    @endif
                </nav>
            @endif
        </section>
    </main>

    <footer>
        GigPulse — Less scrolling. More live music.
    </footer>

    <script>
        const discoveryForm = document.getElementById('discovery-form');

        if (discoveryForm) {
            discoveryForm.addEventListener('submit', () => {
                const button = document.getElementById('discovery-button');
                const status = document.getElementById('discovery-status');

                button.disabled = true;
                button.textContent = 'Finding live events…';

                status.textContent =
                    'Contacting Ticketmaster. Please wait.';
            });
        }
    </script>
</body>
</html>