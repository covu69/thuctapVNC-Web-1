<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThongtinchungController extends Controller
{
    public function showHtml($id)
    {
        $thong_tin_chung = DB::table('thong_tin_chung')->where('id', $id)->whereNull('deleted_at')->first();

        if (!$thong_tin_chung) {
            return response()->json(['error' => '404 not found'], 404);
        }

        // Sử dụng blade view để render HTML từ một file blade.php
        $htmlContent = view('API.thong_tin_chung.show', ['thong_tin_chung' => $thong_tin_chung])->render();

        return response($htmlContent, 200)->header('Content-Type', 'text/html');
    }
}
