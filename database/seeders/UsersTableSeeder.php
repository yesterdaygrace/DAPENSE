<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'supadmin',
                'email' => 'supadmin@gmail.com',
                'usertype' => 'rootsuperuser',
                'image' => '20240617104802.jpg',
                'email_verified_at' => null,
                'password' => '$2y$12$Rp.OXoffDw3FWxzV5DKn6uWusYxtY3Uk22lW0Yp5FX7rK2EMAa9FW', // already hashed
                'remember_token' => null,
                'created_at' => Carbon::parse('2024-06-17 07:58:03'),
                'updated_at' => Carbon::parse('2024-06-17 08:01:07')
            ],
        ]);
    }
}
