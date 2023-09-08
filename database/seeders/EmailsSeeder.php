<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lấy dữ liệu từ bảng users và departments và chèn vào bảng emails
        $emailsData = [];
        
        // SELECT email từ bảng users
        $usersData = DB::table('users')->get();

        // SELECT name từ bảng departments
        $departmentsData = DB::table('departments')->get();

        // Lặp qua dữ liệu từ bảng users và departments để tạo dữ liệu cho bảng emails
        foreach ($usersData as $user) {
            foreach ($departmentsData as $department) {
                if($user->role !== 'admin') {
                    if($user->department == $department->id) {
                        $emailsData[] = [
                            'email' => $user->email,
                            'department_name' => $department->name,
                        ];
                    }
                }
            }
        }

        // Chèn dữ liệu vào bảng emails
        DB::table('emails')->insert($emailsData);
    }
}
