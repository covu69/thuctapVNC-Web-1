<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        .selected-image {
            width: 100px;
            /* Điều chỉnh kích thước ảnh đã chọn theo ý muốn */
            height: auto;
            margin: 5px;
            display: block;
            /* Hiển thị mỗi ảnh trên một dòng mới */
        }

        .delete-image-button {
            margin-top: 5px;
            cursor: pointer;
            display: block;
            /* Hiển thị mỗi nút "Xóa" trên một dòng mới */
        }

        .category-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .category-table th,
        .category-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .category-table th {
            background-color: #435c70;
        }

        .add-row-button,
        .delete-row-button {
            display: inline-block;
            margin-left: 5px;
            cursor: pointer;
        }
    </style>
    @include('project.admin.layout.header')
</head>

<body>
    {{-- Kiểm tra nếu có lỗi --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Đã xảy ra lỗi:</strong>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    {{-- Hiển thị thông báo lỗi --}}
    @if(session('error'))
    <div class="alert alert-danger">
        <strong>Lỗi:</strong> {{ session('error') }}
    </div>
    @endif
    @include('project.admin.layout.sider-bar')
    <div class="container tm-mt-big tm-mb-big">
        <div class="row">
            <div class="col-xl-9 col-lg-10 col-md-12 col-sm-12 mx-auto">
                <div class="tm-bg-primary-dark tm-block tm-block-h-auto">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="tm-block-title d-inline-block">Add News</h2>
                        </div>
                    </div>
                    <div>
                        <div>
                            <form action="{{route('save_thong_tin_chung')}}" method="post" class="tm-edit-product-form" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="">Tiêu đề
                                    </label>
                                    <input id="tieu_de" name="tieu_de" value="{{ old('tieu_de') }}" type="text" class="form-control validate" required />
                                </div>
                                <div class="form-group mb-3" style="margin-top:0px">
                                    <label for="">Nội dung</label>
                                    <textarea class="form-control" id="ckeditor" name="noi_dung" value="{{ old('noi_dung') }}" rows="5"></textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="category">Nhóm thông tin</label>
                                    <select class="custom-select tm-select-accounts" name="status" style="width:100%" id="">
                                        <option value="0">Hiển thị</option>
                                        <option value="1">Nội dung xác nhận</option>
                                        <option value="2">Điều khoản thanh toán</option>
                                        <option value="3">Tư vấn bán hàng</option>
                                        <option value="4">Đơn hàng tối thiểu</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block text-uppercase">Add Now</button>
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
        <!-- hình ảnh -->
        <!-- <script>
                function displaySelectedImages(input) {
                    var container = document.getElementById('imageContainer');
                    container.innerHTML = ''; // Xóa ảnh đã chọn trước đó

                    var files = input.files;

                    for (var i = 0; i < files.length; i++) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            var img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'selected-image';

                            // Tạo nút "Xóa"
                            var deleteButton = document.createElement('button');
                            deleteButton.innerText = 'Xóa';
                            deleteButton.className = 'delete-image-button';
                            deleteButton.addEventListener('click', function() {
                                container.removeChild(img);
                                container.removeChild(deleteButton);
                            });

                            // Đưa ảnh và nút "Xóa" vào container
                            container.appendChild(img);
                            container.appendChild(deleteButton);
                        };
                        reader.readAsDataURL(files[i]);
                    }
                }
            </script> -->
        <!-- kết thúc hình ảnh -->
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