<?php

use Illuminate\Database\Seeder;

class ArticlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('articles')->truncate();
        DB::table('articles')->insert([
          [
            'id' => 1,
            'title' => 'テスト投稿！',
            'body' => 'ここに本文が入ります🐸ここに本文が入ります🐸ここに本文が入ります🐸ここに本文が入ります🐸ここに本文が入ります🐸ここに本文が入ります🐸ここに本文が入ります🐸ここに本文が入ります🐸',
            'status' => 1,
            'published_at' => '2019-12-31 10:00:00',
          ],
      ]);
    }
}
