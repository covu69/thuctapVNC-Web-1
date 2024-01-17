<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductsImport implements ToCollection
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Thêm logic xử lý dữ liệu từ file Excel và thêm vào cơ sở dữ liệu
            DB::table('product')->insert([
                'id_nhomthuoc' => $row[0],
                'name' => $row[1], 
                'cangnang'=>$row[2],
                'unit'=>$row[3],
                'quy_cach_dong_goi'=>$row[4],
                'quantity'=>$row[5],
                'price'=>$row[6],
                'khuyen_mai'=>$row[7],
                'id_nsx'=>$row[8],
                'nuoc_sx'=>$row[9],
                'thong_tin'=>$row[12],
                'code'=>$row[14],
            ]);
        }
    }
}
