<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('events')->insert([
            
            'user_id' => '1',
            'title' => '新規イベント',
            'body' => '2月中に開催予定',
            'start_date' => '2024-02-01',
            'end_date' => '2024-02-29',
            'deadline' => '2024-02-20',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime()
            
        ]);
    }
}
