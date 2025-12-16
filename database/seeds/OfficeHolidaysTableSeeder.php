<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OfficeHolidaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = Carbon::now();
        $year = $date->year;
        $month = $date->month;
        $nextMonth = $month == 12 ? 1 : $month + 1;
        DB::table('office_holidays')->truncate();
        DB::table('office_holidays')->insert([
            [
                'holiday' => new Carbon($year.'-'.$month.'-'.'01')
            ],
            [
                'holiday' => new Carbon($year.'-'.$month.'-'.'12')
            ],
            [
                'holiday' => new Carbon($year.'-'.$month.'-'.'17')
            ],
            [
                'holiday' => new Carbon($year.'-'.$month.'-'.'26')
            ],
            [
                'holiday' => new Carbon($year.'-'.$nextMonth.'-'.'01')
            ],
        ]);
    }
}
