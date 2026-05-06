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
        Role::firstOrCreate(['name' => 'registrar']);
        Role::firstOrCreate(['name' => 'advisor']);
        Role::firstOrCreate(['name' => 'chair']);
        Role::firstOrCreate(['name' => 'dean']);

        $arDept = Department::where('code', 'AR')->first();
        $cysDept = Department::where('code', 'CYS')->first();
        $ltDept = Department::where('code', 'LT')->first();

        $users = [
            [
                'name' => 'محمد المسجل',
                'email' => 'admin@admin.com',
                'password' => Hash::make('123123'),
                'department_id' => $arDept->id,
                'phone' => '0500000000',
                'role' => 'registrar'
            ],
            [
                'name' => 'د. سارة السيبراني',
                'email' => 'admin2@admin.com',
                'password' => Hash::make('123123'),
                'department_id' => $cysDept->id,
                'phone' => '0504445556',
                'role' => 'advisor'
            ],
            [
                'name' => 'د. خالد اللغوي',
                'email' => 'admin3@admin.com',
                'password' => Hash::make('123123'),
                'department_id' => $ltDept->id,
                'phone' => '0507778889',
                'role' => 'advisor'
            ],
            [
                'name' => 'د. فهد رئيس القسم',
                'email' => 'chair@admin.com',
                'password' => Hash::make('123123'),
                'department_id' => $cysDept->id,
                'phone' => '0511112222',
                'role' => 'chair'
            ],
            [
                'name' => 'د. عبدالله العميد',
                'email' => 'dean@admin.com',
                'password' => Hash::make('123123'),
                'department_id' => $arDept->id,
                'phone' => '0533334444',
                'role' => 'dean'
            ],
        ];

        foreach ($users as $userData) {

            $roleName = $userData['role'];
            unset($userData['role']);


            $user = User::updateOrCreate(['email' => $userData['email']], $userData);


            $user->syncRoles([$roleName]);
        }
        //


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
