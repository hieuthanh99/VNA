<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>@yield('title')</title>
    <!-- Định nghĩa các tài nguyên CSS và JS cần thiết -->
    <script>
        $(document).ready(function() {
            $('.nav-link').click(function() {
                $('.nav-link').removeClass('active'); // Loại bỏ lớp active từ tất cả các liên kết
                $(this).addClass('active'); // Thêm lớp active vào liên kết được click
            });
        });
        </script>
        <style>
            .nav-link.active {
                background-color: #ddd;
            }
        </style>
        
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <!-- Định nghĩa phần menu -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('departments.index') }}">Phòng Ban</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('departments.index') }}">Báo Cáo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('departments.index') }}">Báo Cáo Tổng</a>
                </li>
                <!-- Các menu khác -->
            </ul>
        </nav>
        
    </header>
    
    <main>
        @yield('content')
    </main>
    <footer>
        <!-- Định nghĩa phần footer của trang -->
    </footer>
</body>
</html>
