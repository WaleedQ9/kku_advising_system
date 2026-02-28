<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        // جلب معرفات الأقسام
        $csDept = Department::where('code', 'CS')->first();
        $cysDept = Department::where('code', 'CYS')->first();
        $ltDept = Department::where('code', 'LT')->first();

        $advisors = [
            [
                'name' => 'د. أحمد الحاسوبي',
                'email' => 'admin@admin.com',
                'password' => Hash::make('123123'),
                'department_id' => $csDept->id,
                'phone' => '0501112223'
            ],
            [
                'name' => 'د. سارة السيبراني',
                'email' => 'admin2@admin.com',
                'password' => Hash::make('123123'),
                'department_id' => $cysDept->id,
                'phone' => '0504445556'
            ],
            [
                'name' => 'د. خالد اللغوي',
                'email' => 'admin3@admin.com',
                'password' => Hash::make('123123'),
                'department_id' => $ltDept->id,
                'phone' => '0507778889'
            ],
        ];

        foreach ($advisors as $advisor) {
            User::updateOrCreate(['email' => $advisor['email']], $advisor);
        }
    }
}
