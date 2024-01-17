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

        #loc_dk {
            display: grid;
            grid-template-columns: 15% 25% 25% 35%;
            border: 1px solid rgba(0, 0, 0, .125);
        }

        #loc_dk_2 {
            display: grid;
            grid-template-columns: 30% 30% 40%;
            border: 1px solid rgba(0, 0, 0, .125);
        }

        #loc {
            width: 50%;
            height: 30px;
            border-radius: 5px;
        }

        .text_loc {
            color: #ffff;
        }

        .js-example-basic-single {
            width: 50%;
            height: 30px;
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
                        <!-- <div>
                            <a href="" class="button">Tải EXCEl</a>
                        </div> -->
                    </div>

                    <form action="{{ route('muahang') }}" method=" get">
                        <div class="tm-bg-primary-dark tm-block tm-block-h-auto" id="loc_dk">
                            <div>
                                <label for="loc" class="text_loc">Thời gian tạo:</label>
                            </div>
                            <div>
                                <label for="" class="text_loc">Từ ngày</label>
                                <input type="date" name="from_date" id="loc">
                            </div>
                            <div>
                                <label for="loc" class="text_loc">Đến ngày</label>
                                <input type="date" id="loc" name="to_date" max="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div>
                                <label for="loc" class="text_loc">Trạng thái</label>
                                <select name="status" id="loc">
                                    <option value="">Chọn trạng thái</option>
                                    @foreach($trang_thai as $item)
                                    <option value="{{ $item->payment_status }}">{{ $item->payment_status == 0 ? 'Chờ thanh toán' : 'Đã thanh toán' }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="tm-bg-primary-dark tm-block tm-block-h-auto" id="loc_dk_2">
                            <div>
                                <label for="loc" class="text_loc">Tên</label>
                                <select class="js-example-basic-single" name="ten">
                                    <option value="">Chọn tên</option>
                                    @foreach($ten as $item)
                                    <option value="{{ $item->id }}">{{ $item->ten }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="" class="text_loc">Tỉnh</label>
                                <select class="js-example-basic-single" name="tinh">
                                    <option value="">Chọn tỉnh</option>
                                    @foreach($tinh as $item)
                                    <option value="{{ $item->id }}">{{ $item->ten }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <div>
                                    <label for="" class="text_loc">Nhân viên quản lý</label>
                                    <select class="js-example-basic-single" name="nhan_vien_quan_ly">
                                        <option value="">Chọn nhân viên quản lý</option>
                                        @foreach($nv_ql as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
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
                <h2 class="tm-block-title">Lịch sử mua hàng</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <!-- <th scope="col">Chọn</th> -->
                            <th scope="col">Số thứ tự</th>
                            <th scope="col">Mã khách hàng</th>
                            <th scope="col">Tên nhà thuốc</th>
                            <th scope="col">Nhân viên quản lý</th>
                            <th scope="col">Số điện thoại</th>
                            <th scope="col">Tỉnh</th>
                            <th scope="col">Tổng tiền</th>
                            <th scope="col">Hạng thành viên</th>
                            <th scope="col">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $counter = 1;
                        @endphp

                        @foreach($payment as $item)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td><b>{{ $item->ma_khach_hang }}</b></td>
                            <td><b>{{ $item->ten_nha_thuoc }}</b></td>
                            <td><b>{{ $item->ten_nguoi_quan_ly }}</b></td>
                            <td><b>{{ $item->so_dien_thoai }}</b></td>
                            <td><b>{{ $item->ten_tinh }}</b></td>
                            <td><b>{{ number_format($item->total_price, 0, ',', '.') }}</b></td>
                            <td>
                                @if($item->id_hang_tv == 1)
                                <b>Hạng vàng</b>
                                @elseif ($item->id_hang_tv ==2 )
                                <b>Hạng bạc</b>
                                @else
                                <b>Hạng đồng</b>
                                @endif
                            </td>
                            <td>
                                <a href="{{route('detail',$item->id_member)}}"><i class='fas fa-shopping-cart' style='font-size:24px;margin-right:5px'></i></a>
                                <a href=""><i class='fa fa-heart' style='font-size:24px'></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
                <div>
                    {{$payment->links()}}
                </div>
            </div>
        </div>
        @include('project.admin.layout.footer')
    </div>

    <!-- <script src="{{asset('admin/js/jquery-3.3.1.min.js')}}"></script> -->
    <!-- https://jquery.com/download/ -->
    <script src="{{asset('admin/js/bootstrap.min.js')}}"></script>
    <!-- https://getbootstrap.com/ -->
    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>
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