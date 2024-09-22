<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class products extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {        
        $randname = ['Smartphone','Headset','Laptop','Mouse','Keyboard','Komputer','Kamera','Modem','Monitor'];
        $randname2 = ['Apple','HP','Lenovo','Asus','Acer','Advan','Logitech','Samsung','LG'];

        
        for ($i=1; $i <= 20 ; $i++) { 
            $name1 = $randname;
            $name2 = $randname2;

            $getname = $name1[rand(0,8)]." ".$name2[rand(0,8)]." v".$i;
            DB::table('products')->insert([
                'name' => $getname,
                'price' => rand(100000,10000000),
                'stock' => rand(0,1000),
                'sold' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
