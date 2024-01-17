<!DOCTYPE html>
<html lang="en">

<head>
  @include('project.admin.layout.header')
  @if(session('error'))
  <script>
    window.onload = function() {
      alert("{{ session('error') }}");
    };
  </script>
  @endif
</head>

<body>
  @include('project.admin.layout.sider-bar')
  <div class="container tm-mt-big tm-mb-big">
    <div class="row">
      <div class="col-xl-9 col-lg-10 col-md-12 col-sm-12 mx-auto">
        <div class="tm-bg-primary-dark tm-block tm-block-h-auto">
          <div class="row">
            <div class="col-12">
              <h2 class="tm-block-title d-inline-block">Thêm mới nhà sản xuất</h2>
            </div>
          </div>
          <div class="row tm-edit-product-row">
            <div class="col-xl-6 col-lg-6 col-md-12">
              <form action="{{ route('save_nhasanxuat') }}" method="post" enctype="multipart/form-data" class="tm-edit-product-form">
                @csrf
                <div class="form-group mb-3">
                  <label for="name">Tên nhà sản xuất</label>
                  <input id="name" name="name" type="text" class="form-control validate" value="{{ old('name') }}" required />
                  @if(session('error'))
                  <div class="alert alert-danger mt-2">{{ session('error') }}</div>
                  @endif
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