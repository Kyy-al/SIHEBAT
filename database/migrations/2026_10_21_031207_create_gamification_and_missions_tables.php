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
        // 1. Schools Table (PRD section 8 - Multi-sekolah preparation)
        if (! Schema::hasTable('schools')) {
            Schema::create('schools', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('npsn', 20)->unique();
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('accreditation', 5)->default('A');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Add school_id and streak_freeze_used_at to users & classrooms if not exists
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'school_id')) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained('schools')->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'streak_freeze_used_at')) {
                $table->timestamp('streak_freeze_used_at')->nullable()->after('streak_freezes');
            }
        });

        Schema::table('classrooms', function (Blueprint $table) {
            if (! Schema::hasColumn('classrooms', 'school_id')) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained('schools')->nullOnDelete();
            }
        });

        // 2. XP Logs Table (PRD section 8 - Riwayat transparan XP)
        if (! Schema::hasTable('xp_logs')) {
            Schema::create('xp_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->integer('amount');
                $table->string('source'); // 'habit', 'perfect_day', 'streak_bonus', 'mission', 'badge'
                $table->string('description');
                $table->date('date');
                $table->timestamps();
            });
        }

        // 3. Missions Table (FR-GAM-06 Misi mingguan)
        if (! Schema::hasTable('missions')) {
            Schema::create('missions', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->string('habit_slug')->nullable(); // null for general, or 'berolahraga', 'gemar-belajar', etc.
                $table->unsignedInteger('target_count')->default(3); // e.g. 4 days this week
                $table->unsignedInteger('xp_reward')->default(50);
                $table->string('icon')->default('target');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 4. User Missions Pivot/Tracking Table
        if (! Schema::hasTable('user_missions')) {
            Schema::create('user_missions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('mission_id')->constrained('missions')->cascadeOnDelete();
                $table->date('week_start');
                $table->unsignedInteger('current_count')->default(0);
                $table->boolean('is_completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'mission_id', 'week_start']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_missions');
        Schema::dropIfExists('missions');
        Schema::dropIfExists('xp_logs');

        Schema::table('classrooms', function (Blueprint $table) {
            if (Schema::hasColumn('classrooms', 'school_id')) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'streak_freeze_used_at')) {
                $table->dropColumn('streak_freeze_used_at');
            }
            if (Schema::hasColumn('users', 'school_id')) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            }
        });

        Schema::dropIfExists('schools');
    }
};
