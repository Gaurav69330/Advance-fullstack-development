<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Gigs | GigPulse</title>

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

        .account-nav form {
            margin: 0;
        }

        main {
            max-width: 1200px;
            margin: auto;
            padding: 45px 24px;
        }

        h1 {
            margin-bottom: 10px;
            font-size: clamp(36px, 6vw, 60px);
            line-height: 1.1;
        }

        .intro {
            color: #686b60;
            margin-bottom: 30px;
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

        .status {
            display: inline-block;
            padding: 4px 12px;
            background: #d8f36a;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .card h2 {
            margin: 18px 0 10px;
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        .card p {
            margin: 6px 0;
            color: #686b60;
        }

        .past-label {
            font-size: 13px;
        }

        button,
        .button {
            display: inline-block;
            min-height: 44px;
            padding: 11px 20px;
            border: 1px solid #20231e;
            border-radius: 5px;
            background: #20231e;
            color: white;
            font: inherit;
            text-decoration: none;
            cursor: pointer;
        }

        button:hover,
        .button:hover {
            background: #344b38;
        }

        .card .button {
            margin-top: 20px;
        }

        .empty {
            padding: 40px 24px;
            border: 1px dashed #a6b18d;
            border-radius: 8px;
            text-align: center;
        }

        .empty p {
            color: #686b60;
        }

        .success-message {
            padding: 14px 18px;
            background: #e8ecdf;
            border: 1px solid #bbc9a4;
            border-radius: 5px;
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
        }

        @media (max-width: 600px) {
            .cards {
                grid-template-columns: 1fr;
            }

            .header-content {
                flex-direction: column;
                align-items: start;
            }

            main {
                padding: 30px 18px;
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
                <a href="{{ route('events.index') }}">Discover</a>

                <a
                    href="{{ route('my-gigs.index') }}"
                    aria-current="page"
                >
                    My gigs
                </a>

                <span>Hello, {{ auth()->user()->name }}</span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit">Sign out</button>
                </form>
            </nav>
        </div>
    </header>

    <main>
        @if (session('success'))
            <p class="success-message" role="status">
                {{ session('success') }}
            </p>
        @endif

        <h1>My gigs.</h1>

        <p class="intro">
            Your upcoming plans and the nights you remember.
        </p>

        <p>
            {{ $attendances->total() }}
            {{ $attendances->total() === 1 ? 'saved gig' : 'saved gigs' }}
        </p>

        @if ($attendances->isEmpty())
            <section class="empty">
                <h2>Your next good night is waiting.</h2>

                <p>
                    Open a gig and save it as Interested or Attending.
                    It will appear here.
                </p>

                <a class="button" href="{{ route('events.index') }}">
                    Discover gigs →
                </a>
            </section>
        @else
            <div class="cards">
                @foreach ($attendances as $attendance)
                    <article class="card">
                        <span class="status">
                            {{ $attendance->status }}
                        </span>

                        <h2>{{ $attendance->event->artist_name }}</h2>

                        <p>
                            <strong>When:</strong>

                            <time datetime="{{ $attendance->event->event_date->toIso8601String() }}">
                                {{ $attendance->event->event_date->format('D, j M Y · H:i') }}
                            </time>
                        </p>

                        <p>
                            <strong>Venue:</strong>
                            {{ $attendance->event->venue?->name ?? 'To be confirmed' }}
                        </p>

                        <p>
                            <strong>City:</strong>
                            {{ $attendance->event->venue?->city ?? 'To be confirmed' }}
                        </p>

                        @if ($attendance->event->event_date->isPast())
                            <p class="past-label">
                                This event has taken place.
                            </p>
                        @else
                            <p class="past-label">
                                An upcoming night out.
                            </p>
                        @endif

                        <a
                            class="button"
                            href="{{ route('events.show', $attendance->event) }}"
                            aria-label="View gig: {{ $attendance->event->artist_name }}"
                        >
                            View gig →
                        </a>
                    </article>
                @endforeach
            </div>
        @endif

        @if ($attendances->hasPages())
            <nav class="pagination" aria-label="Saved gig pages">
                @if ($attendances->onFirstPage())
                    <span aria-disabled="true">← Previous</span>
                @else
                    <a href="{{ $attendances->previousPageUrl() }}" rel="prev">
                        ← Previous
                    </a>
                @endif

                <span>
                    Page {{ $attendances->currentPage() }}
                    of {{ $attendances->lastPage() }}
                </span>

                @if ($attendances->hasMorePages())
                    <a href="{{ $attendances->nextPageUrl() }}" rel="next">
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