@php
    $eventReviews = $event->reviews()
        ->with('user')
        ->latest('updated_at')
        ->limit(20)
        ->get();

    $myReview = auth()->check()
        ? $event->reviews()
            ->where('user_id', auth()->id())
            ->first()
        : null;

    $reviewsOpen = !$event->event_date->isFuture();
@endphp

<section class="panel" aria-labelledby="reviews-heading">
    <h2 id="reviews-heading">Worth the noise?</h2>

    <p>Share what you thought of the gig.</p>

    <div id="review-list">
        @forelse ($eventReviews as $review)
            <article
                id="review-{{ $review->review_id }}"
                class="review-entry"
            >
                <strong class="review-author">
                    {{ $review->user?->name ?? 'GigPulse member' }}
                </strong>

                <p class="review-rating">
                    {{ $review->rating }} / 5 stars
                </p>

                <p class="review-comment">{{ $review->comment }}</p>

                <time
                    class="hint"
                    datetime="{{ $review->updated_at->toIso8601String() }}"
                >
                    Updated {{ $review->updated_at->format('j M Y') }}
                </time>
            </article>
        @empty
            <p id="no-reviews" class="hint">
                No reviews yet. Be the first to share your experience.
            </p>
        @endforelse
    </div>

    <p class="hint">Showing up to 20 recent reviews.</p>

    @if (!$reviewsOpen)
        <p class="notice">
            Reviews open after the event starts.
        </p>
    @else
        @auth
            <form
                id="review-form"
                method="POST"
                action="{{ route('reviews.store', $event) }}"
            >
                @csrf

                <h3>Your review</h3>

                <label for="review-rating">Rating</label>

                <select id="review-rating" name="rating" required>
                    @for ($rating = 5; $rating >= 1; $rating--)
                        <option
                            value="{{ $rating }}"
                            @selected(
                                (int) old('rating', $myReview?->rating ?? 5)
                                === $rating
                            )
                        >
                            {{ $rating }} {{ $rating === 1 ? 'star' : 'stars' }}
                        </option>
                    @endfor
                </select>

                <label
                    for="review-comment"
                    style="margin-top: 18px;"
                >
                    Tell us about the gig
                </label>

                <textarea
                    id="review-comment"
                    name="comment"
                    rows="5"
                    minlength="5"
                    maxlength="2000"
                    required
                    aria-describedby="review-hint"
                >{{ old('comment', $myReview?->comment ?? '') }}</textarea>

                <p class="hint" id="review-hint">
                    Write between 5 and 2,000 characters.
                    Saving again updates your review.
                </p>

                <button id="save-review" type="submit">
                    Save review
                </button>

                <p
                    id="review-feedback"
                    role="status"
                    aria-live="polite"
                    hidden
                ></p>
            </form>
        @else
            <p>
                <a href="{{ route('login') }}">Sign in</a>
                to leave a review.
            </p>
        @endauth
    @endif
</section>

<style>
    .review-entry {
        margin: 16px 0;
        padding: 18px;
        border: 1px solid #c8cdbd;
        border-radius: 6px;
        background: #fffef9;
    }

    .review-rating {
        margin: 6px 0;
        font-weight: bold;
    }

    .review-comment {
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    #review-comment {
        display: block;
        width: 100%;
        padding: 12px;
        border: 1px solid #bbc5a8;
        border-radius: 5px;
        background: white;
        color: #20231e;
        font: inherit;
        resize: vertical;
    }
</style>

<script>
    (() => {
        const form = document.getElementById('review-form');

        if (!form) {
            return;
        }

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const button = document.getElementById('save-review');
            const feedback = document.getElementById('review-feedback');

            button.disabled = true;
            button.textContent = 'Saving…';
            feedback.hidden = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const contentType =
                    response.headers.get('content-type') || '';

                if (!contentType.includes('application/json')) {
                    throw new Error(
                        'Please refresh the page and sign in again.'
                    );
                }

                const data = await response.json();

                if (!response.ok) {
                    const message = data.errors
                        ? Object.values(data.errors).flat().join(' ')
                        : data.message || 'Could not save your review.';

                    throw new Error(message);
                }

                const review = data.review;
                const list = document.getElementById('review-list');

                document.getElementById('no-reviews')?.remove();

                let article = document.getElementById(
                    'review-' + review.review_id
                );

                if (!article) {
                    article = document.createElement('article');
                    article.id = 'review-' + review.review_id;
                    article.className = 'review-entry';
                }

                const author = document.createElement('strong');
                author.className = 'review-author';
                author.textContent = review.author;

                const rating = document.createElement('p');
                rating.className = 'review-rating';
                rating.textContent = review.rating + ' / 5 stars';

                const comment = document.createElement('p');
                comment.className = 'review-comment';
                comment.textContent = review.comment;

                const updated = document.createElement('time');
                updated.className = 'hint';
                updated.dateTime = review.updated_at;
                updated.textContent =
                    'Updated ' + new Date(review.updated_at)
                        .toLocaleDateString();

                article.replaceChildren(
                    author,
                    rating,
                    comment,
                    updated
                );

                list.prepend(article);

                const entries = list.querySelectorAll('.review-entry');

                entries.forEach((entry, index) => {
                    if (index >= 20) {
                        entry.remove();
                    }
                });

                feedback.textContent = data.message;
                feedback.className = 'feedback';
                feedback.hidden = false;
            } catch (error) {
                feedback.textContent = error.message;
                feedback.className = 'feedback error';
                feedback.hidden = false;
            } finally {
                button.disabled = false;
                button.textContent = 'Save review';
            }
        });
    })();
</script>