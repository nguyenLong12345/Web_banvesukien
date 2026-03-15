@extends('layouts.admin')

@section('title', 'Quản lý đơn đặt vé')
@section('current_page', 'orders')

@section('content')
@if($selectedOrderId && $ticketDetails->isNotEmpty())
    @include('admin.orders.partials.ticket-modal', ['orderId' => $selectedOrderId, 'ticketDetails' => $ticketDetails])
@endif

<div class="container mt-4" style="margin-left: 20px;">
    <h2><i class="bi bi-ticket-perforated"></i> Quản lý đơn đặt vé</h2>
    <div class="d-flex justify-content-between align-items-center flex-wrap my-3">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="d-flex gap-2 align-items-center w-100" id="orderSearchForm">
            <input type="date" name="filter_date" value="{{ $filterDate }}" class="form-control" style="max-width: 200px;" id="orderFilterDate">
            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Tìm kiếm tên sự kiện" id="orderSearchInput">
            <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Mã đơn</th>
                    <th>Sự kiện</th>
                    <th>Ngày đặt</th>
                    <th>Số lượng</th>
                    <th>Trạng thái thanh toán</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @if($orders->isEmpty())
                    <tr><td colspan="6" class="text-center">Không có đơn đặt nào.</td></tr>
                @else
                    @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->order_id }}</td>
                        <td>{{ $order->event->event_name ?? '-' }}</td>
                        <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ $order->payment->pStatus ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admin.orders.index', ['order_id' => $order->order_id]) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

@if($selectedOrderId && $ticketDetails->isNotEmpty())
@push('scripts')
<script>
window.addEventListener('load', () => {
    const modal = new bootstrap.Modal(document.getElementById('ticketModal'));
    modal.show();
});
</script>
@endpush
@endif
@endsection
