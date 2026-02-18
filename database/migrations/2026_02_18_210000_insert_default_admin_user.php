<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admin_users')) {
            return;
        }
        $email = 'admin@myevents.com.br';
        if (!DB::table('admin_users')->where('email', $email)->exists()) {
            DB::table('admin_users')->insert([
                'id' => (string) Str::uuid(),
                'name' => 'admin',
                'email' => $email,
                'password' => Hash::make('Admin@123'),
                'must_change_password' => true,
                'two_factor_enabled' => false,
                'two_factor_code' => null,
                'two_factor_expires_at' => null,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('admin_users')) {
            DB::table('admin_users')->where('email', 'admin@myevents.com.br')->delete();
        }
    }
};

