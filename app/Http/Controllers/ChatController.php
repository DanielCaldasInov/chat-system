<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index()
    {
        return Inertia::render('Chat/Index');
    }

    public function show(Request $request, Room $room)
    {
        if (!$room->users->contains($request->user())) {
            abort(403, 'Não tens acesso a esta sala.');
        }

        $room->load('users');
        $messages = $room->messages()->with('user')->oldest()->get();

        return Inertia::render('Chat/Show', [
            'currentRoom' => $room,
            'messages' => $messages
        ]);
    }
}
