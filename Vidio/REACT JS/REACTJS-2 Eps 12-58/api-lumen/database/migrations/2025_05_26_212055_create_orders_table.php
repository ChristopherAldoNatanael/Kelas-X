<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('idorder');
            $table->unsignedInteger('idpelanggan');
            $table->date('tglorder');
            $table->integer('total');
            $table->integer('bayar');
            $table->integer('kembali');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            // Foreign key ke tabel pelanggans
            $table->foreign('idpelanggan')->references('idpelanggan')->on('pelanggans')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
