<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center" style="min-height: 100vh; background-color: #f6f6f6;">
    <div class="modal-dialog modal-sm" style="border: 1px solid #ff672a; box-sizing: border-box; border-radius: 10px; width: 300px;">
        <div class="modal-content">
            <div class="modal-header text-center position-relative" style="background-color: #ff672a; color: white; padding: 20px; display: flex; flex-direction: column; align-items: center;">
                <h4 class="modal-title w-100"><b>Đặt lại mật khẩu</b></h4>
            </div>
            <div class="modal-body">
                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        @foreach ($errors->all() as $err)
                            {{ $err }}
                        @endforeach
                    </div>
                @endif
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="mb-3">
                        <label class="form-label" style="padding: 10px 10px 0 10px;">Mật khẩu mới</label>
                        <input type="password" name="password" class="form-control" placeholder="Mật khẩu mới" style="padding: 10px;" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="padding: 0 10px;">Xác nhận mật khẩu</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu" style="padding: 10px;" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn w-75" style="padding: 10px; margin: 10px; background-color: #ff672a; color: white;">
                            Cập nhật mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
