<div class="modal fade" id="forgotPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="min-height: 350px;">
            <div class="modal-header text-center position-relative" style="background-color: #ff672a; color: white; padding: 20px;">
                <h4 class="modal-title w-100"><b>Quên mật khẩu</b></h4>
                <span class="close-icon" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-circle"></i>
                </span>
                <img src="{{ asset('assets/images/gaudeptrai2.jpg') }}" alt="Logo" class="header-logo">
            </div>

            <div class="modal-body">
                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nhập email đã đăng ký</label>
                        <input type="email" name="email" class="form-control" placeholder="Email của bạn" required>
                    </div>
                    <button type="submit" class="btn w-100" style="background-color: #ff672a; color: white;">Gửi liên kết</button>
                </form>
            </div>
        </div>
    </div>
</div>
