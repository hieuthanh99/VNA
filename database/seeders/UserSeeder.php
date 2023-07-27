<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\User;
use App\Models\Role;
use App\Models\DepartmentParent;

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

        $staffRoleAdmin = Role::where('name', 'admin')->first();

        $dvmdDepartment = Department::where('name', 'Phòng Dịch vụ mặt đất')->first();
        $dvtkDepartment = Department::where('name', 'Phòng Dịch vụ trên không')->first();
        $qtcpDepartment = Department::where('name', 'Phòng Quản trị chi phí')->first();
        $tnkhDepartment = Department::where('name', 'Phòng Trải nghiệm khách hàng')->first();
        $ttdvvktsbDepartment = Department::where('name', 'Trung tâm Dịch vụ và Khai thác sân bay')->first();
        $doantiepvienDepartment = Department::where('name', 'Đoàn tiếp viên')->first();


        $dvhkDepartment = DepartmentParent::where('code', 'DVHK')->first();
        $asocDepartment = DepartmentParent::where('code', 'ASOC')->first();
        $dtvDepartment = DepartmentParent::where('code', 'ĐTV')->first();

        User::create([
            'name' => 'system',
            'email' => 'system@yopmail.com',
            'password' => bcrypt('123456789'),
            'role' => $staffRoleAdmin->name,
            'department' => $dvmdDepartment->id,
            'department_parent_id' => $dvhkDepartment->id,
        ]);

        User::create([
            'name' => 'Jackie',
            'email' => 'dvmd@gmail.com',
            'password' => bcrypt('123456789'),
            'role' => $staffRole->name,
            'department' => $dvmdDepartment->id,
            'department_parent_id' => $dvhkDepartment->id,
        ]);

        User::create([
            'name' => 'Jackie2',
            'email' => 'dvtk@gmail.com',
            'password' => bcrypt('123456789'),
            'role' => $staffRole->name,
            'department' => $dvtkDepartment->id,
            'department_parent_id' => $dvhkDepartment->id,
        ]);

        User::create([
            'name' => 'Jackie3',
            'email' => 'qtcp@gmail.com',
            'password' => bcrypt('123456789'),
            'role' => $staffRole->name,
            'department' => $qtcpDepartment->id,
            'department_parent_id' => $dvhkDepartment->id,
        ]);

        User::create([
            'name' => 'Jackie4',
            'email' => 'tnkh@gmail.com',
            'password' => bcrypt('123456789'),
            'role' => $staffRole->name,
            'department' => $tnkhDepartment->id,
            'department_parent_id' => $dvhkDepartment->id,
        ]);

        User::create([
            'name' => 'Jackie5',
            'email' => 'asoc@gmail.com',
            'password' => bcrypt('123456789'),
            'role' => $staffRole->name,
            'department' => $ttdvvktsbDepartment->id,
            'department_parent_id' => $asocDepartment->id,
        ]);

        User::create([
            'name' => 'Jackie6',
            'email' => 'dtv@gmail.com',
            'password' => bcrypt('123456789'),
            'role' => $staffRole->name,
            'department' => $doantiepvienDepartment->id,
            'department_parent_id' => $dtvDepartment->id,
        ]);
    }
}
