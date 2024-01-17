<nav class="navbar navbar-expand-xl">
    <div class="container h-100">
        <a class="navbar-brand" href="index.html">
            <h1 class="tm-site-title mb-0">Product Admin</h1>
        </a>
        <button class="navbar-toggler ml-auto mr-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars tm-nav-icon"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto h-100">
                @if(Auth::user()->role == 1)
                <li class="nav-item">
                    <a class="nav-link {{ (Route::currentRouteName() == 'dashboard') ? 'active' : '' }}" href="{{route('dashboard')}}">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                        <span class="sr-only">(current)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/nguoi_quan_ly*')) ?  'active' : ''}}" href="{{route('nguoi_quan_ly')}}">
                        <i class="far fa-user"></i>
                        Người quản lý
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/dai_ly*')) ?  'active' : ''}}" href="{{route('dai_ly')}}">
                        <i class="far fa-user"></i>
                        Danh sách đại lý
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="far fa-file-alt"></i>
                        <span>
                            Danh mục chung <i class="fas fa-angle-down"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{route('hashtag')}}">#hashtag</a>
                        <a class="dropdown-item" href="{{route('nhasanxuat')}}">Nhà sản xuất</a>
                        <a class="dropdown-item" href="{{route('hoatchat')}}">Hoạt chất</a>
                        <a class="dropdown-item" href="{{route('nhomthuoc')}}">Nhóm thuốc</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/sanpham*')) ?  'active' : ''}}" href="{{route('sanpham')}}">
                        <i class="fa fa-bars"></i>
                        Sản phẩm thuốc
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/tin_tuc/index*')) ?  'active' : ''}}" href="{{route('tin_tuc')}}">
                        <i class='far fa-newspaper'></i>
                        Tin tức
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/gio_hang*')) ?  'active' : ''}}" href="{{route('gio_hang')}}">
                        <i class="fas fa-shopping-cart"></i>
                        Giỏ hàng
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/payment*')) ?  'active' : ''}}" href="{{route('payment')}}">
                        <i class="far fa-clock"></i>
                        Lịch sử mua hàng
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="far fa-file-alt"  style='font-size:20px'></i>
                        <span>
                            Báo cáo <i class="fas fa-angle-down"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{route('muahang')}}">Mua hàng</a>
                        <!-- <a class="dropdown-item" href="{{route('nhasanxuat')}}">Nhà sản xuất</a>
                        <a class="dropdown-item" href="{{route('hoatchat')}}">Hoạt chất</a>
                        <a class="dropdown-item" href="{{route('nhomthuoc')}}">Nhóm thuốc</a> -->
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-cog"></i>
                        <span>
                            Settings <i class="fas fa-angle-down"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#">Profile</a>
                        <a class="dropdown-item" href="{{route('thong_tin_chung')}}">Thông tin chung</a>
                        <a class="dropdown-item" href="{{route('hang_tv')}}">Quản lý hạng thành viên</a>
                        <a class="dropdown-item" href="{{route('voucher')}}">Quản lý mã giảm giá</a>
                    </div>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link {{ (Route::currentRouteName() == 'dashboard') ? 'active' : '' }}" href="{{route('dashboard')}}">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                        <span class="sr-only">(current)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/dai_ly*')) ?  'active' : ''}}" href="{{route('dai_ly')}}">
                        <i class="far fa-user"></i>
                        Danh sách đại lý
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/gio_hang*')) ?  'active' : ''}}" href="{{route('gio_hang')}}">
                        <i class="fas fa-shopping-cart"></i>
                        Giỏ hàng
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/payment*')) ?  'active' : ''}}" href="{{route('payment')}}">
                        <i class="far fa-clock"></i>
                        Lịch sử mua hàng
                    </a>
                </li>
                @endif
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link d-block" href="{{route('logout')}}">
                        {{Auth::user()->name}}, <b>Logout</b>
                    </a>
                </li>
            </ul>
        </div>
    </div>

</nav>