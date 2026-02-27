<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoomParticipantController extends Controller
{
    public function index(Request $request, Room $room)
    {
        $search = $request->search;

        $members = $room->users()->orderBy('name')->get();
        $memberIds = $members->pluck('id')->toArray();

        $nonMembersQuery = User::whereNotIn('id', $memberIds);

        if ($search) {
            $nonMembersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $nonMembers = $nonMembersQuery->orderBy('name')->paginate(5)->withQueryString();

        return Inertia::render('Admin/Rooms/Participants', [
            'room' => $room,
            'members' => $members,
            'nonMembers' => $nonMembers,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request, Room $room)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $room->users()->syncWithoutDetaching([$request->user_id]);

        return back()->with('message', 'User successfully added to the room.');
    }

    public function destroy(Room $room, User $user)
    {
        $room->users()->detach($user->id);

        return back()->with('message', 'User removed from the room.');
    }
}
