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
        if (Schema::hasTable('habits') && ! Schema::hasColumn('habits', 'target_value')) {
            Schema::table('habits', function (Blueprint $table) {
                $table->string('target_value')->nullable()->after('xp_reward');
            });
        }

        if (Schema::hasTable('journals')) {
            Schema::table('journals', function (Blueprint $table) {
                if (! Schema::hasColumn('journals', 'is_draft')) {
                    $table->boolean('is_draft')->default(false)->after('is_perfect_day');
                }
                $table->index('date');
            });
        }

        if (Schema::hasTable('journal_entries')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->index('completed');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('journal_entries')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->dropIndex(['completed']);
            });
        }

        if (Schema::hasTable('journals')) {
            Schema::table('journals', function (Blueprint $table) {
                $table->dropIndex(['date']);
                if (Schema::hasColumn('journals', 'is_draft')) {
                    $table->dropColumn('is_draft');
                }
            });
        }

        if (Schema::hasTable('habits') && Schema::hasColumn('habits', 'target_value')) {
            Schema::table('habits', function (Blueprint $table) {
                $table->dropColumn('target_value');
            });
        }
    }
};
