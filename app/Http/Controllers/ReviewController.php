<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function store(
        Request $request,
        Event $event
    ): JsonResponse|RedirectResponse {
        if ($event->event_date->isFuture()) {
            throw ValidationException::withMessages([
                'comment' => 'You can review this gig after it starts.',
            ]);
        }

        $validated = $request->validate([
            'rating' => [
                'required',
                'integer',
                'between:1,5',
            ],
            'comment' => [
                'required',
                'string',
                'min:5',
                'max:2000',
            ],
        ]);

        $comment = trim($validated['comment']);

        if (mb_strlen($comment) < 5) {
            throw ValidationException::withMessages([
                'comment' => 'Write at least five characters.',
            ]);
        }

        $review = Review::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'event_id' => $event->event_id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $comment,
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your review has been saved.',
                'review' => [
                    'review_id' => $review->review_id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'author' => $request->user()->name,
                    'updated_at' => $review->updated_at->toIso8601String(),
                ],
            ]);
        }

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Your review has been saved.');
    }
}