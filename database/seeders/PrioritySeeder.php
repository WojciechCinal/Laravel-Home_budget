<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Priority::create([
            'name_priority' => 'Bardzo wysoki',
        ]);

        Priority::create([
            'name_priority' => 'Wysoki',
        ]);

        Priority::create([
            'name_priority' => 'Średni',
        ]);

        Priority::create([
            'name_priority' => 'Mały',
        ]);

        Priority::create([
            'name_priority' => 'Brak',
        ]);
    }
}
