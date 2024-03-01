<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('schedules')->insert([
            
                'user_id' => '1',
                'title' => '終日予定',
                'body' => '終日',
                'start_date' => '2024-02-10',
                'end_date' => '2024-02-12',
                'start_time' => NULL,
                'end_time' => NULL,
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
        ]);
        
        DB::table('schedules')->insert([        
                'user_id' => '1',
                'title' => '予定',
                'body' => NULL,
                'start_date' => '2024-02-15',
                'end_date' => '2024-02-15',
                'start_time' => '13:00',
                'end_time' => '14:00',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
        ]);
    }
}
