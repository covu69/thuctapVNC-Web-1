<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::guard('customer-api')->user()->id;
        $hangtvId = Auth::guard('customer-api')->user()->id_hang_tv;
        $profile = DB::table('dai_ly')->whereNull('deleted_at')->where('id', $userId)->first();
        $thu_hang = DB::table('hang_thanh_vien')->where('id', $hangtvId)->value('title');
        $data = [
            'ten' => $profile->ten,
            'ten_nha_thuoc' => $profile->ten_nha_thuoc,
            'dia_chi' => $profile->dia_chi_nha_thuoc,
            'email' => $profile->email,
            'sdt' => $profile->so_dien_thoai,
            'tinh' => $profile->id_tinh,
            'ma_so_thue' => $profile->ma_so_thue,
            'img' => asset('upload/img_dai_ly/' . $profile->thumbnail),
            'trang_thai' => $profile->status,
            'coins' => $profile->coin,
        ];

        $tinhThanhList = DB::table('tinhtable')->get();

        $provinces = [];

        foreach ($tinhThanhList as $tinhThanh) {
            $provinces[] = [
                'id' => $tinhThanh->id,
                'ten' => $tinhThanh->ten,
            ];
        }
        if ($request->isMethod('get')) {
            $response = [
                'ten' => $data['ten'],
                'ten_nha_thuoc' => $data['ten_nha_thuoc'],
                'dia_chi' => $data['dia_chi'],
                'email' => $data['email'],
                'sdt' => $data['sdt'],
                'tinh' => $data['tinh'],
                'ma_so_thue' => $data['ma_so_thue'],
                'img' => $data['img'],
                'trang_thai' => $data['trang_thai'],
                'provinces' => $provinces,
                'thu_hang' => $thu_hang,
                'thu_hang_icon' => '',
                'coins' => $data['coins'],
                'description' => 'Lấy thông tin thành viên thành công',
            ];

            return response()->json([
                'code' => 0,
                'message' => [],
                'response' => $response,
            ], 200);
        } elseif ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [
                'ten' => 'required',
                'ten_nha_thuoc' => 'required',
                'email' => 'nullable|email',
                'dia_chi' => 'required',
                'ma_so_thue' => [
                    'nullable',
                    'regex:/^\d{10}$|^\d{10}-\d{3}$/',
                ],
                'password' => [
                    'nullable',
                    'min:8',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*#?&]/',
                ],
                'tinh' => 'required|numeric',
                'img' => 'nullable|image|mimes:jpg,png,gif|max:2048',
            ], [
                'ten.required' => 'Tên là trường bắt buộc.',
                'ten_nha_thuoc.required' => 'Tên nhà thuốc là trường bắt buộc.',
                'email.email' => 'Địa chỉ email không hợp lệ.',
                'dia_chi.required' => 'Địa chỉ là trường bắt buộc.',
                'password.min' => 'Mật khẩu phải chứa ít nhất 8 ký tự.',
                'password.regex' => 'Mật khẩu phải chứa ít nhất một chữ thường, một chữ hoa, một số và một ký tự đặc biệt.',
                'tinh.required' => 'Tỉnh là trường bắt buộc.',
                'tinh.numeric' => 'Tỉnh phải là một giá trị số.',
                'img.image' => 'File phải là hình ảnh.',
                'img.mimes' => 'Chỉ chấp nhận các định dạng hình ảnh: jpg, png, gif.',
                'img.max' => 'Kích thước ảnh không được vượt quá 2MB.',
                'ma_so_thue.regex' => 'Định dạng Mã số thuế không hợp lệ. Vui lòng nhập dạng 10 số hoặc 13 số (XXXXXXXXXX hoặc XXXXXXXXXXXXX-XXX).',
            ]);

            if ($validator->fails()) {
                $errorMessages = [];
                foreach ($validator->errors()->all() as $message) {
                    $errorMessages[] = $message;
                }

                return response()->json([
                    'code' => 1,
                    'message' => $errorMessages,
                    'response' => null
                ], 403);
            }

            try {
                // Lấy dữ liệu từ request
                $dataToUpdate = [
                    'ten' => $request->input('ten'),
                    'ten_nha_thuoc' => $request->input('ten_nha_thuoc'),
                    'email' => $request->input('email'),
                    'dia_chi_nha_thuoc' => $request->input('dia_chi'),
                    'ma_so_thue' => $request->input('ma_so_thue'),
                    'id_tinh' => $request->input('tinh'),
                ];

                // Kiểm tra và thực hiện update password
                if ($request->has('password')) {
                    $dataToUpdate['password'] = bcrypt($request->input('password'));
                }

                // Kiểm tra và thực hiện update ảnh
                if ($request->hasFile('img')) {
                    $file = $request->file('img');
                    $ext = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $ext;
                    $file->move('upload/img_dai_ly/', $filename);
                    $dataToUpdate['thumbnail'] = $filename;
                }

                DB::table('dai_ly')->where('id', $userId)->update($dataToUpdate);

                $response = [
                    'ten' => $dataToUpdate['ten'],
                    'ten_nha_thuoc' => $dataToUpdate['ten_nha_thuoc'],
                    'dia_chi' => $dataToUpdate['dia_chi_nha_thuoc'],
                    'email' => $dataToUpdate['email'],
                    'tinh' => $dataToUpdate['id_tinh'],
                    'ma_so_thue' => $dataToUpdate['ma_so_thue'],
                    'img' => $data['img'],
                    'trang_thai' => $data['trang_thai'],
                    'provinces' => $provinces,
                    'description' => 'Cập nhật thông tin thành viên thành công',
                ];

                return response()->json([
                    'code' => 0,
                    'message' => [],
                    'response' => $response,
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'code' => 1,
                    'message' => $e->getMessage(),
                    'response' => null
                ], 500);
            }
        }
    }
}
