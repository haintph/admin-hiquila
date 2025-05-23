<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {

        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'owner'
        ]);


        User::create([
            'name' => 'Manager',
            'email' => 'manager@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'manager'
        ]);

        User::create([
            'name' => 'Staff',
            'email' => 'staff@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'staff'
        ]);

        User::create([
            'name' => 'Chef',
            'email' => 'chef@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'chef'
        ]);
        User::create([
            'name' => 'Khách hàng test',
            'email' => 'customer@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 'customer'
        ]);
        // User::factory()->count(5)->create();
    }
}
