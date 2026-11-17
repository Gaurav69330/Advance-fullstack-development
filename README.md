# GigPulse

I built GigPulse for my full-stack development coursework. I wanted a place where I could look for live music, check the details of a gig, and save it to a personal list.

## What I built

- **Discover** searches Ticketmaster by artist, city, or country. I can click a result to see the event details before signing in.
- **My gigs** keeps the events I save and lets me track whether I plan to go.
- **Near me** uses my location to search within 10 miles. **Use last search** brings back my previous filters.
- On a saved gig I can post comments, leave a review, and take or upload a photo. The photo is private to my account.
- I used JavaScript Fetch for search suggestions, attendance, comments, and reviews so those parts of the page can update without a full reload. I used CSS media queries to make the layout fit a phone screen.


## How GigPulse works

### Finding and saving a gig

I built Discover around the Ticketmaster Discovery API. You can start with an artist, city, or country, or browse the results page by page. Clicking a title opens a preview with the date, venue, listing status, and artwork when Ticketmaster provides it. The ticket link takes you to Ticketmaster.

Visitors can browse public previews before signing in. When a signed-in user chooses **Save to GigPulse**, Laravel imports the selected listing and venue and adds it to **My gigs**. That gives users a place to track their plans and find the event again. The public preview and saved event page have separate URLs.

### What I can do on a saved event

I can set my attendance to **Interested**, **Attending**, or **Went**. The page shows how many people are going. Event comments let people discuss the gig, and reviews let signed-in users share a rating and write about it once the event has started. Users can edit or delete their own comments and reviews.

The photo section lets me upload an existing picture or capture one with the camera. It accepts JPEG, PNG, or WebP files up to 5 MB. Saving another photo replaces the previous one. GigPulse checks that the signed-in user owns the photo before serving it.

### What works on mobile

I used CSS media queries so the layout adapts automatically and the navigation can collapse into a menu. **Near me** asks the browser for permission to use the user's location. **Use last search** restores filters from localStorage in the same browser. The camera feature asks for permission before opening a live preview. A trusted HTTPS address is needed to test live camera access and geolocation from a phone over the network.

### How the code fits together

I used Laravel's MVC structure. Routes send requests to controllers, Eloquent models handle database records and relationships, and Blade templates render the pages. MySQL or MariaDB stores users, venues, events, attendance, comments, and reviews. Laravel migrations define the tables and relationships.

I put the Ticketmaster requests in a separate PHP service. The service caches API responses, and Laravel reads the API key from the local environment. Search autocomplete uses upcoming events already saved in GigPulse. Submitting the Discover form searches Ticketmaster itself.

I used JavaScript Fetch for autocomplete, attendance, comments, and reviews. Comments also check for new messages in the background, so users can see updates without reloading the event page.


## Run locally

You need PHP 8.4.1 or later with PDO MySQL enabled, Composer, and MySQL or MariaDB. On Windows you can start the database from the XAMPP Control Panel.

1. Open a terminal in the cloned or extracted GigPulse folder and run:

   ```powershell
   composer install
   Copy-Item .env.example .env
   php artisan key:generate
   ```

   If you already have a configured `.env`, keep it and skip the copy and key generation steps.

2. Create an empty database named `gigpulse_laravel`. Set the following values in `.env`, using the credentials for your local database and your own Ticketmaster Discovery API key.

   ```dotenv
   APP_NAME=GigPulse
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=gigpulse_laravel
   DB_USERNAME=root
   DB_PASSWORD=

   TICKETMASTER_API_KEY=your_consumer_key
   ```

   Keep `.env` out of Git. The Ticketmaster key is read by Laravel on the server.

3. Run the migrations and start Laravel.

   ```powershell
   php artisan migrate
   php artisan serve
   ```

4. Open [GigPulse](http://127.0.0.1:8000/). Register an account to save events and use **My gigs**.

To add sample events to a development database, run `php artisan db:seed --class=GigPulseSeeder`.

## What I show in my demo

1. Search on Discover and click an event title to open its public preview. Sign in and save an event, then reopen it from **My gigs**.
2. On a saved event, change attendance or post a comment. In Firefox DevTools, use **Network → XHR** to see the Fetch requests. Open the event in a second tab to demonstrate automatic comment refresh.
3. Show the mobile menu, restore a previous search, run **Near me**, and capture and save a photo. Use localhost on the PC or a trusted HTTPS address on a phone for geolocation and live camera access.

## Where to find things in the project

```text
GigPulse/
├── app/
│   ├── Http/Controllers/    # Web requests and responses
│   ├── Models/              # Eloquent records and relationships
│   ├── Providers/           # Application setup and rate limits
│   └── Services/            # Ticketmaster integration
├── bootstrap/               # Laravel startup and middleware configuration
├── config/                  # Application, database, storage, and service settings
├── database/
│   ├── migrations/          # Tables, columns, constraints, and indexes
│   └── seeders/             # Sample data for local development
├── public/
│   ├── index.php            # Web entry point
│   └── js/                  # Discover page browser scripts
├── resources/views/         # Blade templates
├── routes/                  # Web routes and console commands
├── storage/                 # Runtime files, logs, and private uploads
└── tests/                   # Laravel feature and unit test suites
```

### What handles each request

| File | What it does |
| --- | --- |
| `routes/web.php` | Maps page URLs and form actions to controllers and applies authentication and rate limits. |
| `routes/console.php` | Registers custom Artisan console commands. |
| `app/Http/Controllers/Controller.php` | Base controller for the application. |
| `app/Http/Controllers/LiveEventController.php` | Handles Discover search, pagination, and public Ticketmaster event previews. |
| `app/Http/Controllers/SaveLiveEventController.php` | Imports a selected Ticketmaster event and venue, saves available artwork, and adds the event to My gigs. |
| `app/Http/Controllers/DiscoveryController.php` | Handles bulk import of Ticketmaster listings. |
| `app/Http/Controllers/EventController.php` | Displays saved listings and individual saved event pages. |
| `app/Http/Controllers/AuthController.php` | Handles registration, sign-in, and sign-out. |
| `app/Http/Controllers/MyGigController.php` | Displays the signed-in user's gig diary. |
| `app/Http/Controllers/AttendanceController.php` | Saves attendance status and returns updated attendance information. |
| `app/Http/Controllers/SearchSuggestionController.php` | Returns autocomplete matches from upcoming saved events. |
| `app/Http/Controllers/CommentController.php` | Lists event comments and handles posting, editing, and deleting them. |
| `app/Http/Controllers/ReviewController.php` | Creates, updates, and deletes event reviews. |
| `app/Http/Controllers/TicketPhotoController.php` | Validates, stores, and serves private event photos after checking ownership. |
| `app/Services/TicketmasterService.php` | Sends server-side search and event-detail requests to Ticketmaster and caches responses. |
| `app/Providers/AppServiceProvider.php` | Configures application services, including rate limits for comment actions. |

### Where the data is stored

| File | What it does |
| --- | --- |
| `app/Models/User.php` | Represents a registered user. |
| `app/Models/Event.php` | Represents a gig and links it to its venue, attendance records, comments, and reviews. |
| `app/Models/Venue.php` | Stores venue information, including coordinates, country, and timezone. |
| `app/Models/Attendance.php` | Connects a user to an event and stores their attendance status and private photo path. |
| `app/Models/Comment.php` | Represents a comment posted by a user on an event. |
| `app/Models/Review.php` | Represents a user's event rating and review text. |
| `database/migrations/` | Builds the database schema and adds later changes such as Ticketmaster IDs, comments, indexes, and artwork URLs. |
| `database/seeders/GigPulseSeeder.php` | Adds sample gig data for local testing. |

### What displays on each page

| File | What it does |
| --- | --- |
| `resources/views/live-events/index.blade.php` | Renders Discover, search filters, and Ticketmaster results. |
| `resources/views/live-events/show.blade.php` | Renders an event preview visitors can open before saving. |
| `resources/views/events/index.blade.php` | Lists events saved in the GigPulse database. |
| `resources/views/events/show.blade.php` | Displays a saved event, its artwork, attendance controls, and the photo, comments, and review sections. |
| `resources/views/events/camera.blade.php` | Provides camera preview and capture controls for an event photo. |
| `resources/views/events/comments.blade.php` | Renders comments and uses Fetch to submit changes and poll for updates. |
| `resources/views/events/reviews.blade.php` | Renders the review list and Fetch-powered review controls. |
| `resources/views/my-gigs/index.blade.php` | Shows the signed-in user's saved events. |
| `resources/views/auth/login.blade.php` and `register.blade.php` | Render the sign-in and registration forms. |
| `resources/views/partials/header.blade.php` | Provides shared navigation and the responsive menu. |
| `public/js/near-me.js` | Requests browser geolocation and submits a nearby search. |
| `public/js/search-preferences.js` | Saves and restores Discover filters using localStorage. |
| `public/js/search-suggestions.js` | Fetches and displays search suggestions as the user types. |

I used GitHub Issues and the **v1.0 — Coursework demonstration** milestone to track what I needed to check for the demo.
