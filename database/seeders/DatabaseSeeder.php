<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            CourseSeeder::class,
            UserSeeder::class,
        ]);
        Student::factory(50)->create();
        $this->call([
            StudentCourseSeeder::class,
        ]);
    }
}
