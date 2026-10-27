<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketPhotoController extends Controller
{
    public function store(
        Request $request,
        Event $event
    ): RedirectResponse {
        $attendance = Attendance::query()
            ->where('user_id', $request->user()->id)
            ->where('event_id', $event->event_id)
            ->first();

        if (!$attendance) {
            return back()->with(
                'error',
                'Save this gig to My gigs before adding a photo.'
            );
        }

        $request->validate([
            'photo' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'dimensions:max_width=12000,max_height=12000',
            ],
        ]);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        $path = $request->file('photo')->store(
            'ticket-photos/' . $request->user()->id,
            'local'
        );

        if (!$path) {
            return back()->with(
                'error',
                'The photo could not be saved. Please try again.'
            );
        }

        $previousPath = $attendance->ticket_photo_path;

        try {
            $attendance->ticket_photo_path = $path;
            $attendance->save();
        } catch (\Throwable $exception) {
            $disk->delete($path);

            throw $exception;
        }

        if ($previousPath && $previousPath !== $path) {
            $disk->delete($previousPath);
        }

        return redirect()
            ->route('events.show', $event)
            ->with(
                'success',
                'Your photo has been saved.'
            );
    }

    public function show(
        Request $request,
        Attendance $attendance
    ): StreamedResponse {
        // Only the owner can view this photo.
        abort_unless(
            (int) $attendance->user_id
                === (int) $request->user()->id,
            404
        );

        $path = $attendance->ticket_photo_path;

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        abort_unless(
            $path && $disk->exists($path),
            404
        );

        return $disk->response(
            $path,
            null,
            [
                'Cache-Control' => 'private, no-store',
                'X-Content-Type-Options' => 'nosniff',
            ],
            'inline'
        );
    }
}