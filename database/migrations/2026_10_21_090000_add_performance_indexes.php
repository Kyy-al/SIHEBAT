<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to add high-performance database indexes.
     */
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->index(['date', 'is_perfect_day']);
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->index(['habit_id', 'completed']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'xp']);
            $table->index(['role', 'current_streak']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropIndex(['date', 'is_perfect_day']);
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropIndex(['habit_id', 'completed']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'xp']);
            $table->dropIndex(['role', 'current_streak']);
        });
    }
};
