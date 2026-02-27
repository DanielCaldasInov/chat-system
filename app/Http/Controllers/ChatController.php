<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
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

    public function store(Request $request, Room $room)
    {
        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        if (!$room->users->contains($request->user())) {
            abort(403, 'Não tens acesso a esta sala.');
        }

        $message = $room->messages()->create([
            'body' => $request->body,
            'user_id' => $request->user()->id,
        ]);

        $message->load('user');

        broadcast(new MessageSent($message))->toOthers();

        return back();
    }

    public function leave(Room $room)
    {
        $room->users()->detach(auth()->id());

        return redirect()->route('chat.index')->with('message', 'You have left the room.');
    }
}
