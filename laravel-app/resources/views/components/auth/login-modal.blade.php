<div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="min-height: 550px;">
            <div class="modal-header text-center position-relative" style="background-color: #ff672a; color: white; padding: 20px;">
                <h4 class="modal-title w-100"><b>Đăng Nhập</b></h4>
                <span class="close-icon" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-circle"></i>
                </span>
                <img src="{{ asset('assets/images/gaudeptrai2.jpg') }}" alt="Logo" class="header-logo">
            </div>

            <div class="modal-body">
                <form id="firebaseLoginForm">
                    <div class="mb-3">
                        <label class="form-label">Nhập email hoặc số điện thoại</label>
                        <div class="input-group">
                            <input type="email" name="email" class="form-control" placeholder="Nhập email hoặc số điện thoại" required>
                            <span class="input-group-text"><i class="bi bi-info-circle"></i></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nhập mật khẩu</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                            <span class="input-group-text toggle-password"><i class="bi bi-eye-slash"></i></span>
                        </div>
                    </div>
                    <button type="submit" class="btn w-100" style="background-color: #ff672a; color: white;">Tiếp tục</button>
                </form>
                <div id="loginError" class="text-danger mt-2"></div>

                <div class="text-center mt-3">
                    <a href="#" class="text-muted" data-bs-target="#forgotPasswordModal" data-bs-dismiss="modal" id="openForgotPassword">Quên mật khẩu?</a>
                </div>

                <div class="text-center mt-2">
                    <span>Chưa có tài khoản? <a href="#" style="color: #ff672a" id="openRegister">Tạo tài khoản ngay</a></span>
                </div>

                <hr>
                <div class="text-center">
                    <span>Hoặc</span>
                    <div class="mt-2">
                        <button class="btn btn-light w-100" type="button" id="googleLoginBtn">
                            <img src="{{ asset('assets/images/google-icon.png') }}" alt="Google" width="20" class="me-2"> Đăng nhập với Google
                        </button>
                    </div>
                </div>

                <p class="text-center text-muted mt-3" style="font-size: 12px;">
                    Bằng việc tiếp tục, bạn đã đọc và đồng ý với <a href="#" class="text-primary">Điều khoản sử dụng</a> và <a href="#" class="text-primary">Chính sách bảo mật thông tin</a> của chúng tôi.
                </p>
            </div>
        </div>
    </div>
</div>
