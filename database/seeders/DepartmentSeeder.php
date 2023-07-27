<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departmentParentDVHK = DB::table('department_parents')->where('code', 'DVHK')->first();

        $departmentParentASOC = DB::table('department_parents')->where('code', 'ASOC')->first();

        $departmentParentĐTV = DB::table('department_parents')->where('code', 'ĐTV')->first();

        DB::table('departments')->insert([
            [
                'name' => 'Phòng Dịch vụ mặt đất',
                'department_parent_id' => $departmentParentDVHK->id,
                'code' => 'DVMD',
            ],
            [
                'name' => 'Phòng Dịch vụ trên không',
                'department_parent_id' => $departmentParentDVHK->id,
                'code' => 'DVTK',
            ],
            [
                'name' => 'Phòng Quản trị chi phí',
                'department_parent_id' => $departmentParentDVHK->id,
                'code' => 'QTCP',
            ],
            [
                'name' => 'Phòng Trải nghiệm khách hàng',
                'department_parent_id' => $departmentParentDVHK->id,
                'code' => 'TNKH',
            ],
            [
                'name' => 'Trung tâm Dịch vụ và Khai thác sân bay',
                'department_parent_id' => $departmentParentASOC->id,
                'code' => 'ASOC',
            ],
            [
                'name' => 'Đoàn tiếp viên',
                'department_parent_id' => $departmentParentĐTV->id,
                'code' => 'ĐTV',
            ],
        ]);
    }
}
