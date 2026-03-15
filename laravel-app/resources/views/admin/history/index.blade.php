@extends('layouts.admin')

@section('title', 'Lịch sử thanh toán')
@section('current_page', 'history')

@section('content')
<div class="container mt-4" style="margin-left: 20px;">
    <h2><i class="bi bi-cash-coin"></i> Lịch sử thanh toán</h2>
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <ul class="nav nav-pills my-3">
            <li class="nav-item">
                <a class="nav-link {{ $status == 'paid' ? 'active' : '' }}" href="{{ route('admin.history', ['status' => 'paid'] + ($search ? ['search' => $search] : []) + ($filterDate ? ['filter_date' => $filterDate] : [])) }}">
                    <i class="bi bi-check-circle"></i> Đã thanh toán
                </a>
            </li>
            <li class="nav-item ms-2">
                <a class="nav-link {{ $status == 'pending' ? 'active' : '' }}" href="{{ route('admin.history', ['status' => 'pending'] + ($search ? ['search' => $search] : []) + ($filterDate ? ['filter_date' => $filterDate] : [])) }}">
                    <i class="bi bi-hourglass-split"></i> Đang xử lý
                </a>
            </li>
            <li class="nav-item ms-2">
                <a class="nav-link {{ $status == 'cancel' ? 'active' : '' }}" href="{{ route('admin.history', ['status' => 'cancel'] + ($search ? ['search' => $search] : []) + ($filterDate ? ['filter_date' => $filterDate] : [])) }}">
                    <i class="bi bi-x-circle"></i> Đã hủy
                </a>
            </li>
        </ul>

        <form class="d-flex gap-2 align-items-center mb-3" method="GET" action="{{ route('admin.history') }}" id="historySearchForm">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="date" name="filter_date" value="{{ $filterDate }}" class="form-control" style="max-width: 200px;" id="historyFilterDate">
            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Tìm người thanh toán" style="max-width: 300px;" id="historySearchInput">
            <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Mã</th>
                    <th>Người thanh toán</th>
                    <th>Email</th>
                    <th>SDT</th>
                    <th>Thời gian</th>
                    <th>Số tiền</th>
                    <th>Phương thức</th>
                    <th>VNP Transaction</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @if($payments->isEmpty())
                    <tr><td colspan="9" class="text-center">Không có bản ghi nào.</td></tr>
                @else
                    @foreach($payments as $pay)
                    <tr>
                        <td>{{ $pay->payment_id }}</td>
                        <td>{{ $pay->fullname }}</td>
                        <td>{{ $pay->email }}</td>
                        <td>{{ $pay->phone }}</td>
                        <td>{{ $pay->payment_at?->format('d/m/Y H:i') }}</td>
                        <td>{{ number_format($pay->amount, 0, ',', '.') }}₫</td>
                        <td>{{ $pay->method ?? '-' }}</td>
                        <td>{{ $pay->vnp_transaction_no ?? '-' }}</td>
                        <td>
                            @php
                                $badge = match($pay->pStatus) {
                                    'paid' => 'success',
                                    'pending' => 'warning',
                                    'cancel' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }} px-3 py-2 text-capitalize">{{ $pay->pStatus }}</span>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
