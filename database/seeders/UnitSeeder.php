<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dvkh = Department::where('name', 'Ban Dịch vụ hành khách')->first();
    
        $units = [
            ['name' => 'Phòng Dịch vụ mặt đất', 'department_id' => $dvkh->id],
            ['name' => 'Phòng Dịch vụ trên không', 'department_id' => $dvkh->id],
            ['name' => 'Phòng Quản trị chi phí', 'department_id' => $dvkh->id],
            ['name' => 'Phòng Trải nghiệm khách hàng', 'department_id' => $dvkh->id],
        ];
    
        Unit::insert($units);
    }
}
