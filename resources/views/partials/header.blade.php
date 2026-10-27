<header class="gp-header" id="gp-header">
    <a class="gp-brand" href="{{ route('events.index') }}">
        Gig<span>Pulse</span>
    </a>

    <button
        class="gp-menu-button"
        id="gp-menu-button"
        type="button"
        aria-expanded="false"
        aria-controls="gp-navigation"
    >
        Menu ☰
    </button>

    <nav
        class="gp-navigation"
        id="gp-navigation"
        aria-label="Main navigation"
    >
        <a
            href="{{ route('events.index') }}"
            @if (request()->routeIs('events.index', 'live-events.index'))
                aria-current="page"
            @endif
        >
            Discover
        </a>

        <a
            href="{{ route('saved-events.index') }}"
            @if (request()->routeIs('saved-events.index'))
                aria-current="page"
            @endif
        >
            Saved listings
        </a>

        @auth
            <a
                href="{{ route('my-gigs.index') }}"
                @if (request()->routeIs('my-gigs.index'))
                    aria-current="page"
                @endif
            >
                My gigs
            </a>

            <span class="gp-user">
                Hello, {{ auth()->user()->name }}
            </span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button class="gp-sign-out" type="submit">
                    Sign out
                </button>
            </form>
        @else
            <a
                href="{{ route('login') }}"
                @if (request()->routeIs('login'))
                    aria-current="page"
                @endif
            >
                Sign in
            </a>

            <a
                href="{{ route('register') }}"
                @if (request()->routeIs('register'))
                    aria-current="page"
                @endif
            >
                Register
            </a>
        @endauth
    </nav>
</header>

<style>
    .gp-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 20px 6%;
        border-bottom: 1px solid #d9dccf;
        background: #f6f4ec;
    }

    .gp-brand {
        color: #20231e;
        font-size: 28px;
        font-weight: bold;
        text-decoration: none;
    }

    .gp-brand span {
        color: #637c26;
    }

    .gp-navigation {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 18px;
        font-size: 14px;
    }

    .gp-navigation a {
        display: inline-flex;
        align-items: center;
        min-height: 44px;
        color: #20231e;
        text-decoration: none;
    }

    .gp-navigation a:hover {
        text-decoration: underline;
    }

    .gp-navigation a[aria-current="page"] {
        font-weight: bold;
        text-decoration: underline;
        text-underline-offset: 5px;
    }

    .gp-navigation form {
        margin: 0;
    }

    .gp-user {
        overflow-wrap: anywhere;
    }

    .gp-menu-button,
    .gp-sign-out {
        width: auto;
        min-height: 44px;
        margin: 0;
        padding: 10px 16px;
        border: 1px solid #20231e;
        border-radius: 5px;
        background: #20231e;
        color: white;
        font: inherit;
        cursor: pointer;
    }

    .gp-menu-button {
        display: none;
    }

    .gp-header :focus-visible {
        outline: 3px solid #637c26;
        outline-offset: 4px;
    }

    @media (max-width: 850px) {
        .gp-header.is-enhanced .gp-menu-button {
            display: inline-block;
        }

        .gp-navigation {
            width: 100%;
            align-items: stretch;
            flex-direction: column;
            gap: 8px;
        }

        .gp-header.is-enhanced .gp-navigation:not(.is-open) {
            display: none;
        }

        .gp-navigation a {
            padding: 8px 0;
        }

        .gp-user {
            padding: 8px 0;
        }

        .gp-sign-out {
            width: 100%;
        }
    }
</style>

<script>
    (() => {
        const header = document.getElementById('gp-header');
        const button = document.getElementById('gp-menu-button');
        const navigation = document.getElementById('gp-navigation');

        if (!header || !button || !navigation) {
            return;
        }

        header.classList.add('is-enhanced');

        function closeMenu() {
            navigation.classList.remove('is-open');
            button.setAttribute('aria-expanded', 'false');
        }

        button.addEventListener('click', () => {
            const isOpen = navigation.classList.toggle('is-open');

            button.setAttribute('aria-expanded', String(isOpen));
        });

        header.addEventListener('keydown', (event) => {
            if (
                event.key === 'Escape'
                && navigation.classList.contains('is-open')
            ) {
                closeMenu();
                button.focus();
            }
        });

        document.addEventListener('click', (event) => {
            if (!header.contains(event.target)) {
                closeMenu();
            }
        });
    })();
</script>