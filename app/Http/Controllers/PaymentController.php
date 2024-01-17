<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function payment(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $data = $request->input('search');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $tenId = $request->input('ten');
        $tinhId = $request->input('tinh');
        $nvQuanLyId = $request->input('nhan_vien_quan_ly');
        $trangthai = $request->input('status');
        // Lưu dữ liệu tìm kiếm vào Session
        session(['search_data' => $data]);
        if (Auth::user() && Auth::user()->role == 1) {
            $baseQuery = DB::table('payment')
                ->join('dai_ly', 'payment.id_member', '=', 'dai_ly.id')
                ->leftJoin('tinhtable', 'dai_ly.id_tinh', '=', 'tinhtable.id')
                ->leftJoin('users', 'dai_ly.id_nguoi_quan_ly', '=', 'users.id')
                ->select(
                    'dai_ly.ma_khach_hang',
                    'dai_ly.ten as dai_ly_ten',
                    'dai_ly.ten_nha_thuoc',
                    'tinhtable.ten as ten_tinh',
                    'users.name as ten_nguoi_quan_ly',
                    'dai_ly.so_dien_thoai',
                    'payment.created_at as ngay',
                    'payment.*'
                )
                ->distinct();
        } else {
            $id_nql = Auth::user()->id;
            $baseQuery = DB::table('payment')
                ->join('dai_ly', 'payment.id_member', '=', 'dai_ly.id')
                ->leftJoin('tinhtable', 'dai_ly.id_tinh', '=', 'tinhtable.id')
                ->leftJoin('users', 'dai_ly.id_nguoi_quan_ly', '=', 'users.id')
                ->select(
                    'dai_ly.ma_khach_hang',
                    'dai_ly.id_nguoi_quan_ly',
                    'dai_ly.ten as dai_ly_ten',
                    'dai_ly.ten_nha_thuoc',
                    'tinhtable.ten as ten_tinh',
                    'users.name as ten_nguoi_quan_ly',
                    'dai_ly.so_dien_thoai',
                    'payment.created_at as ngay',
                    'payment.*'
                )
                ->where('dai_ly.id_nguoi_quan_ly', $id_nql)
                ->distinct();
        }
        // Thêm điều kiện tìm kiếm theo thời gian tạo
        if ($fromDate && $toDate) {
            $baseQuery->whereBetween('payment.created_at', [$fromDate, $toDate]);
        }
        // Thêm điều kiện theo trạng thái
        $trangthai = $request->input('status');
        if ($trangthai !== null) {
            $baseQuery->where('payment_status', $trangthai);
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
        $payment = $baseQuery->paginate($itemsPerPage);
        // dd($cart);
        // Thêm các tham số tìm kiếm vào URL của liên kết phân trang
        $payment->appends([
            'search' => $data,
            'itemsPerPage' => $itemsPerPage,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'ten' => $tenId,
            'tinh' => $tinhId,
            'nhan_vien_quan_ly' => $nvQuanLyId,
            'status' => $trangthai
        ]);
        $ten = DB::table('dai_ly')->select('id', 'ten')->whereNull('deleted_at')->get();

        $tinh = DB::table('tinhtable')->get();

        $nv_ql = DB::table('users')->whereNull('deleted_at')->get();

        $trang_thai = DB::table('payment')->whereNull('deleted_at')->select('payment_status')->distinct()->get();
        // Truyền dữ liệu tới view
        return view('project.admin.payment.list', compact('payment', 'data', 'itemsPerPage', 'ten', 'tinh', 'nv_ql', 'trang_thai'));
    }

    public function view($id)
    {
        $id_check = DB::table('payment')
            ->join('dai_ly', 'dai_ly.id', '=', 'payment.id_member')
            ->where('payment.id', $id)->first();
        if (!$id_check) {
            // Xử lý khi không tìm thấy dữ liệu
            return redirect()->route('payment');
        }
        $id_nql = Auth::user()->id;
        if (auth()->user()->role == 0 && $id_check->id_nguoi_quan_ly != auth()->id()) {
            return redirect()->back(); // Chuyển hướng  nếu có lỗi quyền
        }
        $payment_view = DB::table('payment')
            ->join('payment_detail', 'payment.id', '=', 'payment_detail.id_payment')
            ->join('product', 'payment_detail.id_product', '=', 'product.id')
            ->leftJoin('img_product', 'img_product.id_product', '=', 'product.id')
            ->leftJoin('dai_ly', 'dai_ly.id', '=', 'payment.id_member')
            ->select(
                'product.id',
                'product.name',
                'product.unit',
                'product.khuyen_mai',
                'product.sp_uu_dai_gia',
                'dai_ly.id_hang_tv as id_tv',
                'payment.voucher_code',
                'payment.voucher_value',
                'payment.payment_method',
                'payment.use_coin',
                'payment.coins',
                DB::raw('MIN(img_product.thumnail) as thumnail'),
                'payment_detail.so_luong as soluong',
                'payment_detail.price as dongia',
                'payment.total_price'
            )
            ->whereNull('payment.deleted_at')
            ->where('payment.id', $id)
            ->groupBy(
                'product.id',
                'product.name',
                'product.unit',
                'product.khuyen_mai',
                'dai_ly.id_hang_tv',
                'product.sp_uu_dai_gia',
                'so_luong',
                'payment.payment_method',
                'payment.voucher_code',
                'payment.voucher_value',
                'payment.use_coin',
                'payment.coins',
                'payment_detail.so_luong',
                'payment_detail.price',
                'payment.total_price'
            )
            ->paginate(1);
        $totalPrice = DB::table('payment')->where('id', $id)->first();
        $payment_detail = DB::table('payment_detail')->where('id_payment', $id)->get();
        $originalPrice = 0;
        foreach ($payment_detail as $detail) {
            // Tính giá trị sau khuyến mãi (nếu có)
            $discountedPrice = $detail->price * (1 - $detail->khuyen_mai / 100);

            // Tính tổng số tiền, tính cả khuyến mãi
            $originalPrice += $detail->so_luong * $discountedPrice;
        }
        // dd($originalPrice);
        return view('project.admin.payment.view', compact('payment_view', 'totalPrice', 'originalPrice'));
    }

    public function edit_payment($id)
    {
        $payment = DB::table('payment')
            ->join('dai_ly', 'dai_ly.id', '=', 'payment.id_member')
            ->where('payment.id', $id)
            ->whereNull('payment.deleted_at')
            ->first();
        if (!$payment) {
            // Xử lý khi không tìm thấy dữ liệu
            return redirect()->route('payment');
        }
        $id_nql = Auth::user()->id;
        if (auth()->user()->role == 0 && $payment->id_nguoi_quan_ly != auth()->id()) {
            return redirect()->back(); // Chuyển hướng  nếu có lỗi quyền
        }
        // Chuyển đổi định dạng ngày tháng
        $payment->created_at = \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d');
        return view('project.admin.payment.edit', compact('payment'));
    }

    public function update_payment(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'ma_don_hang' => [
                    'required',
                    Rule::unique('payment')->ignore($id),
                ],
            ], [
                'ma_don_hang.unique' => 'Mã đơn hàng đã tồn tại',
                'ma_don_hang.required' => 'Vui lòng nhập mã đơn hàng.',
            ]);

            // // Kiểm tra xem có tệp tin ảnh mới được tải lên và có hợp lệ không
            // if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            //     $file = $request->file('thumbnail');
            //     $ext = $file->getClientOriginalExtension();
            //     $filename = time() . '.' . $ext;
            //     $file->move('upload/img_dai_ly/', $filename);
            // } else {
            //     // Nếu không có tệp tin mới hoặc tệp tin không hợp lệ, giữ nguyên ảnh cũ
            //     $filename = DB::table('dai_ly')->where('id', $id)->value('thumbnail');
            // }
            $input = $request->all();
            // dd($input);
            $payment = DB::table('payment')->where('id', $id)->update([
                'ma_don_hang' => $input['ma_don_hang'],
                'payment_method' => $input['payment_method'],
                'payment_status' => $input['payment_status'],
                'updated_at' => now(),
            ]);
            // dd($payment);
            return redirect()->route('payment')->with('success', 'Chỉnh sửa thành viên thành công.');
        } catch (ValidationException $e) {
            dd($e->errors());
        }
    }

    public function muahang(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $data = $request->input('search');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $tenId = $request->input('ten');
        $tinhId = $request->input('tinh');
        $nvQuanLyId = $request->input('nhan_vien_quan_ly');
        $trangthai = $request->input('status');
        // Lưu dữ liệu tìm kiếm vào Session
        session(['search_data' => $data]);
        $baseQuery = DB::table('payment')
            ->join('dai_ly', 'payment.id_member', '=', 'dai_ly.id')
            ->leftJoin('tinhtable', 'dai_ly.id_tinh', '=', 'tinhtable.id')
            ->leftJoin('users', 'dai_ly.id_nguoi_quan_ly', '=', 'users.id')
            ->whereNull('payment.deleted_at')
            ->select(
                'dai_ly.ma_khach_hang',
                'dai_ly.id_hang_tv',
                'dai_ly.ten as dai_ly_ten',
                'dai_ly.ten_nha_thuoc',
                'tinhtable.ten as ten_tinh',
                'users.name as ten_nguoi_quan_ly',
                'dai_ly.so_dien_thoai',
                'payment.id_member',
                DB::raw('SUM(payment.total_price) as total_price')
            )
            ->groupBy(
                'payment.id_member',
                'dai_ly.ma_khach_hang',
                'dai_ly.id_hang_tv',
                'dai_ly.ten',
                'dai_ly.ten_nha_thuoc',
                'tinhtable.ten',
                'users.name',
                'dai_ly.so_dien_thoai'
            );

        if ($fromDate && $toDate) {
            $fromDate = $fromDate . ' 00:00:00';
            $toDate = $toDate . ' 23:59:59';
            $baseQuery->whereBetween('payment.created_at', [$fromDate, $toDate]);
        }
        // Thêm điều kiện theo trạng thái
        $trangthai = $request->input('status');
        if ($trangthai !== null) {
            $baseQuery->where('payment_status', $trangthai);
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
        $payment = $baseQuery->paginate($itemsPerPage);
        // dd($cart);
        // Thêm các tham số tìm kiếm vào URL của liên kết phân trang
        $payment->appends([
            'search' => $data,
            'itemsPerPage' => $itemsPerPage,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'ten' => $tenId,
            'tinh' => $tinhId,
            'nhan_vien_quan_ly' => $nvQuanLyId,
            'status' => $trangthai
        ]);
        $ten = DB::table('dai_ly')->select('id', 'ten')->whereNull('deleted_at')->get();

        $tinh = DB::table('tinhtable')->get();

        $nv_ql = DB::table('users')->whereNull('deleted_at')->get();

        $trang_thai = DB::table('payment')->whereNull('deleted_at')->select('payment_status')->distinct()->get();
        // Truyền dữ liệu tới view
        return view('project.admin.payment.muahang', compact('payment', 'data', 'itemsPerPage', 'ten', 'tinh', 'nv_ql', 'trang_thai'));
    }
}
