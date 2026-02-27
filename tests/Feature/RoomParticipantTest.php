<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomParticipantTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_participant_to_room()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $room = Room::factory()->create(['type' => 'group']);
        $newUser = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.rooms.participants.store', $room->id), [
            'user_id' => $newUser->id,
        ]);

        $this->assertTrue($room->fresh()->users->contains($newUser->id));
        $response->assertSessionHas('message', 'User successfully added to the room.');
    }

    public function test_admin_can_remove_participant_from_room()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $room = Room::factory()->create(['type' => 'group']);
        $userToRemove = User::factory()->create();

        $room->users()->attach($userToRemove->id);

        $response = $this->actingAs($admin)->delete(route('admin.rooms.participants.destroy', [
            'room' => $room->id,
            'user' => $userToRemove->id
        ]));

        $this->assertFalse($room->fresh()->users->contains($userToRemove->id));
        $response->assertSessionHas('message', 'User removed from the room.');
    }
}
