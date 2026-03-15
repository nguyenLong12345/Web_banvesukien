@extends('layouts.app')

@section('title', 'Vé đã mua')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Vé đã mua</h2>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $tstatus == 'all' ? 'active' : '' }}" href="{{ route('tickets.index', ['tstatus' => 'all', 'estatus' => $estatus]) }}">Tất cả</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tstatus == 'Thành công' ? 'active' : '' }}" href="{{ route('tickets.index', ['tstatus' => 'Thành công', 'estatus' => $estatus]) }}">Thành công</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tstatus == 'Đã hủy' ? 'active' : '' }}" href="{{ route('tickets.index', ['tstatus' => 'Đã hủy', 'estatus' => $estatus]) }}">Đã hủy</a>
        </li>
    </ul>

    @if($orders->isEmpty())
        <p>Bạn chưa có vé nào trong mục này.</p>
    @else
        @foreach($orders as $ticket)
            @if(empty($ticket->event_id)) @continue @endif
            @php
                $img = $ticket->event_img ?? '';
                $imgUrl = str_starts_with($img, 'http') ? $img : asset('assets/images/' . $img);
            @endphp
            <div class="card mb-4">
                <div class="row g-0">
                    <div class="col-md-3">
                        <img src="{{ $imgUrl }}" class="img-fluid rounded-start" alt="Ảnh sự kiện" style="height: 150px; object-fit: cover;">
                    </div>
                    <div class="col-md-9">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">{{ $ticket->event_name }}</h5>
                                @if($ticket->tStatus === 'Thành công')
                                    <span class="badge bg-success">Thành công</span>
                                @elseif($ticket->tStatus === 'Đã hủy')
                                    <span class="badge bg-danger">Đã hủy</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $ticket->tStatus }}</span>
                                @endif
                            </div>
                            <p class="mb-1">Ngày tổ chức: {{ $ticket->start_time }}</p>
                            <p class="mb-1">Email: {{ $ticket->email }} | SĐT: {{ $ticket->phone }}</p>
                            <p class="mb-1">Ghế: {{ $ticket->seat_number }}</p>
                            <p class="mb-1">Người mua: {{ $ticket->fullname }}</p>
                            <p class="mb-0">Trạng thái sự kiện: {{ $ticket->eStatus }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <a href="{{ route('home') }}" class="btn btn-outline-primary">Về trang chủ</a>
</div>
@endsection
