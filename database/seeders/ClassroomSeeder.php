<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Classroom;

class ClassroomSeeder extends Seeder
{
    public function run()
    {
        Classroom::create([
            'name' => 'Classroom A',
            'timetable' => json_encode(['monday' => range(9, 18), 'wednesday' => range(9, 18)]),
            'capacity' => 10,
        ]);

        Classroom::create([
            'name' => 'Classroom B',
            'timetable' => json_encode(['monday' => range(8, 18, 2), 'thursday' => range(8, 18, 2), 'saturday' => range(8, 18, 2)]),
            'capacity' => 15,
        ]);

        Classroom::create([
            'name' => 'Classroom C',
            'timetable' => json_encode(['tuesday' => range(15, 22), 'friday' => range(15, 22), 'saturday' => range(15, 22)]),
            'capacity' => 7,
        ]);
    }
}
