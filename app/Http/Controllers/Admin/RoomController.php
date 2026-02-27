<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RoomController extends Controller
{
    public function create()
    {
        return Inertia::render('Admin/Rooms/Create', [
            'users' => User::orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('room_avatars', 'public');
            $avatarPath = '/storage/' . $path;
        }

        $room = Room::create([
            'name' => $request->name,
            'type' => 'group',
            'avatar' => $avatarPath,
            'reference' => Str::uuid(),
        ]);

        $userIds = $request->users ?? [];
        if (!in_array(auth()->id(), $userIds)) {
            $userIds[] = auth()->id();
        }

        $room->users()->sync($userIds);

        return redirect()->route('chat.show', $room->id)
            ->with('message', 'Room created successfully!');
    }

    public function edit(Room $room)
    {
        return Inertia::render('Admin/Rooms/Edit', [
            'room' => $room
        ]);
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('room_avatars', 'public');
            $room->avatar = '/storage/' . $path;
        }

        $room->name = $request->name;
        $room->save();

        return redirect()->route('chat.show', $room->id)
            ->with('message', 'Room updated successfully!');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('chat.index')->with('message', 'Room deleted!');
    }
}
