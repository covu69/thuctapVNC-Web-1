<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class DailyController extends Controller
{
    public function dai_ly(Request $request)
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
            $baseQuery = DB::table('dai_ly')
                ->join('users', 'users.id', '=', 'dai_ly.id_nguoi_quan_ly')
                ->join('tinhtable', 'dai_ly.id_tinh', '=', 'tinhtable.id')
                ->select('dai_ly.*', 'users.name as nguoi_quan_ly', 'tinhtable.ten as tinh')
                ->where('dai_ly.deleted_at', null);
        } else {
            $id_nql = Auth::user()->id;
            $baseQuery = DB::table('dai_ly')
                ->join('users', 'users.id', '=', 'dai_ly.id_nguoi_quan_ly')
                ->join('tinhtable', 'dai_ly.id_tinh', '=', 'tinhtable.id')
                ->select('dai_ly.*', 'users.name as nguoi_quan_ly', 'tinhtable.ten as tinh')
                ->where('dai_ly.deleted_at', null)
                ->where('id_nguoi_quan_ly', $id_nql);
        }
        // Thêm điều kiện tìm kiếm theo thời gian tạo
        if ($fromDate && $toDate) {
            $baseQuery->whereBetween('dai_ly.created_at', [$fromDate, $toDate]);
            // dd($fromDate);
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
        $dai_ly = $baseQuery->paginate($itemsPerPage);
        // dd($toDate);
        // Thêm các tham số tìm kiếm vào URL của liên kết phân trang
        $dai_ly->appends([
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
        return view('project.admin.dai_ly.danhsach', compact('dai_ly', 'data', 'itemsPerPage', 'ten', 'tinh', 'nv_ql'));
    }

    public function themmoi_thanh_vien()
    {
        $tinh = DB::table('tinhtable')->get();
        $nguoi_dai_dien = DB::table('users')->whereNull('deleted_at')->get();
        return view('project.admin.dai_ly.add_thanhvien', compact('nguoi_dai_dien', 'tinh'));
    }

    public function save_thanh_vien(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'ten' => 'required',
                'ten_nha_thuoc' => 'required',
                'password' => [
                    'required',
                    'min:8',             // ít nhất 8 ký tự
                    'regex:/[a-z]/',      // ít nhất 1 chữ thường
                    'regex:/[A-Z]/',      // ít nhất 1 chữ in hoa
                    'regex:/[0-9]/',      // ít nhất 1 số
                    'regex:/[@$!%*#?&]/', // ít nhất 1 ký tự đặc biệt
                ],
                'dia_chi_nha_thuoc' => 'required',
                'thumbnail' => 'required|image|mimes:jpg,png,gif|max:10240',
                'email' => 'nullable|email',
                'so_dien_thoai' => 'required|digits_between:9,10|unique:dai_ly',
            ], [
                'ten.required' => 'Vui lòng nhập tên.',
                'ten_nha_thuoc.required' => 'Vui lòng nhập tên nhà thuốc.',
                'password.password' => 'Mật khẩu phải chứa ít nhất 6 ký tự và đáp ứng các yêu cầu.',
                'dia_chi_nha_thuoc.required' => 'Vui lòng nhập địa chỉ nhà thuốc.',
                'thumbnail.required' => 'Vui lòng tải lên một hình ảnh.',
                'thumbnail.image' => 'File phải là hình ảnh.',
                'thumbnail.mimes' => 'Hình ảnh phải có định dạng là jpeg, png hoặc gif.',
                'thumbnail.max' => 'Kích thước hình ảnh không được vượt quá 20 MB.',
                'email.email' => 'Email phải là địa chỉ email hợp lệ.',
                'so_dien_thoai.unique' => 'Số điện thoại đã tồn tại',
                'so_dien_thoai.required' => 'Vui lòng nhập số điện thoại.',
                'so_dien_thoai.digits_between' => 'Số điện thoại phải có độ dài từ 9 đến 10 chữ số.',
            ]);
            // dd($request->all());
            $input = $request->all();
            // Xử lý upload image
            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $ext = $file->getClientOriginalExtension();
                $filename = time() . '.' . $ext;
                $file->move('upload/img_dai_ly/', $filename);
            }
            $thanh_vien = DB::table('dai_ly')->insert([
                'ma_khach_hang' => $input['ma_khach_hang'],
                'ten' => $input['ten'],
                'ten_nha_thuoc' => $input['ten_nha_thuoc'],
                'email' => $input['email'],
                'so_dien_thoai' => $input['so_dien_thoai'],
                'password' => Hash::make($input['password']),
                'dia_chi_nha_thuoc' => $input['dia_chi_nha_thuoc'],
                'ma_so_thue' => $input['ma_so_thue'],
                'id_nguoi_quan_ly' => $input['id_nguoi_quan_ly'],
                'id_tinh' => $input['id_tinh'],
                'thumbnail' => $filename,
                'status' => $input['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return redirect()->route('dai_ly')->with('success', 'Thêm thành viên thành công.');
        } catch (ValidationException $e) {
            // Lỗi xảy ra, gửi thông báo lỗi và chuyển hướng về form với old input
            dd($e->errors());
        }
    }

    public function edit_thanh_vien($id)
    {
        // Lấy thông tin người quản lý của người đại lý
        $thanh_vien = DB::table('dai_ly')->where('id', $id)->whereNull('deleted_at')->first();

        // Kiểm tra xem có dữ liệu trả về hay không
        if (!$thanh_vien) {
            // Xử lý khi không tìm thấy dữ liệu, có thể chuyển hướng hoặc hiển thị thông báo lỗi
            return redirect()->route('dai_ly');
        }

        // Kiểm tra role của người dùng
        if (auth()->user()->role == 0 && $thanh_vien->id_nguoi_quan_ly != auth()->id()) {
            // Nếu role là 0 và không phải là người quản lý của người dùng hiện tại, chuyển hướng hoặc xử lý theo nhu cầu của bạn
            return redirect()->back(); // Chuyển hướng hoặc xử lý nếu có lỗi quyền
        }

        $tinh = DB::table('tinhtable')->get();
        $nguoi_dai_dien = DB::table('users')->whereNull('deleted_at')->get();

        return view('project.admin.dai_ly.edit_thanhvien', compact('thanh_vien', 'tinh', 'nguoi_dai_dien'));
    }

    public function update_thanh_vien(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'ten' => 'required',
                'ten_nha_thuoc' => 'required',
                'password' => [
                    'nullable',
                    'min:8',             // ít nhất 8 ký tự
                    'regex:/[a-z]/',      // ít nhất 1 chữ thường
                    'regex:/[A-Z]/',      // ít nhất 1 chữ in hoa
                    'regex:/[0-9]/',      // ít nhất 1 số
                    'regex:/[@$!%*#?&]/', // ít nhất 1 ký tự đặc biệt
                ],
                'dia_chi_nha_thuoc' => 'required',
                'thumbnail' => 'nullable|image|mimes:jpg,png,gif|max:10240',
                'email' => 'nullable|email',
                'so_dien_thoai' => [
                    'required',
                    'digits_between:9,10',
                    Rule::unique('dai_ly')->ignore($id),
                ],
            ], [
                'ten.required' => 'Vui lòng nhập tên.',
                'ten_nha_thuoc.required' => 'Vui lòng nhập tên nhà thuốc.',
                'password.password' => 'Mật khẩu phải chứa ít nhất 6 ký tự và đáp ứng các yêu cầu.',
                'dia_chi_nha_thuoc.required' => 'Vui lòng nhập địa chỉ nhà thuốc.',
                'thumbnail.required' => 'Vui lòng tải lên một hình ảnh.',
                'thumbnail.image' => 'File phải là hình ảnh.',
                'thumbnail.mimes' => 'Hình ảnh phải có định dạng là jpeg, png hoặc gif.',
                'thumbnail.max' => 'Kích thước hình ảnh không được vượt quá 20 MB.',
                'email.email' => 'Email phải là địa chỉ email hợp lệ.',
                'so_dien_thoai.unique' => 'Số điện thoại đã tồn tại',
                'so_dien_thoai.required' => 'Vui lòng nhập số điện thoại.',
                'so_dien_thoai.digits_between' => 'Số điện thoại phải có độ dài từ 9 đến 10 chữ số.',
            ]);
            // Kiểm tra xem có tệp tin ảnh mới được tải lên và có hợp lệ không
            if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                $file = $request->file('thumbnail');
                $ext = $file->getClientOriginalExtension();
                $filename = time() . '.' . $ext;
                $file->move('upload/img_dai_ly/', $filename);
            } else {
                // Nếu không có tệp tin mới hoặc tệp tin không hợp lệ, giữ nguyên ảnh cũ
                $filename = DB::table('dai_ly')->where('id', $id)->value('thumbnail');
            }
            // dd($filename);

            $input = $request->all();
            // Kiểm tra xem mật khẩu đã được thay đổi hay chưa
            if ($input['password'] !== null && $input['password'] !== '') {
                // Nếu mật khẩu có thay đổi, sử dụng mật khẩu mới
                $passwordHash = Hash::make($input['password']);
            } else {
                // Nếu mật khẩu không thay đổi, giữ nguyên mật khẩu cũ
                $passwordHash = DB::table('dai_ly')->where('id', $id)->value('password');
            }
            if (Auth::user() && Auth::user()->role == 1) {
                $thanh_vien = DB::table('dai_ly')->where('id', $id)->update([
                    'ma_khach_hang' => $input['ma_khach_hang'],
                    'ten' => $input['ten'],
                    'ten_nha_thuoc' => $input['ten_nha_thuoc'],
                    'email' => $input['email'],
                    'so_dien_thoai' => $input['so_dien_thoai'],
                    'password' =>  $passwordHash,
                    'dia_chi_nha_thuoc' => $input['dia_chi_nha_thuoc'],
                    'ma_so_thue' => $input['ma_so_thue'],
                    'id_nguoi_quan_ly' => $input['id_nguoi_quan_ly'],
                    'id_tinh' => $input['id_tinh'],
                    'thumbnail' => $filename,
                    'status' => $input['status'],
                    'updated_at' => now(),
                ]);
            } else {
                $thanh_vien = DB::table('dai_ly')->where('id', $id)->update([
                    'ma_khach_hang' => $input['ma_khach_hang'],
                    'ten' => $input['ten'],
                    'ten_nha_thuoc' => $input['ten_nha_thuoc'],
                    'email' => $input['email'],
                    'so_dien_thoai' => $input['so_dien_thoai'],
                    'password' =>  $passwordHash,
                    'dia_chi_nha_thuoc' => $input['dia_chi_nha_thuoc'],
                    'ma_so_thue' => $input['ma_so_thue'],
                    'id_tinh' => $input['id_tinh'],
                    'thumbnail' => $filename,
                    'status' => $input['status'],
                    'updated_at' => now(),
                ]);
            }
            // dd($thanh_vien);
            return redirect()->route('dai_ly')->with('success', 'Chỉnh sửa thành viên thành công.');
        } catch (ValidationException $e) {
            // Lỗi xảy ra, gửi thông báo lỗi và chuyển hướng về form với old input
            dd($e->errors());
        }
    }
}
