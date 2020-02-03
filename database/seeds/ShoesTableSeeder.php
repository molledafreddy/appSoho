<?php

use App\Shoe;
use Illuminate\Database\Seeder;

class ShoesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Shoe::class, 20)->create();
    }
}
