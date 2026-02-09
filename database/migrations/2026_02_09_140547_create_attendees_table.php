<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('invitation_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name')->nullable(); // Redundant if user_id or invitation_id exists, but good for quick access or walk-ins
            $table->string('email')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'email']); // Prevent double entry
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendees');
    }
};
