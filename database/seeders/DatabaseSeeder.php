<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*
         | Default passwords (development only — change everywhere before production).
         |
         | admin@zaffico.test      — Administrator
         | fleet@zaffico.test      — Fleet officer
         | management@zaffico.test — Management
         | test@example.com      — Fleet officer — used by automated auth tests (password matches below)
        */
        $password = Hash::make('password');

        User::withoutEvents(function () use ($password): void {
            User::factory()->create([
                'name' => 'System Administrator',
                'email' => 'admin@zaffico.test',
                'password' => $password,
                'role' => User::ROLE_ADMIN,
            ]);

            User::factory()->create([
                'name' => 'Fleet Desk',
                'email' => 'fleet@zaffico.test',
                'password' => $password,
                'role' => User::ROLE_FLEET_OFFICER,
            ]);

            User::factory()->create([
                'name' => 'Management Oversight',
                'email' => 'management@zaffico.test',
                'password' => $password,
                'role' => User::ROLE_MANAGEMENT,
            ]);

            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => $password,
                'role' => User::ROLE_FLEET_OFFICER,
            ]);
        });

        $this->call(DemoDataSeeder::class);
    }
}
