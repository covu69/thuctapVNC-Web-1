<?php

use App\Http\Controllers\adminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\loginController;
use App\Http\Controllers\adminControllerController;
use App\Http\Controllers\sanphamController;
use App\Http\Controllers\DailyController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ThongtinchungController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('project.login');
});
Route::get('loguot', [loginController::class, 'logout'])->name('logout');
Route::post('check', [loginController::class, 'check'])->name('check');
Route::prefix('admin')->middleware(['auth:web'])->group(function () {
    Route::group(['middleware' => 'checkUserRole'], function () {
        Route::get('themmoi', [adminController::class, 'themmoi'])->name('themmoi');
        Route::post('save_quan_ly', [adminController::class, 'save_quan_ly'])->name('save_quan_ly');
        Route::get('xoa/{id}', [adminController::class, 'xoa'])->name('xoa');
        //nhà sản xuất
        Route::get('nhasanxuat', [adminController::class, 'nhasanxuat'])->name('nhasanxuat');
        Route::get('add_nhasanxuat', [adminController::class, 'add_nhasanxuat'])->name('add_nhasanxuat');
        Route::post('save_nhasanxuat', [adminController::class, 'save_nhasanxuat'])->name('save_nhasanxuat');
        Route::get('edit_nhasx/{id}', [adminController::class, 'edit_nhasx'])->name('edit_nhasx');
        Route::put('update_nhasx/{id}', [adminController::class, 'update_nhasx'])->name('update_nhasx');
        Route::get('xoa_nhasx/{id}', [adminController::class, 'xoa_nhasx'])->name('xoa_nhasx');
        // danh mục sản phẩm
        // nhóm thuốc
        Route::get('nhomthuoc', [adminController::class, 'nhomthuoc'])->name('nhomthuoc');
        Route::get('add_nhomthuoc', [adminController::class, 'add_nhomthuoc'])->name('add_nhomthuoc');
        Route::post('save_nhomthuoc', [adminController::class, 'save_nhomthuoc'])->name('save_nhomthuoc');
        Route::get('edit_nhomthuoc/{id}', [adminController::class, 'edit_nhomthuoc'])->name('edit_nhomthuoc');
        Route::put('update_nhomthuoc/{id}', [adminController::class, 'update_nhomthuoc'])->name('update_nhomthuoc');
        Route::get('xoa_nhomthuoc/{id}', [adminController::class, 'xoa_nhomthuoc'])->name('xoa_nhomthuoc');

        //  hoạt chất
        Route::get('hoatchat', [adminController::class, 'hoatchat'])->name('hoatchat');
        Route::get('add_hoatchat', [adminController::class, 'add_hoatchat'])->name('add_hoatchat');
        Route::post('save_hoatchat', [adminController::class, 'save_hoatchat'])->name('save_hoatchat');
        Route::get('edit_hoatchat/{id}', [adminController::class, 'edit_hoatchat'])->name('edit_hoatchat');
        Route::put('update_hoatchat/{id}', [adminController::class, 'update_hoatchat'])->name('update_hoatchat');
        Route::get('xoa_hoatchat/{id}', [adminController::class, 'xoa_hoatchat'])->name('xoa_hoatchat');

        //hashtag
        Route::get('hashtag', [adminController::class, 'hashtag'])->name('hashtag');
        Route::get('add_hashtag', [adminController::class, 'add_hashtag'])->name('add_hashtag');
        Route::post('save_hashtag', [adminController::class, 'save_hashtag'])->name('save_hashtag');
        Route::get('edit_hashtag/{id}', [adminController::class, 'edit_hashtag'])->name('edit_hashtag');
        Route::put('update_hashtag/{id}', [adminController::class, 'update_hashtag'])->name('update_hashtag');
        Route::get('xoa_hashtag/{id}', [adminController::class, 'xoa_hashtag'])->name('xoa_hashtag');

        // hạng thành viên
        Route::get('hang_tv', [adminController::class, 'hang_tv'])->name('hang_tv');
        Route::get('add_hang_tv', [adminController::class, 'add_hang_tv'])->name('add_hang_tv');
        Route::post('save_hang_tv', [adminController::class, 'save_hang_tv'])->name('save_hang_tv');

        //sản phẩm
        Route::get('sanpham', [sanphamController::class, 'sanpham'])->name('sanpham');
        Route::get('add_sanpham', [sanphamController::class, 'add_sanpham'])->name('add_sanpham');
        Route::post('save_sanpham', [sanphamController::class, 'saveProduct'])->name('save_sanpham');
        Route::get('edit_product/{id}', [sanphamController::class, 'edit_product'])->name('edit_product');
        Route::put('update_sp/{id}', [sanphamController::class, 'update_sp'])->name('update_sp');
        Route::get('destroy_product/{id}', [sanphamController::class, 'destroy_product'])->name('destroy_product');
        Route::get('product_ghim/{id}', [sanphamController::class, 'product_ghim'])->name('product_ghim');
        Route::post('updateProductStatus', [sanphamController::class, 'updateProductStatus'])->name('updateProductStatus');
        // hình ảnh
        Route::get('delete_image/{imageName}', [sanphamController::class, 'deleteImage'])->name('delete_image');
        //EXCEL
        Route::get('exportDataToExcel', [sanphamController::class, 'exportDataToExcel'])->name('exportDataToExcel');
        // import
        Route::post('import', [sanphamController::class, 'import'])->name('import');


        // voucher
        Route::get('voucher', [VoucherController::class, 'voucher'])->name('voucher');
        Route::get('add_voucher', [VoucherController::class, 'add_voucher'])->name('add_voucher');
        Route::post('save_voucher', [VoucherController::class, 'save_voucher'])->name('save_voucher');
        Route::get('edit_voucher/{id}', [VoucherController::class, 'edit_voucher'])->name('edit_voucher');
        Route::put('update_voucher/{id}', [VoucherController::class, 'update_voucher'])->name('update_voucher');
        // tin tức
        Route::get('tin_tuc/index', [NewsController::class, 'index'])->name('tin_tuc');
        Route::get('tin_tuc/create', [NewsController::class, 'add_news'])->name('add_news');
        Route::post('save_news', [NewsController::class, 'save_news'])->name('save_news');
        Route::get('edit_news/{id}', [NewsController::class, 'edit'])->name('edit_news');
        Route::put('update_news/{id}', [NewsController::class, 'update_news'])->name('update_news');

        // thông tin chung
        Route::get('thong_tin_chung/index', [ThongtinchungController::class, 'index'])->name('thong_tin_chung');
        Route::get('thong_tin_chung/create', [ThongtinchungController::class, 'add_thong_tin_chung'])->name('add_thong_tin_chung');
        Route::post('save', [ThongtinchungController::class, 'save'])->name('save_thong_tin_chung');
        Route::get('thong_tin_chung/edit/{id}', [ThongtinchungController::class, 'edit'])->name('edit_thong_tin_chung');
        Route::put('update_thong_tin_chung/{id}', [ThongtinchungController::class, 'update'])->name('update_thong_tin_chung');
        // mua hàng
        Route::get('muahang', [PaymentController::class, 'muahang'])->name('muahang');

    });
    Route::get('dashboard', [adminController::class, 'index'])->name('dashboard');
    // người quản lý
    Route::get('nguoi_quan_ly', [adminController::class, 'nguoi_quan_ly'])->name('nguoi_quan_ly');
    Route::get('edit_user/{id}', [adminController::class, 'edit_user'])->name('edit_user');
    Route::put('update_user/{id}', [adminController::class, 'update_user'])->name('update_user');

    // thành viên
    Route::get('dai_ly', [DailyController::class, 'dai_ly'])->name('dai_ly');
    Route::get('themmoi_thanh_vien', [DailyController::class, 'themmoi_thanh_vien'])->name('themmoi_thanh_vien');
    Route::post('save_thanh_vien', [DailyController::class, 'save_thanh_vien'])->name('save_thanh_vien');
    Route::get('edit_thanh_vien/{id}', [DailyController::class, 'edit_thanh_vien'])->name('edit_thanh_vien');
    Route::put('update_thanh_vien/{id}', [DailyController::class, 'update_thanh_vien'])->name('update_thanh_vien');
    // payment
    Route::get('payment', [PaymentController::class, 'payment'])->name('payment');
    Route::get('view/{id}', [PaymentController::class, 'view'])->name('view');
    Route::get('edit_payment/{id}', [PaymentController::class, 'edit_payment'])->name('edit_payment');
    Route::put('update_payment/{id}', [PaymentController::class, 'update_payment'])->name('update_payment');
    // giỏ hàng
    Route::get('gio_hang', [CartController::class, 'cart'])->name('gio_hang');
    Route::get('detail/{id}', [CartController::class, 'detail'])->name('detail');
});
