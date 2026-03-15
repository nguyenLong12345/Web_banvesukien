@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="profile-page-wrapper py-5">
    <div class="container">
        
        <div class="row align-items-start gx-5">
            <!-- Sidebar -->
            <div class="col-lg-4 mb-4">
                <div class="profile-sidebar card border-0 shadow-sm text-center glass-card">
                    <div class="card-body p-5">
                        <div class="avatar-circle mx-auto mb-4 d-flex align-items-center justify-content-center shadow-lg">
                            <span class="avatar-initials display-4 fw-bold text-white">{{ strtoupper(mb_substr($user->fullname, 0, 1)) }}</span>
                        </div>
                        <h4 class="fw-bold mb-1 text-dark">{{ $user->fullname }}</h4>
                        <p class="text-muted mb-4 opacity-75">{{ $user->email }}</p>
                        
                        <div class="d-flex justify-content-between text-start mt-4 bg-light p-3 rounded-3">
                            <div>
                                <small class="text-muted d-block">Trạng thái</small>
                                <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 mt-1 rounded-pill">Hoạt động</span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Mã KH</small>
                                <span class="fw-bold mt-1 d-block text-dark">{{ $user->user_id }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Form -->
            <div class="col-lg-8">
                <div class="profile-content">
                    
                    <!-- Update Info Card -->
                    <div class="card border-0 shadow-sm mb-5 glass-card profile-section">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                            <h5 class="fw-bold mb-0 float-start text-dark">Thông tin chung</h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('profile.update.info') }}" class="modern-form">
                                @csrf
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="form-label text-muted fw-bold small text-uppercase">Email</label>
                                        <input type="email" class="form-control bg-light border-0" value="{{ $user->email }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fullname" class="form-label text-muted fw-bold small text-uppercase">Họ và tên</label>
                                        <input type="text" class="form-control @error('fullname') is-invalid @enderror" id="fullname" name="fullname" value="{{ old('fullname', $user->fullname) }}" required placeholder="Nhập họ tên của bạn">
                                        @error('fullname')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="text-end mt-4 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm gradient-btn">Lưu Thay Đổi</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password Card -->
                    <div class="card border-0 shadow-sm glass-card profile-section">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                            <h5 class="fw-bold mb-0 text-dark">Đổi mật khẩu</h5>
                            <p class="text-muted small mt-2 mb-0">Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu cho người khác</p>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('profile.update.password') }}" class="modern-form">
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="current_password" class="form-label text-muted fw-bold small text-uppercase">Mật khẩu hiện tại</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required placeholder="Nhập mật khẩu cũ">
                                    @error('current_password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row gx-4 mb-4">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="password" class="form-label text-muted fw-bold small text-uppercase">Mật khẩu mới</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Mật khẩu mới (Tối thiểu 6 ký tự)">
                                        @error('password')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label text-muted fw-bold small text-uppercase">Nhập lại MK mới</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Xác nhận mật khẩu mới">
                                    </div>
                                </div>

                                <div class="text-end mt-4 pt-3 border-top">
                                    <button type="submit" class="btn btn-dark px-4 rounded-pill shadow-sm">Cập Nhật Mật Khẩu</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
