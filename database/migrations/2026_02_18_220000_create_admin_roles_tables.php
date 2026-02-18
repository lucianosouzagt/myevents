<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('admin_role_user', function (Blueprint $table) {
            $table->id();
            $table->uuid('admin_user_id');
            $table->foreign('admin_user_id')->references('id')->on('admin_users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('admin_roles')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['admin_user_id', 'role_id']);
        });

        DB::table('admin_roles')->insert([
            ['name' => 'Super Admin', 'slug' => 'superadmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Admin', 'slug' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Moderador', 'slug' => 'moderator', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_role_user');
        Schema::dropIfExists('admin_roles');
    }
};

