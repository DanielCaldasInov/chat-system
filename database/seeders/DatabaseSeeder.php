<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@chat.test',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $joao = User::factory()->create([
            'name' => 'João Silva',
            'email' => 'joao@chat.test',
            'password' => bcrypt('password'),
        ]);

        $maria = User::factory()->create([
            'name' => 'Maria Santos',
            'email' => 'maria@chat.test',
            'password' => bcrypt('password'),
        ]);

        $randomUsers = User::factory(5)->create();

        $salaGeral = Room::create([
            'name' => 'Geral',
            'type' => 'group',
            'reference' => Str::uuid(),
        ]);

        $todosOsUsers = User::pluck('id')->toArray();
        $salaGeral->users()->attach($todosOsUsers);

        $salaDM = Room::create([
            'type' => 'direct',
            'reference' => Str::uuid(),
        ]);
        $salaDM->users()->attach([$admin->id, $joao->id]);

        Message::create(['room_id' => $salaGeral->id, 'user_id' => $admin->id, 'body' => 'Bem-vindos ao InovChat, equipa!']);
        Message::create(['room_id' => $salaGeral->id, 'user_id' => $joao->id, 'body' => 'Obrigado! A interface ficou brutal.']);
        Message::create(['room_id' => $salaGeral->id, 'user_id' => $maria->id, 'body' => 'Bom dia a todos, já estou online.']);

        Message::create(['room_id' => $salaDM->id, 'user_id' => $admin->id, 'body' => 'João, consegues rever aquele PR quando tiveres um minuto?']);
        Message::create(['room_id' => $salaDM->id, 'user_id' => $joao->id, 'body' => 'Claro, vejo isso logo a seguir ao almoço.']);
    }
}
