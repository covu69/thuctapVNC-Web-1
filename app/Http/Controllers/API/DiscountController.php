<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    public function discount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_id' => 'required|array',
            'data_id.*' => 'nullable|numeric',
            'coin' => 'required|numeric|in:0,1',
            'voucher' => 'nullable'
        ], [
            'data_id.required' => 'Vui lòng nhập data id',
            'data_id.array' => 'Data id phải là một mảng',
            'coin.required' => 'vui lòng nhập coin',
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
        $use = Auth::guard('customer-api')->user()->id;
        $idtv = Auth::guard('customer-api')->user()->id_hang_tv;
        $tong_tien = 0;
        foreach ($request->data_id as $dataId) {
            $check_cart = DB::table('cart')
                ->join('product', 'cart.id_product', '=', 'product.id')
                ->select('cart.id as gio_hang_id', 'cart.id_member', 'cart.so_luong', 'product.*')
                ->where('cart.id', $dataId)
                ->where('cart.id_member', $use)
                ->first();

            if ($check_cart) {
                $uu_thanh_vien = json_decode($check_cart->sp_uu_dai_gia, true);
                $price = $check_cart->price;

                // Kiểm tra xem chuỗi JSON có lỗi hay không
                if (json_last_error() === JSON_ERROR_NONE && is_array($uu_thanh_vien)) {
                    // Kiểm tra xem id_hang_tv có trong mảng JSON hay không
                    foreach ($uu_thanh_vien as $uu_item) {
                        if ($uu_item['id_hang_thanh_vien'] == $idtv) {
                            // Nếu trùng, sử dụng giá ưu đãi
                            $price = $uu_item['uu_dai_gia'];
                            break;
                        }
                    }
                }

                // Tính tổng giá cho từng sản phẩm
                $discountedPrice = $price * (100 - $check_cart->khuyen_mai) / 100;
                $tong_tien += $check_cart->so_luong * $discountedPrice;
            }
        }
        if (!$check_cart) {
            return response()->json([
                'code' => 1,
                'message' => [
                    "Sản phẩm không có trong giỏ hàng"
                ],
                'response' => null
            ], 500);
        }
        $coin_available = 0;
        $voucher_available = 0;
        $coin_description = "";
        $voucher_description = "";
        $money = 0;
        $coin = Auth::guard('customer-api')->user()->coin;

        if ($request->coin == 1) {
            if ($coin == 0) {
                $coin_description = " Khong du coin ";
            }
            if ($coin > 0) {
                $coin_available = 1;
                $coin_description = "Sử dụng " . $coin . " coin được giảm giá " . $coin . " VND";
                $money += $coin;
            }
        }

        if ($request->voucher) {
            $vc = DB::table('voucher')
                ->where('ma_giam_gia', $request->voucher)
                ->whereNull('deleted_at')
                ->where('ngay_bat_dau', '<=', now())
                ->where('ngay_ket_thuc', '>=', now())
                ->first();
            // dd($vc);
            if ($vc) {
                $so_tien = $vc->tong_hoa_don;
                $tien_giam = $vc->muc_tien;

                if ($tong_tien > $so_tien) {
                    $voucher_available = 1;
                    $voucher_description = "Sử dụng voucher " . $request->voucher . " được giảm " . $tien_giam . "VND";
                    $money += $tien_giam;
                } else {
                    $voucher_description = "Voucher không hợp lệ do đơn hàng chưa đạt giá trị tối thiểu " . $so_tien . " VND";
                }
            } else {
                $voucher_description = "Mã giảm giá không hợp lệ";
            }
        }

        return response()->json([
            'code' => 0,
            'message' => [],
            'response' => [
                'voucher_available' => $voucher_available,
                'coin_available' => $coin_available,
                'money' => $money,
                'voucher_description' => $voucher_description,
                'coin_description' => $coin_description,
            ],
        ], 200);
    }
}
