<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Meifer Rodriguez',
            'email' => 'juanjo_meifer@hotmail.com',
            'password' => bcrypt('password'),
        ]);
        \App\Models\User::factory(10)->create();
        \App\Models\Tag::factory(10)->create();
        \App\Models\Task::factory(500)->create();
        \App\Models\Task::all()->each(function ($task) {
            $task->tags()->attach(\App\Models\Tag::all()->random(rand(1, 5)));
        });
    }
}
