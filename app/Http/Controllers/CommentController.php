<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    public function index(Event $event): JsonResponse
    {
        $comments = $event->comments()
            ->with('user')
            ->orderByDesc('comment_id')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        return response()
            ->json([
                'comments' => $comments->map(function ($comment) {
                    return [
                        'comment_id' => $comment->comment_id,
                        'author' => $comment->user?->name
                            ?? 'GigPulse member',
                        'body' => $comment->body,
                        'created_at' => $comment->created_at
                            ->toIso8601String(),
                    ];
                })->all(),
            ])
            ->header('Cache-Control', 'no-store');
    }

    public function store(
        Request $request,
        Event $event
    ): JsonResponse|RedirectResponse {
        $validated = $request->validate([
            'body' => [
                'required',
                'string',
                'min:2',
                'max:1000',
            ],
        ]);

        $body = trim($validated['body']);

        if (mb_strlen($body) < 2) {
            throw ValidationException::withMessages([
                'body' => 'Write at least two characters.',
            ]);
        }

        $comment = $event->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $body,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your comment has been posted.',
                'comment' => [
                    'comment_id' => $comment->comment_id,
                    'author' => $request->user()->name,
                    'body' => $comment->body,
                    'created_at' => $comment->created_at
                        ->toIso8601String(),
                ],
            ], 201);
        }

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Your comment has been posted.');
    }
}