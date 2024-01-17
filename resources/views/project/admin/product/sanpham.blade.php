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

    label.button {
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

    .tm-block-title button {
      border-radius: 5px;
      width: 150px;
    }
  </style>

</head>

<body id="reportsPage">
  <div class="" id="home">
    @include('project.admin.layout.sider-bar')
    @if (session('error'))
    <div class="alert alert-danger">
      {{ session('error') }}
      @if (session('errorRows'))
      <ul>
        @foreach (session('errorRows') as $errorRow)
        <li>{{ json_encode($errorRow) }}</li>
        @endforeach
      </ul>
      @endif
    </div>
    @endif

    @if (session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
    @endif
    <div class="container mt-5">
      <div class="row tm-content-row">
        <div class="col-12 tm-block-col">
          <div class="tm-bg-primary-dark tm-block tm-block-h-auto" style="      display: grid;
             grid-template-columns: 30% 20% 20% 20%;">
            <div>
              <h2 class="tm-block-title">List of Product</h2>
              <select class="custom-select" id="itemsPerPage" name="itemsPerPage" onchange="updateItemsPerPage()">
                <option value="10" {{ $itemsPerPage==10 ? 'selected' : '' }}>10</option>
                <option value="20" {{ $itemsPerPage==20 ? 'selected' : '' }}>20</option>
                <option value="50" {{ $itemsPerPage==50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $itemsPerPage==100 ? 'selected' : '' }}>100</option>
              </select>
            </div>
            <div>
              <a href="{{route('add_sanpham')}}" class="button">Thêm mới</a>
            </div>
            <div>
              <a href="{{route('exportDataToExcel')}}" class="button"><i class='fas fa-file-export'></i>Tải EXCEL</a>
            </div>
            <div>
              <form id="importForm" action="{{ route('import') }}" method="post" enctype="multipart/form-data">
                @csrf
                <label for="file" class="button">Import EXCEL</label>
                <input type="file" name="file" id="file" style="display:none;" accept=".xlsx, .csv" onchange="validateFile()">
                <button type="submit" style="display:none;" id="submitButton">Submit</button>
              </form>
            </div>
          </div>
          <form action="{{ route('sanpham') }}" method=" get">
            <div class="tm-bg-primary-dark tm-block tm-block-h-auto">
              <select name="status" id="" style="height: 50px; width:20%;border-radius: 5px">
                <option value="">Chọn sản phẩm</option>
                <option value="0">Ẩn tạm thời</option>
                <option value="1">Hiển thị</option>
                <option value="2">Sản phẩm hết hàng</option>
              </select>
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
      <form action="{{ route('updateProductStatus') }}" method="post">
        @csrf
        <div class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll">
          <div class="tm-block-title">
            <button type="submit" name="action" value="hide_temporarily">Ẩn tạm thời</button>
            <button type="submit" name="action" value="show">Hiển thị</button>
          </div>
          <table class="table">
            <thead>
              <tr>
                <!-- <th scope="col">Chọn</th> -->
                <th scope="col">Chọn</th>
                <th scope="col">Số thứ tự</th>
                <th scope="col">Hình ảnh</th>
                <th scope="col">Tên sản phẩm thuốc</th>
                <th scope="col">Trạng thái</th>
                <th scope="col">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              @foreach($sp as $item)
              <tr>
                <td>
                  <input type="checkbox" name="ids[{{$item->id}}]" value="{{$item->id}}">
                </td>
                <td><b>{{$item->id}}</b></td>
                <td>
                  @if($item->thumnail)
                  <img src="{{ asset('uploads/img_sp/' . $item->thumnail) }}" alt="Product Image" width="100px" height="100px">
                  @else
                  <img src="{{ asset('image/anh_loi.jpg') }}" alt="Default Image" width="100px" height="100px">
                  @endif
                </td>
                <td>
                  <b>{{$item->name}}</b></br>
                  @if($item->sp_ban_chay == 1)
                  <b class="item-text item-text{{$item->id}}" data-target="item-text{{$item->id}}" style="color:#45a049">Ghim sản phẩm bán chạy</b></br>
                  @endif
                  @if($item->quantity == 0 || $item->price == 0 )
                  <b style="color:red">Hết hàng</b>
                  @endif
                </td>
                @if($item->status == 1)
                <td><b>Hiển thị</b></td>
                @else
                <td><b>Ẩn tạm thời</b></td>
                @endif
                <td>
                  <a href="{{route('product_ghim',$item->id)}}" class="toggle-icon" data-target="item-text{{$item->id}}">
                    <i class="fa fa-thumb-tack" style="font-size:24px;margin-right:5px"></i>
                  </a>
                  <a href="{{route('edit_product',$item->id)}}"><i class='far fa-edit' style='font-size:24px;margin-right:5px'></i></a>
                  <a href="{{route('destroy_product',$item->id)}}" onclick="return confirm('Bạn có chắc muốn sản phẩm này không?')"><i class='far fa-trash-alt' style='font-size:24px'></i></a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <div>
            {{$sp->links()}}
          </div>
        </div>
        <form>
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
    function validateFile() {
      var inputFile = document.getElementById('file');
      var fileName = inputFile.value;
      var ext = fileName.split('.').pop().toLowerCase();
      var submitButton = document.getElementById('submitButton');

      if (ext !== 'xlsx' && ext !== 'csv') {
        alert('Chỉ chấp nhận file Excel (xlsx) hoặc CSV (csv). Vui lòng chọn lại.');
        // Hoặc bạn có thể sử dụng thư viện thông báo đẹp hơn như SweetAlert
        // swal('Lỗi', 'Chỉ chấp nhận file Excel (xlsx) hoặc CSV (csv). Vui lòng chọn lại.', 'error');
        inputFile.value = ''; // Xóa giá trị file đã chọn để người dùng chọn lại
        submitButton.style.display = 'none'; // Ẩn nút submit khi file không hợp lệ
      } else {
        document.getElementById('importForm').submit(); // Tự động submit form
      }
    }
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