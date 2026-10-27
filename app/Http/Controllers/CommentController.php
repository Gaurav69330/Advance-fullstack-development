<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    public function index(
        Request $request,
        Event $event
    ): JsonResponse {
        $comments = $event->comments()
            ->with('user')
            ->orderByDesc('comment_id')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        return response()
            ->json([
                'comments' => $comments->map(
                    function (Comment $comment) use ($request): array {
                        return $this->commentData(
                            $comment,
                            $request
                        );
                    }
                )->all(),
            ])
            ->header('Cache-Control', 'private, no-store');
    }

    public function store(
        Request $request,
        Event $event
    ): JsonResponse|RedirectResponse {
        $body = $this->validatedBody($request);

        $comment = $event->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $body,
        ]);

        $comment->load('user');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your comment has been posted.',
                'comment' => $this->commentData(
                    $comment,
                    $request
                ),
            ], 201);
        }

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Your comment has been posted.');
    }

    public function update(
        Request $request,
        Comment $comment
    ): JsonResponse|RedirectResponse {
        $this->authorizeOwner($request, $comment);

        $comment->body = $this->validatedBody($request);
        $comment->save();
        $comment->load('user');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your comment has been updated.',
                'comment' => $this->commentData(
                    $comment,
                    $request
                ),
            ]);
        }

        return redirect()
            ->route('events.show', $comment->event_id)
            ->with('success', 'Your comment has been updated.');
    }

    public function destroy(
        Request $request,
        Comment $comment
    ): JsonResponse|RedirectResponse {
        $this->authorizeOwner($request, $comment);

        $eventId = $comment->event_id;

        $comment->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your comment has been deleted.',
            ]);
        }

        return redirect()
            ->route('events.show', $eventId)
            ->with('success', 'Your comment has been deleted.');
    }

    private function validatedBody(Request $request): string
    {
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

        return $body;
    }

    private function authorizeOwner(
        Request $request,
        Comment $comment
    ): void {
        abort_unless(
            $request->user()
            && (int) $comment->user_id
                === (int) $request->user()->id,
            403,
            'You can only change your own comments.'
        );
    }

    private function commentData(
        Comment $comment,
        Request $request
    ): array {
        return [
            'comment_id' => $comment->comment_id,
            'author' => $comment->user?->name
                ?? 'GigPulse member',
            'body' => $comment->body,
            'created_at' => $comment->created_at
                ->toIso8601String(),
            'updated_at' => $comment->updated_at
                ->toIso8601String(),
            'can_manage' => $request->user()
                && (int) $comment->user_id
                    === (int) $request->user()->id,
        ];
    }
}