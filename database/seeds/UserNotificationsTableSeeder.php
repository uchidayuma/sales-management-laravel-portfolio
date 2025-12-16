<?php

use Illuminate\Database\Seeder;

class UserNotificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('user_notifications')->truncate();
        DB::table('user_notifications')->insert([
        [
          'user_id' => 2,
          'contact_id' => 2,
          'notification_type' => 1,
          'read' => 0,
          'created_at' => '2019-09-27 00:00:00',
          'updated_at' => '2019-09-27 00:00:00',
        ],
        [
          'user_id' => 2,
          'contact_id' => 1,
          'notification_type' => 1,
          'read' => 1,
          'created_at' => '2019-09-27 00:00:00',
          'updated_at' => '2019-09-27 00:00:00',
        ],
        [
          'user_id' => 2,
          'contact_id' => 1,
          'notification_type' => 2,
          'read' => 0,
          'created_at' => '2019-09-27 00:00:00',
          'updated_at' => '2019-09-27 00:00:00',
        ],
        [
          'user_id' => 2,
          'contact_id' => 4,
          'notification_type' => 2,
          'read' => 0,
          'created_at' => '2019-09-27 00:00:00',
          'updated_at' => '2019-09-27 00:00:00',
        ],
        [
          'user_id' => 2,
          'contact_id' => 20,
          'notification_type' => 3,
          'read' => 0,
          'created_at' => '2019-09-27 00:00:00',
          'updated_at' => '2019-09-27 00:00:00',
        ],
      ]);
    }
}
