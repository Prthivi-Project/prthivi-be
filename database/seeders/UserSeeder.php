<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            "name" => "admin",
            "email" => "admin@prthivi.com",
            "password" => Hash::make('password'),
            'role_id' => 2 # admin
        ]);

        User::create([
            "name" => "super admin",
            "email" => "super@prthivi.com",
            "password" => Hash::make('password'),
            'role_id' => 1 # super admin
        ]);

        $role = Role::where("role", 'customer')->first();
        $users = User::factory(20)->create();

        $role->users()->saveMany($users);
    }
}
