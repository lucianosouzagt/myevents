<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public bool $withinTransaction = false;

    public function up(): void
    {
        DB::unprepared("
        DO $$
        BEGIN
            IF NOT EXISTS (
                SELECT 1
                FROM pg_constraint
                WHERE conrelid = 'users'::regclass
                  AND contype = 'p'
            ) THEN
                ALTER TABLE users ADD PRIMARY KEY (id);
            END IF;
        END
        $$;
        ");
    }

    public function down(): void
    {
    }
};

