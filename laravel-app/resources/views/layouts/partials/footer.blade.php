<footer class="footer text-white py-4">
    <div class="container">
        <div class="row align-items-center text-center text-lg-start">
            <div class="col-lg-4 mb-3 mb-lg-0">
                <a href="{{ route('home') }}" class="text-white text-decoration-none">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" height="60">
                </a>
                <p class="mt-2">© 2026 TicketBox. All rights reserved.</p>
                <h6>Email</h6>
                <p><i class="bi bi-envelope"></i> noreply.webclon@gmail.com</p>
                <h6>Văn phòng</h6>
                <p><i class="bi bi-geo-alt-fill"></i> Home</p>
            </div>

            <div class="col-lg-4 mb-3 mb-lg-0">
                <h6>Dành cho khách hàng</h6>
                <p><i class="bi bi-card-list"></i> Điều khoản sử dụng cho khách hàng</p>
                <h6>Dành cho ban tổ chức</h6>
                <p><i class="bi bi-card-list"></i> Điều khoản sử dụng cho ban tổ chức</p>
                <h6>Đăng ký nhận email về các sự kiện hot</h6>
                <form id="subscribeForm">
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" id="emailInput" name="email" class="form-control" placeholder="Nhập email của bạn" required>
                        <button type="submit" class="btn1"><i class="bi bi-send"></i></button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <h6>Về công ty chúng tôi</h6>
                <p>Quy chế hoạt động</p>
                <p>Chính sách bảo mật thông tin</p>
                <p>Cơ chế giải quyết tranh chấp/ khiếu nại</p>
                <p>Chính sách bảo mật thanh toán</p>
                <p>Chính sách đổi trả và kiểm hàng</p>
                <p>Điều kiện vận chuyển và giao nhận</p>
                <p>Phương thức thanh toán</p>
            </div>
        </div>

        <hr class="my-4">
        <div class="text-center">
            <h6>Theo dõi chúng tôi</h6>
            <div class="d-flex justify-content-center">
                <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-2x"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-2x"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-x-twitter fa-2x"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-youtube fa-2x"></i></a>
            </div>
        </div>
    </div>
</footer>

<!-- Subscribe success/error modals -->
<div class="modal fade" id="subscribeSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="mx-auto mb-3" style="width: 80px;">
                <lord-icon src="https://cdn.lordicon.com/lupuorrc.json" trigger="loop" delay="1000"
                    colors="primary:#0ab39c,secondary:#0ab39c" style="width:80px;height:80px"></lord-icon>
            </div>
            <h4 class="text-success fw-bold">Thành công!</h4>
            <p id="subscribeSuccessMessage"></p>
            <button class="btn btn-outline-success" data-bs-dismiss="modal">Đóng</button>
        </div>
    </div>
</div>
<div class="modal fade" id="subscribeErrorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <h4 class="text-danger fw-bold">Lỗi</h4>
            <p id="subscribeErrorMessage"></p>
            <button class="btn btn-outline-danger" data-bs-dismiss="modal">Đóng</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $("#subscribeForm").submit(function(event) {
        event.preventDefault();
        var email = $("#emailInput").val();
        $.ajax({
            url: "{{ route('subscribe') }}",
            type: "POST",
            data: { email: email, _token: "{{ csrf_token() }}" },
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    $("#subscribeSuccessMessage").text(response.message);
                    new bootstrap.Modal(document.getElementById("subscribeSuccessModal")).show();
                } else {
                    $("#subscribeErrorMessage").text(response.message);
                    new bootstrap.Modal(document.getElementById("subscribeErrorModal")).show();
                }
            },
            error: function(xhr, status, error) {
                $("#subscribeErrorMessage").text("Lỗi hệ thống: " + error);
                new bootstrap.Modal(document.getElementById("subscribeErrorModal")).show();
            }
        });
    });
});
</script>
@endpush
