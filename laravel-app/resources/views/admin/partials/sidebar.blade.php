<div class="sidebar d-flex flex-column">
    <div class="logo">
        <i class="bi bi-shield-lock"></i> ADMIN PANEL
    </div>
    <nav class="nav flex-column mt-3 px-2">
        <a class="nav-link {{ ($current_page ?? '') == 'dashboard' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a class="nav-link {{ ($current_page ?? '') == 'users' ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
            <i class="bi bi-person"></i> Quản lý tài khoản
        </a>
        <a class="nav-link {{ ($current_page ?? '') == 'events' ? 'active' : '' }}" href="{{ route('admin.events.index', ['status' => 'upcoming']) }}">
            <i class="bi bi-calendar-event"></i> Quản lý sự kiện
        </a>
        <a class="nav-link {{ ($current_page ?? '') == 'orders' ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
            <i class="bi bi-ticket-perforated"></i> Quản lý đơn hàng
        </a>
        <a class="nav-link {{ ($current_page ?? '') == 'history' ? 'active' : '' }}" href="{{ route('admin.history') }}">
            <i class="bi bi-check-circle"></i> Quản lý thanh toán
        </a>
        <form method="POST" action="{{ route('admin.logout') }}" class="nav-link">
            @csrf
            <button type="submit" class="btn btn-link nav-link p-0 border-0 bg-transparent text-start w-100" style="color: inherit;">
                <i class="bi bi-box-arrow-right"></i> Đăng xuất
            </button>
        </form>
    </nav>
</div>
