<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Role;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Department::create([
            'name' => 'Ban Dịch vụ hành khách',
            'code' => 'DVHK',
        ]);

        Department::create([
            'name' => 'Trung tâm Dịch vụ và Khai thác sân bay',
            'code' => 'ASOC',
        ]);

        Department::create([
            'name' => 'Đoàn tiếp viên',
            'code' => 'ĐTV',
        ]);
    }
}
