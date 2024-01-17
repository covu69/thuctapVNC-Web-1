<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function add_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'number' => 'required|integer',
        ], [
            'id.required' => 'Vui lòng nhập id sản phẩm',
            'number.required' => 'Vui lòng nhập số lượng sản phẩm',
            'number.integer' => 'Số lượng sản phẩm phải là số'
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

        $check_product = DB::table('product')->where('id', $request->id)->first();
        if (!$check_product) {
            return response()->json([
                'code' => 1,
                'message' => 'Không tồn tại sản phẩm',
                'response' => null
            ], 500);
        }
        try {
            $add_cart = DB::table('cart')
                ->where('id_member', Auth::guard('customer-api')->user()->id)
                ->where('id_product', $request->id)
                ->first();

            if (!$add_cart) {
                // Nếu mục giỏ hàng không tồn tại, chèn bản ghi mới
                DB::table('cart')->insert([
                    'id_product' => $request->id,
                    'id_member' => Auth::guard('customer-api')->user()->id,
                    'so_luong' => $request->number,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // Nếu mục giỏ hàng đã tồn tại, cập nhật số lượng
                DB::table('cart')
                    ->where('id_member', Auth::guard('customer-api')->user()->id)
                    ->where('id_product', $request->id)
                    ->update([
                        'so_luong' => $add_cart->so_luong + $request->number,
                        'updated_at' => now(),
                    ]);
            }
            $cartPaginator = DB::table('cart')
                ->join('product', 'cart.id_product', '=', 'product.id')
                ->select('cart.id as gio_hang_id', 'cart.id_member', 'cart.so_luong', 'product.*')
                ->where('cart.id_member', Auth::guard('customer-api')->user()->id)
                ->orderBy('cart.updated_at', 'desc')
                ->paginate(10);

            $data = $cartPaginator->items(); // Use the items() method to get the underlying array
            $totalNumber = collect($data)->sum('so_luong');
            // Lấy giá trị của cột sp_uu_dai_gia từ mỗi phần tử trong $data và đặt nó vào một mảng mới
            $idtv = Auth::guard('customer-api')->user()->id_hang_tv;
            $totalPrice = 0;

            foreach ($data as $item) {
                $uu_thanh_vien = json_decode($item->sp_uu_dai_gia, true);
                $price = $item->price;

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
                $discountedPrice = $price * (100 - $item->khuyen_mai) / 100;
                $totalPrice += $item->so_luong * $discountedPrice;
            }
            $response = [
                'code' => 0,
                'message' => [],
                'response' => [
                    'products' => [
                        'current_page' => $cartPaginator->currentPage(),
                        'data' => collect($data)->map(function ($cartItem) {
                            $san_pham_uu_dai_gia = json_decode($cartItem->sp_uu_dai_gia, true);
                            $idHangTV = Auth::guard('customer-api')->user()->id_hang_tv;
                            $price = $cartItem->price;

                            // Kiểm tra xem chuỗi JSON có lỗi hay không
                            if (json_last_error() === JSON_ERROR_NONE && is_array($san_pham_uu_dai_gia)) {
                                // Kiểm tra xem id_hang_tv có trong mảng JSON hay không
                                foreach ($san_pham_uu_dai_gia as $item) {
                                    if ($item['id_hang_thanh_vien'] == $idHangTV) {
                                        // Nếu trùng, sử dụng giá ưu đãi
                                        $price = $item['uu_dai_gia'];
                                        break;
                                    }
                                }
                            }
                            $discountedPrice = $price * (100 - $cartItem->khuyen_mai) / 100;
                            return [
                                'gio_hang_id' => $cartItem->gio_hang_id,
                                'id_member' => $cartItem->id_member,
                                'id' => $cartItem->id,
                                'so_luong' => $cartItem->so_luong,
                                'ten_san_pham' => $cartItem->name,
                                'quy_cach_dong_goi' => $cartItem->quy_cach_dong_goi,
                                'khuyen_mai' => $cartItem->khuyen_mai,
                                'don_gia' => $price,
                                'gia_uu_dai' => $san_pham_uu_dai_gia,
                                'id_sp_km' => $cartItem->sp_km,
                                'so_luong_km' => $cartItem->sl_km,
                                'so_luong_toi_thieu' => $cartItem->sl_toi_thieu,
                                'so_luong_toi_da' => $cartItem->sl_toi_da,
                                'ngay_het_han' => $cartItem->ngay_het_han,
                                'bonus_coins' => $cartItem->coin,
                                'img_url' => null,
                                'img_sp_km' => null,
                                'discount_price' => $discountedPrice,
                            ];
                        }),
                        'first_page_url' => $cartPaginator->url(1),
                        'from' => $cartPaginator->firstItem(),
                        'last_page' => $cartPaginator->lastPage(),
                        'last_page_url' => $cartPaginator->url($cartPaginator->lastPage()),
                        'next_page_url' => $cartPaginator->nextPageUrl(),
                        'path' => $cartPaginator->path(),
                        'per_page' => $cartPaginator->perPage(),
                        'prev_page_url' => $cartPaginator->previousPageUrl(),
                        'to' => $cartPaginator->lastItem(),
                        'total' => $cartPaginator->total(),
                    ],
                    'total_number' => $totalNumber,
                    'total_price' => $totalPrice,
                    'ti_le_giam' => '0.5'
                ],
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 1,
                'message' => $e->getMessage(),
                'response' => null
            ], 500);
        }
    }

    public function index(Request $request)
    {

        $cartPaginator = DB::table('cart')
            ->join('product', 'cart.id_product', '=', 'product.id')
            ->select('cart.id as gio_hang_id', 'cart.id_member', 'cart.so_luong', 'product.*')
            ->where('cart.id_member', Auth::guard('customer-api')->user()->id)
            ->orderBy('cart.updated_at', 'desc')
            ->paginate(10);

        $data = $cartPaginator->items(); // Use the items() method to get the underlying array
        $totalNumber = collect($data)->sum('so_luong');
        // Lấy giá trị của cột sp_uu_dai_gia từ mỗi phần tử trong $data và đặt nó vào một mảng mới
        $idtv = Auth::guard('customer-api')->user()->id_hang_tv;
        $totalPrice = 0;

        foreach ($data as $item) {
            $uu_thanh_vien = json_decode($item->sp_uu_dai_gia, true);
            $price = $item->price;

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
            $discountedPrice = $price * (100 - $item->khuyen_mai) / 100;
            $totalPrice += $item->so_luong * $discountedPrice;
        }

        $response = [
            'code' => 0,
            'message' => [],
            'response' => [
                'products' => [
                    'current_page' => $cartPaginator->currentPage(),
                    'data' => collect($data)->map(function ($cartItem) {
                        $san_pham_uu_dai_gia = json_decode($cartItem->sp_uu_dai_gia, true);
                        $idHangTV = Auth::guard('customer-api')->user()->id_hang_tv;
                        $price = $cartItem->price;

                        // Kiểm tra xem chuỗi JSON có lỗi hay không
                        if (json_last_error() === JSON_ERROR_NONE && is_array($san_pham_uu_dai_gia)) {
                            // Kiểm tra xem id_hang_tv có trong mảng JSON hay không
                            foreach ($san_pham_uu_dai_gia as $item) {
                                if ($item['id_hang_thanh_vien'] == $idHangTV) {
                                    // Nếu trùng, sử dụng giá ưu đãi
                                    $price = $item['uu_dai_gia'];
                                    break;
                                }
                            }
                        }
                        $discountedPrice = $price * (100 - $cartItem->khuyen_mai) / 100;
                        return [
                            'gio_hang_id' => $cartItem->gio_hang_id,
                            'id_member' => $cartItem->id_member,
                            'id' => $cartItem->id,
                            'so_luong' => $cartItem->so_luong,
                            'ten_san_pham' => $cartItem->name,
                            'quy_cach_dong_goi' => $cartItem->quy_cach_dong_goi,
                            'khuyen_mai' => $cartItem->khuyen_mai,
                            'don_gia' => $price,
                            'gia_uu_dai' => $san_pham_uu_dai_gia,
                            'id_sp_km' => $cartItem->sp_km,
                            'so_luong_km' => $cartItem->sl_km,
                            'so_luong_toi_thieu' => $cartItem->sl_toi_thieu,
                            'so_luong_toi_da' => $cartItem->sl_toi_da,
                            'ngay_het_han' => $cartItem->ngay_het_han,
                            'bonus_coins' => $cartItem->coin,
                            'img_url' => null,
                            'img_sp_km' => null,
                            'discount_price' => $discountedPrice,
                        ];
                    }),
                    'first_page_url' => $cartPaginator->url(1),
                    'from' => $cartPaginator->firstItem(),
                    'last_page' => $cartPaginator->lastPage(),
                    'last_page_url' => $cartPaginator->url($cartPaginator->lastPage()),
                    'next_page_url' => $cartPaginator->nextPageUrl(),
                    'path' => $cartPaginator->path(),
                    'per_page' => $cartPaginator->perPage(),
                    'prev_page_url' => $cartPaginator->previousPageUrl(),
                    'to' => $cartPaginator->lastItem(),
                    'total' => $cartPaginator->total(),
                ],
                'total_number' => $totalNumber,
                'total_price' => $totalPrice,
                'ti_le_giam' => '0.5'
            ],
        ];
        return response()->json($response, 200);
    }
}
