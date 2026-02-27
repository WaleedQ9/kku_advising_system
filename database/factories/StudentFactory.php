<?php

namespace Database\Factories;

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
        return [
            //
            'student_id' => '44' . $this->faker->unique()->numberBetween(1000000, 9999999),
            'name_ar' => $this->faker->name(),
            'name_en' => $this->faker->name(),
            'major' => $this->faker->randomElement(['نظم معلومات', 'علوم حاسب', 'هندسة برمجيات', 'ذكاء اصطناعي']),
            'gpa' => $this->faker->randomFloat(2, 1.5, 5.0),
            'total_credits' => $this->faker->numberBetween(12, 140),
            'status' => $this->faker->randomElement(['منتظم', 'متعثر', 'خريج']),
            'advisor_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
