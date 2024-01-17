<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'John Doe',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Vuco@692002'), // Bạn nên sử dụng Hash::make cho mật khẩu
            'role'=>'1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Thêm các dòng dữ liệu khác nếu cần thiết
    }
}


