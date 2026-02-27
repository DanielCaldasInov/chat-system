<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

    public function newDirectMessage(Request $request)
    {
        $users = User::where('id', '!=', auth()->id())
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->select('id', 'name', 'email', 'avatar')
            ->get();

        return Inertia::render('Chat/NewDirectMessage', [
            'users' => $users,
            'filters' => $request->only(['search'])
        ]);
    }

    public function startDirectMessage(User $user)
    {
        $authUserId = auth()->id();

        $room = Room::where('type', 'direct')
            ->whereHas('users', fn($q) => $q->where('users.id', $authUserId))
            ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
            ->first();

        if (!$room) {
            $room = Room::create([
                'name' => 'Direct Chat',
                'type' => 'direct',
                'reference' => Str::uuid(),
            ]);
            $room->users()->attach([$authUserId, $user->id]);
        }

        return redirect()->route('chat.show', $room->id);
    }
}
