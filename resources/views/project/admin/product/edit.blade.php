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
                            <h2 class="tm-block-title d-inline-block">Edit Product</h2>
                        </div>
                    </div>
                    <div>
                        <div>
                            <form action="{{route('update_sp',$pro->id)}}" method="post" class="tm-edit-product-form" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-group mb-3">
                                    <label for="name">Mã sản phẩm
                                    </label>
                                    <input id="code" name="code" value="{{ $pro->code }}" type="text" class="form-control validate" required />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="category">Nhóm thuốc</label>
                                    <select class="custom-select tm-select-accounts" name="id_nhomthuoc" style="width:100%" id="category">
                                        <option selected>Chọn nhóm thuốc</option>
                                        @foreach ($nhomthuoc as $nt)
                                        <option value="{{ $nt->id }}" @if ($pro->id_nhomthuoc == $nt->id) selected @endif>{{ $nt->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="name">Tên sản phẩm
                                    </label>
                                    <input id="name" value="{{ $pro->name }}" name="name" type="text" class="form-control validate" required />
                                </div>
                                <div class="row">
                                    <div class="form-group mb-3 col-xs-12 col-sm-6">
                                        <label for="expire_date">Đơn vị tính
                                        </label>
                                        <input id="unit" value="{{ $pro->unit }}" name="unit" type="text" class="form-control validate" />
                                    </div>
                                    <div class="form-group mb-3 col-xs-12 col-sm-6">
                                        <label for="stock">Số lượng
                                        </label>
                                        <input id="quantity" value="{{ $pro->quantity }}" name="quantity" type="number" class="form-control validate" required />
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name">Đơn giá
                                    </label>
                                    <input id="price" value="{{ $pro->price }}" name="price" type="text" class="form-control validate" required />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name">Cân nặng
                                    </label>
                                    <input id="cangnang" value="{{ $pro->cangnang }}" name="cangnang" type="text" class="form-control validate" required />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name">Nước sản xuất
                                    </label>
                                    <input id="nuoc_sx" value="{{ $pro->nuoc_sx }}" name="nuoc_sx" type="text" class="form-control validate" required />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="category">Nhà sản xuất</label>
                                    <select class="custom-select tm-select-accounts" name="id_nsx" style="width:100%" id="">
                                        <option selected>Chọn nhà sản xuất</option>
                                        @foreach ($nhasx as $sx)
                                        <option value="{{ $sx->id }}" @if ($pro->id_nsx == $sx->id) selected @endif>{{ $sx->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-3" style="margin-top:0px">
                                    <label for="category">Hoạt chất</label>
                                    <table class="category-table" id="category-table">
                                        <thead>
                                            <tr>
                                                <th>Tên hoạt chất</th>
                                                <th>Hàm lượng</th>
                                                <th><span class="add-row-button" id="addRowButton">Thêm</span></th>
                                            </tr>
                                        </thead>
                                        <tbody id="category-body">
                                            @foreach($hoatChatData as $item)
                                            <tr>
                                                <td>
                                                    <select class="custom-select tm-select-accounts" style="width:100%" name="hoat_chat[]">
                                                        @foreach($options as $option)
                                                        <option value="{{ $option->id }}" {{ $item['id_hoat_chat'] == $option->id ? 'selected' : '' }}>
                                                            {{ $option->name }}
                                                        </option>

                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="ham-luong-input" name="ham_luong[]" value="{{ $item['ham_luong'] }}">
                                                </td>
                                                <td>
                                                    <button type="button" class="delete-row-button" onclick="deleteRow(this)">Xóa</button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="category">Hình ảnh</label>
                                    <div id="imageContainer">
                                        <input type="file" id="fileInput" name="thumnail[]" multiple onchange="displaySelectedImages(this)">
                                    </div>
                                    <h3>Tất cả ảnh sản phẩm</h3>
                                    <div id="allProductImages" class="image-row">
                                        <!-- Loop qua các hình ảnh và hiển thị -->
                                        @foreach($allImages as $image)
                                        <div class="image-container">
                                            <img src="{{ asset('uploads/img_sp/' . $image) }}" alt="Product Image">
                                            <a href="{{ route('delete_image', ['id' => $pro->id, 'imageName' => $image]) }}" onclick="return confirm('Bạn chắc chắn muốn xóa ảnh này?')">Xóa</a>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-group mb-3" style="margin-top:0px">
                                    <label for="">Thông tin sản phẩm</label>
                                    <textarea class="form-control" id="ckeditor" name="thongtin" rows="5">{!!$pro->thong_tin!!}</textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="doi_tuong">Hashtag</label>
                                    <select id="js-example-basic-multiple" class="custom-select tm-select-accounts" style="width:100%" name="hashtag[]" multiple="multiple">
                                        @foreach($hashtag as $item)
                                        @php
                                        $selected = in_array(['id_tag' => $item->id], $options_tag) ? 'selected' : '';
                                        @endphp
                                        <option value="{{ $item->id }}" {{ $selected }}>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block text-uppercase">Add Product Now</button>
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
            $(document).ready(function() {
                // Get your PHP variable containing the data
                var hashtag = <?php echo json_encode($hashtag); ?>;

                // Initialize Select2
                var select2 = $('#js-example-basic-multiple').select2();

                // Add options from the PHP variable only if they don't exist
                for (var i = 0; i < hashtag.length; i++) {
                    var optionValue = hashtag[i].id;
                    var optionExists = select2.find('option[value="' + optionValue + '"]').length > 0;

                    if (!optionExists) {
                        var option = new Option(hashtag[i].name, optionValue);
                        select2.append(option);
                    }
                }

                // Trigger the change event to refresh Select2
                select2.trigger('change');
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var options = @json($options);

                function createRow(data, index) {
                    var tbody = document.getElementById('category-body');
                    var newRow = document.createElement('tr');
                    newRow.setAttribute('data-index', index);

                    var selectCell = document.createElement('td');
                    var select = document.createElement('select');
                    select.className = 'custom-select tm-select-accounts';
                    select.style.width = '100%';
                    select.name = 'hoat_chat[]';

                    options.forEach(function(option) {
                        var optionElement = document.createElement('option');
                        optionElement.text = option.name;
                        optionElement.value = option.id;
                        select.add(optionElement);
                    });

                    if (data && data.id_hoat_chat) {
                        select.value = data.id_hoat_chat;
                    }

                    selectCell.appendChild(select);
                    newRow.appendChild(selectCell);

                    var inputCell = document.createElement('td');
                    var input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'ham-luong-input';
                    input.name = 'ham_luong[]';

                    if (data && data.ham_luong) {
                        input.value = data.ham_luong;
                    }

                    inputCell.appendChild(input);
                    newRow.appendChild(inputCell);

                    var deleteCell = document.createElement('td');
                    var deleteButton = document.createElement('button');
                    deleteButton.innerText = 'Xóa';
                    deleteButton.className = 'delete-row-button';
                    deleteButton.addEventListener('click', function() {
                        deleteRow(newRow);
                    });

                    deleteCell.appendChild(deleteButton);
                    newRow.appendChild(deleteCell);

                    return newRow;
                }

                function deleteRow(row) {
                    var tbody = document.getElementById('category-body');
                    var index = row.getAttribute('data-index');

                    // Gửi chỉ số hoặc dữ liệu cần thiết đến server ở đây nếu cần
                    // Ví dụ: ajax call để xóa dữ liệu từ cơ sở dữ liệu

                    // Xóa hàng từ DOM
                    tbody.removeChild(row);

                    // Cập nhật chỉ số của các hàng còn lại
                    updateRowIndexes();
                }

                function updateRowIndexes() {
                    var tbody = document.getElementById('category-body');
                    var rows = tbody.getElementsByTagName('tr');

                    for (var i = 0; i < rows.length; i++) {
                        rows[i].setAttribute('data-index', i);
                    }
                }

                document.getElementById('addRowButton').addEventListener('click', function() {
                    var tbody = document.getElementById('category-body');
                    var newRow = createRow(null, tbody.getElementsByTagName('tr').length);

                    tbody.appendChild(newRow);
                });

                document.addEventListener('click', function(event) {
                    if (event.target && event.target.className == 'delete-row-button') {
                        var row = event.target.parentNode.parentNode;
                        deleteRow(row);
                    }
                });

                // Khởi tạo các hàng với dữ liệu hiện có
                initializeRows();
            });
        </script>



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