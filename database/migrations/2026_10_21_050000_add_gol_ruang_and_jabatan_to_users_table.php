<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'gol_ruang')) {
                $table->string('gol_ruang', 50)->nullable()->after('nip');
            }
            if (! Schema::hasColumn('users', 'jabatan')) {
                $table->string('jabatan', 100)->nullable()->after('gol_ruang');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'gol_ruang')) {
                $table->dropColumn('gol_ruang');
            }
            if (Schema::hasColumn('users', 'jabatan')) {
                $table->dropColumn('jabatan');
            }
        });
    }
};
