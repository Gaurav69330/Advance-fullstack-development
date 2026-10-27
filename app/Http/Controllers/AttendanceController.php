<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function store(
        Request $request,
        Event $event
    ): JsonResponse|RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['Interested', 'Attending', 'Went']),
            ],
        ]);

        if (
            $validated['status'] === 'Went'
            && $event->event_date->isFuture()
        ) {
            throw ValidationException::withMessages([
                'status' => 'You can mark this gig as Went after it starts.',
            ]);
        }

        Attendance::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'event_id' => $event->event_id,
            ],
            [
                'status' => $validated['status'],
            ]
        );

        $attendanceCount = $event->attendances()
            ->where('status', 'Attending')
            ->count();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your gig has been saved.',
                'status' => $validated['status'],
                'attendance_count' => $attendanceCount,
            ]);
        }

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Your gig has been saved.');
    }
}