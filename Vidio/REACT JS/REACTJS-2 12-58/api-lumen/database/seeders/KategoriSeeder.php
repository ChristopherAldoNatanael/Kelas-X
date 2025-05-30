<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kategori; // Tambahkan ini
use Faker\Factory as Faker;


class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i=0; $i < 100; $i++) { 
            $data = [
                'kategori' => $faker->name,
                'keterangan' => $faker->text
            ];
            Kategori::create($data);
        }
        
    }
}
