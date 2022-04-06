<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Wine;
use App\Models\Photo;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->count(10)
            ->create();

        Role::factory()
            ->count(10)
            ->create();

        Wine::factory()
            ->count(10)
            ->create();

        Photo::factory()
            ->count(10)
            ->create();
    }
}
