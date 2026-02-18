<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class DefaultAdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Administrador']);
        $user = User::firstOrCreate(
            ['email' => 'admin@myevents.com.br'],
            ['name' => 'admin', 'password' => Hash::make('Admin@123'), 'must_change_password' => true]
        );
        $user->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}

