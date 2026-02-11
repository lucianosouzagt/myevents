<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // intentionally not overriding withinTransaction type/property

    public function up(): void
    {
        // Ensure pgcrypto for gen_random_uuid()
        DB::unprepared('CREATE EXTENSION IF NOT EXISTS "pgcrypto";');

        // Fill NULL ids with UUIDs
        DB::unprepared("UPDATE users SET id = gen_random_uuid() WHERE id IS NULL;");

        // Fix duplicates: keep first row, update others to new UUID
        DB::unprepared("
        WITH dupes AS (
            SELECT id
            FROM users
            WHERE id IS NOT NULL
            GROUP BY id
            HAVING COUNT(*) > 1
        ),
        tofix AS (
            SELECT u.ctid AS row_ctid
            FROM users u
            JOIN dupes d ON u.id = d.id
            WHERE u.ctid <> (
                SELECT MIN(u2.ctid) FROM users u2 WHERE u2.id = d.id
            )
        )
        UPDATE users u
        SET id = gen_random_uuid()
        FROM tofix
        WHERE u.ctid = tofix.row_ctid;
        ");

        // Add PK if missing
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
