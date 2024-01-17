<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    public function voucher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_id' => 'required|array',
            'data_id.*'=>'nullable|numeric',
        ], [
            'data_id.required' => 'Vui lòng nhập data id',
            'data_id.array' => 'Data id phải là một mảng',
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
        foreach($request->data_id as $dataId){
            $check_cart = DB::table('cart')->where('id', $dataId)->where('id_member', $use)->first();
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
        $idHangTV = Auth::guard('customer-api')->user()->id_hang_tv;
        $vcherCollection = DB::table('voucher')->whereNull('deleted_at')->get();

        // Kiểm tra xem collection có rỗng hay không
        if ($vcherCollection->isEmpty()) {
            return response()->json([
                'code' => 1,
                'message' => ["Không có voucher nào khả dụng"],
                'response' => null
            ], 500);
        }

        // Lặp qua mỗi voucher trong collection
        $vouchers = [];
        foreach ($vcherCollection as $voucher) {
            $doiTuongArray = json_decode($voucher->doi_tuong, true);

            // Kiểm tra xem có id_hang_thanh_vien trùng với id_hang_tv hay không
            foreach ($doiTuongArray as $doiTuong) {
                if ($doiTuong['id_hang_thanh_vien'] == $idHangTV) {
                    $vouchers[] = [
                        'id' => $voucher->id,
                        'title' => $voucher->tieu_de,
                        'value' => $voucher->ma_giam_gia,
                        'content' => $voucher->noi_dung
                    ];
                    break;
                }
            }
        }
        // Kiểm tra xem mảng vouchers có rỗng hay không
        if (empty($vouchers)) {
            return response()->json([
                'code' => 1,
                'message' => ["Bạn không có voucher nào"],
                'response' => null
            ], 200);
        }
        return response()->json([
            'code' => 1,
            'message' => [],
            'response' => $vouchers
        ], 200);
    }
}
