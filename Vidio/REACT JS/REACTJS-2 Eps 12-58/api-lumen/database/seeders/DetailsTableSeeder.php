<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('details')->insert([
            [
                'idorder' => 1,
                'idmenu' => 19,
                'jumlah' => 2,
                'hargajual' => 20000,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idorder' => 1,
                'idmenu' => 31,
                'jumlah' => 1,
                'hargajual' => 15000,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
