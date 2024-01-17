<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $news = DB::table('news')->whereNull('deleted_at')->paginate(10);
        $data = [];

        foreach ($news as $item) {
            $data[] = [
                'id' => $item->id,
                'tieu_de' => $item->tieu_de,
                'mo_ta' => strip_tags(html_entity_decode($item->mo_ta)),
                'img' => asset('uploads/news/' . $item->thumnail),
                'ngay_cong_khai' => $item->ngay_cong_khai,
                'url' => 'http://18.138.176.213/system/news/' . $item->id
            ];
        }

        return response()->json([
            'code' => 0,
            'message' => [],
            'response' => [
                'current_page' => $news->currentPage(),
                'data' => $data,
                'first_page_url' => $news->url(1),
                'from' => $news->firstItem(),
                'last_page' => $news->lastPage(),
                'last_page_url' => $news->url($news->lastPage()),
                'next_page_url' => $news->nextPageUrl(),
                'path' => $news->url($news->currentPage()),
                'per_page' => $news->perPage(),
                'prev_page_url' => $news->previousPageUrl(),
                'to' => $news->lastItem(),
                'total' => $news->total(),
            ]
        ], 200);
    }
}
