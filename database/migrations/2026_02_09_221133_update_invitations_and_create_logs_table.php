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
        if (!Schema::hasColumn('invitations', 'whatsapp')) {
            Schema::table('invitations', function (Blueprint $table) {
                $table->string('whatsapp')->nullable()->after('email');
            });
        }
        if (!Schema::hasColumn('invitations', 'allowed_guests')) {
            Schema::table('invitations', function (Blueprint $table) {
                $table->integer('allowed_guests')->default(0)->after('guest_name');
            });
        }
        if (!Schema::hasColumn('invitations', 'confirmed_guests')) {
            Schema::table('invitations', function (Blueprint $table) {
                $table->integer('confirmed_guests')->default(0)->after('allowed_guests');
            });
        }

        if (!Schema::hasTable('invitation_logs')) {
            Schema::create('invitation_logs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('invitation_id')->constrained()->onDelete('cascade');
                $table->string('channel'); // email, whatsapp
                $table->string('status'); // sent, failed, delivered
                $table->text('response')->nullable(); // API response or error message
                $table->timestamp('sent_at')->useCurrent();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitation_logs');

        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn(['whatsapp', 'allowed_guests', 'confirmed_guests']);
        });
    }
};
