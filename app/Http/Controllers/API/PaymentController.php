<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_id' => 'required|array',
            'device' => 'nullable',
            'ten' => 'required',
            'sdt' => 'required',
            'email' => 'nullable',
            'dia_chi' => 'required',
            'ma_so_thue' => 'nullable',
            'ghi_chu' => 'nullable',
            'ck_truoc' => 'required|numeric|in:0,1',
            'data_id.*' => 'nullable|numeric',
            'coin' => 'required|numeric|in:0,1',
            'voucher' => 'nullable',
            'total_price' => 'nullable'
        ], [
            'data_id.required' => 'Vui lòng nhập data id',
            'data_id.array' => 'Data id phải là một mảng',
            'coin.required' => 'vui lòng nhập coin',
            'ten.required' => 'Vui lòng nhập tên',
            'sdt.required' => 'Vui lòng nhập số điện thoại',
            'dia_chi.required' => 'Vui lòng nhập địa chỉ',
            'ck_truoc' => 'Vui lòng chọn phương thức thanh toán',
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
        // Lấy ngày hiện tại
        $currentDate = Carbon::now();

        // Format ngày thành chuỗi (vd: "2024-01-05")
        $formattedDate = $currentDate->format('Y-m-d');

        // Tạo chuỗi ngẫu nhiên 5 ký tự
        $randomString = Str::random(5);

        // Kết hợp ngày và chuỗi ngẫu nhiên
        $ma_dh = $formattedDate . $randomString;
        $use = Auth::guard('customer-api')->user()->id;
        $idtv = Auth::guard('customer-api')->user()->id_hang_tv;
        $tong_tien = 0;
        $tien = 0;
        $coins = 0;
        $voucher_value = 0;
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
                $discountedPrice = $price * (100 - $check_cart->khuyen_mai) / 100;
                // dd($discountedPrice);
                $tong_tien += $check_cart->so_luong * $discountedPrice;
            }
        }
        // dd($tong_tien);
        if (!$check_cart) {
            return response()->json([
                'code' => 1,
                'message' => [
                    "Sản phẩm không có trong giỏ hàng"
                ],
                'response' => null
            ], 500);
        }
        // dd($price);
        $coin = Auth::guard('customer-api')->user()->coin;

        if ($request->coin == 1) {
            $tien = $tong_tien;
            if ($coin > 0) {
                if ($coin < $tien) {
                    $tien -= $coin;
                    DB::table('dai_ly')
                        ->where('id', Auth::guard('customer-api')->user()->id)
                        ->update(['coin' => 0]);
                } else {
                    $coin -= $tien;
                    DB::table('dai_ly')
                        ->where('id', Auth::guard('customer-api')->user()->id)
                        ->update(['coin' => $coin]);
                }
                $coins = $coin;
                if ($request->voucher == null) {
                    $tong_tien = $tien;
                }
            } else {
                return response()->json([
                    'code' => 1,
                    'message' => [
                        "Số coin không đủ để thanh toán đơn hàng."
                    ],
                    'response' => null
                ], 200);
            }
        }
        // dd([$tong_tien,$tien]);
        // dd($coins);
        $voucher_code = "";
        if ($request->voucher) {
            $vc = DB::table('voucher')
                ->where('ma_giam_gia', $request->voucher)
                ->whereNull('deleted_at')
                ->where('ngay_bat_dau', '<=', now())
                ->where('ngay_ket_thuc', '>=', now())
                ->first();
            if ($vc) {
                $so_tien = $vc->tong_hoa_don;
                $tien_giam = $vc->muc_tien;
                if ($request->coin == 1) {
                    if ($tong_tien > $so_tien) {
                        $tong_tien = $tien - $tien_giam;
                        $voucher_code = $request->voucher;
                        $voucher_value = $tien_giam;
                    }
                } else {
                    if ($tong_tien > $so_tien) {
                        $tong_tien = $tong_tien - $tien_giam;
                        $voucher_code = $request->voucher;
                        $voucher_value = $tien_giam;
                    }
                }
            } else {
                return response()->json([
                    'code' => 1,
                    'message' => [
                        "Mã giảm giá không hợp lệ"
                    ],
                    'response' => null
                ], 200);
            }
        }
        // dd($voucher_value);
        // dd($tong_tien);
        if ($request->ck_truoc == 1) {
            $discountPercentage = 0.5;
            $discountAmount = $tong_tien * $discountPercentage / 100;
            $tong_tien -= $discountAmount;
        }

        // dd($tong_tien);
        try {
            $cartItems = [];
            foreach ($request->data_id as $dataId) {
                $cartData = DB::table('cart')
                    ->select('id_product', 'so_luong')
                    ->where('id', $dataId)
                    ->where('id_member', Auth::guard('customer-api')->user()->id)
                    ->first();

                if ($cartData) {
                    $cartItems[] = [
                        'id_product' => $cartData->id_product,
                        'so_luong' => $cartData->so_luong,
                    ];
                }
            }
            // dd($tong_tien);
            $paymentId = DB::table('payment')->insertGetId([
                'ma_don_hang' => $ma_dh,
                'device' => $request->device,
                'name' => $request->ten,
                'id_member' => Auth::guard('customer-api')->user()->id,
                'sdt' => $request->sdt,
                'email' => $request->email,
                'address' => $request->dia_chi,
                'voucher_code' => $request->voucher,
                'use_coin' => $request->coin,
                'payment_method' => $request->ck_truoc,
                'total_price' => $tong_tien,
                'voucher_code' => $voucher_code,
                'coins' => $coins,
                'voucher_value' => $voucher_value,
            ]);
            // Xóa các dòng trong bảng 'cart'
            DB::table('cart')->whereIn('id', $request->data_id)->delete();
            if ($paymentId) {
                // Cập nhật số lượng sản phẩm trong bảng 'product' và thêm vào bảng 'payment_detail'
                // Khởi tạo mảng để lưu dữ liệu payment_detail
                $paymentDetails = [];

                foreach ($cartItems as $cartItem) {
                    // Lấy giá của sản phẩm từ bảng 'product'
                    $product = DB::table('product')->where('id', $cartItem['id_product'])->first();
                    $firstImage = DB::table('img_product')->where('id_product', $cartItem['id_product'])->first();

                    // Nếu sản phẩm có giá ưu đãi
                    if ($product->sp_uu_dai_gia) {
                        $uu_thanh_vien = json_decode($product->sp_uu_dai_gia, true);

                        // Kiểm tra xem chuỗi JSON có lỗi hay không
                        if (json_last_error() === JSON_ERROR_NONE && is_array($uu_thanh_vien)) {
                            // Kiểm tra xem có ưu đãi cho thành viên hiện tại hay không
                            $price = $product->price; // Giá mặc định
                            foreach ($uu_thanh_vien as $uu_item) {
                                if ($uu_item['id_hang_thanh_vien'] == $idtv) {
                                    // Nếu trùng, sử dụng giá ưu đãi
                                    $price = $uu_item['uu_dai_gia'];
                                    break;
                                }
                            }
                        }
                    } else {
                        // Nếu không có giá ưu đãi, sử dụng giá mặc định
                        $price = $product->price;
                    }

                    // Thêm thông tin sản phẩm vào mảng $paymentDetails
                    $paymentDetails[] = [
                        'id_payment' => $paymentId,
                        'id_product' => $cartItem['id_product'],
                        'so_luong' => $cartItem['so_luong'],
                        'price' => $price,
                        'name_product' => $product->name,
                        'khuyen_mai' => $product->khuyen_mai,
                        'bonus_coins' => $product->coin,
                        'tags' => $product->tags,
                        'img_product' => ($firstImage) ? $firstImage->thumnail : null,
                    ];
                }

                // Lưu toàn bộ thông tin sản phẩm vào bảng 'payment_detail' sau khi vòng lặp kết thúc
                DB::table('payment_detail')->insert($paymentDetails);
            } else {
                // Xử lý trường hợp nếu $paymentId không hợp lệ
                // Ví dụ: Log hoặc thông báo lỗi
                Log::error("Invalid paymentId: $paymentId");
                // Hoặc có thể thực hiện các hành động khác theo yêu cầu của bạn
            }

            if ($request->ck_truoc == 0) {
                return response()->json([
                    'code' => 0,
                    'message' => [],
                    'response' => [
                        "description" => "Tạo đơn hàng thành công"
                    ],
                ], 200);
            } else {
                return response()->json([
                    'code' => 0,
                    'message' => [],
                    'response' => [
                        "description" => "Di chuyển tới màn hình thanh toán của MegaPay",
                        'url_payment' => "http://18.138.176.213/agency/megapay/va?ma_don_hang=" . $ma_dh . "&total_price=" . $tong_tien . "&email=" . $request->email . "&sdt=" . $request->sdt,
                    ],
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 1,
                'message' => $e->getMessage(),
                'response' => null
            ], 500);
        }
    }
    public function history(Request $request)
    {
        $payments = DB::table('payment')->where('id_member', Auth::guard('customer-api')->user()->id)->paginate(10);

        if ($payments) {
            $data = [];

            foreach ($payments as $paymentItem) {
                $ti_le_giam = ($paymentItem->payment_method == 1) ? 0.5 : ($paymentItem->payment_method == 0 ? null : '');

                $data[] = [
                    'id' => $paymentItem->id,
                    'ma_don_hang' => $paymentItem->ma_don_hang,
                    'dia_chi' => $paymentItem->address,
                    'created_at' => $paymentItem->created_at,
                    'ck_truoc' => $paymentItem->payment_method,
                    'ti_le_giam' => $ti_le_giam,
                    'trang_thai' => $paymentItem->payment_status,
                    'voucher' => $paymentItem->voucher_code !== '' ? $paymentItem->voucher_code : null,
                    'voucher_value' => $paymentItem->voucher_value == 0 ? null : $paymentItem->voucher_value,
                    'coins' => $paymentItem->use_coin,
                    'coin_value' => $paymentItem->coins,
                    'ghi_chu' => $paymentItem->ghi_chu !== '' ? $paymentItem->ghi_chu : null,
                    'tong_tien' => $paymentItem->total_price,
                ];
            }

            return response()->json([
                'code' => 0,
                'message' => [],
                'response' => [
                    'current_page' => $payments->currentPage(),
                    'data' => $data,
                    'first_page_url' => $payments->url(1),
                    'from' => $payments->firstItem(),
                    'last_page' => $payments->lastPage(),
                    'last_page_url' => $payments->url($payments->lastPage()),
                    'next_page_url' => $payments->nextPageUrl(),
                    'path' => $payments->url($payments->currentPage()),
                    'per_page' => $payments->perPage(),
                    'prev_page_url' => $payments->previousPageUrl(),
                    'to' => $payments->lastItem(),
                    'total' => $payments->total(),
                ],
            ], 200);
        } else {
            return response()->json([
                'code' => 0,
                'message' => [
                    'No payment history available.'
                ],
                'response' => [],
            ], 200);
        }
    }

    public function history_detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ], [
            'id.required' => 'Không có đơn hàng',
            'id.numeric' => 'Phải là một số',
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
        $check_payment = DB::table('payment')->where('id', $request->id)->where('id_member', Auth::guard('customer-api')->user()->id)->first();
        if (!$check_payment) {
            return response()->json([
                'code' => 1,
                'message' => [
                    'Không tồn tại đơn hàng'
                ],
                'response' => null
            ], 500);
        }
        try {
            $detail = DB::table('payment_detail')
                ->join('payment', 'payment_detail.id_payment', '=', 'payment.id')
                ->select('payment.*', 'payment_detail.*', 'payment.created_at as ngay')
                ->where('payment_detail.id_payment', $request->id)
                ->paginate(10);
            $total_price = 0;
            $transferred_value = 0;
            if ($detail) {
                $data = [];
                $totalQuantity = 0;
                foreach ($detail as $item) {
                    $tags = json_decode($item->tags, true);
                    $dateTime = $item->ngay;
                    $discountedPrice = $item->price * (100 - $item->khuyen_mai) / 100;
                    $tien_tra = $item->total_price;
                    $total_price += $discountedPrice * $item->so_luong;
                    $coins = $item->coins == 0 ? null : $item->coins;
                    $voucher_code = $item->voucher_code;
                    $voucher_value = $item->voucher_value;
                    $transferred = $item->payment_method;
                    $ghi_chu = $item->ghi_chu;
                    $discount_factor =  0.5 / 100;
                    $transferred_value = ($total_price - ($voucher_value + ($coins * 1)))*$discount_factor;
                    $formattedTags = [];

                    // Check if $tags is not null before iterating
                    if ($tags !== null) {
                        foreach ($tags as $tag) {
                            $hashtag = DB::table('hashtag')->where('id', $tag['id_tag'])->first();

                            if ($hashtag) {
                                $formattedTags[] = [
                                    'key' => 'hashtag',
                                    'value' => $tag['id_tag'],
                                    'name' => '#' . $hashtag->name,
                                ];
                            }
                        }
                    }
                    $totalQuantity += $item->so_luong;
                    $data[] = [
                        'so_luong' => $item->so_luong,
                        'don_gia' => $item->price,
                        'discount_price' => $item->khuyen_mai !== null ? $item->price - ($item->price * $item->khuyen_mai / 100) : $item->price,
                        'bonus_coins' => $item->bonus_coins == 0 ? null : $item->bonus_coins,
                        'id' => $item->id_product,
                        'ten_san_pham' => $item->name_product,
                        'img_url' => url(asset('uploads/img_sp/' . $item->img_product)),
                        'khuyen_mai' => $item->khuyen_mai,
                        'detail_url' => url('/api/product/detail/' . $item->id_product),
                        'tags' => $formattedTags,
                    ];
                }
            }
            $total_order_price = $total_price;
            // dd([$discount_factor,$transferred_value]);   
            return response()->json([
                'code' => 0,
                'message' => [],
                'response' => [
                    'products' => [
                        'current_page' => $detail->currentPage(),
                        'data' => $data,
                        'first_page_url' => $detail->url(1),
                        'from' => $detail->firstItem(),
                        'last_page' => $detail->lastPage(),
                        'last_page_url' => $detail->url($detail->lastPage()),
                        'next_page_url' => $detail->nextPageUrl(),
                        'path' => $detail->url($detail->currentPage()),
                        'per_page' => $detail->perPage(),
                        'prev_page_url' => $detail->previousPageUrl(),
                        'to' => $detail->lastItem(),
                        'total' => $detail->total(),
                    ],
                    'total_products' => $totalQuantity,
                    'date_time' => $dateTime,
                    'total_price' => $total_price,
                    'price' => $tien_tra,
                    'coin' => $coins,
                    'coin_value' => $coins * 1 !== 0 ? $coins * 1 : null,
                    'coin_bonus' => null,
                    'voucher' => !empty($voucher_code) ? $voucher_code : null,
                    'voucher_value' => !empty($voucher_value) ? $voucher_value : null,
                    'transferred' => $transferred == 1 ? 0.5 : null,
                    'transferred_value' => $transferred == 1 ? $transferred_value : null,
                    'ghi_chu' => $ghi_chu,
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
}
