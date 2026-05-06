<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class UserSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'advisor']);
        Role::firstOrCreate(['name' => 'chair']);
        Role::firstOrCreate(['name' => 'dean']);

        $cs  = Department::where('code', 'CS')->first();
        $cys = Department::where('code', 'CYS')->first();
        $is  = Department::where('code', 'IS')->first();
        $cen = Department::where('code', 'CEN')->first();

        $users = [
            // ══════════ المرشدون الأكاديميون ══════════
            [
                'name'          => 'د. أحمد الحاسوبي',
                'email'         => 'advisor.cs@kku.edu.sa',
                'password'      => Hash::make('123123'),
                'department_id' => $cs->id,
                'phone'         => '0501112221',
                'role'          => 'advisor',
            ],
            [
                'name'          => 'د. سارة السيبراني',
                'email'         => 'advisor.cys@kku.edu.sa',
                'password'      => Hash::make('123123'),
                'department_id' => $cys->id,
                'phone'         => '0501112222',
                'role'          => 'advisor',
            ],
            [
                'name'          => 'د. خالد النظمي',
                'email'         => 'advisor.is@kku.edu.sa',
                'password'      => Hash::make('123123'),
                'department_id' => $is->id,
                'phone'         => '0501112223',
                'role'          => 'advisor',
            ],
            [
                'name'          => 'د. منى الهندسية',
                'email'         => 'advisor.cen@kku.edu.sa',
                'password'      => Hash::make('123123'),
                'department_id' => $cen->id,
                'phone'         => '0501112224',
                'role'          => 'advisor',
            ],

            // ══════════ رؤساء الأقسام ══════════
            [
                'name'          => 'د. فهد رئيس قسم الحاسب',
                'email'         => 'chair.cs@kku.edu.sa',
                'password'      => Hash::make('123123'),
                'department_id' => $cs->id,
                'phone'         => '0511112221',
                'role'          => 'chair',
            ],
            [
                'name'          => 'د. نورة رئيسة قسم السيبراني',
                'email'         => 'chair.cys@kku.edu.sa',
                'password'      => Hash::make('123123'),
                'department_id' => $cys->id,
                'phone'         => '0511112222',
                'role'          => 'chair',
            ],
            [
                'name'          => 'د. عمر رئيس قسم نظم المعلومات',
                'email'         => 'chair.is@kku.edu.sa',
                'password'      => Hash::make('123123'),
                'department_id' => $is->id,
                'phone'         => '0511112223',
                'role'          => 'chair',
            ],
            [
                'name'          => 'د. ريم رئيسة قسم هندسة الحاسب',
                'email'         => 'chair.cen@kku.edu.sa',
                'password'      => Hash::make('123123'),
                'department_id' => $cen->id,
                'phone'         => '0511112224',
                'role'          => 'chair',
            ],

            // ══════════ العميد ══════════
            [
                'name'          => 'أ.د. عبدالله العميد',
                'email'         => 'dean@kku.edu.sa',
                'password'      => Hash::make('123123'),
                'department_id' => $cs->id,
                'phone'         => '0533334444',
                'role'          => 'dean',
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']);
            $user = User::updateOrCreate(['email' => $userData['email']], $userData);
            $user->syncRoles([$roleName]);
        }
    }
}
