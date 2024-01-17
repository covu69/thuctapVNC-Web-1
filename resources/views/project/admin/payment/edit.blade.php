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
                            <h2 class="tm-block-title d-inline-block">Chỉnh sửa lịch sử mua</h2>
                        </div>
                    </div>
                    <div class="row tm-edit-product-row">
                        <div class="col-xl-6 col-lg-6 col-md-12">
                            <form action="{{route('update_payment',$payment->id)}}" enctype="multipart/form-data" method="post" class="tm-edit-product-form">
                                @csrf
                                @method('PUT')
                                <div class="form-group mb-3">
                                    <label for="name">Tên
                                    </label>
                                    <input id="name" name="name" type="text" value="{{$payment->name}}" class="form-control validate" readonly>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name">Số điện thoại
                                    </label>
                                    <input id="sdt" name="sdt" type="text" value="{{$payment->sdt}}" class="form-control validate" readonly>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="email">Mã đơn hàng</label>
                                    <input id="ma_don_hang" name="ma_don_hang" type="text" value="{{$payment->ma_don_hang}}" class="form-control validate" />
                                    @error('email')
                                    <div class="alert alert-danger" id="nameError">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password">Ngày tháng</label>
                                    <input id="" name="created_at" type="date" value="{{$payment->created_at}}" class="form-control validate"  readonly>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="phone">Chuyển khoản trước</label>
                                    <select id="payment_method" name="payment_method" class="custom-select tm-select-accounts" required>
                                        <option value="0" @if(old('role', $payment->payment_method) == 0) selected @endif>Không</option>
                                        <option value="1" @if(old('role', $payment->payment_method) == 1) selected @endif>Có</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="quyen">Trạng thái</label>
                                    <select id="payment_status" name="payment_status" class="custom-select tm-select-accounts" required>
                                        <option value="0" @if(old('role', $payment->payment_status) == 0) selected @endif>Chờ thanh toán</option>
                                        <option value="1" @if(old('role', $payment->payment_status) == 1) selected @endif>Đã thanh toán</option>
                                    </select>
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