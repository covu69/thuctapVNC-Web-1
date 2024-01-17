<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function Category(Request $request)
    {
        $hoatchat = $this->getCategory('hoat_chat', 'Hoạt chất', 'http://18.138.176.213/images/icons/hoat_chat.png');
        $nhomthuoc = $this->getCategory('nhom_thuoc', 'Nhóm thuốc', 'http://example.com/icon/nhom_thuoc.png');
        $nhasx = $this->getCategory('nha_san_xuat', 'Nhà sản xuất', 'http://example.com/icon/nha_san_xuat.png');

        $response = [
            'code' => 0,
            'message' => [],
            'response' => [$hoatchat, $nhomthuoc, $nhasx],
        ];

        return response()->json($response);
    }

    private function getCategory($category, $name, $icon)
    {
        $categorys = [];

        switch ($category) {
            case 'hoat_chat':
                $categorys = DB::table('hoatchat')->take(4)->get();
                break;

            case 'nhom_thuoc':
                $categorys = DB::table('nhomthuoc')->take(4)->get();
                break;

            case 'nha_san_xuat':
                $categorys = DB::table('nhasanxuat')->take(4)->get();
                break;

            default:
                // Xử lý mặc định hoặc thông báo lỗi
                abort(404, 'Danh mục không hợp lệ.');
        }

        // Kiểm tra xem có sản phẩm hay không
        if ($categorys->isEmpty()) {
            return [];
        }

        return [
            'name' => $name,
            'key' => $category,
            'icon' => $icon,
            'category' => $categorys->map(function ($category) {
                return [
                    'value' => $category->id,
                    'name' => $category->name,
                ];
            })->toArray(),
        ];
    }

    public function category_type(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'page' => 'nullable|integer',
            'search' => 'nullable',
        ], [
            'type.required' => 'Vui lòng nhập danh mục',
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();

            return response()->json([
                'code' => 1,
                'message' => $errorMessages,
                'response' => null
            ], 403);
        }

        $danh_muc = $request->type;
        $searchKeyword = $request->query('search');
        $c_type = collect();

        if ($danh_muc && in_array($danh_muc, ['hoat_chat', 'nhom_thuoc', 'nha_san_xuat'])) {
            if ($danh_muc == 'hoat_chat') {
                $c_type = DB::table('hoatchat')->whereNull('deleted_at');
            } elseif ($danh_muc == 'nhom_thuoc') {
                $c_type = DB::table('nhomthuoc')->whereNull('deleted_at');
            } else {
                $c_type = DB::table('nhasanxuat')->whereNull('deleted_at');
            }
        }

        if (!empty($searchKeyword)) {
            $c_type->where(function ($query) use ($searchKeyword) {
                $query->where('name', 'like', '%' . $searchKeyword . '%');
            });
        }

        $perPage = 2; // Số mục hiển thị trên mỗi trang
        $page = $request->input('page', 1); // Trang hiện tại

        $c_type = $c_type->paginate($perPage, ['*'], 'page', $page);

        $responseData = $c_type->map(function ($item) {
            return [
                'value' => $item->id,
                'name' => $item->name,
            ];
        });

        return response()->json([
            'code' => 0,
            'message' => [],
            'response' => [
                'current_page' => $c_type->currentPage(),
                'data' => $responseData,
                'first_page_url' => $c_type->url(1),
                'from' => $c_type->firstItem(),
                'last_page' => $c_type->lastPage(),
                'last_page_url' => $c_type->url($c_type->lastPage()),
                'next_page_url' => $c_type->nextPageUrl(),
                'path' => $c_type->path(),
                'per_page' => $c_type->perPage(),
                'prev_page_url' => $c_type->previousPageUrl(),
                'to' => $c_type->lastItem(),
                'total' => $c_type->total(),
            ],
        ]);
    }
}
