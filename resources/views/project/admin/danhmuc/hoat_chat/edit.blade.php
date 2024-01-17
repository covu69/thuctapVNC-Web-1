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
                            <h2 class="tm-block-title d-inline-block">Chỉnh sửa</h2>
                        </div>
                    </div>
                    <div class="row tm-edit-product-row">
                        <div class="col-xl-6 col-lg-6 col-md-12">
                            <form action="{{route('update_hoatchat',$hc->id)}}" enctype="multipart/form-data" method="post" class="tm-edit-product-form">
                                @csrf
                                @method('PUT')
                                <div class="form-group mb-3">
                                    <label for="name">Tên hoạt chất
                                    </label>
                                    <input id="name" name="name" type="text" value="{{$hc->name}}" class="form-control validate" />
                                    @error('name')
                                    <div class="alert alert-danger" id="nameError">{{ $message }}</div>
                                    @enderror
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