<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToPelanggans extends Migration
{
    public function up()
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Add aktif column if it doesn't exist
            if (!Schema::hasColumn('pelanggans', 'aktif')) {
                $table->boolean('aktif')->default(1)->after('telp');
            }

            // Add api_token column if it doesn't exist
            if (!Schema::hasColumn('pelanggans', 'api_token')) {
                $table->string('api_token', 80)->nullable()->after('aktif');
            }
        });
    }

    public function down()
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropColumn(['aktif', 'api_token']);
        });
    }
}
