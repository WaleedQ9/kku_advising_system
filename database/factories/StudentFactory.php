<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        $department = Department::where('code', '!=', 'AR')->inRandomOrder()->first();

        $advisor = User::role('advisor')
            ->where('department_id', $department->id)
            ->inRandomOrder()
            ->first()
            ?? User::role('advisor')->inRandomOrder()->first();

        $gpa = $this->faker->randomFloat(2, 1.0, 4.0);

        // academic_status يعتمد على الـ gpa
        $academicStatus = $gpa < 2.0 ? 'Warning' : 'Regular';
        $status         = $gpa < 2.0 ? 'متعثر' : 'منتظم';

        return [
            'student_id'      => $this->faker->unique()->numberBetween(441000000, 445000000),
            'name_ar'         => $this->faker->name(),
            'name_en'         => $this->faker->name(),
            'major'           => $department->name_ar ?? 'غير محدد', // مضاف من التوثيق
            'department_id'   => $department->id,
            'advisor_id'      => $advisor?->id ?? 1,
            'gpa'             => $gpa,
            'total_credits'   => $this->faker->randomElement([0, 18, 36, 54, 72, 90]),
            'status'          => $status,
            'academic_status' => $academicStatus, // مضاف من التوثيق
        ];
    }
}
