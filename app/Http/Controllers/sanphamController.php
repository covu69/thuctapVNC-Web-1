<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExport;
use App\Imports\ProductsImport;
use Intervention\Image\ImageManagerStatic as Image;

class sanphamController extends Controller
{
    // sản phẩm
    public function sanpham(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $data = $request->input('search');
        $status = $request->input('status');

        // Lưu dữ liệu tìm kiếm vào Session
        session(['search_data' => $data]);

        $baseQuery = DB::table('product')
            ->leftJoin('img_product', 'product.id', '=', 'img_product.id_product')
            ->select('product.id', 'product.code', 'product.name', 'product.status', 'product.sp_ban_chay', 'product.quantity', 'product.price', DB::raw('MIN(img_product.thumnail) as thumnail'))
            ->whereNull('product.deleted_at');

        // Thêm điều kiện theo chọn sản phẩm 
        if ($status !== null) {
            if ($status == 2) {
                // Trạng thái "Sản phẩm hết hàng"
                $baseQuery->where(function ($query) {
                    $query->where('product.quantity', '=', 0)
                        ->orWhere('product.price', '=', 0);
                });
            } else {
                $baseQuery->where('product.status', '=', $status);
            }

            // Lưu trạng thái vào Session
            session(['status' => $status]);
        } else {
            // Nếu không có trạng thái, xóa trạng thái khỏi Session
            session()->forget('status');
        }
        // Kiểm tra xem có thông tin tìm kiếm trong Session không
        if (session()->has('search_data')) {
            $searchData = session('search_data');
            $baseQuery->where(function ($query) use ($searchData) {
                $query->where('name', 'like', '%' . $searchData . '%');
            });
        }

        // Lấy trạng thái từ Session
        if (session()->has('status')) {
            $statusFromSession = session('status');

            // Sử dụng trạng thái từ Session trong truy vấn
            if ($statusFromSession == 2) {
                // Trạng thái "Sản phẩm hết hàng"
                $baseQuery->where(function ($query) {
                    $query->where('product.quantity', '=', 0)
                        ->orWhere('product.price', '=', 0);
                });
            } else {
                $baseQuery->where('product.status', '=', $statusFromSession);
            }
        }

        // Thực hiện phân trang và lấy danh sách tài khoản
        $sp = $baseQuery->groupBy('product.id', 'product.code', 'product.name', 'product.status', 'product.sp_ban_chay', 'product.quantity', 'product.price')->paginate($itemsPerPage);

        // Thêm các tham số tìm kiếm vào URL của liên kết phân trang
        $sp->appends(['search' => $data, 'status' => $status, 'itemsPerPage' => $itemsPerPage]);

        // Truyền dữ liệu tới view
        return view('project.admin.product.sanpham', compact('sp', 'data', 'itemsPerPage'));
    }

    public function add_sanpham()
    {
        $nhomthuoc = DB::table('nhomthuoc')->get();
        $nhasx = DB::table('nhasanxuat')->get();
        $hoatchatWithId = DB::table('hoatchat')->select('id', 'name')->get();
        $hoatchatWithId = $hoatchatWithId->toArray();

        $hang_thanh_vien = DB::table('hang_thanh_vien')->select('id', 'title')->get();
        $hang_thanh_vien = $hang_thanh_vien->toArray();

        $hashtag = DB::table('hashtag')->select('id', 'name')->get();
        $hashtag = $hashtag->toArray();
        // dd($hang_thanh_vien);
        // Sửa lại biến từ 'hoatchat' thành 'hoatchatWithId' trong hàm compact
        return view('project.admin.product.themmoi', compact('nhomthuoc', 'nhasx', 'hoatchatWithId', 'hang_thanh_vien', 'hashtag'));
    }


    public function saveProduct(Request $request)
    {

        try {
            DB::beginTransaction();

            // Kiểm tra validation
            $validator = Validator::make($request->all(), [
                'code' => 'required|unique:product',
                'name' => 'required|unique:product',
                'unit' => 'required',
                'id_nsx' => 'required',
                'id_nhomthuoc' => 'required',
                'quantity' => 'required',
                'price' => 'required|numeric',
                'nuoc_sx' => 'required',
                'thongtin' => 'required',
                'thumnail' => 'array',
                'thumnail.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            ], [
                'code.required' => 'Mã sản phẩm là trường bắt buộc',
                'code.unique' => 'Mã sản phẩm đã tồn tại',
                'name.unique' => 'Tên sản phẩm đã tồn tại',
                'name.required' => 'Tên sản phẩm không được bỏ trống',
                'unit.required' => 'Đơn vị là trường bắt buộc',
                'price.required' => 'Giá sản phẩm là trường bắt buộc',
                'price.numeric' => 'Giá sản phẩm phải là một số',
                'nuoc_sx.required' => 'Nước sản xuất là trường bắt buộc',
                'thongtin.required' => 'Thông tin sản phẩm là trường bắt buộc',
                'thumnail.array' => 'Hình ảnh sản phẩm phải là một mảng',
                'thumnail.*.image' => 'File phải là hình ảnh',
                'thumnail.*.mimes' => 'Định dạng hình ảnh không hợp lệ. Chỉ chấp nhận các định dạng: jpeg, png, jpg, gif',
                'thumnail.*.max' => 'Dung lượng hình ảnh không được vượt quá 5MB',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $selectedValues = $request->input('hashtag');
            $hashtag_array = [];

            if (!empty($selectedValues)) {
                foreach ($selectedValues as $value) {
                    $hashtag_array[] = ['id_tag' => $value];
                }
            }

            $hashtag = json_encode($hashtag_array);
            // Lưu thông tin sản phẩm
            $product = [
                'code' => $request->input('code'),
                'id_nhomthuoc' => $request->input('id_nhomthuoc'),
                'name' => $request->input('name'),
                'unit' => $request->input('unit'),
                'quantity' => $request->input('quantity'),
                'price' => $request->input('price'),
                'nuoc_sx' => $request->input('nuoc_sx'),
                'id_nsx' => $request->input('id_nsx'),
                'thong_tin' => $request['thongtin'],
                'created_at' => now(),
                'updated_at' => now(),
                'tags' => $hashtag,
                'cangnang' => $request['cangnang'],
                'hoatchat' => $this->formatHoatChatData($request->input('hoat_chat'), $request->input('ham_luong')),
                'sp_uu_dai_gia' => $this->formatSanphamuudaiData($request->input('hang_thanh_vien'), $request->input('gia_uu_dai')),
            ];
            // dd($product);
            $productId = DB::table('product')->insertGetId($product);

            // Lưu ảnh sản phẩm
            if ($request->hasFile('thumnail')) {
                foreach ($request->file('thumnail') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('uploads/img_sp'), $imageName);

                    $imageProduct = [
                        'id_product' => $productId,
                        'thumnail' => $imageName,
                    ];

                    $result =  DB::table('img_product')->insert($imageProduct);

                    if (!$result) {
                        dd('Lỗi khi thêm mới dữ liệu.');
                    }
                }
            }


            DB::commit();

            // Ghi log
            Log::info('Sản phẩm đã được lưu thành công. ID sản phẩm: ' . $productId);

            return redirect()->route('sanpham')->with('success', 'Sản phẩm đã được lưu thành công.');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // Ghi log
            Log::error('Đã có lỗi xảy ra: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    // Hàm để định dạng dữ liệu hoạt chất và hàm lượng thành một cấu trúc JSON
    private function formatHoatChatData($hoatChatArray, $hamLuongArray)
    {
        // Kiểm tra nếu mảng $hoatChatArray là rỗng, trả về JSON rỗng ngay lập tức
        if (empty($hoatChatArray)) {
            return json_encode([]);
        }

        $formattedData = [];

        foreach ($hoatChatArray as $key => $hoatChat) {

            $formattedData[] = [
                'id_hoat_chat' => $hoatChat,
                'ham_luong' => $hamLuongArray[$key],
            ];
        }
        $jsonResult = json_encode($formattedData);
        return $jsonResult;
    }


    private function formatSanphamuudaiData($hangthanhVienArray, $uudaiGiaArray)
    {

        // Kiểm tra nếu mảng $hangthanhVienArray là rỗng, trả về JSON rỗng ngay lập tức
        if (empty($hangthanhVienArray)) {
            return json_encode([]);
        }
        $formatted = [];

        foreach ($hangthanhVienArray as $key => $hangthanhVien) {
            $formatted[] = [
                'id_hang_thanh_vien' => $hangthanhVien,
                'uu_dai_gia' => $uudaiGiaArray[$key],
            ];
        }

        $uu_dai = json_encode($formatted);

        return $uu_dai;
    }


    public function edit_product($id)
    {
        $pro = DB::table('product')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        // Lấy danh sách tất cả các ảnh của sản phẩm
        $allImages = DB::table('img_product')
            ->where('id_product', $id)
            ->pluck('thumnail')
            ->toArray();

        $hoatChatData = json_decode($pro->hoatchat, true);
        $options = DB::table('hoatchat')->select('id', 'name')->get()->toArray();
        $nhomthuoc = DB::table('nhomthuoc')->get();
        $nhasx = DB::table('nhasanxuat')->get();
        $options_tag = json_decode($pro->tags, true);
        $hashtag = DB::table('hashtag')->select('id', 'name')->get()->toArray();
        return view('project.admin.product.edit', compact('pro', 'nhomthuoc', 'nhasx', 'hoatChatData', 'options', 'allImages', 'options_tag', 'hashtag'));
    }



    public function update_sp(Request $request, $id)
    {
        // Kiểm tra validation
        $validator = $this->validateUpdateRequest($request, $id);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Lấy dữ liệu sản phẩm
            $productData = $this->getProductData($request);

            // Kiểm tra xem có thay đổi trong ảnh không
            if ($request->has('thumnail')) {
                $this->uploadAndSaveImages($id, $request->file('thumnail'));
            }

            // Cập nhật sản phẩm
            DB::table('product')->where('id', $id)->update($productData);

            return redirect()->route('sanpham')->with('success', 'Cập nhật sản phẩm thành công.');
        } catch (\Exception $e) {
            // Xử lý ngoại lệ nếu có
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật sản phẩm.');
        }
    }

    public function destroy_product($id)
    {
        $destroy = DB::table('product')->where('id', $id)->update([
            'deleted_at' => now()
        ]);

        if ($destroy) {
            return redirect()->route('sanpham')->with('success', 'Sản phẩm đã được xóa thành công.');
        } else {
            return redirect()->back()->with('error', 'Xóa không thành công!');
        }
    }

    public function product_ghim($id)
    {

        $currentValue = DB::table('product')->where('id', $id)->value('sp_ban_chay');

        // Thay đổi giá trị
        $newValue = ($currentValue == 1) ? 0 : 1;

        // Cập nhật giá trị trong cơ sở dữ liệu
        DB::table('product')->where('id', $id)->update([
            'sp_ban_chay' => $newValue
        ]);

        return redirect()->back();
    }

    private function validateUpdateRequest($request, $id)
    {
        return Validator::make($request->all(), [
            'code' => 'required|unique:product,code,' . $id,
            'name' => 'required|unique:product,name,' . $id,
            'unit' => 'required',
            'price' => 'required|numeric',
            'id_nsx' => 'required',
            'id_nhomthuoc' => 'required',
            'quantity' => 'required',
            'nuoc_sx' => 'required',
            'thongtin' => 'required',
            'thumnail' => 'array',
            'thumnail.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'code.required' => 'Mã sản phẩm là trường bắt buộc',
            'code.unique' => 'Mã sản phẩm đã tồn tại',
            'name.required' => 'Tên sản phẩm là trường bắt buộc',
            'name.unique' => 'Tên sản phẩm đã tồn tại',
            'unit.required' => 'Đơn vị là trường bắt buộc',
            'price.required' => 'Giá sản phẩm là trường bắt buộc',
            'price.numeric' => 'Giá sản phẩm phải là một số',
            'nuoc_sx.required' => 'Nước sản xuất là trường bắt buộc',
            'thongtin.required' => 'Thông tin sản phẩm là trường bắt buộc',
            'thumnail.array' => 'Hình ảnh sản phẩm phải là một mảng',
            'thumnail.*.image' => 'File phải là hình ảnh',
            'thumnail.*.mimes' => 'Định dạng hình ảnh không hợp lệ. Chỉ chấp nhận các định dạng: jpeg, png, jpg, gif',
            'thumnail.*.max' => 'Dung lượng hình ảnh không được vượt quá 5MB',
        ]);
    }

    private function getProductData($request)
    {
        $selectedValues = $request->input('hashtag');
        $hashtag = null;

        if (!empty($selectedValues)) {
            $hashtag_array = [];
            foreach ($selectedValues as $value) {
                $hashtag_array[] = ['id_tag' => $value];
            }
            $hashtag = json_encode($hashtag_array);
        }

        return [
            'code' => $request->input('code'),
            'id_nhomthuoc' => $request->input('id_nhomthuoc'),
            'name' => $request->input('name'),
            'unit' => $request->input('unit'),
            'quantity' => $request->input('quantity'),
            'price' => $request->input('price'),
            'nuoc_sx' => $request->input('nuoc_sx'),
            'id_nsx' => $request->input('id_nsx'),
            'thong_tin' => $request['thongtin'],
            'cangnang' => $request['cangnang'],
            'hoatchat' => $this->formatHoatChatData($request->input('hoat_chat'), $request->input('ham_luong')),
            'updated_at' => now(),
            'tags' => $hashtag,
        ];
    }

    private function uploadAndSaveImages($productId, $images)
    {
        foreach ($images as $image) {
            if ($image->isValid()) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/img_sp'), $imageName);

                $imageProduct = [
                    'id_product' => $productId,
                    'thumnail' => $imageName,
                ];

                DB::table('img_product')->insert($imageProduct);
            }
        }
    }


    // kết thúc sản phảm

    // xử lý hình ảnh
    public function deleteImage($imageName)
    {
        try {
            // Xóa hình ảnh từ thư mục
            File::delete('uploads/img_sp/' . $imageName);

            // Xóa hình ảnh từ cơ sở dữ liệu
            DB::table('img_product')->where('thumnail', $imageName)->delete();

            return redirect()->back()->with('success', 'Xóa ảnh thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa ảnh.');
        }
    }
    public function updateProductStatus(Request $request)
    {
        $action = $request->input('action');
        $selectedIds = $request->input('ids');

        if (is_array($selectedIds) && count($selectedIds) > 0) {
            if ($action == 'hide_temporarily') {
                DB::table('product')->whereIn('id', $selectedIds)->update(['status' => 0]);
            } elseif ($action == 'show') {
                DB::table('product')->whereIn('id', $selectedIds)->update(['status' => 1]);
            }
            return redirect()->back()->with('success', 'Cập nhật trạng thái thành công');
        } else {
            return redirect()->back()->with('error', 'Vui lòng chọn ít nhất một sản phẩm để cập nhật trạng thái');
        }
    }
    // tải EXCEL
    public function exportDataToExcel(Request $request)
    {
        $searchData = session('search_data');
        $statusFilter = session('status');
        $baseQuery = DB::table('product')
            ->whereNull('deleted_at');

        if ($searchData) {
            $baseQuery->where(function ($query) use ($searchData) {
                $query->where('name', 'like', '%' . $searchData . '%');
            });
        }

        if ($statusFilter !== null) {
            if ($statusFilter == 2) {
                $baseQuery->where(function ($query) use ($searchData) {
                    if ($searchData) {
                        // Nếu có searchData, thêm điều kiện lọc cho trường name
                        $query->where('name', 'like', '%' . $searchData . '%');
                    }

                    // Thêm điều kiện lọc cho trường quantity và price
                    $query->where('quantity', '=', 0)
                        ->orWhere('price', '=', 0);
                });
            } else {
                // Trạng thái khác
                if ($searchData) {
                    // Nếu có searchData, thêm điều kiện lọc cho trường name
                    $baseQuery->where('name', 'like', '%' . $searchData . '%');
                }

                // Thêm điều kiện lọc cho trường status
                $baseQuery->where('status', $statusFilter);
            }
        }
        $data = $baseQuery->get();
        return Excel::download(new ProductExport($data), 'Product_data.xlsx');
    }

    // Import excel

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        $file = $request->file('file');

        // Use skipRows(1) to skip the first row (header row)
        $data = Excel::toArray(new ProductsImport, $file);

        $errorRows = [];
        $successRows = [];
        $headerSkipped = false; // Biến để kiểm tra xem đã bắt đầu từ dòng thứ 2 chưa

        foreach ($data[0] as $row) {
            // Kiểm tra nếu header đã được bỏ qua
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue; // Bỏ qua dòng header
            }

            // Kiểm tra lỗi cho từng dòng và lưu vào $errorRows nếu có lỗi
            $rowErrors = $this->hasError($row);

            if (!empty($rowErrors)) {
                $errorRows[] = [
                    'row' => $row,
                    'errors' => $rowErrors,
                ];
            } else {
                // Xử lý dòng đúng ở đây
                $this->processRow($row);
                $successRows[] = $row;
            }
        }

        // Lưu trữ thông báo lỗi và các dòng đúng trong Session
        if (!empty($errorRows)) {
            dd($errorRows);
            session()->flash('error', 'Có dòng không hợp lệ trong tệp Excel.');
            session()->flash('errorRows', $errorRows);
        }
        if (!empty($successRows)) {
            session()->flash('success', 'Các dòng đã được import thành công.');
        }

        return redirect()->back();
    }


    private function hasError($row)
    {
        $errors = [];

        $rules = [
            'Mã sản phẩm' => 'required|unique:product',
            'Nhóm thuốc' => 'required',
            // Thêm các quy tắc khác cho các cột khác
        ];

        $customMessages = [
            'Mã sản phẩm.required' => 'Mã sản phẩm là trường bắt buộc',
            'Mã sản phẩm.unique' => 'Mã sản phẩm đã tồn tại',
            'Nhóm thuốc.required' => 'Nhóm thuốc là trường bắt buộc',
            // Thêm các thông báo tùy chỉnh cho các cột khác
        ];

        // Kiểm tra dữ liệu dựa trên rules và customMessages
        $validator = Validator::make($row, $rules, $customMessages);

        // Kiểm tra xem có lỗi không
        if ($validator->fails()) {
            // Lặp qua tất cả các lỗi trong dòng và gán vào mảng $errors
            foreach ($validator->errors()->all() as $error) {
                $errors[] = [
                    'message' => $error,
                ];
            }
        }

        // Trả về mảng chứa thông báo lỗi của cột vi phạm trong dòng
        return $errors;
    }


    private function processRow($row)
    {
        // $imagePath = null;
        // dd(file_exists($cleanFilePath));

        $existingRecord = DB::table('product')->where('name', $row[1])->first();

        if ($existingRecord) {
            // Nếu đã tìm thấy dòng hiện tại, so sánh các trường với dữ liệu từ tệp Excel
            // và cập nhật các trường khác nhau
            if (isset($row['password']) && !empty($row['password']) && $existingRecord->password != $row['password']) {
                $existingRecord->password = $row['password'];
            }

            if ($existingRecord->phone != $row['phone']) {
                $existingRecord->phone = $row['phone'];
            }

            if ($existingRecord->role != $row['role']) {
                $existingRecord->role = $row['role'];
            }

            if ($existingRecord->name != $row['name']) {
                $existingRecord->name = $row['name'];
            }

            // Xử lý hình ảnh
            $existingRecord->save();
        } else {
            $hoatChatHamLuong = [];

            if ((isset($row[10]) && !empty($row[10])) || (isset($row[11]) && !empty($row[11]))) {
                if (isset($row[10]) && isset($row[11]) && !empty($row[10]) && !empty($row[11])) {
                    $tenHoatChatList = explode(',', $row[10]);
                    $hamLuongList = explode(',', $row[11]);

                    // Kiểm tra xem cả hai mảng có cùng độ dài hay không
                    if (count($tenHoatChatList) === count($hamLuongList)) {
                        foreach ($tenHoatChatList as $key => $tenHoatChat) {
                            // Loại bỏ khoảng trắng thừa
                            $tenHoatChat = trim($tenHoatChat);

                            $hamLuong = isset($hamLuongList[$key]) ? trim($hamLuongList[$key]) : null;
                            $hoatChatId = DB::table('hoatchat')->where('name', $tenHoatChat)->value('id');

                            if ($hoatChatId) {
                                $hoatChatHamLuong[] = [
                                    'id_hoat_chat' => $hoatChatId,
                                    'ham_luong' => $hamLuong,
                                ];
                            }
                        }
                    } else {
                        // Thông báo lỗi nếu độ dài của hai mảng không khớp
                        dd("Lỗi: Số lượng dữ liệu trong cột 'Tên hoạt chất' và 'Hàm lượng' không khớp");
                    }
                } else {
                    // Thông báo lỗi nếu chỉ một trong hai cột có dữ liệu
                    dd("Vui lòng nhập thêm 'Tên hoạt chất' hoặc 'Hàm lượng'");
                }
            }

            $spUuDaiGia = [];
            if (isset($row[15]) && !empty($row[16])) {
                $hangtvList = explode(',', $row[15]);
                $giauudaiList = explode(',', $row[16]);

                foreach ($hangtvList as $key => $hangThanhVien) {
                    // Loại bỏ khoảng trắng thừa
                    $hangThanhVien = trim($hangThanhVien);

                    $giauudai = isset($giauudaiList[$key]) ? trim($giauudaiList[$key]) : null;
                    $hangTVId = DB::table('hang_thanh_vien')->where('title', $hangThanhVien)->value('id');

                    if ($hangTVId) {
                        $spUuDaiGia[] = [
                            'id_hang_thanh_vien' => $hangTVId,
                            'uu_dai_gia' => $giauudai,
                        ];
                    }
                }
            }
            $hashtags = [];
            if (isset($row['13']) && !empty($row['13'])) {
                $tagsList = explode(',', $row['13']);
                foreach ($tagsList as $key => $tags) {
                    $tags = trim($tags);
                    $tagsId = DB::table('hashtag')->where('name', $tags)->value('id');
                    if ($tagsId) {
                        $hashtags[] = [
                            'id_tag' => $tagsId
                        ];
                    }
                }
            }
            $nhomthuoc_id = DB::table('nhomthuoc')->where('name', $row['0'])->value('id');
            $nsx_id = DB::table('nhasanxuat')->where('name', $row['8'])->value('id');
            $productId = DB::table('product')->insertGetId([
                'id_nhomthuoc' => $nhomthuoc_id,
                'name' => $row[1],
                'cangnang' => $row[2],
                'unit' => $row[3],
                'quy_cach_dong_goi' => $row[4],
                'quantity' => $row[5],
                'price' => $row[6],
                'khuyen_mai' => $row[7],
                'id_nsx' => $nsx_id,
                'nuoc_sx' => $row[9],
                'thong_tin' => $row[12],
                'hoatchat' => empty($hoatChatHamLuong) ? '[]' : json_encode($hoatChatHamLuong),
                'sp_uu_dai_gia' => empty($spUuDaiGia) ? '[]' : json_encode($spUuDaiGia),
                'code' => $row[14],
                'tags' => json_encode($hashtags),
            ]);

            // if (isset($row[15])) {
            //     // Clean and trim the file path
            //     $cleanFilePath = trim($row[15]);

            //     // Check if the file exists
            //     if (file_exists($cleanFilePath)) {
            //         // Generate a unique filename based on the current time
            //         $imageName = time() . '_' . basename($cleanFilePath);
            //         $imagePath = 'C:\\Users\\phamm\\OneDrive\\Desktop\\banthuoc\\public\\uploads\\img_sp\\' . $imageName;

            //         // Copy the file to the destination folder
            //         if (copy($cleanFilePath, $imagePath)) {
            //             // File copied successfully, insert into the database
            //             DB::table('img_product')->insert([
            //                 'id_product' => $productId,
            //                 'thumnail' => $imageName,
            //             ]);
            //         } else {
            //             // Handle the copy error
            //             echo "Failed to copy file.";
            //         }
            //     } else {
            //         echo "File does not exist: " . $cleanFilePath;
            //     }
            // }
        }
    }
}
