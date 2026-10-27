<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MyGigController extends Controller
{
    public function index(Request $request): View
    {
        $attendances = $request->user()
            ->attendances()
            ->with('event.venue')
            ->latest()
            ->paginate(9);

        return view('my-gigs.index', compact('attendances'));
    }
}