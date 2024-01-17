<!DOCTYPE html>
<html lang="en">

<head>
    @include('project.admin.layout.header')
    <style>
        a.button {
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #4CAF50;
            /* Màu nền */
            color: white;
            /* Màu chữ */
            border: 1px solid #4CAF50;
            /* Viền */
            border-radius: 4px;
            /* Bo góc */
            transition: background-color 0.3s;
            /* Hiệu ứng hover */
            margin-top: 54px;
        }

        a.button:hover {
            background-color: #45a049;
            /* Màu nền khi hover */
            border-color: #45a049;
            /* Màu viền khi hover */
        }
    </style>
</head>

<body id="reportsPage">
    <div class="" id="home">
        @include('project.admin.layout.sider-bar')
        @if(session('success'))
        <div id="success-alert" class="alert alert-success">
            {{ session('success') }}
        </div>

        <script>
            setTimeout(function() {
                document.getElementById('success-alert').style.display = 'none';
            }, 5000); // 5000 milliseconds = 5 seconds
        </script>
        @endif

        <div class="container mt-5">
            <div class="row tm-content-row">
                <div class="col-12 tm-block-col">
                    <div class="tm-bg-primary-dark tm-block tm-block-h-auto" style="      display: grid;
             grid-template-columns: 30% 70%;">
                        <div>
                            <h2 class="tm-block-title">List of Accounts</h2>
                            <select class="custom-select" id="itemsPerPage" name="itemsPerPage" onchange="updateItemsPerPage()">
                                <option value="10" {{ $itemsPerPage==10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ $itemsPerPage==20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ $itemsPerPage==50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $itemsPerPage==100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                        <div>
                            <a href="{{route('add_voucher')}}" class="button">Thêm mới</a>
                        </div>
                    </div>
                    <form action="{{route('voucher')}}" method=" get">
                        <div class="tm-bg-primary-dark tm-block tm-block-h-auto" style="      display: grid;
             grid-template-columns: 80% 20%;">
                            <div style="margin-right:5px">
                                <input type="text" class="form-control" name="search" id="search-field" placeholder="Search" @if(isset($data)){ value="{{$data}}" } @endif style="border-radius: 5px;">
                            </div>
                            <div class="input-group-append">
                                <button class="btn btn-success" type="submit" style="border-radius: 5px;">Tìm kiếm</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll">
                <h2 class="tm-block-title">Danh sách mã giảm giá</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <!-- <th scope="col">Chọn</th> -->
                            <th scope="col">ID</th>
                            <th scope="col">Mã giảm giá</th>
                            <th scope="col">Tiêu đề</th>
                            <th scope="col">Loại voucher</th>
                            <th scope="col">Ngày bắt đầu</th>
                            <th scope="col">Ngày kết thúc</th>
                            <th scope="col">Thao tác</th>
                        </tr>
                    </thead> 
                    <tbody>
                        @foreach($giam_gia as $item)
                        <tr>
                            <td><b>{{$item->id}}</b></td>
                            <td><b>{{$item->ma_giam_gia}}</b></td>
                            <td><b>{{$item->tieu_de}}</b></td>
                            @if($item->loai == 0)
                            <td>Sử dụng 1 lần</td>
                            @else
                            <td>Sử dụng nhiều lần</td>
                            @endif
                            <td><b>{{$item->ngay_bat_dau}}</b></td>
                            <td><b>{{$item->ngay_ket_thuc}}</b></td>
                            <td>
                                <a href="{{route('edit_voucher',$item->id)}}"><i class='far fa-edit' style='font-size:24px;margin-right:5px'></i></a>
                                <a href="" onclick="return confirm('Bạn có chắc muốn xóa Voucher này không?')"><i class='far fa-trash-alt' style='font-size:24px'></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div>
                    {{$giam_gia->links()}}
                </div>
            </div>
        </div>
        @include('project.admin.layout.footer')
    </div>

    <script src="{{asset('admin/js/jquery-3.3.1.min.js')}}"></script>
    <!-- https://jquery.com/download/ -->
    <script src="{{asset('admin/js/bootstrap.min.js')}}"></script>
    <!-- https://getbootstrap.com/ -->
    <script>
        function updateItemsPerPage() {
            var itemsPerPage = document.getElementById("itemsPerPage").value;
            var currentUrl = window.location.href;

            // Sử dụng URLSearchParams để thay đổi tham số itemsPerPage trong URL
            var searchParams = new URLSearchParams(window.location.search);
            searchParams.set("itemsPerPage", itemsPerPage);
            var newUrl = currentUrl.split('?')[0] + '?' + searchParams.toString();
            window.location.href = newUrl;
        }
    </script>
</body>

</html>