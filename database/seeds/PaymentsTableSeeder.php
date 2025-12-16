<?php

use Illuminate\Database\Seeder;

class PaymentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payments')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'regularly' => 1,
                'price' => 10000,
            ],
        ]);
    }
}
