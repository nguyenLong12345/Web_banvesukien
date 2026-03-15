<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center" style="min-height: 100vh; background-color: #f6f6f6;">
    <div class="card" style="width: 360px; border: 1px solid #ff672a; border-radius: 10px;">
        <div class="card-header text-center text-white" style="background-color: #ff672a;">
            <h5 class="mb-0"><b>Quên mật khẩu</b></h5>
        </div>
        <div class="card-body p-4">
            @if (session('error'))
                <div class="alert alert-danger py-2 small">{{ session('error') }}</div>
            @endif
            <p class="small text-muted mb-3">Nhập email để nhận liên kết khôi phục mật khẩu.</p>
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required autofocus>
                </div>
                <button type="submit" class="btn w-100 text-white" style="background-color: #ff672a;">Gửi liên kết</button>
            </form>
            <a href="{{ route('home') }}" class="d-block text-center mt-3 small">Về trang chủ</a>
        </div>
    </div>
</body>
</html>
