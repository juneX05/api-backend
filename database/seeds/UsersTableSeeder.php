<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (User::count() === 0) {
            $users = [
                [
                    'id' => 1,
                    'name' => 'Super Admin',
                    'email' => 'super_admin@gmail.com',
                    'password' => '$2y$10$hnb9WR6v6O8imYNUJ/YTouKWZZi73jhYG6CAz7stC/PScTjJXl3Zm',
                    'remember_token' => null,
                ],
                [
                    'id' => 2,
                    'name' => 'Joel Kibona',
                    'email' => 'joel@gmail.com',
                    'password' => '$2y$10$hnb9WR6v6O8imYNUJ/YTouKWZZi73jhYG6CAz7stC/PScTjJXl3Zm',
                    'remember_token' => null,
                ],
            ];

            User::insert($users);
        } else {
            print "Users table not empty. \n";
        }
    }
}
