<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailToPelanggansTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Tambahkan kolom email baru jika belum ada
            if (!Schema::hasColumn('pelanggans', 'email')) {
                $table->string('email')->nullable()->after('pelanggan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggans', function (Blueprint $table) {
            // Hapus kolom email jika ada
            if (Schema::hasColumn('pelanggans', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
}
