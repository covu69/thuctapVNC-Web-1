<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use MicrosoftAzure\Storage\Common\Internal\Validate;
use Illuminate\Validation\ValidationException;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $data = $request->input('search');

        // Lưu dữ liệu tìm kiếm vào Session
        session(['search_data' => $data]);

        // Bạn có thể đặt tên biến khác để tránh ghi đè
        $baseQuery = DB::table('news')
            ->select('*')
            ->where('deleted_at', null);
        // Kiểm tra xem có thông tin tìm kiếm trong Session không
        if (session()->has('search_data')) {
            $searchData = session('search_data');
            $baseQuery->where(function ($query) use ($searchData) {
                $query->where('tieu_de', 'like', '%' . $searchData . '%')
                    ->orWhere('mo_ta', 'like', '%' . $searchData . '%');
            });
        }

        // Thực hiện phân trang và lấy danh sách tài khoản
        $news = $baseQuery->paginate($itemsPerPage);

        // Thêm các tham số tìm kiếm vào URL của liên kết phân trang
        $news->appends(['search' => $data, 'itemsPerPage' => $itemsPerPage]);

        // Truyền dữ liệu tới view
        return view('project.admin.news.list', compact('news', 'data', 'itemsPerPage'));
    }

    public function add_news(Request $request)
    {

        return view('project.admin.news.themmoi');
    }

    public function save_news(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tieu_de' => 'required|unique:news',
                'thumnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,tmp|max:10240',
                'mo_ta' => 'required',
                'noi_dung' => 'required'
            ]);
            if ($request->hasFile('thumnail')) {
                $file = $request->file('thumnail');
                $ext = $file->getClientOriginalExtension();
                $filename = time() . '.' . $ext;
                $file->move('uploads/news/', $filename);
            }
            $input = $request->all();
            $news = DB::table('news')->insert([
                'tieu_de' => $input['tieu_de'],
                'thumnail' => $filename,
                'mo_ta' => $input['mo_ta'],
                'ngay_cong_khai' => $input['ngay_cong_khai'],
                'status' => $input['status'],
                'noi_bat' => $input['noi_bat'],
                'top_news' => $input['top_news'],
                'noi_dung' => $input['noi_dung'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($news) {
                return redirect()->route('tin_tuc')->with('success', 'Thao tác thành công!');
            } else {
                return redirect()->route('add_news')->with('error', 'Có lỗi xảy ra trong quá trình thao tác.')->withInput();
            }
        } catch (ValidationException $e) {
            dd($e->errors());
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function edit($id)
    {
        $news = DB::table('news')->where('id', $id)->whereNull('deleted_at')->first();

        return view('project.admin.news.edit', compact('news'));
    }

    public function update_news(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'tieu_de' => 'required|unique:news,tieu_de,' . $id,
                'thumnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,tmp|max:10240',
                'mo_ta' => 'required',
                'noi_dung' => 'required'
            ]);
            
            if ($request->hasFile('thumnail')) {
                $file = $request->file('thumnail');
                $ext = $file->getClientOriginalExtension();
                $filename = time() . '.' . $ext;
                $file->move('uploads/news/', $filename);
            } else {
                // Nếu không có tệp tin mới hoặc tệp tin không hợp lệ, giữ nguyên ảnh cũ
                $filename = DB::table('news')->where('id', $id)->value('thumnail');
            }
            $input = $request->all();
            // dd($input);
            $news = DB::table('news')
                ->where('id', $id)
                ->update([
                    'tieu_de' => $input['tieu_de'],
                    'thumnail' => $filename,
                    'mo_ta' => $input['mo_ta'],
                    'ngay_cong_khai' => $input['ngay_cong_khai'],
                    'status' => $input['status'],
                    'noi_bat' => $input['noi_bat'],
                    'top_news' => $input['top_news'],
                    'noi_dung' => $input['noi_dung'],
                    'updated_at' => now(),
                ]);

            if ($news) {
                return redirect()->route('tin_tuc')->with('success', 'Thao tác thành công!');
            } else {
                return redirect()->back()->with('error', 'Có lỗi xảy ra trong quá trình thao tác.')->withInput();
            }
        } catch (ValidationException $e) {
            // Validation failed
            dd($e->errors());
        } catch (\Exception $e) {
            // Other exceptions
            dd($e->getMessage());
        }
    }

}
