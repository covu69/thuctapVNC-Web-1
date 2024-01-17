<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductExport implements FromCollection, WithMapping, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function map($row): array
    {
        $hoatChatInfo = $this->getHoatChatInfo($row->hoatchat);

        $tenHoatChatArray = array_column($hoatChatInfo, 'ten_hoat_chat');
        $hamLuongArray = array_column($hoatChatInfo, 'ham_luong');
        return [
            $this->getNhomThuocName($row->id_nhomthuoc),
            $row->name,
            $row->cangnang,
            $row->unit,
            $row->quy_cach_dong_goi,
            $row->quantity,
            $row->price,
            $row->khuyen_mai,
            $this->getNhaSanXuat($row->id_nsx),
            $row->nuoc_sx,
            implode(', ', $tenHoatChatArray),
            implode(', ', $hamLuongArray),
            $row->thong_tin,
            $this->getTagNames($row->tags),
            $row->code,
            $row->sl_toi_thieu,
            $row->sp_ban_chay,
            $row->status
        ];
    }

    public function headings(): array
    {
        // Tiêu đề của các cột
        return [
            'Nhóm thuốc',
            'Tên sản phẩm thuốc',
            'Cân nặng',
            'Đơn vị tính',
            'Quy cách đóng gói',
            'Số lượng',
            'Đơn giá',
            'Khuyến mãi',
            'Nhà sản xuất',
            'Nước sản xuất',
            'Tên hoạt chất',
            'Hàm lượng',
            'Thông tin',
            'Hashtag',
            'Mã sản phẩm',
            'Số lượng tối thiểu',
            'Bán chạy',
            'Trạng thái'
        ];
    }

    public function getNhomThuocName($idNhomThuoc)
    {
        $nhomThuoc = DB::table('nhomthuoc')->where('id', $idNhomThuoc)->first();
        return $nhomThuoc ? $nhomThuoc->name : '';
    }

    public function getNhaSanXuat($id_nsx)
    {
        $nha_sx = DB::table('nhasanxuat')->where('id', $id_nsx)->first();
        return $nha_sx ? $nha_sx->name : '';
    }

    public function getTagNames($tags)
    {
        $tagIds = collect(json_decode($tags))->pluck('id_tag')->toArray();

        $tagNames = DB::table('hashtag')
            ->whereIn('id', $tagIds)
            ->pluck('name')
            ->toArray();

        return implode(', ', $tagNames);
    }
    public function getHoatChatInfo($hoatChat)
    {
        $hoatChatArray = json_decode($hoatChat, true);
        $hoatChatInfo = [];

        foreach ($hoatChatArray as $item) {
            $hoatChatId = $item['id_hoat_chat'];
            $hamLuong = $item['ham_luong'];

            $hoatChatName = DB::table('hoatchat')
                ->where('id', $hoatChatId)
                ->value('name');

            $hoatChatInfo[] = [
                'ten_hoat_chat' => $hoatChatName,
                'ham_luong' => $hamLuong,
            ];
        }

        return $hoatChatInfo;
    }
}
