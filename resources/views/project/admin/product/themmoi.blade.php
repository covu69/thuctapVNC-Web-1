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
                                <h2 class="tm-block-title d-inline-block">Add Product</h2>
                            </div>
                        </div>
                        <div>
                            <div>
                                <form action="{{route('save_sanpham')}}" method="post" class="tm-edit-product-form" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="name">Mã sản phẩm
                                        </label>
                                        <input id="code" name="code" value="{{ old('code') }}" type="text" class="form-control validate" required />
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="category">Nhóm thuốc</label>
                                        <select class="custom-select tm-select-accounts" name="id_nhomthuoc" style="width:100%" id="category">
                                            <option selected>Chọn nhóm thuốc</option>
                                            @foreach ($nhomthuoc as $nt)
                                            <option value="{{ $nt->id }}">{{ $nt->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="name">Tên sản phẩm
                                        </label>
                                        <input id="name" value="{{ old('name') }}" name="name" type="text" class="form-control validate" required />
                                    </div>
                                    <div class="row">
                                        <div class="form-group mb-3 col-xs-12 col-sm-6">
                                            <label for="expire_date">Đơn vị tính
                                            </label>
                                            <input id="unit" value="{{ old('unit') }}" name="unit" type="text" class="form-control validate" />
                                        </div>
                                        <div class="form-group mb-3 col-xs-12 col-sm-6">
                                            <label for="stock">Số lượng
                                            </label>
                                            <input id="quantity" value="{{ old('quantity') }}" name="quantity" type="number" class="form-control validate" required />
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="name">Đơn giá
                                        </label>
                                        <input id="price" value="{{ old('price') }}" name="price" type="text" class="form-control validate" required />
                                    </div>
                                    <div class="form-group mb-3" style="margin-top: 0px">
                                        <label for="category">Sản phẩm ưu đãi giá</label>
                                        <table class="category-table" id="category-table-membership">
                                            <thead>
                                                <tr>
                                                    <th>Hạng thành viên</th>
                                                    <th>Giá ưu đãi</th>
                                                    <th><span class="add-row-button" id="addMembershipRowButton">Thêm</span></th>
                                                </tr>
                                            </thead>
                                            <tbody id="category-body-membership">
                                                <!-- Chưa có dòng selected -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="name">Cân nặng
                                        </label>
                                        <input id="cangnang" value="{{ old('cangnang') }}" name="cangnang" type="text" class="form-control validate" required />
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="name">Nước sản xuất
                                        </label>
                                        <input id="nuoc_sx" value="{{ old('nuoc_sx') }}" name="nuoc_sx" type="text" class="form-control validate" required />
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="category">Nhà sản xuất</label>
                                        <select class="custom-select tm-select-accounts" name="id_nsx" style="width:100%" id="">
                                            <option selected>Chọn nhà sản xuất</option>
                                            @foreach ($nhasx as $sx)
                                            <option value="{{ $sx->id }}">{{ $sx->name }}</option>
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
                                                <!-- Chưa có dòng selected -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="category">Hình ảnh</label>
                                        <div class="tm-product-img-dummy mx-auto" id="imageContainer">
                                            <input type="file" id="fileInput" name="thumnail[]" multiple onchange="displaySelectedImages(this)">
                                        </div>
                                    </div>

                                    <div class="form-group mb-3" style="margin-top:0px">
                                        <label for="">Thông tin sản phẩm</label>
                                        <textarea class="form-control" id="ckeditor" name="thongtin" value="{{ old('thongtin') }}" rows="5"></textarea>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="name">Hashtag</label>
                                        <select id="js-example-basic-multiple" class="custom-select tm-select-accounts" style="width:100%" name="hashtag[]" multiple="multiple">
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
                    $('#js-example-basic-multiple').select2();

                    // Dynamically add options from the PHP variable
                    for (var i = 0; i < hashtag.length; i++) {
                        var option = new Option(hashtag[i].name, hashtag[i].id); // Change 'name' to 'title'
                        $('#js-example-basic-multiple').append(option);
                    }

                    // Trigger the change event to refresh Select2
                    $('#js-example-basic-multiple').trigger('change');
                });
            </script>
            <script>
                var hoatchatOptionsWithId = @json($hoatchatWithId);
            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var tbody = document.getElementById('category-body');

                    document.getElementById('addRowButton').addEventListener('click', function() {
                        var newRow = document.createElement('tr');

                        var selectCell = document.createElement('td');
                        var select = document.createElement('select');
                        select.className = 'custom-select tm-select-accounts';
                        select.style.width = '100%';
                        select.name = 'hoat_chat[]';

                        hoatchatOptionsWithId.forEach(function(option) {
                            var optionElement = document.createElement('option');
                            optionElement.text = option.name;
                            optionElement.value = option.id;
                            select.add(optionElement);
                        });

                        selectCell.appendChild(select);
                        newRow.appendChild(selectCell);

                        var inputCell = document.createElement('td');
                        var input = document.createElement('input');
                        input.type = 'text';
                        input.className = 'ham-luong-input';
                        input.name = 'ham_luong[]';
                        inputCell.appendChild(input);
                        newRow.appendChild(inputCell);

                        var deleteCell = document.createElement('td');
                        var deleteButton = document.createElement('button');
                        deleteButton.innerText = 'Xóa';
                        deleteButton.className = 'delete-row-button';
                        deleteButton.addEventListener('click', function() {
                            tbody.removeChild(newRow);
                        });
                        deleteCell.appendChild(deleteButton);
                        newRow.appendChild(deleteCell);

                        tbody.appendChild(newRow);
                    });
                });
            </script>

            <script>
                var membershipOptionsWithId = @json($hang_thanh_vien);
            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var tbodyMembership = document.getElementById('category-body-membership');

                    document.getElementById('addMembershipRowButton').addEventListener('click', function() {
                        var newRowMembership = document.createElement('tr');

                        var selectCellMembership = document.createElement('td');
                        var selectMembership = document.createElement('select');
                        selectMembership.className = 'custom-select tm-select-accounts';
                        selectMembership.style.width = '100%';
                        selectMembership.name = 'hang_thanh_vien[]';

                        membershipOptionsWithId.forEach(function(option) {
                            var optionElementMembership = document.createElement('option');
                            optionElementMembership.text = option.title;
                            optionElementMembership.value = option.id;
                            selectMembership.add(optionElementMembership);
                        });

                        selectCellMembership.appendChild(selectMembership);
                        newRowMembership.appendChild(selectCellMembership);

                        var inputCellMembership = document.createElement('td');
                        var inputMembership = document.createElement('input');
                        inputMembership.type = 'text';
                        inputMembership.className = 'gia-uu-dai-input';
                        inputMembership.name = 'gia_uu_dai[]';
                        inputCellMembership.appendChild(inputMembership);
                        newRowMembership.appendChild(inputCellMembership);

                        var deleteCellMembership = document.createElement('td');
                        var deleteButtonMembership = document.createElement('button');
                        deleteButtonMembership.innerText = 'Xóa';
                        deleteButtonMembership.className = 'delete-row-button';
                        deleteButtonMembership.addEventListener('click', function() {
                            tbodyMembership.removeChild(newRowMembership);
                        });
                        deleteCellMembership.appendChild(deleteButtonMembership);
                        newRowMembership.appendChild(deleteCellMembership);

                        tbodyMembership.appendChild(newRowMembership);
                    });
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