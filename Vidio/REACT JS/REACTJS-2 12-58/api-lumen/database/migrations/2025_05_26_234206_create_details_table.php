<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('details', function (Blueprint $table) {
            $table->increments('iddetail');
            $table->unsignedInteger('idorder');
            $table->unsignedInteger('idmenu');
            $table->integer('jumlah');
            $table->decimal('hargajual', 10, 2);
            $table->timestamps();

            // Foreign keys
            $table->foreign('idorder')->references('idorder')->on('orders')->onDelete('cascade');
            $table->foreign('idmenu')->references('idmenu')->on('menus')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('details');
    }
}
