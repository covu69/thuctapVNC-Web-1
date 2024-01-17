<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class CustomUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'dai_ly';

    protected $fillable = [
        'ten', // Thay 'name' bằng tên cột thực tế trong bảng dai_ly
        'email',
        'ten_nha_thuoc',
        'password',
        'so_dien_thoai',
        'dia_chi_nha_thuoc',
        'ma_so_thue',
        'status'
    ];
    
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Các phương thức và quan hệ khác nếu cần
}

