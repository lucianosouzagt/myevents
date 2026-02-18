<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('actor_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('target_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['actor_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

