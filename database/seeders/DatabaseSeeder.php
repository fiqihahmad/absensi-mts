<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Siswa;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // User::create([
        //     'username' => 'admin',
        //     'password' => bcrypt('Adminmts123'),
        //     'role' => 'admin'
        // ]);

        $faker = Factory::create('id_ID'); // Create a Faker instance with Indonesian locale


        for ($i = 0; $i < 7; $i++) {
            Siswa::create([
                'nis' => $faker->unique()->randomNumber(8),
                'nama' => $faker->name,
                'jk' => $faker->randomElement(['L', 'P']),
                'kelas_id' => '8B',
            ]);
        }
    }
}
