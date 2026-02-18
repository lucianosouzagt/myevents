<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visit_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('session_id', 100)->index();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('device_type', 20)->nullable()->index();
            $table->string('browser', 50)->nullable();
            $table->string('os', 50)->nullable();
            $table->string('country', 2)->nullable()->index();
            $table->string('region', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->unsignedInteger('pageviews_count')->default(0);
            $table->unsignedInteger('duration_ms')->default(0);
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            $table->index(['started_at', 'ended_at']);
        });

        Schema::create('page_views', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('visit_session_id')->constrained('visit_sessions')->cascadeOnDelete();
            $table->string('path', 2048)->index();
            $table->string('route_name', 191)->nullable()->index();
            $table->string('referrer', 2048)->nullable();
            $table->string('utm_source', 100)->nullable()->index();
            $table->string('utm_medium', 100)->nullable()->index();
            $table->string('utm_campaign', 100)->nullable()->index();
            $table->string('device_type', 20)->nullable()->index();
            $table->string('ip_hash', 64)->nullable()->index();
            $table->unsignedInteger('duration_ms')->default(0);
            $table->timestamp('viewed_at')->useCurrent()->index();
            $table->timestamps();
            $table->index(['device_type', 'utm_source', 'utm_medium', 'utm_campaign']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('visit_sessions');
    }
};

