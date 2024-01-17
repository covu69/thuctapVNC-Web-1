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

        .image-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            /* Khoảng cách giữa các hình ảnh */
        }

        .image-container {
            position: relative;
            flex-basis: calc(25% - 10px);
            /* 25% của chiều rộng và trừ khoảng cách giữa các hình ảnh */
            box-sizing: border-box;
            margin-bottom: 10px;
            /* Khoảng cách giữa các hàng */
        }

        .image-container img {
            width: 100%;
            height: 150px;
            /* Điều chỉnh chiều cao nếu cần */
            object-fit: cover;
            /* Đảm bảo kích thước ảnh giữ nguyên */
            border: 1px solid #ddd;
            /* Đường viền ảnh */
            border-radius: 4px;
            /* Bo tròn góc ảnh */
        }

        .image-container a {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #f00;
            color: #fff;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
    @include('project.admin.layout.header')
</head>

<body>
    {{-- Kiểm tra nếu có lỗi --}}
    @if ($errors->any())
    <div class="alert alert-danger" id="danger-alert">
        <strong>Đã xảy ra lỗi:</strong>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <script>
        setTimeout(function() {
            document.getElementById('danger-alert').style.display = 'none';
        }, 5000); // 5000 milliseconds = 5 seconds
    </script>
    @endif
    {{-- Hiển thị thông báo lỗi --}}
    @if(session('error'))
    <div class="alert alert-danger" id="danger-alert">
        <strong>Lỗi:</strong> {{ session('error') }}
    </div>
    <script>
        setTimeout(function() {
            document.getElementById('danger-alert').style.display = 'none';
        }, 5000); // 5000 milliseconds = 5 seconds
    </script>
    @endif

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
    @include('project.admin.layout.sider-bar')
    <div class="container tm-mt-big tm-mb-big">
        <div class="row">
            <div class="col-xl-9 col-lg-10 col-md-12 col-sm-12 mx-auto">
                <div class="tm-bg-primary-dark tm-block tm-block-h-auto">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="tm-block-title d-inline-block">Edit Voucher</h2>
                        </div>
                    </div>
                    <div>
                        <div>
                            <form action="{{route('update_voucher',$voucher->id)}}" method="post" class="tm-edit-product-form" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-group mb-3">
                                    <label for="name">Tiêu đề
                                    </label>
                                    <input id="tieu_de" name="tieu_de" value="{{ $voucher->tieu_de }}" type="text" class="form-control validate" required />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name">Mã giảm giá
                                    </label>
                                    <input id="ma_giam_gia" value="{{ $voucher->ma_giam_gia }}" name="ma_giam_gia" type="text" class="form-control validate" required />
                                </div>
                                <div class="row">
                                    <div class="form-group mb-3 col-xs-12 col-sm-6">
                                        <label for="expire_date">Mức tiền
                                        </label>
                                        <input id="muc_tien" value="{{ $voucher->muc_tien }}" name="muc_tien" type="text" class="form-control validate" />
                                    </div>
                                    <div class="form-group mb-3 col-xs-12 col-sm-6">
                                        <label for="stock">Tổng hóa đơn
                                        </label>
                                        <input id="tong_hoa_don" value="{{ $voucher->tong_hoa_don }}" name="tong_hoa_don" type="number" class="form-control validate" required />
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name">Ngày bắt đầu
                                    </label>
                                    <input id="ngay_bat_dau" value="{{ $voucher->ngay_bat_dau }}" name="ngay_bat_dau" type="date" class="form-control validate" required />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name">Ngày kết thúc
                                    </label>
                                    <input id="ngay_ket_thuc" value="{{ $voucher->ngay_ket_thuc }}" name="ngay_ket_thuc" type="date" class="form-control validate" required />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="doi_tuong">Đối tượng gửi</label>
                                    <select id="js-example-basic-multiple" class="custom-select tm-select-accounts" style="width:100%" name="doi_tuong[]" multiple="multiple">
                                        @foreach($doi_tuong as $item)
                                        @php
                                        $selected = in_array(['id_hang_thanh_vien' => $item->id], $options) ? 'selected' : '';
                                        @endphp
                                        <option value="{{ $item->id }}" {{ $selected }}>{{ $item->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="category">Loại voucher</label>
                                    <select class="custom-select tm-select-accounts" name="loai" style="width:100%" id="">
                                        <option value="0" @if($voucher->loai == '0' ) selected @endif>Sử dụng 1 lần</option>
                                        <option value="1" @if($voucher->loai == '1' ) selected @endif>Sử dụng nhiều lần</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3" style="margin-top:0px">
                                    <label for="">Nội dung</label>
                                    <textarea class="form-control" id="ckeditor" name="noi_dung" rows="5">{!! $voucher->noi_dung !!}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block text-uppercase">Thực hiện</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('project.admin.layout.footer')

        <!-- <script src="{{asset('admin/js/jquery-3.3.1.min.js')}}"></script> -->
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
        <script>
            $(document).ready(function() {
                // Get your PHP variable containing the data
                var doi_tuong = <?php echo json_encode($doi_tuong); ?>;

                // Initialize Select2
                var select2 = $('#js-example-basic-multiple').select2();

                // Add options from the PHP variable only if they don't exist
                for (var i = 0; i < doi_tuong.length; i++) {
                    var optionValue = doi_tuong[i].id;
                    var optionExists = select2.find('option[value="' + optionValue + '"]').length > 0;

                    if (!optionExists) {
                        var option = new Option(doi_tuong[i].title, optionValue);
                        select2.append(option);
                    }
                }

                // Trigger the change event to refresh Select2
                select2.trigger('change');
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