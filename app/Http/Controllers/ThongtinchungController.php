<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MicrosoftAzure\Storage\Common\Internal\Validate;
use Illuminate\Validation\ValidationException;

class ThongtinchungController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $data = $request->input('search');

        // Lưu dữ liệu tìm kiếm vào Session
        session(['search_data' => $data]);

        // Bạn có thể đặt tên biến khác để tránh ghi đè
        $baseQuery = DB::table('thong_tin_chung')
            ->select('*')
            ->where('deleted_at', null);
        // Kiểm tra xem có thông tin tìm kiếm trong Session không
        if (session()->has('search_data')) {
            $searchData = session('search_data');
            $baseQuery->where(function ($query) use ($searchData) {
                $query->where('tieu_de', 'like', '%' . $searchData . '%');
                // ->orWhere('mo_ta', 'like', '%' . $searchData . '%');
            });
        }

        $thong_tin_chung = $baseQuery->paginate($itemsPerPage);

        // Thêm các tham số tìm kiếm vào URL của liên kết phân trang
        $thong_tin_chung->appends(['search' => $data, 'itemsPerPage' => $itemsPerPage]);

        // Truyền dữ liệu tới view
        return view('project.admin.thong_tin_chung.list', compact('thong_tin_chung', 'data', 'itemsPerPage'));
    }

    public function add_thong_tin_chung()
    {
        return view('project.admin.thong_tin_chung.themmoi');
    }

    public function save(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tieu_de' => 'required|unique:thong_tin_chung',
                'noi_dung' => 'required'
            ]);
            $input = $request->all();
            // dd($input);
            $news = DB::table('thong_tin_chung')->insert([
                'tieu_de' => $input['tieu_de'],
                'status' => $input['status'],
                'noi_dung' => $input['noi_dung'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($news) {
                return redirect()->route('thong_tin_chung')->with('success', 'Thao tác thành công!');
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

    public function edit($id)
    {
        $thong_tin_chung = DB::table('thong_tin_chung')->whereNull('deleted_at')->where('id', $id)->first();

        return view('project.admin.thong_tin_chung.edit', compact('thong_tin_chung'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'tieu_de' => 'required|unique:thong_tin_chung,tieu_de,' . $id,
                'noi_dung' => 'required'
            ]);
            $input = $request->all();
            $thong_tin_chung = DB::table('thong_tin_chung')
                ->where('id', $id)
                ->update([
                    'tieu_de' => $input['tieu_de'],
                    'status' => $input['status'],
                    'noi_dung' => $input['noi_dung'],
                    'updated_at' => now(),
                ]);

            if ($thong_tin_chung) {
                return redirect()->route('thong_tin_chung')->with('success', 'Thao tác thành công!');
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
