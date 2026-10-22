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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'gender')) {
                $table->string('gender', 10)->nullable()->after('name'); // 'L' (Laki-laki) or 'P' (Perempuan)
            }
            if (! Schema::hasColumn('users', 'nisn')) {
                $table->string('nisn', 30)->nullable()->after('nis');
            }
        });

        if (! Schema::hasTable('ai_insights')) {
            Schema::create('ai_insights', function (Blueprint $table) {
                $table->id();
                $table->string('category')->default('school_summary');
                $table->string('title');
                $table->text('summary');
                $table->json('strengths')->nullable();
                $table->json('weaknesses')->nullable();
                $table->json('recommendations')->nullable();
                $table->longText('raw_response')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_insights');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gender', 'nisn']);
        });
    }
};
