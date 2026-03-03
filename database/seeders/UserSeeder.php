<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. إنشاء الأدوار الأساسية
        $registrarRole = Role::firstOrCreate(['name' => 'registrar']);
        $advisorRole = Role::firstOrCreate(['name' => 'advisor']);

        // 2. جلب معرفات الأقسام
        $arDept = Department::where('code', 'AR')->first();
        $cysDept = Department::where('code', 'CYS')->first();
        $ltDept = Department::where('code', 'LT')->first();

        // 3. تعريف المستخدمين مع أدوارهم
        $users = [
            [
                'name' => 'محمد المسجل', // هذا سيكون مسجل الطلاب
                'email' => 'admin@admin.com',
                'password' => Hash::make('123123'),
                'department_id' => $arDept->id,
                'phone' => '0500000000',
                'role' => 'registrar'
            ],
            [
                'name' => 'د. سارة السيبراني', // مرشدة 1
                'email' => 'admin2@admin.com',
                'password' => Hash::make('123123'),
                'department_id' => $cysDept->id,
                'phone' => '0504445556',
                'role' => 'advisor'
            ],
            [
                'name' => 'د. خالد اللغوي', // مرشد 2
                'email' => 'admin3@admin.com',
                'password' => Hash::make('123123'),
                'department_id' => $ltDept->id,
                'phone' => '0507778889',
                'role' => 'advisor'
            ],
        ];

        foreach ($users as $userData) {
            // فصل الـ role عن بيانات الجدول الأساسية
            $roleName = $userData['role'];
            unset($userData['role']);

            // إنشاء أو تحديث المستخدم
            $user = User::updateOrCreate(['email' => $userData['email']], $userData);

            // تعيين الدور للمستخدم باستخدام Spatie
            $user->syncRoles([$roleName]);
        }
        //

        // // جلب معرفات الأقسام
        // $csDept = Department::where('code', 'CS')->first();
        // $cysDept = Department::where('code', 'CYS')->first();
        // $ltDept = Department::where('code', 'LT')->first();

        // $advisors = [
        //     [
        //         'name' => 'د. أحمد الحاسوبي',
        //         'email' => 'admin@admin.com',
        //         'password' => Hash::make('123123'),
        //         'department_id' => $csDept->id,
        //         'phone' => '0501112223'
        //     ],
        //     [
        //         'name' => 'د. سارة السيبراني',
        //         'email' => 'admin2@admin.com',
        //         'password' => Hash::make('123123'),
        //         'department_id' => $cysDept->id,
        //         'phone' => '0504445556'
        //     ],
        //     [
        //         'name' => 'د. خالد اللغوي',
        //         'email' => 'admin3@admin.com',
        //         'password' => Hash::make('123123'),
        //         'department_id' => $ltDept->id,
        //         'phone' => '0507778889'
        //     ],
        // ];

        // foreach ($advisors as $advisor) {
        //     User::updateOrCreate(['email' => $advisor['email']], $advisor);
        // }
    }
}
