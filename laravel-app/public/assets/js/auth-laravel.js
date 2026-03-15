/**
 * Laravel Standard Authentication (No Firebase)
 * This file handles login/register form submissions via AJAX
 */

document.addEventListener("DOMContentLoaded", function () {
    console.log("→ Laravel authentication loaded");

    // Get CSRF token and dynamic URLs from body data attributes
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const loginUrl = document.body.dataset.loginUrl || '/login';
    const registerUrl = document.body.dataset.registerUrl || '/register';

    // Login Form Handler
    const loginForm = document.getElementById('firebaseLoginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const email = loginForm.email.value.trim();
            const password = loginForm.password.value;
            const errorDiv = document.getElementById('loginError');
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';

            // Client-side validation
            if (!email || !password) {
                errorDiv.textContent = 'Vui lòng nhập đầy đủ email và mật khẩu.';
                errorDiv.style.display = 'block';
                return;
            }

            try {
                const response = await fetch(loginUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    window.location.href = data.redirect || '/';
                } else if (response.status === 422) {
                    // Validation errors
                    const errors = data.errors;
                    let msg = '';
                    if (errors) {
                        for (const key in errors) {
                            msg += errors[key].join(', ') + '\n';
                        }
                    } else {
                        msg = data.message || 'Dữ liệu không hợp lệ.';
                    }
                    errorDiv.textContent = msg.trim();
                    errorDiv.style.display = 'block';
                } else {
                    errorDiv.textContent = data.message || 'Đăng nhập thất bại. Vui lòng kiểm tra lại email và mật khẩu.';
                    errorDiv.style.display = 'block';
                }
            } catch (error) {
                console.error('Login error:', error);
                errorDiv.textContent = 'Có lỗi xảy ra. Vui lòng thử lại.';
                errorDiv.style.display = 'block';
            }
        });
    }

    // Register Form Handler
    const registerForm = document.getElementById('firebaseRegisterForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const fullname = registerForm.fullname.value.trim();
            const email = registerForm.email.value.trim();
            const password = registerForm.password.value;
            const password_confirmation = registerForm.password_confirmation.value;
            const errorDiv = document.getElementById('registerError');
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';

            // Client-side validation
            if (!fullname || !email || !password || !password_confirmation) {
                errorDiv.textContent = 'Vui lòng nhập đầy đủ thông tin.';
                errorDiv.style.display = 'block';
                return;
            }
            if (password.length < 6) {
                errorDiv.textContent = 'Mật khẩu phải có ít nhất 6 ký tự.';
                errorDiv.style.display = 'block';
                return;
            }
            if (password !== password_confirmation) {
                errorDiv.textContent = 'Mật khẩu nhập lại không khớp.';
                errorDiv.style.display = 'block';
                return;
            }

            try {
                const response = await fetch(registerUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ fullname, email, password, password_confirmation })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    if (data.action === 'show_login') {
                        // Hide register modal
                        bootstrap.Modal.getInstance(document.getElementById('registerModal'))?.hide();
                        
                        // Show login modal
                        const loginModalEl = document.getElementById('loginModal');
                        if (loginModalEl) {
                            const loginModal = bootstrap.Modal.getOrCreateInstance(loginModalEl);
                            
                            // Show floating success message
                            if (typeof showAlert === 'function') {
                                showAlert(data.message || 'Đăng ký thành công! Vui lòng đăng nhập.', 'success');
                            }
                            
                            loginModal.show();
                        }
                    } else {
                        window.location.href = data.redirect || '/';
                    }
                } else if (response.status === 422) {
                    // Validation errors from Laravel
                    const errors = data.errors;
                    let msg = '';
                    if (errors) {
                        for (const key in errors) {
                            msg += errors[key].join(', ') + '\n';
                        }
                    } else {
                        msg = data.message || 'Dữ liệu không hợp lệ.';
                    }
                    errorDiv.textContent = msg.trim();
                    errorDiv.style.display = 'block';
                } else {
                    errorDiv.textContent = data.message || 'Đăng ký thất bại. Vui lòng thử lại.';
                    errorDiv.style.display = 'block';
                }
            } catch (error) {
                console.error('Register error:', error);
                errorDiv.textContent = 'Có lỗi xảy ra. Vui lòng thử lại.';
                errorDiv.style.display = 'block';
            }
        });
    }
});
