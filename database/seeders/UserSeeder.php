<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([ //Admin
            'name' => 'Admin',
            'email' => 'Admin@mydomain.com',
            'password' => 12345678,
            'monthly_budget' => 0,
            'id_role' => 1
        ]);

        User::create([ //Tom
            'name' => 'Tom',
            'email' => 'tom@mydomain.com',
            'password' => 12345678,
            'monthly_budget' => 3459,
            'id_role' => 2
        ]);

        User::create([ //Woj
            'name' => 'Woj',
            'email' => 'woj@mydomain.com',
            'password' => 12345678,
            'monthly_budget' => 4300,
            'id_role' => 2
        ]);
    }
}
