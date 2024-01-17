<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        // Lấy ID nhà sản xuất từ tham số truyền lên URL
        $nha_san_xuat = $request->query('nha_san_xuat');

        // Lấy nhóm thuốc từ tham số truyền lên URL
        $nhom_thuoc = $request->query('nhom_thuoc');

        // Lấy từ khóa tìm kiếm từ tham số truyền lên URL
        $searchKeyword = $request->query('search');
        // Số sản phẩm trên mỗi trang
        $perPage = $request->query('per_page', 10);
        // Thêm tham số hoạt chất từ request
        $hoat_chat_param = $request->query('hoat_chat');
        // Thêm tham số tags từ request
        $tags_param = $request->query('hashtag');
        // Thêm tham số phân loại từ request
        $phan_loai = $request->query('category');

        // Bổ sung điều kiện truy vấn cho ID nhà sản xuất
        $query = DB::table('product')->whereNull('deleted_at');

        if (!empty($nha_san_xuat)) {
            $query->where('id_nsx', $nha_san_xuat);
        }

        // Bổ sung điều kiện truy vấn cho nhóm thuốc
        if (!empty($nhom_thuoc)) {
            $query->where('id_nhomthuoc', $nhom_thuoc);
        }

        // Bổ sung điều kiện truy vấn cho tìm kiếm
        if (!empty($searchKeyword)) {
            $query->where(function ($query) use ($searchKeyword) {
                $query->where('code', 'like', '%' . $searchKeyword . '%')
                    ->orWhere('name', 'like', '%' . $searchKeyword . '%');
            });
        }

        // Bổ sung điều kiện truy vấn cho hoạt chất
        if (!empty($hoat_chat_param)) {
            $query->where(function ($query) use ($hoat_chat_param) {
                $query->orWhereJsonContains('hoatchat', ['id_hoat_chat' => $hoat_chat_param]);
            });
        }
        // Bổ sung điều kiện truy vấn cho tags
        if (!empty($tags_param)) {
            $query->where(function ($query) use ($tags_param) {
                $query->orWhereJsonContains('tags', ['id_tag' => $tags_param]);
            });
        }
        // Bổ sung điều kiện truy vấn cho phân loại
        // Bổ sung điều kiện truy vấn cho phân loại
        if ($phan_loai && in_array($phan_loai, ['khuyen_mai', 'moi', 'ban_chay', 'all'])) {
            if ($phan_loai == 'khuyen_mai') {
                $query->whereNotNull('khuyen_mai');
            } elseif ($phan_loai == 'ban_chay') {
                $query->where('sp_ban_chay', 1);
            } elseif ($phan_loai == 'moi') {
                $query->orderBy('updated_at', 'desc')->take(4);
            } else {
                // Nếu $phan_loai là 'all', không cần thêm điều kiện nào khác, vì là hiển thị tất cả sản phẩm.
            }
        }

        $products = $query->paginate($perPage);
        $pro_collection = new ProductCollection($products);
        return response()->json($pro_collection, 200);
    }
}
