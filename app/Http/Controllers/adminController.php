<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class adminController extends Controller
{
    public function index()
    {
        return view('project.admin.dashboard');
    }
    //người quản lý
    public function nguoi_quan_ly(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $loggedInUserId = Auth::id();
        $data = $request->input('search');

        // Lưu dữ liệu tìm kiếm vào Session
        session(['search_data' => $data]);

        // Bạn có thể đặt tên biến khác để tránh ghi đè
        $baseQuery = DB::table('users')
            ->select('*')
            ->where('deleted_at', null);
        // Kiểm tra xem có thông tin tìm kiếm trong Session không
        if (session()->has('search_data')) {
            $searchData = session('search_data');
            $baseQuery->where(function ($query) use ($searchData) {
                $query->where('name', 'like', '%' . $searchData . '%')
                    ->orWhere('email', 'like', '%' . $searchData . '%');
            });
        }

        // Thực hiện phân trang và lấy danh sách tài khoản
        $users = $baseQuery->paginate($itemsPerPage);

        // Thêm các tham số tìm kiếm vào URL của liên kết phân trang
        $users->appends(['search' => $data, 'itemsPerPage' => $itemsPerPage]);

        // Truyền dữ liệu tới view
        return view('project.admin.accounts.danhsach', compact('users', 'data', 'itemsPerPage'));
    }

    public function themmoi()
    {
        return view('project.admin.accounts.themmoi');
    }

    public function save_quan_ly(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'password' => [
                'required',
                'min:6',             // ít nhất 10 ký tự
                'regex:/[a-z]/',      // ít nhất 1 chữ thường
                'regex:/[A-Z]/',      // ít nhất 1 chữ in hoa
                'regex:/[0-9]/',      // ít nhất 1 số
                'regex:/[@$!%*#?&]/', // ít nhất 1 ký tự đặc biệt
            ],
            'email' => 'required|email|unique:users',
            'sodienthoai' => 'required|digits_between:9,10',
            'password_confirmation' => 'required|same:password'
        ], [
            'Ma_don_vi.required' => 'Vui lòng nhập mã đơn vị.',
            'name.required' => 'Vui lòng nhập tên.',
            'email.email' => 'Email phải là địa chỉ email hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
            'sodienthoai.digits_between' => 'Số điện thoại phải có độ dài từ 9 đến 10 chữ số.',
        ]);

        $input = $request->all();
        $user = DB::table('users')->insert([
            'Ma_don_vi' => $input['Ma_don_vi'],
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'sodienthoai' => $input['sodienthoai'],
            'role' => $request->input('role'),
            'created_at' => now(),
        ]);
        if ($user) {
            return redirect()->route('nguoi_quan_ly')->with('success', 'Thao tác thành công!');
        } else {
            return redirect()->route('themmoi')->with('error', 'Có lỗi xảy ra trong quá trình thao tác.')->withInput();
        }
    }

    public function edit_user($id)
    {
        $edit_user = DB::table('users')->where('id', $id)->first();
        return view('project.admin.accounts.chinhsua', compact('edit_user'));
    }

    public function update_user(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => ['required', Rule::unique('users')->ignore($id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'sodienthoai' => 'required|digits_between:9,10',
            'password' => [
                'nullable',
                'min:6',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ], [
            'name.unique' => 'Tên đã tồn tại.',
            'email.email' => 'Email phải là địa chỉ email hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
            'sodienthoai.digits_between' => 'Số điện thoại phải có độ dài từ 9 đến 10 chữ số.',
        ]);

        // Lấy dữ liệu từ request
        $data = [
            'Ma_don_vi' => $request->input('Ma_don_vi'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'sodienthoai' => $request->input('sodienthoai'),
            'role' => $request->input('role'),
            'updated_at' => now(),
        ];

        // Kiểm tra xem mật khẩu mới có được cung cấp không
        if ($request->filled('password')) {
            // Băm mật khẩu mới và thêm vào dữ liệu
            $data['password'] = Hash::make($request->input('password'));
        }

        $update = DB::table('users')->where('id', $id)->update($data);

        if ($update) {
            return redirect()->route('nguoi_quan_ly')->with('success', 'Thao tác thành công!');
        } else {
            return redirect()->back()->with('error', 'Có lỗi xảy ra trong quá trình thao tác.');
        }
    }

    public function xoa($id)
    {
        $authUserId = Auth::user()->id;
        $user = DB::table('users')->where('id', $id)->first();
        if (!$user) {
            return redirect()->route('taikhoan')->with('error', 'Không thể tìm thấy người dùng để xóa.');
        }
        if ($user->role == 1 && $user->id !== $authUserId) {
            abort(404);
        }


        DB::table('users')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);

        return redirect()->route('nguoi_quan_ly')->with('success', 'Người dùng đã được xóa thành công.');
    }

    // kết thúc

    // danh mục sản phẩm
    //nhà sản xuất
    public function nhasanxuat(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $data = $request->input('search');

        // Lưu dữ liệu tìm kiếm vào Session
        session(['search_data' => $data]);

        // Bạn có thể đặt tên biến khác để tránh ghi đè
        $baseQuery = DB::table('nhasanxuat')
            ->select('*')->whereNull('deleted_at');

        // Kiểm tra xem có thông tin tìm kiếm trong Session không
        if (session()->has('search_data')) {
            $searchData = session('search_data');
            $baseQuery->where(function ($query) use ($searchData) {
                $query->where('name', 'like', '%' . $searchData . '%');
            });
        }

        // Thực hiện phân trang và lấy danh sách tài khoản
        $dm = $baseQuery->paginate($itemsPerPage);

        // Thêm các tham số tìm kiếm vào URL của liên kết phân trang
        $dm->appends(['search' => $data, 'itemsPerPage' => $itemsPerPage]);

        // Truyền dữ liệu tới view
        return view('project.admin.danhmuc.nha_san_xuat.nhasanxuat', compact('dm', 'data', 'itemsPerPage'));
    }

    public function add_nhasanxuat()
    {
        return view('project.admin.danhmuc.nha_san_xuat.themmoi');
    }

    public function save_nhasanxuat(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:nhasanxuat',
        ], [
            'name.unique' => 'Tên nhà sản xuất đã tồn tại.',
        ]);

        $input = $request->all();
        $nhasx = DB::table('nhasanxuat')->insert([
            'name' => $input['name'],
            'created_at' => now(),
        ]);
        if ($nhasx) {
            return redirect()->route('nhasanxuat')->with('success', 'Thao tác thành công!');
        } else {
            return view('project.admin.danhmuc.nha_san_xuat.themmoi')->with('error', 'Có lỗi xảy ra trong quá trình thao tác.');
        }
    }

    public function edit_nhasx($id)
    {
        $nhasx = DB::table('nhasanxuat')->where('id', $id)->first();

        return view('project.admin.danhmuc.nha_san_xuat.edit', compact('nhasx'));
    }

    public function update_nhasx(Request $request, $id)
    {
        // Kiểm tra xem dữ liệu mới có thay đổi so với dữ liệu hiện tại không
        $currentData = DB::table('nhasanxuat')->where('id', $id)->first();

        $validatedData = $request->validate([
            'name' => ['required', Rule::unique('nhasanxuat')->ignore($id)],
        ], [
            'name.unique' => 'Tên nhà sản xuất đã tồn tại.',
        ]);

        // Kiểm tra xem có sự thay đổi không
        if ($request['name'] !== $currentData->name) {
            // Nếu có thay đổi, thực hiện cập nhật
            $update = DB::table('nhasanxuat')->where('id', $id)->update([
                'name' => $request['name']
            ]);

            if ($update) {
                return redirect()->route('nhasanxuat')->with('success', 'Thao tác thành công!');
            } else {
                return redirect()->back()->with('error', 'Có lỗi xảy ra trong quá trình thao tác.');
            }
        } else {
            // Nếu không có thay đổi, không cần thực hiện cập nhật
            return redirect()->route('nhasanxuat')->with('success', 'Không có sự thay đổi nào được thực hiện.');
        }
    }

    public function xoa_nhasx($id)
    {
        DB::table('nhasanxuat')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);

        return redirect()->route('nhasanxuat')->with('success', 'Nhà sản xuất đã được xóa thành công.');
    }
    //kết thúc nhà sản xuất

    // nhóm thuốc
    public function nhomthuoc(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $data = $request->input('search');

        // Lưu dữ liệu tìm kiếm vào Session
        session(['search_data' => $data]);

        // Bạn có thể đặt tên biến khác để tránh ghi đè
        $baseQuery = DB::table('nhomthuoc')
            ->select('*')->whereNull('deleted_at');

        // Kiểm tra xem có thông tin tìm kiếm trong Session không
        if (session()->has('search_data')) {
            $searchData = session('search_data');
            $baseQuery->where(function ($query) use ($searchData) {
                $query->where('name', 'like', '%' . $searchData . '%');
            });
        }

        // Thực hiện phân trang và lấy danh sách tài khoản
        $nt = $baseQuery->paginate($itemsPerPage);

        // Thêm các tham số tìm kiếm vào URL của liên kết phân trang
        $nt->appends(['search' => $data, 'itemsPerPage' => $itemsPerPage]);

        // Truyền dữ liệu tới view
        return view('project.admin.danhmuc.nhom_thuoc.nhomthuoc', compact('nt', 'data', 'itemsPerPage'));
    }

    public function add_nhomthuoc()
    {
        return view('project.admin.danhmuc.nhom_thuoc.themmoi');
    }

    public function save_nhomthuoc(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:nhomthuoc',
        ], [
            'name.unique' => 'Tên nhà sản xuất đã tồn tại.',
        ]);

        $input = $request->all();
        $nhasx = DB::table('nhomthuoc')->insert([
            'name' => $input['name'],
            'created_at' => now(),
        ]);
        if ($nhasx) {
            return redirect()->route('nhomthuoc')->with('success', 'Thao tác thành công!');
        } else {
            return redirect()->back()->with('error', 'Có lỗi xảy ra trong quá trình thao tác.')->withInput();
        }
    }

    public function edit_nhomthuoc($id)
    {
        $nt = DB::table('nhomthuoc')->where('id', $id)->first();

        return view('project.admin.danhmuc.nhom_thuoc.edit', compact('nt'));
    }

    public function update_nhomthuoc(Request $request, $id)
    {
        // Kiểm tra xem dữ liệu mới có thay đổi so với dữ liệu hiện tại không
        $currentData = DB::table('nhomthuoc')->where('id', $id)->first();

        $validatedData = $request->validate([
            'name' => ['required', Rule::unique('nhomthuoc')->ignore($id)],
        ], [
            'name.unique' => 'Nhóm thuốc đã tồn tại.',
        ]);

        // Kiểm tra xem có sự thay đổi không
        if ($request['name'] !== $currentData->name) {
            // Nếu có thay đổi, thực hiện cập nhật
            $update = DB::table('nhomthuoc')->where('id', $id)->update([
                'name' => $request['name']
            ]);

            if ($update) {
                return redirect()->route('nhomthuoc')->with('success', 'Thao tác thành công!');
            } else {
                return redirect()->back()->with('error', 'Có lỗi xảy ra trong quá trình thao tác.');
            }
        } else {
            // Nếu không có thay đổi, không cần thực hiện cập nhật
            return redirect()->route('nhomthuoc')->with('success', 'Không có sự thay đổi nào được thực hiện.');
        }
    }

    public function xoa_nhomthuoc($id)
    {
        DB::table('nhomthuoc')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);

        return redirect()->route('nhomthuoc')->with('success', 'Nhà sản xuất đã được xóa thành công.');
    }
    // kết thúc nhóm thuốc

    // hoạt chất
    public function hoatchat(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $data = $request->input('search');

        // Lưu dữ liệu tìm kiếm vào Session
        session(['search_data' => $data]);

        // Bạn có thể đặt tên biến khác để tránh ghi đè
        $baseQuery = DB::table('hoatchat')
            ->select('*')->whereNull('deleted_at');

        // Kiểm tra xem có thông tin tìm kiếm trong Session không
        if (session()->has('search_data')) {
            $searchData = session('search_data');
            $baseQuery->where(function ($query) use ($searchData) {
                $query->where('name', 'like', '%' . $searchData . '%');
            });
        }

        // Thực hiện phân trang và lấy danh sách tài khoản
        $hc = $baseQuery->paginate($itemsPerPage);

        // Thêm các tham số tìm kiếm vào URL của liên kết phân trang
        $hc->appends(['search' => $data, 'itemsPerPage' => $itemsPerPage]);

        // Truyền dữ liệu tới view
        return view('project.admin.danhmuc.hoat_chat.hoatchat', compact('hc', 'data', 'itemsPerPage'));
    }

    public function add_hoatchat()
    {
        return view('project.admin.danhmuc.hoat_chat.themmoi');
    }

    public function save_hoatchat(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:hoatchat',
        ], [
            'name.unique' => 'Tên nhà sản xuất đã tồn tại.',
        ]);

        $input = $request->all();
        $nhasx = DB::table('hoatchat')->insert([
            'name' => $input['name'],
            'created_at' => now(),
        ]);
        if ($nhasx) {
            return redirect()->route('hoatchat')->with('success', 'Thao tác thành công!');
        } else {
            return redirect()->back()->with('error', 'Có lỗi xảy ra trong quá trình thao tác.')->withInput();
        }
    }

    public function edit_hoatchat($id)
    {
        $hc = DB::table('hoatchat')->where('id', $id)->first();

        return view('project.admin.danhmuc.hoat_chat.edit', compact('hc'));
    }

    public function update_hoatchat(Request $request, $id)
    {
        // Kiểm tra xem dữ liệu mới có thay đổi so với dữ liệu hiện tại không
        $currentData = DB::table('hoatchat')->where('id', $id)->first();

        $validatedData = $request->validate([
            'name' => ['required', Rule::unique('hoatchat')->ignore($id)],
        ], [
            'name.unique' => 'Hoạt chất đã tồn tại.',
        ]);

        // Kiểm tra xem có sự thay đổi không
        if ($request['name'] !== $currentData->name) {
            // Nếu có thay đổi, thực hiện cập nhật
            $update = DB::table('hoatchat')->where('id', $id)->update([
                'name' => $request['name']
            ]);

            if ($update) {
                return redirect()->route('hoatchat')->with('success', 'Thao tác thành công!');
            } else {
                return redirect()->back()->with('error', 'Có lỗi xảy ra trong quá trình thao tác.');
            }
        } else {
            // Nếu không có thay đổi, không cần thực hiện cập nhật
            return redirect()->route('hoatchat')->with('success', 'Không có sự thay đổi nào được thực hiện.');
        }
    }
    public function xoa_hoatchat($id)
    {
        DB::table('hoatchat')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);

        return redirect()->route('hoatchat')->with('success', 'Hoạt chất đã được xóa thành công.');
    }
    // kết thúc hoạt chất
    // hashtag
    public function hashtag(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $data = $request->input('search');

        // Lưu dữ liệu tìm kiếm vào Session
        session(['search_data' => $data]);

        // Bạn có thể đặt tên biến khác để tránh ghi đè
        $baseQuery = DB::table('hashtag')
            ->select('*')->whereNull('deleted_at');

        // Kiểm tra xem có thông tin tìm kiếm trong Session không
        if (session()->has('search_data')) {
            $searchData = session('search_data');
            $baseQuery->where(function ($query) use ($searchData) {
                $query->where('name', 'like', '%' . $searchData . '%');
            });
        }

        // Thực hiện phân trang và lấy danh sách tài khoản
        $htag = $baseQuery->paginate($itemsPerPage);

        // Thêm các tham số tìm kiếm vào URL của liên kết phân trang
        $htag->appends(['search' => $data, 'itemsPerPage' => $itemsPerPage]);

        // Truyền dữ liệu tới view
        return view('project.admin.danhmuc.hashtag.dsach', compact('htag', 'data', 'itemsPerPage'));
    }

    public function add_hashtag()
    {
        return view('project.admin.danhmuc.hashtag.themmoi');
    }

    public function save_hashtag(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:hashtag',
        ], [
            'name.unique' => 'Tên tag sản xuất đã tồn tại.',
        ]);

        $input = $request->all();
        $nhasx = DB::table('hashtag')->insert([
            'name' => $input['name'],
            'created_at' => now(),
        ]);
        if ($nhasx) {
            return redirect()->route('hashtag')->with('success', 'Thao tác thành công!');
        } else {
            return redirect()->back()->with('error', 'Có lỗi xảy ra trong quá trình thao tác.')->withInput();
        }
    }

    public function edit_hashtag($id)
    {
        $htag = DB::table('hashtag')->where('id', $id)->first();

        return view('project.admin.danhmuc.hashtag.edit', compact('htag'));
    }

    public function update_hashtag(Request $request, $id)
    {
        // Kiểm tra xem dữ liệu mới có thay đổi so với dữ liệu hiện tại không
        $currentData = DB::table('hashtag')->where('id', $id)->first();

        $validatedData = $request->validate([
            'name' => ['required', Rule::unique('hashtag')->ignore($id)],
        ], [
            'name.unique' => 'Tag đã tồn tại.',
        ]);

        // Kiểm tra xem có sự thay đổi không
        if ($request['name'] !== $currentData->name) {
            // Nếu có thay đổi, thực hiện cập nhật
            $update = DB::table('hashtag')->where('id', $id)->update([
                'name' => $request['name']
            ]);

            if ($update) {
                return redirect()->route('hashtag')->with('success', 'Thao tác thành công!');
            } else {
                return redirect()->back()->with('error', 'Có lỗi xảy ra trong quá trình thao tác.');
            }
        } else {
            // Nếu không có thay đổi, không cần thực hiện cập nhật
            return redirect()->route('hashtag')->with('success', 'Không có sự thay đổi nào được thực hiện.');
        }
    }
    public function xoa_hashtag($id)
    {
        DB::table('hashtag')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);

        return redirect()->route('hashtag')->with('success', 'Tag đã được xóa thành công.');
    }
    //kết thúc hashtag
    //kết thúc danh mục sản phẩm

    // quản lý hạng thành viên
    public function hang_tv(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $data = $request->input('search');

        // Lưu dữ liệu tìm kiếm vào Session
        session(['search_data' => $data]);

        // Bạn có thể đặt tên biến khác để tránh ghi đè
        $baseQuery = DB::table('hang_thanh_vien')
            ->select('*')->whereNull('deleted_at');

        // Kiểm tra xem có thông tin tìm kiếm trong Session không
        if (session()->has('search_data')) {
            $searchData = session('search_data');
            $baseQuery->where(function ($query) use ($searchData) {
                $query->where('title', 'like', '%' . $searchData . '%');
            });
        }

        // Thực hiện phân trang và lấy danh sách tài khoản
        $h_tv = $baseQuery->paginate($itemsPerPage);

        // Thêm các tham số tìm kiếm vào URL của liên kết phân trang
        $h_tv->appends(['search' => $data, 'itemsPerPage' => $itemsPerPage]);

        // Truyền dữ liệu tới view
        return view('project.admin.hang_thanh_vien.dsach', compact('h_tv', 'data', 'itemsPerPage'));
    }

    public function add_hang_tv()
    {
        return view('project.admin.hang_thanh_vien.themmoi');
    }

    public function save_hang_tv(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|unique:hang_thanh_vien',
            'thumbnail' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'muctien' => 'required',
            'content' => 'required'
        ], [
            'title.required' => 'Tên tag sản xuất không được để trống.',
            'title.unique' => 'Tên tag sản xuất đã tồn tại.',
            'thumbnail.image' => 'Hình ảnh không hợp lệ.',
            'thumbnail.mimes' => 'Hình ảnh phải có định dạng là jpeg, png, jpg hoặc gif.',
            'thumbnail.max' => 'Hình ảnh không được vượt quá kích thước 5MB.',
            'muctien.required' => 'Mục tiêu không được để trống.',
            'content.required' => 'Nội dung không được để trống.',
        ]);
        $input = $request->all();
        $nhasx = DB::table('hang_thanh_vien')->insert([
            'title' => $input['title'],
            'muctien' => $input['muctien'],
            'content' => $input['content'],
            'status' => $input['status'],
            'created_at' => now(),
        ]);
        if ($nhasx) {
            return redirect()->route('hang_tv')->with('success', 'Thao tác thành công!');
        } else {
            return redirect()->back()->with('error', 'Có lỗi xảy ra trong quá trình thao tác.')->withInput();
        }
    }
    //kết thúc hạng thành viên

    
}
