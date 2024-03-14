<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class event_userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=1; $i<=3; $i++)
        {
            DB::table('event_user')->insert([
                
                'event_id' => '1',
                'user_id' => "{$i}"
            ]);
        }
    }
}
