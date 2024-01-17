<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\AgencyController;
use App\Http\Controllers\API\VoucherController;
use App\Http\Controllers\API\DiscountController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\ContactsController;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\ThongtinchungController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::middleware(['auth:customer-api'])->group(function () {
    Route::post('v2/product/index', [ProductController::class, 'index']);
    Route::get('logout', [LoginController::class, 'logout']);
    Route::get('v2/system/homepage', [HomeController::class, 'getProducts']);
    Route::post('v2/cart/update',[CartController::class,'add_cart']);
    Route::post('v2/cart/index',[CartController::class,'index']);
    Route::get('v2/cart/list_voucher',[VoucherController::class,'voucher']);
    Route::post('v2/cart/discount',[DiscountController::class,'discount']);
    Route::post('v2/cart/payment',[PaymentController::class,'index']);
    Route::get('v2/system/category', [CategoryController::class, 'Category']);
    Route::post('v2/system/category_type',[CategoryController::class,'category_type']);
    Route::post('v2/search', [HomeController::class, 'search']);
    Route::post('v2/history/payment',[PaymentController::class,'history']);
    Route::post('v2/history/payment_details',[PaymentController::class,'history_detail']);
    Route::match(['get', 'post'], 'v2/member/profile', [ProfileController::class,'index']);
});

Route::post('v2/member/register', [LoginController::class, 'register']);
Route::post('v2/member/login', [LoginController::class, 'login'])->name('login');
Route::get('v2/system/provinces/agency_list',[AgencyController::class,'index']);
Route::get('v2/system/contact',[ContactsController::class,'index']);
Route::get('v2/system/list_news',[NewsController::class,'index']);
Route::get('system/general_information/{id}', [ThongtinchungController::class, 'showHtml']);
