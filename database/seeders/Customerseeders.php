<?php

namespace Database\Seeders;

use App\Models\customerinfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class Customerseeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customerinfos')->truncate();

        $faker=Faker::create();
        for ($i=1;$i<=100; $i++) { 
    
       
       $cus=new customerinfo();
       $cus->name=$faker->name;
       $cus->address=$faker->address;
       $cus->email=$faker->email;
       $cus->phoneno=$faker->phoneNumber;
       $cus->remarks="test";

       $cus->save();

    }

    }

    
}
