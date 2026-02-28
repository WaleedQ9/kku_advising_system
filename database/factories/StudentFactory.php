<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // جلب قسم عشوائي
        $department = Department::inRandomOrder()->first();

        // جلب مرشد عشوائي ينتمي لنفس القسم (لضمان منطقية البيانات)
        $advisor = User::where('department_id', $department->id)->inRandomOrder()->first()
            ?? User::inRandomOrder()->first();

        return [
            'student_id'    => $this->faker->unique()->numberBetween(441000000, 445000000), // أرقام جامعية تشبه KKU
            'name_ar'       => $this->faker->name(), // سيولد اسم عربي ثلاثي أو رباعي
            'name_en'       => $this->faker->name(), // اختياري بالإنجليزية
            'department_id' => $department->id,
            'advisor_id'    => $advisor->id,
            'gpa'           => $this->faker->randomFloat(2, 2.0, 5.0), // معدل بين 2 و 5
            'total_credits' => $this->faker->numberBetween(12, 134),
            'status'        => $this->faker->randomElement(['منتظم', 'متعثر', 'خريج']),
        ];
    }
}
