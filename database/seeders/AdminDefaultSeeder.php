<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;

class AdminDefaultSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::firstOrCreate(
            ['email' => 'admin@myevents.com.br'],
            [
                'name' => 'admin',
                'password' => Hash::make('Admin@123'),
                'must_change_password' => true,
            ]
        );
    }
}

