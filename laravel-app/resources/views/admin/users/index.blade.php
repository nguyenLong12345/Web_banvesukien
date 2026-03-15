@extends('layouts.admin')

@section('title', 'Quản lý tài khoản')
@section('current_page', 'users')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4"><i class="bi bi-person"></i> Quản lý tài khoản</h2>
    <div class="d-flex justify-content-between align-items-center mb-3 w-100">
        <form method="GET" class="d-flex gap-2 w-100">
            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Tìm theo ID hoặc tên người dùng">
            <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
            <tr>
                <th>User id</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->user_id }}</td>
                <td>{{ $user->fullname }}</td>
                <td>{{ $user->email }}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal_{{ $user->user_id }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Xác nhận xóa người dùng?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>

            <div class="modal fade" id="editModal_{{ $user->user_id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.users.update', $user) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Chỉnh sửa tài khoản người dùng</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="user_id" value="{{ $user->user_id }}">
                                <div class="mb-3">
                                    <label class="form-label">Họ tên</label>
                                    <input type="text" name="fullname" class="form-control" value="{{ $user->fullname }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Lưu</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
