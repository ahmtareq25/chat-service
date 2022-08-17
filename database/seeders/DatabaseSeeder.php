<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        User::create([
            'name'=>'Usman',
            'email'=>'usman@gmail.com',
            'password'=>bcrypt('123'),
            'api_token'=>'123',
        ]);
        User::create([
            'name'=>'Nouman',
            'email'=>'nouman@gmail.com',
            'password'=>bcrypt('123'),
            'api_token'=>'123',
        ]);
        User::create([
            'name'=>'Usama',
            'email'=>'usama@gmail.com',
            'password'=>bcrypt('123'),
            'api_token'=>'123',
        ]);
    }
}
