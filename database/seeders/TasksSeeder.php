<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tasks;
use Carbon\Carbon;


class TasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tasks::truncate();
  
        $csvFile = fopen(base_path("database/data/tasks.csv"), "r");
  
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                Tasks::create([
                    "user_id" => '1',
                    "name" => $data['0'],
                    "status" => $data['1'],
                    "date" => Carbon::now()->format('Y-m-d'),
                ]);    
            }
            $firstline = false;
        }
   
        fclose($csvFile);
    }
}
