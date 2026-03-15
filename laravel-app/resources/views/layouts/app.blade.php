<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="{{ asset('assets/images/icove.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    @stack('styles')
</head>
<body data-login-url="{{ route('login.store') }}" data-register-url="{{ route('register.store') }}" data-firebase-verify-url="{{ route('auth.firebase.verify') }}" data-home-url="{{ route('home') }}">
    @include('layouts.partials.header')

    @guest
        @include('components.auth.login-modal')
        @include('components.auth.register-modal')
        @include('components.auth.forgot-password-modal')
        @include('components.notifications')
    @endguest

    <main>
        @if(session('error'))
            <div class="container mt-2"><div class="alert alert-danger">{{ session('error') }}</div></div>
        @endif
        @if(session('success'))
            <div class="container mt-2"><div class="alert alert-success">{{ session('success') }}</div></div>
        @endif
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    <!-- AI Chatbot Widget -->
    <div id="ai-chat-widget" class="chat-widget-container position-fixed" style="bottom: 20px; right: 20px; z-index: 9999;">
        <!-- Chat Button -->
        <button id="chatbot-toggle-btn" class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: linear-gradient(135deg, #ff672a, #d2501a); border: none;">
            <i class="fas fa-robot fs-3 text-white"></i>
        </button>

        <!-- Chat Window -->
        <div id="chatbot-window" class="chat-window d-none shadow-lg d-flex flex-column" style="position: absolute; bottom: 70px; right: 0; width: 325px; height: 465px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 15px; overflow: hidden; border: 1px solid rgba(0,0,0,0.1);">
            <div class="chat-header p-3 text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #ac5858, #333333);">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-robot fs-4"></i>
                    <div>
                        <h6 class="mb-0 fw-bold">Trợ lý Ticket Events</h6>
                        <small class="text-white-50" style="font-size: 0.75rem;">Sẵn sàng hỗ trợ</small>
                    </div>
                </div>
                <button id="chatbot-close-btn" class="btn text-white p-0"><i class="fas fa-times fs-5"></i></button>
            </div>
            
            <div id="chat-messages" class="chat-messages flex-grow-1 p-3 overflow-auto" style="background: #f1f5f9; display: flex; flex-direction: column;">
                <div class="message ai-message mb-3 d-flex flex-column align-items-start" style="animation: fadeIn 0.3s ease-out;">
                    <div class="bg-white text-dark shadow-sm p-2 px-3" style="border-radius: 18px 18px 18px 4px; max-width: 85%; line-height: 1.5; text-align: left; background: linear-gradient(135deg, #f8f9fa, #ffffff) !important; border: 1px solid rgba(0,0,0,0.05);">
                        Xin chào! Bạn cần giúp gì?
                    </div>
                </div>
            </div>

            <div class="chat-input-area p-3 bg-white border-top">
                <form id="chat-form" class="d-flex gap-2 mb-0">
                    <input type="text" id="chat-input" class="form-control rounded-pill border-0 shadow-sm bg-light px-4 py-2" placeholder="Nhập tin nhắn..." required autocomplete="off" style="font-size: 0.95rem;">
                    <button type="submit" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 chat-submit-btn shadow-sm" style="width: 42px; height: 42px; background: linear-gradient(135deg, #ff672a, #d2501a); border: none;">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Chatbot Javascript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('chatbot-toggle-btn');
            const closeBtn = document.getElementById('chatbot-close-btn');
            const chatWindow = document.getElementById('chatbot-window');
            const chatForm = document.getElementById('chat-form');
            const chatInput = document.getElementById('chat-input');
            const chatMessages = document.getElementById('chat-messages');
            const submitBtn = document.querySelector('.chat-submit-btn');

            // Toggle window
            toggleBtn.addEventListener('click', () => {
                chatWindow.classList.toggle('d-none');
                if(!chatWindow.classList.contains('d-none')) {
                    chatInput.focus();
                }
            });

            closeBtn.addEventListener('click', () => {
                chatWindow.classList.add('d-none');
            });

            function appendMessage(text, isUser = false) {
                const alignClass = isUser ? 'align-items-end' : 'align-items-start';
                const pillRadius = isUser ? '18px 18px 4px 18px' : '18px 18px 18px 4px';
                const bgClass = isUser ? 'text-white' : 'bg-white text-dark';
                const botGradient = 'linear-gradient(135deg, #f8f9fa, #ffffff)';
                const userGradient = 'linear-gradient(135deg, #ff672a, #d2501a)';
                
                // Allow simple formatting for AI text (newlines)
                const formattedText = text.trim().replace(/\n/g, '<br>');

                const msgHtml = `
                    <div class="message mb-3 d-flex flex-column ${alignClass}" style="animation: fadeIn 0.3s ease-out;">
                        <div class="${bgClass} shadow-sm p-2 px-3" style="border-radius: ${pillRadius}; max-width: 85%; font-size: 0.95rem; line-height: 1.5; text-align: left; background: ${isUser ? userGradient : botGradient} !important; border: ${isUser ? 'none' : '1px solid rgba(0,0,0,0.05)'};">
                            ${formattedText}
                        </div>
                    </div>
                `;
                chatMessages.insertAdjacentHTML('beforeend', msgHtml);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function appendTypingIndicator() {
                const indicatorId = 'typing-' + Date.now();
                const msgHtml = `
                    <div id="${indicatorId}" class="message mb-3 d-flex flex-column align-items-start">
                        <span class="badge bg-light text-muted fw-normal shadow-sm p-2 px-3 text-start d-flex gap-1 align-items-center" style="border-radius: 15px 15px 15px 0; height: 32px;">
                            <span class="spinner-grow spinner-grow-sm bg-secondary" style="width: 0.4rem; height: 0.4rem;" role="status"></span>
                            <span class="spinner-grow spinner-grow-sm bg-secondary" style="width: 0.4rem; height: 0.4rem; animation-delay: 0.2s;" role="status"></span>
                            <span class="spinner-grow spinner-grow-sm bg-secondary" style="width: 0.4rem; height: 0.4rem; animation-delay: 0.4s;" role="status"></span>
                        </span>
                    </div>
                `;
                chatMessages.insertAdjacentHTML('beforeend', msgHtml);
                chatMessages.scrollTop = chatMessages.scrollHeight;
                return indicatorId;
            }

            chatForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const message = chatInput.value.trim();
                if(!message) return;

                // Lock input
                chatInput.value = '';
                chatInput.disabled = true;
                submitBtn.disabled = true;

                // Append user message
                appendMessage(message, true);

                // Show typing indicator
                const typingId = appendTypingIndicator();

                try {
                    const response = await fetch('/chatbot/message', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ message: message })
                    });

                    const data = await response.json();
                    
                    // Remove indicator
                    document.getElementById(typingId)?.remove();

                    if(response.ok && data.reply) {
                        appendMessage(data.reply, false);
                    } else {
                        appendMessage(data.error || 'Có lỗi xảy ra, thử lại sau.', false);
                    }

                } catch(error) {
                    document.getElementById(typingId)?.remove();
                    appendMessage('Mất kết nối tới máy chủ AI.', false);
                } finally {
                    chatInput.disabled = false;
                    submitBtn.disabled = false;
                    chatInput.focus();
                }
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @guest
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginModalEl = document.getElementById('loginModal');
        const registerModalEl = document.getElementById('registerModal');
        const forgotModalEl = document.getElementById('forgotPasswordModal');
        if (loginModalEl) {
            const loginModal = new bootstrap.Modal(loginModalEl, { backdrop: 'static' });
            document.querySelectorAll('.openLogin').forEach(btn => {
                btn.addEventListener('click', function(e) { e.preventDefault(); loginModal.show(); });
            });
            const myTicketsBtn = document.getElementById('myTicketsBtn');
            if (myTicketsBtn) {
                myTicketsBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    loginModal.show();
                });
            }
        }
        if (registerModalEl) {
            const registerModal = new bootstrap.Modal(registerModalEl);
            document.querySelectorAll('#openRegister').forEach(el => {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    bootstrap.Modal.getInstance(document.getElementById('loginModal'))?.hide();
                    registerModal.show();
                });
            });
        }
        document.querySelectorAll('#openLogin').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                bootstrap.Modal.getInstance(document.getElementById('registerModal'))?.hide();
                bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'))?.hide();
                bootstrap.Modal.getInstance(document.getElementById('loginModal'))?.show();
            });
        });
        document.querySelectorAll('#openForgotPassword').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                bootstrap.Modal.getInstance(document.getElementById('loginModal'))?.hide();
                new bootstrap.Modal(document.getElementById('forgotPasswordModal')).show();
            });
        });
    });
    </script>
    @endguest
    @guest
    <script src="{{ asset('assets/js/auth-laravel.js') }}?v={{ time() }}"></script>
    
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
        import { getAuth, signInWithPopup, GoogleAuthProvider } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

        // Cấu hình Firebase config lấy từ Firebase Console của bạn
        const firebaseConfig = {
            apiKey: "[REMOVED]",
            authDomain: "event-bookings-a9e23.firebaseapp.com",
            projectId: "event-bookings-a9e23",
            storageBucket: "event-bookings-a9e23.firebasestorage.app",
            messagingSenderId: "17505647210",
            appId: "1:17505647210:web:d3ca38daa2efcebe5ee813"
        };
        
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const provider = new GoogleAuthProvider();

        const googleBtn = document.getElementById('googleLoginBtn');
        if (googleBtn) {
            googleBtn.addEventListener('click', () => {
                signInWithPopup(auth, provider)
                    .then((result) => {
                        return result.user.getIdToken();
                    })
                    .then((idToken) => {
                        return fetch(document.body.dataset.firebaseVerifyUrl || '/auth/firebase/verify', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ idToken: idToken })
                        });
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.status === 'success') {
                            window.location.reload(); 
                        } else {
                            alert('Đăng nhập lỗi: ' + data.message);
                        }
                    })
                    .catch((error) => {
                        console.error(error);
                        alert('Có lỗi xảy ra khi đăng nhập bằng Google.');
                    });
            });
        }
    </script>
    @endguest
    @stack('scripts')

</body>
</html>
