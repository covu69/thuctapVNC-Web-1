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
              <h2 class="tm-block-title d-inline-block">Thêm mới người quản lý</h2>
            </div>
          </div>
          <div class="row tm-edit-product-row">
            <!-- <div class="col-xl-6 col-lg-6 col-md-12"> -->
            <form action="{{ route('save_quan_ly') }}" method="post" enctype="multipart/form-data" class="tm-edit-product-form">
              @csrf
              <div class="form-group mb-3">
                <label for="name">Mã đơn vị
                </label>
                <input id="Ma_don_vi" name="Ma_don_vi" type="text" class="form-control validate" value="{{ old('Ma_don_vi') }}" required />
                @error('name')
                <div class="alert alert-danger" id="nameError">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group mb-3">
                <label for="name">Họ tên
                </label>
                <input id="name" name="name" type="text" class="form-control validate" value="{{ old('name') }}" required />
                @error('name')
                <div class="alert alert-danger" id="nameError">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group mb-3">
                <label for="sodienthoai">Số điện thoại</label>
                <input id="sodienthoai" name="sodienthoai" type="number" class="form-control validate" value="{{ old('sodienthoai') }}" required />
                @error('sodienthoai')
                <div class="alert alert-danger" id="nameError">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group mb-3">
                <label for="email">Email</label>
                <input id="email" name="email" type="text" class="form-control validate" value="{{ old('email') }}" required />
                @error('email')
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
                  <label for="password_confirmation">Nhập lại mật khẩu</label>
                  <input id="password_confirmation" name="password_confirmation" type="password" class="form-control validate" value="{{ old('password_confirmation') }}" required />
                  <small id="passwordConfirmHelp" class="form-text text-muted"></small>
                </div>
              </div>
              <div class="form-group mb-3">
                <label for="quyen">Quyền</label>
                <select id="quyen" name="role" class="form-control" required>
                  <option value="1" @if(old('quyen')=='admin' ) selected @endif>Quyền admin</option>
                  <option value="0" @if(old('quyen')=='user' ) selected @endif>Quyền user</option>
                </select>
                @error('quyen')
                <div class="alert alert-danger" id="quyenError">{{ $message }}</div>
                @enderror
              </div>


              <!-- </div> -->
              <!-- <div class="col-xl-6 col-lg-6 col-md-12 mx-auto mb-4">
              <div class="tm-product-img-dummy mx-auto">
                <i class="fas fa-cloud-upload-alt tm-upload-icon" onclick="document.getElementById('fileInput').click();"></i>
              </div>
              <div class="custom-file mt-3 mb-3">
                <input id="fileInput" type="file" style="display:none;" />
                <input type="button" class="btn btn-primary btn-block mx-auto" value="UPLOAD  IMAGE" onclick="document.getElementById('fileInput').click();" />
              </div>
            </div> -->
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
    // Lấy các element
    var passwordInput = document.getElementById('password');
    var passwordConfirmInput = document.getElementById('password_confirmation');
    var passwordConfirmHelp = document.getElementById('passwordConfirmHelp');

    // Sự kiện input khi nhập mật khẩu
    passwordInput.addEventListener('input', function() {
      validatePasswordMatch();
    });

    // Sự kiện input khi nhập lại mật khẩu
    passwordConfirmInput.addEventListener('input', function() {
      validatePasswordMatch();
    });

    // Hàm kiểm tra sự khớp của mật khẩu
    function validatePasswordMatch() {
      var password = passwordInput.value;
      var passwordConfirm = passwordConfirmInput.value;

      if (password !== passwordConfirm) {
        passwordConfirmHelp.textContent = 'Mật khẩu không khớp.';
        passwordConfirmInput.setCustomValidity('Mật khẩu không khớp.');
      } else {
        passwordConfirmHelp.textContent = '';
        passwordConfirmInput.setCustomValidity('');
      }
    }
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