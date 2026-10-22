<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_warning', 500)->nullable()->after('avatar');
            $table->timestamp('avatar_moderated_at')->nullable()->after('avatar_warning');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_warning', 'avatar_moderated_at']);
        });
    }
};
