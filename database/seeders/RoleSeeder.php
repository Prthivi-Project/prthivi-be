<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = Role::factory()->createManyQuietly(
            [
                ["role" => "super_admin",],
                ["role" => "admin"],
                ["role" => "vendor"],
                ["role" => "customer"],
            ]
        );
        $users = User::all();
        foreach ($users as $key => $user) {
            $i = \rand(0, count($roles) - 1);
            // $user->roles()->attach($roles[$i]);
            $user->roles()->associate($roles[$i])->save();
        }
    }
}
