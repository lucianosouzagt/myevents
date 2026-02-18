<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('actor_id');
            $table->string('action');
            $table->string('ip')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->foreign('actor_id')->references('id')->on('admin_users')->onDelete('cascade');
            $table->index(['actor_id','action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_audit_logs');
    }
};

