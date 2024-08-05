<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Belal',
            'email' => 'belal@gmail.com',
            'password' => Hash::make('123456'),
            'role_id' => Role::IS_ADMIN,
        ]);

        // Editor
        User::create([
            'name' => 'Ahmed',
            'email' => 'ahmed@gmail.com',
            'password' => Hash::make('123456'),
            'role_id' => Role::IS_EDITOR,
        ]);

        // User
        User::create([
            'name' => 'Omar',
            'email' => 'omar@gmail.com',
            'password' => Hash::make('123456'),
            'role_id' => Role::IS_USER,
        ]);
        


    }
}
