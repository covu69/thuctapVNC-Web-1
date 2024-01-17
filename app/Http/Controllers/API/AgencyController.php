<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AgencyController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'province_id' => 'required|numeric|min:1',
        ], [
            'province_id.required' => 'Vui lòng nhập province_id',
            'province_id.numeric' => 'Province_id phố phải là một số',
            'province_id.min' => 'Province_id phố không được nhỏ hơn 1',
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
            $agency = DB::table('dai_ly')->where('id_tinh', $request->province_id)->get();
            if ($agency->isEmpty()) {
                // Xử lý khi không có dữ liệu thỏa mãn điều kiện
                $response = [
                    'code' => 0,
                    'message' => [],
                    'response' => null
                ];
            } else {
                // Xử lý khi có dữ liệu
                $formattedAgencies = $agency->map(function ($agency) {
                    return [
                        'id' => $agency->id,
                        'ten_nha_thuoc' => $agency->ten_nha_thuoc,
                        'sdt' => $agency->so_dien_thoai,
                        'dia_chi' => $agency->dia_chi_nha_thuoc,
                    ];
                })->toArray();

                $response = [
                    'code' => 0,
                    'message' => [],
                    'response' => $formattedAgencies,
                ];
            }

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 1,
                'message' => $e->getMessage(),
                'response' => null
            ], 500);
        }
    }
}
