<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $idHangTV = Auth::guard('customer-api')->user()->id_hang_tv;
        $san_pham_uu_dai_gia = json_decode($this->sp_uu_dai_gia, true);
        $price = $this->price;

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
        $hoatchat_san_pham = json_decode($this->hoatchat, true);
        $tags = json_decode($this->tags, true);

        if ($tags === null) {
            $tags = [];
        }

        $discountedPrice = $price - ($price * ($this->khuyen_mai / 100));
        $img_san_pham = DB::table('img_product')
            ->select('id', 'id_product', 'thumnail', 'created_at', 'updated_at')
            ->where('id_product', $this->id)
            ->get()
            ->toArray();

        $firstImgUrl = !empty($img_san_pham) ? url('/uploads/img_sp/' . $img_san_pham[0]->thumnail) : null;

        return [
            'id' => $this->id,
            'khuyen_mai' => $this->khuyen_mai,
            'ten_san_pham' => $this->name,
            'quy_cach_dong_goi' => $this->quy_cach_dong_goi,
            'so_luong' => $this->quantity,
            'don_gia' => $price,
            'gia_uu_dai' => $san_pham_uu_dai_gia,
            'so_luong_toi_thieu' => $this->sl_toi_thieu,
            'so_luong_toi_da' => $this->sl_toi_da,
            'img_url' => $firstImgUrl,
            'discount_price' => $discountedPrice,
            'detail_url' => null,
            'tags' => $tags,
            'img_san_pham' => $img_san_pham,
            'product_tags' => [],
            'hoatchat_san_pham' => $hoatchat_san_pham,
        ];
    }
}
