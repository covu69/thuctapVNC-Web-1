<!DOCTYPE html>
<html lang="en">

<head>
    @include('project.admin.layout.header')
</head>

<body>
    @include('project.admin.layout.sider-bar')
    <div class="container tm-mt-big tm-mb-big">
        <div class="row">
            <div class="col-xl-9 col-lg-10 col-md-12 col-sm-12 mx-auto">
                <div class="tm-bg-primary-dark tm-block tm-block-h-auto">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="tm-block-title d-inline-block">Chỉnh sửa thông tin</h2>
                        </div>
                    </div>
                    <div class="row tm-edit-product-row">
                        <div class="col-xl-6 col-lg-6 col-md-12">
                            <form action="{{route('update_user',$edit_user->id)}}" enctype="multipart/form-data" method="post" class="tm-edit-product-form">
                                @csrf
                                @method('PUT')
                                <div class="form-group mb-3">
                                    <label for="name">Mã đơn vị
                                    </label>
                                    <input id="Ma_don_vi" name="Ma_don_vi" type="text" value="{{$edit_user->Ma_don_vi}}" class="form-control validate" />
                                    @error('name')
                                    <div class="alert alert-danger" id="nameError">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name">Họ tên
                                    </label>
                                    <input id="name" name="name" type="text" value="{{$edit_user->name}}" class="form-control validate" />
                                    @error('name')
                                    <div class="alert alert-danger" id="nameError">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="email">Email</label>
                                    <input id="email" name="email" type="text" value="{{$edit_user->email}}" class="form-control validate" />
                                    @error('email')
                                    <div class="alert alert-danger" id="nameError">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password">Mật khẩu</label>
                                    <input id="password" name="password" type="password" value="{{$edit_user->password}}" class="form-control validate" />
                                </div>

                                <div class="form-group mb-3">
                                    <label for="phone">Số điện thoại</label>
                                    <input id="sodienthoai" name="sodienthoai" type="text" value="{{$edit_user->sodienthoai}}" class="form-control validate" />
                                    @error('sodienthoai')
                                    <div class="alert alert-danger" id="nameError">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="quyen">Quyền</label>
                                    <select id="quyen" name="role" class="form-control" required>
                                        <option value="0" @if(old('role', $edit_user->role) == 0) selected @endif>Quyền user</option>
                                        <option value="1" @if(old('role', $edit_user->role) == 1) selected @endif>Quyền admin</option>
                                    </select>
                                    @error('role')
                                    <div class="alert alert-danger" id="quyenError">{{ $message }}</div>
                                    @enderror
                                </div>

                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 mx-auto mb-4">
                            <div class="tm-product-img-edit mx-auto">
                                <img src="{{asset('admin/img/product-image.jpg')}}" alt="Product image" class="img-fluid d-block mx-auto">
                                <i class="fas fa-cloud-upload-alt tm-upload-icon" onclick="document.getElementById('fileInput').click();"></i>
                            </div>
                            <div class="custom-file mt-3 mb-3">
                                <input id="fileInput" type="file" style="display:none;" />
                                <input type="button" class="btn btn-primary btn-block mx-auto" value="CHANGE IMAGE NOW" onclick="document.getElementById('fileInput').click();" />
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block text-uppercase">Update Now</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('project.admin.layout.footer')

    <script src="js/jquery-3.3.1.min.js"></script>
    <!-- https://jquery.com/download/ -->
    <script src="jquery-ui-datepicker/jquery-ui.min.js"></script>
    <!-- https://jqueryui.com/download/ -->
    <script src="js/bootstrap.min.js"></script>
    <!-- https://getbootstrap.com/ -->
    <script>
        $(function() {
            $("#expire_date").datepicker({
                defaultDate: "10/22/2020"
            });
        });
    </script>

    <script>
        $(function() {
            // Ẩn thông báo lỗi sau 3 giây
            setTimeout(function() {
                $("#nameError").fadeOut("slow");
            }, 3000);
        });
    </script>
</body>

</html>