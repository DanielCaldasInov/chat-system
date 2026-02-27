<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_create_room_page()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get(route('admin.rooms.create'));

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_view_create_room_page()
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get(route('admin.rooms.create'));

        $response->assertStatus(403);
    }

    public function test_admin_can_create_a_group_room()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $userToAdd = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.rooms.store'), [
            'name' => 'Projeto Alpha',
            'users' => [$userToAdd->id],
        ]);

        $this->assertDatabaseHas('rooms', [
            'name' => 'Projeto Alpha',
            'type' => 'group',
        ]);

        $room = Room::first();

        $this->assertTrue($room->users->contains($admin->id));
        $this->assertTrue($room->users->contains($userToAdd->id));

        $response->assertRedirect(route('chat.show', $room->id));
    }

    public function test_admin_can_delete_a_room()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $room = Room::factory()->create(['type' => 'group']);

        $response = $this->actingAs($admin)->delete(route('admin.rooms.destroy', $room->id));

        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
        $response->assertRedirect(route('chat.index'));
    }

    public function test_normal_user_can_leave_a_room()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $room = Room::factory()->create(['type' => 'group']);

        $room->users()->attach($user->id);

        $response = $this->actingAs($user)->delete(route('chat.leave', $room->id));

        $this->assertFalse($room->fresh()->users->contains($user->id));
        $response->assertRedirect(route('chat.index'));
    }

    public function test_admin_can_update_a_room()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $room = Room::factory()->create([
            'name' => 'Old Name',
            'type' => 'group'
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.rooms.update', $room->id), [
            'name' => 'New Awesome Name',
        ]);

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'name' => 'New Awesome Name',
        ]);

        $response->assertRedirect(route('chat.show', $room->id));
    }

    public function test_non_admin_cannot_delete_a_room()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $room = Room::factory()->create(['type' => 'group']);

        $response = $this->actingAs($user)->delete(route('admin.rooms.destroy', $room->id));

        $response->assertStatus(403);

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
        ]);
    }
}
