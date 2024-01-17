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
      grid-template-columns: 20% 30% 30%;
      border: 1px solid rgba(0, 0, 0, .125);
    }

    #loc_dk_2 {
      display: grid;
      grid-template-columns: 30% 30% 40%;
      border: 1px solid rgba(0, 0, 0, .125);
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
             grid-template-columns: 30% 20% 50%;">
            <div>
              <h2 class="tm-block-title">List of Accounts</h2>
              <select class="custom-select" id="itemsPerPage" name="itemsPerPage" onchange="updateItemsPerPage()">
                <option value="10" {{ $itemsPerPage==10 ? 'selected' : '' }}>10</option>
                <option value="20" {{ $itemsPerPage==20 ? 'selected' : '' }}>20</option>
                <option value="50" {{ $itemsPerPage==50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $itemsPerPage==100 ? 'selected' : '' }}>100</option>
              </select>
            </div>
            @if(Auth::user()->role == 1)
            <div>
              <a href="{{route('themmoi_thanh_vien')}}" class="button">Thêm mới</a>
            </div>
            <div>
              <a href="" class="button">Tải EXCEL</a>
            </div>
            @endif
          </div>
          <form action="{{ route('dai_ly') }}" method=" get" style="display:grid">
            <div class="tm-bg-primary-dark tm-block tm-block-h-auto" id="loc_dk">
              <p class="text_loc">Thời gian tạo:</p>
              <div>
                <label for="" class="text_loc">Từ ngày</label>
                <input type="date" name="from_date" id="loc">
              </div>
              <div>
                <label for="loc" class="text_loc">Đến ngày</label>
                <input type="date" id="loc" name="to_date" max="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>">
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
        <h2 class="tm-block-title">Danh sách thành viên</h2>
        <table class="table">
          <thead>
            <tr>
              <!-- <th scope="col">Chọn</th> -->
              <th scope="col">Số thứ tự</th>
              <th scope="col">Tên</th>
              <th scope="col">Tên nhà thuốc</th>
              <th scope="col">Người quản lý</th>
              <th scope="col">Số điện thoại</th>
              <th scope="col">Tỉnh</th>
              <th scope="col">Ngày tạo tài khoản</th>
              <th scope="col">Trạng thái</th>
              <th scope="col">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            @foreach($dai_ly as $dl)
            <tr>
              <!-- <td>
                <input type="checkbox" name="ids[{{$dl->id}}]" value="{{$dl->id}}">
              </td> -->
              <td><b>{{$dl->id}}</b></td>
              <td><b>{{$dl->ten}}</b></td>
              <td><b>{{$dl->ten_nha_thuoc}}</b></td>
              <td><b>{{$dl->nguoi_quan_ly}}</b></td>
              <td><b>{{$dl->so_dien_thoai}}</b></td>
              <td><b>{{$dl->tinh}}</b></td>
              <td><b>{{$dl->created_at}}</b></td>
              <td>
                @if($dl->status == 0)
                <b>Tạm thời</b>
                @elseif ($dl->status == 1)
                <b>Chờ xác thực</b>
                @else
                <b>Chính thức</b>
                @endif
              </td>
              <td>
                <a href="{{route('edit_thanh_vien',$dl->id)}}"><i class='far fa-edit' style='font-size:24px;margin-right:5px'></i></a>
                @if(Auth::user()->role == 1)
                <a href="" onclick="return confirm('Bạn có chắc muốn xóa bài viết này không?')"><i class='far fa-trash-alt' style='font-size:24px'></i></a>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        <div>
          {{$dai_ly->links()}}
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