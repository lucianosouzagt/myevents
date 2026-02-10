<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrador']
        );

        $user = User::first();
        if ($user && !$user->roles()->where('slug', 'admin')->exists()) {
            $user->roles()->attach($admin->id);
        }
    }
}

