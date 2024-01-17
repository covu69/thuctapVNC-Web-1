<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class VoucherController extends Controller
{
    public function voucher(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $data = $request->input('search');

        // Lưu dữ liệu tìm kiếm vào Session
        session(['search_data' => $data]);

        $baseQuery = DB::table('voucher')
            ->whereNull('deleted_at');
        // Kiểm tra xem có thông tin tìm kiếm trong Session không
        if (session()->has('search_data')) {
            $searchData = session('search_data');
            $baseQuery->where(function ($query) use ($searchData) {
                $query->where('tieu_de', 'like', '%' . $searchData . '%')
                    ->orWhere('ma_giam_gia', 'like', '%' . $searchData . '%');
            });
        }

        // Thực hiện phân trang và lấy danh sách tài khoản
        $giam_gia = $baseQuery->paginate($itemsPerPage);
        // dd($cart);
        // Thêm các tham số tìm kiếm vào URL của liên kết phân trang
        $giam_gia->appends(['search' => $data, 'itemsPerPage' => $itemsPerPage]);

        return view('project.admin.voucher.danhsach', compact('giam_gia', 'data', 'itemsPerPage'));
    }
    public function add_voucher()
    {
        $doi_tuong = DB::table('hang_thanh_vien')->select('id', 'title')->get();
        $doi_tuong = $doi_tuong->toArray();
        return view('project.admin.voucher.themmoi', compact('doi_tuong'));
    }

    public function save_voucher(Request $request)
    {

        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'tieu_de' => 'required',
                'ma_giam_gia' => 'required|unique:voucher',
                'muc_tien' => 'required|numeric',
                'ngay_bat_dau' => 'required',
                'ngay_ket_thuc' => 'required',
                'noi_dung' => 'required',
                'doi_tuong' => 'required',
            ], [
                'tieu_de.required' => 'Tiêu đề là trường bắt buộc',
                'ma_giam_gia.required' => 'Mã giảm giá là trường bắt buộc',
                'ma_giam_gia.unique' => 'Mã giảm giá đã tồn tại',
                'muc_tien.required' => 'Mức tiền là trường bắt buộc',
                'muc_tien.numeric' => 'Mức tiền phải là một số',
                'ngay_bat_dau.required' => 'Ngày bắt đầu là trường bắt buộc',
                'ngay_ket_thuc.required' => 'Ngày kết thúc là trường bắt buộc',
                'noi_dung.required' => 'Nội dung là trường bắt buộc',
                'doi_tuong.required' => 'Đối tượng là trường bắt buộc',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $selectedValues = $request->input('doi_tuong');
            $doi_tuong_array = [];
            foreach ($selectedValues as $value) {
                $doi_tuong_array[] = ['id_hang_thanh_vien' => $value];
            }
            $doi_tuong = json_encode($doi_tuong_array);
            $voucher = [
                'tieu_de' => $request->input('tieu_de'),
                'ma_giam_gia' => $request->input('ma_giam_gia'),
                'muc_tien' => $request->input('muc_tien'),
                'tong_hoa_don' => $request->input('tong_hoa_don'),
                'ngay_bat_dau' => $request->input('ngay_bat_dau'),
                'ngay_ket_thuc' => $request->input('ngay_ket_thuc'),
                'loai' => $request->input('loai'),
                'noi_dung' => $request['noi_dung'],
                'doi_tuong'=>$doi_tuong,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            // dd($voucher);
            $productId = DB::table('voucher')->insertGetId($voucher);
            DB::commit();

            // Ghi log
            Log::info('Voucher đã được lưu thành công. ID voucher: ' . $productId);

            return redirect()->route('voucher')->with('success', 'Voucher đã được lưu thành công.');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // Ghi log
            Log::error('Đã có lỗi xảy ra: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }
    public function edit_voucher($id)
    {
        $voucher = DB::table('voucher')->whereNull('deleted_at')->where('id', $id)->first();
        $options = json_decode($voucher->doi_tuong, true); 
        $doi_tuong = DB::table('hang_thanh_vien')->select('id', 'title')->get()->toArray();
        return view('project.admin.voucher.edit', compact('voucher', 'doi_tuong', 'options'));
    }
    
    public function update_voucher(Request $request, $id){
        try {
            // dd(1);
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'tieu_de' => 'required',
                'ma_giam_gia' => 'required|unique:voucher,ma_giam_gia,'.$id,
                'muc_tien' => 'required|numeric',
                'ngay_bat_dau' => 'required',
                'ngay_ket_thuc' => 'required',
                'noi_dung' => 'required',
                'doi_tuong' => 'required',
            ], [
                'tieu_de.required' => 'Tiêu đề là trường bắt buộc',
                'ma_giam_gia.required' => 'Mã giảm giá là trường bắt buộc',
                'ma_giam_gia.unique' => 'Mã giảm giá đã tồn tại',
                'muc_tien.required' => 'Mức tiền là trường bắt buộc',
                'muc_tien.numeric' => 'Mức tiền phải là một số',
                'ngay_bat_dau.required' => 'Ngày bắt đầu là trường bắt buộc',
                'ngay_ket_thuc.required' => 'Ngày kết thúc là trường bắt buộc',
                'noi_dung.required' => 'Nội dung là trường bắt buộc',
                'doi_tuong.required' => 'Đối tượng là trường bắt buộc',
            ]);
            

            if ($validator->fails()) {
                // dd($validator);
                return redirect()->back()->withErrors($validator)->withInput();
            }
            // dd(2);
            $selectedValues = $request->input('doi_tuong');
            $doi_tuong_array = [];
            foreach ($selectedValues as $value) {
                $doi_tuong_array[] = ['id_hang_thanh_vien' => $value];
            }
            $doi_tuong = json_encode($doi_tuong_array);
            $voucher = [
                'tieu_de' => $request->input('tieu_de'),
                'ma_giam_gia' => $request->input('ma_giam_gia'),
                'muc_tien' => $request->input('muc_tien'),
                'tong_hoa_don' => $request->input('tong_hoa_don'),
                'ngay_bat_dau' => $request->input('ngay_bat_dau'),
                'ngay_ket_thuc' => $request->input('ngay_ket_thuc'),
                'loai' => $request->input('loai'),
                'noi_dung' => $request['noi_dung'],
                'doi_tuong'=>$doi_tuong,
                'updated_at' => now(),
            ];
            // dd($voucher);    
            $vouchers = DB::table('voucher')->where('id',$id)->update($voucher);
            DB::commit();

            // Ghi log
            Log::info('Voucher đã được lưu thành công. ID voucher: ' . $vouchers);

            return redirect()->route('voucher')->with('success', 'Voucher đã được lưu thành công.');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // Ghi log
            Log::error('Đã có lỗi xảy ra: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }
}
