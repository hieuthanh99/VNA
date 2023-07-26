<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $staffRole = Role::where('name', 'staff')->first();

        $dvhkDepartment = Department::where('name', 'Ban Dịch vụ hành khách')->first();
        $asocDepartment = Department::where('name', 'Trung tâm Dịch vụ và Khai thác sân bay')->first();
        $dtvDepartment = Department::where('name', 'Đoàn tiếp viên')->first();

        User::create([
            'name' => 'Jackie',
            'email' => 'dvhk@gmail.com',
            'password' => bcrypt('123456789'),
            'role' => $staffRole->name,
            'department' => $dvhkDepartment->id,
        ]);

        User::create([
            'name' => 'Jackie2',
            'email' => 'asoc@gmail.com',
            'password' => bcrypt('123456789'),
            'role' => $staffRole->name,
            'department' => $asocDepartment->id,
        ]);

        User::create([
            'name' => 'Jackie3',
            'email' => 'dtv@gmail.com',
            'password' => bcrypt('123456789'),
            'role' => $staffRole->name,
            'department' => $dtvDepartment->id,
        ]);
    }
}
