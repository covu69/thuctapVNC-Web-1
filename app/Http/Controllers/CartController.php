<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function cart(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $data = $request->input('search');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $tenId = $request->input('ten');
        $tinhId = $request->input('tinh');
        $nvQuanLyId = $request->input('nhan_vien_quan_ly');
        // Lưu dữ liệu tìm kiếm vào Session
        session(['search_data' => $data]);
        if (Auth::user() && Auth::user()->role == 1) {
            $baseQuery = DB::table('cart')
                ->select(
                    'dai_ly.ma_khach_hang',
                    'dai_ly.ten as dai_ly_ten',
                    'dai_ly.ten_nha_thuoc',
                    'tinhtable.ten as ten_tinh',
                    'users.name as ten_nguoi_quan_ly',
                    'dai_ly.so_dien_thoai',
                    DB::raw('MAX(cart.id) as max_cart_id'),
                    'cart.id_member'
                )
                ->join('dai_ly', 'cart.id_member', '=', 'dai_ly.id')
                ->leftJoin('tinhtable', 'dai_ly.id_tinh', '=', 'tinhtable.id')
                ->leftJoin('users', 'dai_ly.id_nguoi_quan_ly', '=', 'users.id')
                ->groupBy(
                    'dai_ly.ma_khach_hang',
                    'dai_ly.ten',
                    'dai_ly.ten_nha_thuoc',
                    'tinhtable.ten',
                    'users.name',
                    'dai_ly.so_dien_thoai',
                    'cart.id_member'
                );
        } else {
            $id_nql = Auth::user()->id;
            $baseQuery = DB::table('cart')
                ->select(
                    'dai_ly.ma_khach_hang',
                    'dai_ly.ten as dai_ly_ten',
                    'dai_ly.ten_nha_thuoc',
                    'dai_ly.id_nguoi_quan_ly',
                    'tinhtable.ten as ten_tinh',
                    'users.name as ten_nguoi_quan_ly',
                    'dai_ly.so_dien_thoai',
                    DB::raw('MAX(cart.id) as max_cart_id'),
                    'cart.id_member'
                )
                ->join('dai_ly', 'cart.id_member', '=', 'dai_ly.id')
                ->leftJoin('tinhtable', 'dai_ly.id_tinh', '=', 'tinhtable.id')
                ->leftJoin('users', 'dai_ly.id_nguoi_quan_ly', '=', 'users.id')
                ->where('dai_ly.id_nguoi_quan_ly', $id_nql)
                ->groupBy(
                    'dai_ly.ma_khach_hang',
                    'dai_ly.ten',
                    'dai_ly.ten_nha_thuoc',
                    'dai_ly.id_nguoi_quan_ly',
                    'tinhtable.ten',
                    'users.name',
                    'dai_ly.so_dien_thoai',
                    'cart.id_member'
                );
        }
        // Thêm điều kiện tìm kiếm theo thời gian tạo
        if ($fromDate && $toDate) {
            $baseQuery->whereBetween('dai_ly.created_at', [$fromDate, $toDate]);
        }

        // Thêm điều kiện tìm kiếm theo tên
        if ($tenId) {
            $baseQuery->where('dai_ly.id', $tenId);
        }

        // Thêm điều kiện tìm kiếm theo tỉnh
        if ($tinhId) {
            $baseQuery->where('dai_ly.id_tinh', $tinhId);
        }

        // Thêm điều kiện tìm kiếm theo nhân viên quản lý
        if ($nvQuanLyId) {
            $baseQuery->where('dai_ly.id_nguoi_quan_ly', $nvQuanLyId);
        }
        // Kiểm tra xem có thông tin tìm kiếm trong Session không
        if (session()->has('search_data')) {
            $searchData = session('search_data');
            $baseQuery->where(function ($query) use ($searchData) {
                $query->where('dai_ly.ten', 'like', '%' . $searchData . '%')
                    ->orWhere('dai_ly.ten_nha_thuoc', 'like', '%' . $searchData . '%');
            });
        }

        // Thực hiện phân trang và lấy danh sách tài khoản
        $cart = $baseQuery->paginate($itemsPerPage);
        // dd($cart);
        // Thêm các tham số tìm kiếm vào URL của liên kết phân trang
        $cart->appends([
            'search' => $data,
            'itemsPerPage' => $itemsPerPage,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'ten' => $tenId,
            'tinh' => $tinhId,
            'nhan_vien_quan_ly' => $nvQuanLyId,
        ]);
        $ten = DB::table('dai_ly')->select('id', 'ten')->whereNull('deleted_at')->get();

        $tinh = DB::table('tinhtable')->get();

        $nv_ql = DB::table('users')->whereNull('deleted_at')->get();
        // Truyền dữ liệu tới view
        return view('project.admin.cart.danhsach', compact('cart', 'data', 'itemsPerPage', 'ten', 'tinh', 'nv_ql'));
    }

    public function detail($id)
    {
        $id_check = DB::table('cart')
        ->join('dai_ly','cart.id_member','=','dai_ly.id')
        ->where('cart.id_member', $id)->first();
        if (!$id_check) {
            // Xử lý khi không tìm thấy dữ liệu
            return redirect()->route('gio_hang');
        }

        $id_nql = Auth::user()->id;
        if (auth()->user()->role == 0 && $id_check->id_nguoi_quan_ly != auth()->id()) {
            return redirect()->back(); // Chuyển hướng  nếu có lỗi quyền
        }
        $gh_detail = DB::table('cart')
            ->join('product', 'product.id', '=', 'cart.id_product')
            ->leftJoin('img_product', 'img_product.id_product', '=', 'product.id')
            ->leftJoin('dai_ly', 'dai_ly.id', '=', 'cart.id_member')
            ->select(
                'product.id',
                'product.name',
                'product.unit',
                'product.price',
                'product.khuyen_mai',
                'product.sp_uu_dai_gia',
                'cart.so_luong',
                'cart.id_member',
                'dai_ly.id_hang_tv as id_tv',
                DB::raw('MIN(img_product.thumnail) as thumnail')
            )
            ->whereNull('cart.deleted_at')
            ->where('cart.id_member', $id)
            ->groupBy(
                'product.id',
                'product.name',
                'product.unit',
                'product.price',
                'product.khuyen_mai',
                'cart.so_luong',
                'cart.id_member',
                'dai_ly.id_hang_tv',
                'product.sp_uu_dai_gia'
            )
            ->paginate(10);

        return view('project.admin.cart.detail', compact('gh_detail'));
    }
}
