<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{

    public function getProducts(Request $request)
    {
        $banchay = $this->getProductCategory('ban_chay', 'Sản phẩm bán chạy');
        $moi = $this->getProductCategory('moi', 'Sản phẩm mới');
        $khuyenmai = $this->getProductCategory('khuyen_mai', 'Sản phẩm khuyến mãi');
        $all = $this->getProductCategory('all', 'Tất cả sản phẩm');
        $cartPaginator = DB::table('cart')
            ->join('product', 'cart.id_product', '=', 'product.id')
            ->select('cart.id as gio_hang_id', 'cart.id_member', 'cart.so_luong', 'product.*')
            ->where('cart.id_member', Auth::guard('customer-api')->user()->id)->get();
        $total_cart = collect($cartPaginator)->sum('so_luong');
        $response = [
            'code' => 0,
            'message' => [],
            'response' => [
                'banners' => [],
                'events' => [],
                'products' => [$banchay, $moi, $khuyenmai, $all],
                'total_cart' => $total_cart,
                'total_notifications' => 0, // Điền giá trị thực tế
                'member_name' => Auth::guard('customer-api')->user()->ten,
                'member_status' => Auth::guard('customer-api')->user()->status,
                'thu_hang_icon' => null,
            ],
        ];

        return response()->json($response);
    }

    private function getProductCategory($category, $name)
    {
        $products = [];

        switch ($category) {
            case 'ban_chay':
                $products = DB::table('product')->where('sp_ban_chay', 1)->take(4)->get();
                break;

            case 'moi':
                $products = DB::table('product')->orderBy('updated_at', 'desc')->take(4)->get();
                break;

            case 'khuyen_mai':
                $products = DB::table('product')->whereNotNull('khuyen_mai')->take(4)->get();
                break;

            case 'all':
                $products = DB::table('product')->take(4)->get(); // Giới hạn số lượng sản phẩm trả về bằng 4
                break;

            default:
                // Xử lý mặc định hoặc thông báo lỗi
                abort(404, 'Danh mục không hợp lệ.');
        }

        // Kiểm tra xem có sản phẩm hay không
        if ($products->isEmpty()) {
            return [];
        }

        return [
            'key' => 'category',
            'value' => $category,
            'name' => $name,
            'data' => $products->map(function ($product) {
                // lấy thông id_hang_tv của người dùng 
                $idHangTV = Auth::guard('customer-api')->user()->id_hang_tv;
                // kiểm tra xem có được giá ưu đãi không
                $san_pham_uu_dai_gia = json_decode($product->sp_uu_dai_gia, true);
                $price = $product->price;

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
                // Lấy thông tin hình ảnh sản phẩm
                $img_san_pham = DB::table('img_product')
                    ->select('id', 'id_product', 'thumnail', 'created_at', 'updated_at')
                    ->where('id_product', $product->id)
                    ->get()
                    ->toArray();

                // Lấy URL của hình ảnh đầu tiên 
                $firstImgUrl = !empty($img_san_pham) ? url('/uploads/img_sp/' . $img_san_pham[0]->thumnail) : null;
                $tags = json_decode($product->tags, true);

                if ($tags === null) {
                    $tags = [];
                }
                // Chuyển đổi định dạng tags
                $formattedTags = array_map(function ($tag) {
                    return [
                        'key' => 'hashtag',
                        'value' => $tag['id_tag'],
                        'name' => '#' . $this->getTagName($tag['id_tag']), // Thay thế bằng hàm lấy tên từ id_tag
                    ];
                }, $tags);
                return [
                    'id' => $product->id,
                    'khuyen_mai' => $product->khuyen_mai,
                    'ten_san_pham' => $product->name,
                    'quy_cach_dong_goi' => $product->quy_cach_dong_goi,
                    'so_luong' => $product->quantity,
                    'don_gia' => $price,
                    'bonus_coins' => $product->coin,
                    'so_luong_toi_thieu' => $product->sl_toi_thieu,
                    'so_luong_toi_da' => $product->sl_toi_da,
                    'img_url' => $firstImgUrl,
                    'discount_price' => $discountedPrice = $price - ($price * ($product->khuyen_mai / 100)),
                    'detail_url' => 'http://18.138.176.213/api/product/detail/' . $product->id,
                    'tags' => $formattedTags, // Thêm xử lý tags nếu cần
                ];
            })->toArray(),
        ];
    }
    // Hàm lấy tên của tag từ id_tag (cần điều chỉnh tùy thuộc vào cách bạn lưu trữ dữ liệu tag)
    function getTagName($tagId)
    {
        // Thực hiện truy vấn hoặc xử lý để lấy tên của tag từ id_tag
        return DB::table('hashtag')->where('id', $tagId)->value('name');
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required|min:2',
        ], [
            'search.required' => 'Vui lòng không để trống ',
            'search.min' => 'Vui lòng không nhập nhỏ hơn 2 ký tự'
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();

            return response()->json([
                'code' => 1,
                'message' => $errorMessages,
                'response' => null
            ], 403);
        }

        $query = DB::table('product')->whereNull('deleted_at');

        $searchKeyword = $request->query('search');
        if (!empty($searchKeyword)) {
            $query->where(function ($query) use ($searchKeyword) {
                $query->where('code', 'like', '%' . $searchKeyword . '%')
                    ->orWhere('name', 'like', '%' . $searchKeyword . '%');
            });
        }

        $products = $query->get();

        return response()->json([
            'code' => 0,
            'message' => [],
            'response' => collect($products)->map(function ($item) {
                 return [
                'id'=>$item->id,
                'ten_san_pham'=>$item->name,
                'ban_chay'=>$item->sp_ban_chay,
                'khuyen_mai'=>$item->khuyen_mai
                 ];
            })
        ], 200);
    }
}
