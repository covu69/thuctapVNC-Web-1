<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        #previewImageContainer {
            max-width: 100%;
            /* Đặt chiều rộng tối đa là 100% của thẻ cha */
            max-height: 300px;
            /* Đặt chiều cao tối đa để tránh phá phông, điều chỉnh theo nhu cầu của bạn */
            overflow: hidden;
            /* Ẩn phần nằm ngoài phạm vi kích thước của thẻ */
        }

        #previewImageContainer img {
            width: 100%;
            /* Đặt chiều rộng của hình ảnh là 100% để nó lấp đầy phần kích thước của thẻ */
            height: 250px;
            /* Tự động tính toán chiều cao để giữ tỉ lệ khung hình ban đầu */
        }
    </style>
    @include('project.admin.layout.header')
</head>

<body>
    @include('project.admin.layout.sider-bar')
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Rest of your form -->

    <div class="container tm-mt-big tm-mb-big">
        <div class="row">
            <div class="col-xl-9 col-lg-10 col-md-12 col-sm-12 mx-auto">
                <div class="tm-bg-primary-dark tm-block tm-block-h-auto">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="tm-block-title d-inline-block">Tạo thành viên</h2>
                        </div>
                    </div>
                    <div class="row tm-edit-product-row">
                        <div class="col-xl-6 col-lg-6 col-md-12">
                            <form action="{{ route('save_thanh_vien') }}" method="post" enctype="multipart/form-data" class="tm-edit-product-form">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="name">Mã khách hàng
                                    </label>
                                    <input id="ma_khach_hang" name="ma_khach_hang" type="text" class="form-control validate" value="{{ old('ma_khach_hang') }}" />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name">Tên
                                    </label>
                                    <input id="ten" name="ten" type="text" class="form-control validate" value="{{ old('ten') }}" required />
                                    @error('name')
                                    <div class="alert alert-danger" id="nameError">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name">Tên nhà thuốc
                                    </label>
                                    <input id="ten_nha_thuoc" name="ten_nha_thuoc" type="text" class="form-control validate" value="{{ old('ten_nha_thuoc') }}" required />
                                    @error('name')
                                    <div class="alert alert-danger" id="nameError">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="email">Địa chỉ thư điện tử</label>
                                    <input id="email" name="email" type="text" class="form-control validate" value="{{ old('email') }}" />
                                    @error('email')
                                    <div class="alert alert-danger" id="nameError">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="sodienthoai">Số điện thoại</label>
                                    <input id="so_dien_thoai" name="so_dien_thoai" type="number" class="form-control validate" value="{{ old('so_dien_thoai') }}" required />
                                    @error('sodienthoai')
                                    <div class="alert alert-danger" id="nameError">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="form-group mb-3 col-xs-12 col-sm-6">
                                        <label for="password">Mật khẩu</label>
                                        <input id="password" name="password" type="password" class="form-control validate" value="{{ old('password') }}" required />
                                        <small id="passwordHelp" class="form-text text-muted">Mật khẩu phải chứa ít nhất 1 ký tự đặc biệt, 1 chữ hoa, 1 số, và tối thiểu 6 ký tự.</small>
                                    </div>
                                    <div class="form-group mb-3 col-xs-12 col-sm-6">
                                        <label for="">Tỉnh</label>
                                        <select class="custom-select tm-select-accounts" name="id_tinh" style="width:100%" id="">
                                            @foreach ($tinh as $t)
                                            <option value="{{ $t->id }}">{{ $t->ten }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="trangthai">Trạng thái</label>
                                    <select id="trangthai" name="status" class="custom-select tm-select-accounts" style="width:100%" required>
                                        <option value="0" @if(old('trangthai')=='Tạm thời' ) selected @endif>Chờ xác thực</option>
                                        <option value="1" @if(old('trangthai')=='Chờ xác thực' ) selected @endif>Tạm thời</option>
                                        <option value="2" @if(old('trangthai')=='Chính thức' ) selected @endif>Chính thức</option>
                                    </select>
                                    @error('trangthai')
                                    <div class="alert alert-danger" id="trangthaiError">{{ $message }}</div>
                                    @enderror
                                </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 mx-auto mb-4">
                            <div class="tm-product-img-dummy mx-auto" id="previewImageContainer">
                                <!-- Hiển thị hình ảnh đã chọn ở đây -->
                            </div>
                            <div class="custom-file mt-3 mb-3">
                                <input id="fileInput" type="file" name="thumbnail" style="display:none;" onchange="displayImage(this);" />
                                <input type="button" class="btn btn-primary btn-block mx-auto" value="UPLOAD IMAGE" onclick="document.getElementById('fileInput').click();" />
                            </div>
                            <div class="form-group mb-3" style="margin-top: 90px;">
                                <label for="sodienthoai">Địa chỉ nhà thuốc</label>
                                <input id="dia_chi_nha_thuoc" name="dia_chi_nha_thuoc" type="text" class="form-control validate" value="{{ old('dia_chi_nha_thuoc') }}" required />
                                @error('sodienthoai')
                                <div class="alert alert-danger" id="nameError">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="sodienthoai">Mã số thuế</label>
                                <input id="ma_so_thue" name="ma_so_thue" type="text" class="form-control validate" value="{{ old('ma_so_thue') }}" />
                                @error('sodienthoai')
                                <div class="alert alert-danger" id="nameError">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group mb-3" style="margin-top:77px">
                                <label for="">Người quản lý</label>
                                <select class="custom-select tm-select-accounts" name="id_nguoi_quan_ly" style="width:100%" id="">
                                    @foreach ($nguoi_dai_dien as $dd)
                                    <option value="{{ $dd->id }}">{{ $dd->name }} - {{ $dd->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block text-uppercase">Thêm mới</button>
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
            $("#expire_date").datepicker();
        });
    </script>
    <script>
        function displayImage(input) {
            var previewContainer = document.getElementById('previewImageContainer');
            var file = input.files[0];

            if (file) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    var imgElement = document.createElement('img');
                    imgElement.src = e.target.result;
                    imgElement.classList.add('img-fluid');

                    previewContainer.innerHTML = '';
                    previewContainer.appendChild(imgElement);
                };

                reader.readAsDataURL(file);
            }
        }
    </script>
    <script>
        document.getElementById('password').addEventListener('input', function() {
            var password = this.value;

            // Kiểm tra mật khẩu có ít nhất 1 ký tự đặc biệt, 1 chữ hoa, 1 số, và ít nhất 6 ký tự
            var regex = /^(?=.*[!@#$%^&*()_+{}\[\]:;<>,.?~\\\/-])(?=.*[A-Z])(?=.*[0-9]).{6,}$/;

            if (!regex.test(password)) {
                this.setCustomValidity('Mật khẩu không đủ mạnh. Phải chứa ít nhất 1 ký tự đặc biệt, 1 chữ hoa, 1 số, và tối thiểu 6 ký tự.');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            // Ẩn thông báo lỗi sau 3 giây
            setTimeout(function() {
                $(".alert").fadeOut("slow");
            }, 3000);
        });
    </script>

</body>

</html>