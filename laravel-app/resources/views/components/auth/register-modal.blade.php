<div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="min-height: 550px;">
            <div class="modal-header text-center position-relative" style="background-color: #ff672a; color: white; padding: 20px;">
                <h4 class="modal-title w-100"><b>Đăng Ký</b></h4>
                <span class="close-icon" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i></span>
                <img src="{{ asset('assets/images/gaudeptrai2.jpg') }}" alt="Logo" class="header-logo">
            </div>

            <div class="modal-body">
                <form id="firebaseRegisterForm">
                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="fullname" class="form-control" placeholder="Nhập họ và tên" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Nhập email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nhập lại mật khẩu</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu" required>
                    </div>
                    <button type="submit" class="btn w-100" style="background-color: #ff672a; color: white;">Đăng ký</button>
                </form>
                <div id="registerError" class="text-danger mt-2"></div>

                <div class="text-center mt-3">
                    <span>Đã có tài khoản? <a href="#" style="color: #ff672a" id="openLogin">Đăng nhập ngay</a></span>
                </div>
            </div>
        </div>
    </div>
</div>
