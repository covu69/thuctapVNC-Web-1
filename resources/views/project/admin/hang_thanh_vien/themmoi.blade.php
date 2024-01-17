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
                            <h2 class="tm-block-title d-inline-block">Tạo hạng thành viên</h2>
                        </div>
                    </div>
                    <div>
                        <div>
                            <form action="{{route('save_hang_tv')}}" method="post" class="tm-edit-product-form">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="name">Tên hạng thành viên
                                    </label>
                                    <input id="title" name="title" type="text" class="form-control validate" required />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="category">Trạng thái</label>
                                    <select class="custom-select tm-select-accounts" name="status" style="width:100%" id="category" required>
                                        <option value="" selected disabled>Chọn trạng thái</option>
                                        <option value="1">Hoạt động</option>
                                        <option value="0">Không hoạt động</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name">Mức tiền
                                    </label>
                                    <input id="muctien" name="muctien" type="number" class="form-control validate" required />
                                </div>
                                <div class="form-group mb-3" style="margin-top:0px">
                                    <label for="">Nội dung</label>
                                    <textarea class="form-control" id="ckeditor" name="content" rows="5"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block text-uppercase">Tạo mới</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('project.admin.layout.footer')

        <script src="{{asset('admin/js/jquery-3.3.1.min.js')}}"></script>
        <!-- https://jquery.com/download/ -->
        <script src="{{asset('admin/jquery-ui-datepicker/jquery-ui.min.js')}}"></script>
        <!-- https://jqueryui.com/download/ -->
        <script src="{{asset('admin/js/bootstrap.min.js')}}"></script>
        <!-- https://getbootstrap.com/ -->
        <script>
            $(function() {
                $("#expire_date").datepicker();
            });
        </script>

        <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
        <script src="{{ asset('ckfinder/ckfinder.js') }}"></script>
        <script>
            createCkeditor('ckeditor');

            function createCkeditor(name) {
                CKEDITOR.replace(name, {
                    filebrowserBrowseUrl: "{{ asset('plugin/ckfinder/ckfinder.html') }}",
                    filebrowserImageBrowseUrl: "{{ asset('plugin/ckfinder/ckfinder.html?type=Images') }}",
                    filebrowserFlashBrowseUrl: "{{ asset('plugin/ckfinder/ckfinder.html?type=Flash') }}",
                    filebrowserUploadUrl: "{{ asset('plugin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files') }}",
                    filebrowserImageUploadUrl: "{{ asset('plugin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images') }}",
                    filebrowserFlashUploadUrl: "{{ asset('plugin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash') }}",
                });
            }
        </script>
</body>

</html>