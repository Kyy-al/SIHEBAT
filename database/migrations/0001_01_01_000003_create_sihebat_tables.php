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
        // 7 Kebiasaan master
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->string('name');        // e.g. "Bangun Pagi"
            $table->string('slug')->unique(); // e.g. "bangun-pagi"
            $table->string('icon');        // icon name (Phosphor)
            $table->string('color');       // tailwind color key e.g. "bangun"
            $table->unsignedInteger('xp_reward')->default(10);
            $table->unsignedTinyInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Jurnal harian
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->unsignedTinyInteger('habits_completed')->default(0);
            $table->boolean('is_perfect_day')->default(false);
            $table->unsignedInteger('xp_earned')->default(0);
            $table->enum('mood', ['sangat_senang', 'senang', 'biasa', 'sedih', 'sangat_sedih'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'date']);
        });

        // Detail jurnal per kebiasaan
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('journals')->cascadeOnDelete();
            $table->foreignId('habit_id')->constrained('habits')->cascadeOnDelete();
            $table->boolean('completed')->default(false);
            $table->json('value')->nullable(); // flexible JSON: {time: "05:00"}, {done: true}, {duration: 30, type: "Futsal"}, etc.
            $table->unsignedInteger('xp_earned')->default(0);
            $table->timestamps();
            $table->unique(['journal_id', 'habit_id']);
        });

        // Badge master
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon');
            $table->string('color')->default('lavender');
            $table->string('requirement_type'); // e.g. "streak", "xp", "perfect_day", "habit_streak"
            $table->unsignedInteger('requirement_value')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot: siswa <-> badge
        Schema::create('badge_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained('badges')->cascadeOnDelete();
            $table->timestamp('earned_at');
            $table->unique(['user_id', 'badge_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badge_user');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('journals');
        Schema::dropIfExists('habits');
    }
};
