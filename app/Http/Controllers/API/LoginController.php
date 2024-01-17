<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\CustomUser;

class LoginController extends Controller
{
    public function register(Request $request)
    {
        // $requiredFields = ['ten', 'sdt', 'password', 'tinh', 'dia_chi', 'ten_nha_thuoc'];
        // $missingFields = [];

        // foreach ($requiredFields as $field) {
        //     if (empty($request->input($field))) {
        //         $missingFields[] = "Vui lòng nhập $field";
        //     }
        // }

        // if (!empty($missingFields)) {
        //     return response()->json([
        //         'code' => 1,
        //         'message' => $missingFields,
        //         'response' => null
        //     ], 403);
        // }
        // dd($request->all());
        $validator = Validator::make($request->all(), [
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
            'dia_chi' => 'required',
            'img' => 'required|image|mimes:jpg,png,gif|max:10240',
            'email' => 'nullable|email',
            'sdt' => 'required|digits_between:9,10|unique:dai_ly,so_dien_thoai',
            'tinh' => 'required|numeric|min:1'
        ], [
            'ten.required' => 'Vui lòng nhập tên.',
            'ten_nha_thuoc.required' => 'Vui lòng nhập tên nhà thuốc.',
            'password.password' => 'Mật khẩu phải chứa ít nhất 6 ký tự và đáp ứng các yêu cầu.',
            'dia_chi.required' => 'Vui lòng nhập địa chỉ nhà thuốc.',
            'img.required' => 'Vui lòng tải lên một hình ảnh.',
            'img.image' => 'File phải là hình ảnh.',
            'img.mimes' => 'Hình ảnh phải có định dạng là jpeg, png hoặc gif.',
            'img.max' => 'Kích thước hình ảnh không được vượt quá 20 MB.',
            'email.email' => 'Email phải là địa chỉ email hợp lệ.',
            'sdt.unique' => 'Số điện thoại đã tồn tại',
            'sdt.required' => 'Vui lòng nhập số điện thoại.',
            'sdt.digits_between' => 'Số điện thoại phải có độ dài từ 9 đến 10 chữ số.',
            'tinh.required' => 'Vui lòng chọn tỉnh',
            'tinh.numeric' => 'Phải là số',
            'tinh.min'=>'Tỉnh không được nhỏ hơn 1',
        ]);
        // dd($request->all());
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
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('upload/img_dai_ly/', $filename);
        }
        try {
            $user = DB::table('dai_ly')->insert([
                'ten' => $request->ten,
                'email' => $request->email,
                'password' => Hash::make($request['password']),
                'status' => 0,
                'ten_nha_thuoc' => $request->ten_nha_thuoc,
                'id_tinh' => $request->tinh,
                'dia_chi_nha_thuoc' => $request->dia_chi,
                'so_dien_thoai' => $request->sdt,
                'id_nguoi_quan_ly' => 1,
                'thumbnail' => $filename,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return response()->json([
                'code' => 0,
                'message' => [],
                'response' => [
                    'description' => 'Tạo tài khoản thành công',
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 1,
                'message' => $e->getMessage(),
                'response' => null
            ], 500);
        }
    }


    public function login(Request $request)
    {
        // dd(1);
        // Kiểm tra xem các trường bắt buộc có hiện diện và không trống rỗng hay không
        if (!$request->filled('username') || !$request->filled('password')) {
            return response()->json([
                'code' => 1,
                'message' => ['Vui lòng nhập đầy đủ thông tin.'],
                'response' => null
            ], 403);
        }
        // Kiểm tra hợp lệ
        $data = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Vui lòng không để trống',
            'password.required' => 'Vui lòng nhập mật khẩu'
        ]);
        // dd($data);


        // Kiểm tra xem username có phải là số điện thoại hay không
        if (!is_numeric($data['username'])) {
            return response()->json([
                'errors' => 'Định dạng số điện thoại không hợp lệ'
            ], 400);
        }


        // Check if the phone number exists in the table
        $userCount = DB::table('dai_ly')
            ->where('so_dien_thoai', $data['username'])
            ->count();

        if ($userCount > 0) {
            // Retrieve user data
            $user = DB::table('dai_ly')
                ->where('so_dien_thoai', $data['username'])
                ->first();

            // Check password
            if ($user && Hash::check($data['password'], $user->password)) {
                $eloquentUser = CustomUser::find($user->id);

                // Return successful response
                if ($eloquentUser) {
                    $token = $eloquentUser->createToken('RestaurantCustomerAuth')->accessToken;
                    return response()->json([
                        'code' => 0,
                        'message' => [],
                        'response' => [
                            'id' => $eloquentUser->id,
                            'ten' => $eloquentUser->ten,
                            'sdt' => $eloquentUser->so_dien_thoai,
                            'email' => $eloquentUser->email,
                            'ten_nha_thuoc' => $eloquentUser->ten_nha_thuoc,
                            'dia_chi' => $eloquentUser->dia_chi_nha_thuoc,
                            'ma_so_thue' => $eloquentUser->ma_so_thue,
                            'trang_thai' => $eloquentUser->status,
                            'token' => $token,
                            'description' => 'Đăng nhập thành công',
                        ],
                    ], 200);
                }
            } else {
                // Return invalid credentials error
                return response()->json([
                    'errors' => 'Invalid credentials'
                ], 401);
            }
        } else {
            // Return phone number not found error
            return response()->json([
                'errors' => 'Vui lòng kiểm tra lại tài khoản mật khẩu của bạn!'
            ], 404);
        }
    }


    public function logout(Request $request)
    {
        $user = Auth::guard('customer-api')->user();
        // dd($user);
        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => 'Người dùng không hợp lệ',
                'response' => null
            ], 200);
        }
        if ($user) {
            $accessToken = $user->token();

            if ($accessToken) {
                $accessToken->revoke();

                return response()->json([
                    'code' => 0,
                    'message' => 'Phiên làm việc đã hết hạn',
                    'response' => null,
                ], 200);
            }
        }

        return response()->json([
            'code' => 1,
            'message' => 'Mã thông báo không hợp lệ',
            'response' => null,
        ], 401);
    }
}
