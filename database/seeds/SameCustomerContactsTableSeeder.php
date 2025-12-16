<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SameCustomerContactsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1 to 161 random insert ref_contact_id and contact_id
        for ($i = 1; $i <= 161; $i++) {
            $ref_contact_id = rand(1, 161);
            $contact_id = rand(1, 161);
            \DB::table('same_customer_contacts')->insert([
                'ref_contact_id' => $ref_contact_id,
                'contact_id' => $contact_id,
            ]);
        }
    }
}
