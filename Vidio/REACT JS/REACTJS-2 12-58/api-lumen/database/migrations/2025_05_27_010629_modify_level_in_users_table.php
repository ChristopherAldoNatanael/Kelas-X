<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyLevelInUsersTable extends Migration
{
    public function up()
    {
        // First modify existing integer values to their string equivalents
        DB::table('users')->where('level', '1')->update(['level' => 'admin']);
        DB::table('users')->where('level', '2')->update(['level' => 'pelanggan']);
        DB::table('users')->where('level', '3')->update(['level' => 'koki']);
        DB::table('users')->where('level', '4')->update(['level' => 'kasir']);

        // Then modify the column type
        DB::statement("ALTER TABLE users MODIFY COLUMN level ENUM('admin', 'koki', 'kasir', 'pelanggan') NOT NULL DEFAULT 'pelanggan'");
    }

    public function down()
    {
        // Convert back to integer if needed to rollback
        DB::statement("ALTER TABLE users MODIFY COLUMN level INT NOT NULL DEFAULT 2");

        // Convert string values back to integers
        DB::table('users')->where('level', 'admin')->update(['level' => 1]);
        DB::table('users')->where('level', 'pelanggan')->update(['level' => 2]);
        DB::table('users')->where('level', 'koki')->update(['level' => 3]);
        DB::table('users')->where('level', 'kasir')->update(['level' => 4]);
    }
}
