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
                            <a href="{{route('add_hang_tv')}}" class="button">Thêm mới</a>
                        </div>
                    </div>
                    <form action="{{route('hang_tv')}}" method=" get">
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

            <!-- row -->
            <!-- <div class="row tm-content-row">
          <div class="tm-block-col tm-col-avatar">
            <div class="tm-bg-primary-dark tm-block tm-block-avatar">
              <h2 class="tm-block-title">Change Avatar</h2>
              <div class="tm-avatar-container">
                <img
                  src="img/avatar.png"
                  alt="Avatar"
                  class="tm-avatar img-fluid mb-4"
                />
                <a href="#" class="tm-avatar-delete-link">
                  <i class="far fa-trash-alt tm-product-delete-icon"></i>
                </a>
              </div>
              <button class="btn btn-primary btn-block text-uppercase">
                Upload New Photo
              </button>
            </div>
          </div>
          <div class="tm-block-col tm-col-account-settings">
            <div class="tm-bg-primary-dark tm-block tm-block-settings">
              <h2 class="tm-block-title">Account Settings</h2>
              <form action="" class="tm-signup-form row">
                <div class="form-group col-lg-6">
                  <label for="name">Account Name</label>
                  <input
                    id="name"
                    name="name"
                    type="text"
                    class="form-control validate"
                  />
                </div>
                <div class="form-group col-lg-6">
                  <label for="email">Account Email</label>
                  <input
                    id="email"
                    name="email"
                    type="email"
                    class="form-control validate"
                  />
                </div>
                <div class="form-group col-lg-6">
                  <label for="password">Password</label>
                  <input
                    id="password"
                    name="password"
                    type="password"
                    class="form-control validate"
                  />
                </div>
                <div class="form-group col-lg-6">
                  <label for="password2">Re-enter Password</label>
                  <input
                    id="password2"
                    name="password2"
                    type="password"
                    class="form-control validate"
                  />
                </div>
                <div class="form-group col-lg-6">
                  <label for="phone">Phone</label>
                  <input
                    id="phone"
                    name="phone"
                    type="tel"
                    class="form-control validate"
                  />
                </div>
                <div class="form-group col-lg-6">
                  <label class="tm-hide-sm">&nbsp;</label>
                  <button
                    type="submit"
                    class="btn btn-primary btn-block text-uppercase"
                  >
                    Update Your Profile
                  </button>
                </div>
                <div class="col-12">
                  <button
                    type="submit"
                    class="btn btn-primary btn-block text-uppercase"
                  >
                    Delete Your Account
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div> -->
            <div class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll">
                <h2 class="tm-block-title">Danh sách hạng thành viên</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <!-- <th scope="col">Chọn</th> -->
                            <th scope="col">Số thứ tự</th>
                            <th scope="col">Tên hạng thành viên</th>
                            <th scope="col">Mức tiền VND</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($h_tv as $item)
                        <tr>
                            <td><b>{{$item->id}}</b></td>
                            <td><b>{{$item->title}}</b></td>
                            <td><b>{{$item->muctien}}</b></td>
                            @if($item->status == 1)
                            <td>Hoạt Động</td>
                            @else
                            <td>Không Hoạt Động</td>
                            @endif
                            <td>
                                <a href="{{route('edit_hashtag',$item->id)}}"><i class='far fa-edit' style='font-size:24px;margin-right:5px'></i></a>
                                <a href="{{route('xoa_hashtag',$item->id)}}" onclick="return confirm('Bạn có chắc muốn xóa Tag này không?')"><i class='far fa-trash-alt' style='font-size:24px'></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div>
                    {{$h_tv->links()}}
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