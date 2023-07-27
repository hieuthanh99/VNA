<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentParentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('department_parents')->insert([
            [
                'name' => 'Ban Dịch vụ hành khách',
                'code' => 'DVHK',
            ],
            [
                'name' => 'Trung tâm Dịch vụ và Khai thác sân bay',
                'code' => 'ASOC',
            ],
            [
                'name' => 'Đoàn tiếp viên',
                'code' => 'ĐTV',
            ],
        ]);
    }
}
