document.addEventListener("DOMContentLoaded", function () {
    var loginModalEl = document.getElementById("loginModal");
    var registerModalEl = document.getElementById("registerModal");
    var forgotModalEl = document.getElementById("forgotPasswordModal");

    var loginModal = new bootstrap.Modal(loginModalEl, { backdrop: "static" });
    var registerModal = new bootstrap.Modal(registerModalEl, { backdrop: "static" });
    var forgotModal = new bootstrap.Modal(forgotModalEl, { backdrop: "static" });

    // Chuyển từ đăng nhập sang đăng ký
    document.getElementById("openRegister").addEventListener("click", function () {
        loginModalEl.addEventListener("hidden.bs.modal", function () {
            registerModal.show();
        }, { once: true });
        loginModal.hide();
    });

    // Chuyển từ đăng nhập sang quên mật khẩu
    document.getElementById("openForgotPassword").addEventListener("click", function () {
        loginModalEl.addEventListener("hidden.bs.modal", function () {
            forgotModal.show();
        }, { once: true });
        loginModal.hide();
    });

    // Chuyển từ đăng ký về đăng nhập
    document.getElementById("openLogin").addEventListener("click", function () {
        registerModalEl.addEventListener("hidden.bs.modal", function () {
            loginModal.show();
        }, { once: true });
        registerModal.hide();
    });

    // Khi đóng modal cuối cùng thì xóa backdrop
    function removeBackdrop() {
        if (!loginModalEl.classList.contains("show") && !registerModalEl.classList.contains("show")) {
            document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());
            document.body.classList.remove("modal-open");
            document.body.style.overflow = "";
        }
    }
    loginModalEl.addEventListener("hidden.bs.modal", removeBackdrop);
    registerModalEl.addEventListener("hidden.bs.modal", removeBackdrop);

    // Các nút mở login modal từ bên ngoài
    document.querySelectorAll(".openLogin").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            loginModal.show();
        });
    });

    // Kiểm tra login khi bấm nút "Vé của tôi"
    const myTicketBtn = document.getElementById("myTicketsBtn");
    if (myTicketBtn) {
        myTicketBtn.addEventListener("click", function (e) {
            if (typeof isLoggedIn !== "undefined" && !isLoggedIn) {
                e.preventDefault();
                loginModal.show();
            } else {
                window.location.href = "/my-tickets";
            }
        });
    }
});
