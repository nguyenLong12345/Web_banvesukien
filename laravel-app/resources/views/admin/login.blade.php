<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admin/css/login.css') }}">
    <style>
        body {
            background: linear-gradient(to right, #002366, #6A0DAD);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-size: 20px;
        }
        .login-wrapper {
            display: flex;
            align-items: center;
            gap: 2rem;
            background: rgba(255,255,255,0.1);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .login-image img {
            max-width: 200px;
            height: auto;
        }
        .login-container {
            background: #391fad;
            padding: 30px;
            border-radius: 8px;
            width: 400px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-container .form-control {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 4px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            margin: 10px 0;
            cursor: pointer;
            font-size: 1rem;
        }
        .login-container button:hover {
            background: #0056b3;
            color: #fff;
        }
        .alert-danger {
            color: #dc3545;
            font-size: 14px;
            margin-bottom: 10px;
            padding: 8px 12px;
            border-radius: 4px;
            background: rgba(220,53,69,0.2);
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-image">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Login Banner" onerror="this.style.display='none'">
        </div>
        <div class="login-container">
            <h2><i class="bi bi-person-vcard-fill"></i> Admin Login</h2>
            @if ($errors->has('error'))
                <div class="alert-danger">{{ $errors->first('error') }}</div>
            @endif
            <form method="POST" action="{{ route('admin.login.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-person-square"></i> Tên đăng nhập</label>
                    <input type="text" name="username" class="form-control" placeholder="Tên đăng nhập" value="{{ old('username') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-lock-fill"></i> Mật khẩu</label>
                    <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
                </div>
                <button type="submit"><i class="bi bi-door-open-fill"></i> <b>Đăng nhập</b></button>
            </form>
        </div>
    </div>
</body>
</html>
