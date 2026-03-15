<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center" style="min-height: 100vh; background-color: #f6f6f6;">
    <div class="card" style="width: 360px; border: 1px solid #ff672a; border-radius: 10px;">
        <div class="card-header text-center text-white" style="background-color: #ff672a;">
            <h5 class="mb-0"><b>Đăng nhập</b></h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn w-100 text-white" style="background-color: #ff672a;">Đăng nhập</button>
            </form>
            <div class="mt-3 text-center small">
                <a href="{{ route('password.request') }}">Quên mật khẩu?</a> |
                <a href="#" class="openRegister">Đăng ký</a> |
                <a href="{{ route('home') }}">Trang chủ</a>
            </div>
        </div>
    </div>
</body>
</html>
