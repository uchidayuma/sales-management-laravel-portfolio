<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AreaOpenEmailSendsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('area_open_email_sends')->truncate();
        DB::table('area_open_email_sends')->insert([
            [
                'user_id' => 2,
                'send_status' => 0,
                'status' => 1,
                'created_at' => Carbon::now()->addMonthNoOverflow()->subYear()->subYear()->toDateString(),
            ],
            //   [
            //       'user_id' => 2,
            //       'send_status' => 1,
            //       'status' => 1,
            //       'created_at' => Carbon::now()->addMonthNoOverflow()->subYear()->toDateString(),
            //   ],
            [
                'user_id' => 2,
                'send_status' => 1,
                'status' => 1,
                'created_at' => Carbon::now()->addMonthNoOverflow()->toDateString(),
            ],
            [
                'user_id' => 51,
                'send_status' => 0,
                'status' => 1,
                'created_at' => Carbon::now()->addMonthNoOverflow()->subYear()->toDateString(),
            ],
            // 来月更新分
            [
                'user_id' => 52,
                'send_status' => 1,
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m') . '-01 01:00:00',
            ],
            [
                'user_id' => 59,
                'send_status' => 1,
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m') . '-01 01:00:00',
            ],
            [
                'user_id' => 71,
                'send_status' => 1,
                'status' => 0,
                'created_at' => Carbon::now()->format('Y-m') . '-01 01:00:00',
            ],
            [
                'user_id' => 91,
                'send_status' => 1,
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m') . '-01 01:00:00',
            ],
            [
                'user_id' => 105,
                'send_status' => 1,
                'status' => 1,
                'created_at' => Carbon::now()->toDateString(),
            ],
            // 先月更新分
            [
                'user_id' => 110,
                'send_status' => 1,
                'status' => 1,
                'created_at' => Carbon::now()->subMonthNoOverflow()->toDateString(),
            ],
            [
                'user_id' => 111,
                'send_status' => 1,
                'status' => 1,
                'created_at' => Carbon::now()->subMonthNoOverflow()->toDateString(),
            ],
            [
                'user_id' => 112,
                'send_status' => 1,
                'status' => 1,
                'created_at' => Carbon::now()->subMonthNoOverflow()->toDateString(),
            ],
        ]);
    }
}
