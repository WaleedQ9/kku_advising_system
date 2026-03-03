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
        $department = Department::inRandomOrder()->first();

        // 2. جلب مرشد عشوائي ينتمي لنفس القسم ويملك دور 'advisor'
        // استخدمنا شرط الـ role لضمان عدم تعيين طالب لمسجل الطلاب (Registrar)
        $advisor = User::role('advisor')
            ->where('department_id', $department->id)
            ->inRandomOrder()
            ->first()
            ?? User::role('advisor')->inRandomOrder()->first();

        return [
            'student_id'    => $this->faker->unique()->numberBetween(441000000, 445000000),
            'name_ar'       => $this->faker->name(),
            'name_en'       => $this->faker->name(),
            'department_id' => $department->id,
            'advisor_id'    => $advisor ? $advisor->id : 1, // حماية في حال لم يجد مرشد
            'gpa'           => 0.00,       // المعدل يبدأ من صفر
            'total_credits' => 0,          // الساعات المجتازة تبدأ من صفر
            'status'        => 'منتظم',    // الحالة الافتراضية لأي طالب جديد
        ];
    }
}
