@extends('layouts.app')

@section('title', 'Tìm kiếm sự kiện')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="m-0">
            @if(!empty($query))
                Kết quả cho từ khóa: <strong>{{ $query }}</strong>
            @else
                Danh sách sự kiện sắp tới
            @endif
        </h3>
        <form method="GET" class="d-flex align-items-center gap-2">
            <input type="hidden" name="query" value="{{ $query }}">
            <select name="time_filter" class="form-select w-auto" onchange="this.form.submit()">
                <option value="">-- Tất cả thời gian --</option>
                <option value="week" {{ $timeFilter === 'week' ? 'selected' : '' }}>Tuần này</option>
                <option value="month" {{ $timeFilter === 'month' ? 'selected' : '' }}>Tháng này</option>
            </select>
        </form>
    </div>
    <hr>

    @if($results->isNotEmpty())
    <div class="row">
        @foreach($results as $ev)
        @php
            $parts = array_map('trim', explode(',', $ev->location ?? ''));
            $locationDisplay = count($parts) >= 2 ? implode(', ', array_slice($parts, -2)) : ($ev->location ?? '');
        @endphp
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <a href="{{ route('events.payment', $ev->event_id) }}" class="text-decoration-none text-dark">
                    <img src="{{ $ev->event_img && str_starts_with($ev->event_img, 'http') ? $ev->event_img : asset('assets/images/' . $ev->event_img) }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="{{ $ev->event_name }}">
                    <div class="card-body">
                        <div class="date-tag small text-muted">{{ $ev->start_time->format('d/m/Y') }}</div>
                        <p class="card-title fw-bold">{{ $ev->event_name }}</p>
                        <p class="card-text small text-secondary">{{ $locationDisplay }}</p>
                        <p class="price fw-bold">{{ number_format($ev->price) }}+</p>
                    </div>
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <h5>Không tìm thấy sự kiện nào phù hợp.</h5>
    @endif
</div>
@endsection
