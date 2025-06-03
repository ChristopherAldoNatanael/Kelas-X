<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPelanggansTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Add password column if it doesn't exist
            if (!Schema::hasColumn('pelanggans', 'password')) {
                $table->string('password')->after('email');
            }

            // Add api_token column if it doesn't exist
            if (!Schema::hasColumn('pelanggans', 'api_token')) {
                $table->string('api_token', 80)->unique()->nullable()->after('password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropColumn(['password', 'api_token']);
        });
    }
};
