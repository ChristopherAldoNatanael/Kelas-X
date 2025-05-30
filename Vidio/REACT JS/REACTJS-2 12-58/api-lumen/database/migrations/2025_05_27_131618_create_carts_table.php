<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->increments('idcart');
            $table->unsignedInteger('idpelanggan');
            $table->unsignedInteger('idmenu');
            $table->integer('qty')->default(1);
            $table->timestamps();

            // Foreign keys
            $table->foreign('idpelanggan')->references('idpelanggan')->on('pelanggans')->onDelete('cascade');
            $table->foreign('idmenu')->references('idmenu')->on('menus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
