<!DOCTYPE html>
<html lang="en">

<head>
    @include('project.admin.layout.header')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
        <div class="container mt-5">
            <div class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll" style="margin-top:150px">
                <h2 class="tm-block-title">Chi tiết giỏ hàng</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Số thứ tự</th>
                            <th scope="col">Tên sản phẩm</th>
                            <th scope="col">Hình ảnh</th>
                            <th scope="col">Đơn vị tính</th>
                            <th scope="col">Số lượng</th>
                            <th scope="col">Đơn giá</th>
                            <th scope="col">Khuyến mãi</th>
                            <th scope="col">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $counter = 1;
                        @endphp

                        @foreach($payment_view as $item)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>{{ $item->name }}</td>
                            <td>
                                @if($item->thumnail)
                                <img src="{{ asset('uploads/img_sp/' . $item->thumnail) }}" alt="Product Image" width="100px" height="100px">
                                @else
                                <img src="{{ asset('image/imager_loi.jpg') }}" alt="Default Image" width="100px" height="100px">
                                @endif
                            </td>
                            <td>{{ $item->unit }}</td>
                            <td>{{ $item->soluong }}</td>
                            @if($item->khuyen_mai == null)
                            <td>{{ number_format($item->dongia) }}</td>
                            @else
                            <td>
                                {{ number_format($item->dongia * (100 - $item->khuyen_mai) / 100) }} VND </br>
                                <del>{{ number_format($item->dongia) }} VND</del>
                            </td>
                            @endif

                            <td>
                                @if($item->khuyen_mai == null)
                                0
                                @else
                                {{ $item->khuyen_mai }}
                                @endif
                            </td>
                            <td>{{ number_format($item->soluong * ($item->dongia * (100 - $item->khuyen_mai) / 100)) }}</td>
                        </tr>
                    </tbody>
                    @endforeach
                </table>
                <div>
                    {{$payment_view->links()}}
                </div>
                <div style="margin-top: 10px">
                    @if($item->payment_method == 1 )
                    @if($item->voucher_code != null && $item->voucher_value > 0)
                    <b style="color: #e9ecef">Sử dụng voucher {{ $item->voucher_code }} được giảm {{ $item->voucher_value }} VND</b><br>
                    @endif
                    @if($item->use_coin == 1 && $item->coins > 0)
                    <b style="color: #e9ecef">Sử dụng coin được giảm {{ $item->coins }} VND</b><br>
                    @endif
                    <b style="color: #e9ecef">Hóa đơn đã được chuyển khoản trước (giảm 0.5%)</b></br>
                    <b style="color: #e9ecef">
                        Tổng hóa đơn: {{ number_format($totalPrice->total_price)}}đ
                        <del>{{ number_format($originalPrice)}}đ </del>
                    </b>
                    @else
                    <b style="color: #e9ecef">Tổng hóa đơn: {{ number_format($totalPrice->total_price) }} VND</b>
                    @endif
                </div>
            </div>
        </div>
        @include('project.admin.layout.footer')
    </div>

    <script src="{{asset('admin/js/jquery-3.3.1.min.js')}}"></script>
    <!-- https://jquery.com/download/ -->
    <script src="{{asset('admin/js/bootstrap.min.js')}}"></script>
    <!-- https://getbootstrap.com/ -->
    <!-- <script>
    $(document).ready(function() {
      $('.toggle-icon').click(function(e) {
        e.preventDefault();
        var target = $(this).data('target');
        $('.' + target).toggle();

        // Lưu trạng thái ẩn/hiển vào Local Storage
        localStorage.setItem(target, $('.' + target).is(':visible') ? 'visible' : 'hidden');
      });

      // Khôi phục trạng thái ẩn/hiển của các dòng từ Local Storage khi load lại trang
      $('.item-text').each(function() {
        var target = $(this).data('target');
        var storedState = localStorage.getItem(target);

        if (storedState === 'visible') {
          $('.' + target).show();
        }
      });
    });
  </script> -->

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