@extends('layouts.app')

@section('title', $eventTypeDisplay . ' - Sự kiện')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active">{{ $eventTypeDisplay }}</li>
            <li class="breadcrumb-item active">{{ $results->count() }} Sự kiện</li>
        </ol>
    </nav>

    @if($mainEvent)
    @php
        $parts = array_map('trim', explode(',', $mainEvent->location ?? ''));
        $locationDisplay = count($parts) >= 2 ? implode(', ', array_slice($parts, -2)) : ($mainEvent->location ?? '');
    @endphp
    <a href="{{ route('events.payment', $mainEvent->event_id) }}" class="text-decoration-none text-dark d-block card mb-4">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="{{ $mainEvent->event_img && str_starts_with($mainEvent->event_img, 'http') ? $mainEvent->event_img : asset('assets/images/' . $mainEvent->event_img) }}" class="img-fluid rounded-start" alt="{{ $mainEvent->event_name }}" style="height: 200px; object-fit: cover;">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h4>{{ $mainEvent->event_name }}</h4>
                    <p class="mb-1"><i class="bi bi-calendar3"></i> {{ $mainEvent->start_time->format('d/m/Y H:i') }}</p>
                    <p class="mb-1"><i class="bi bi-geo-alt"></i> {{ $locationDisplay }}</p>
                    <p class="fw-bold text-primary">VNĐ {{ number_format($mainEvent->price) }}+</p>
                </div>
            </div>
        </div>
    </a>
    @endif

    <div class="row">
        @foreach($results->skip(1) as $ev)
        @php
            $parts = array_map('trim', explode(',', $ev->location ?? ''));
            $locationDisplay = count($parts) >= 2 ? implode(', ', array_slice($parts, -2)) : ($ev->location ?? '');
        @endphp
        <div class="col-md-3 mb-4">
            <a href="{{ route('events.payment', $ev->event_id) }}" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm">
                    <img src="{{ $ev->event_img && str_starts_with($ev->event_img, 'http') ? $ev->event_img : asset('assets/images/' . $ev->event_img) }}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="{{ $ev->event_name }}">
                    <div class="card-body">
                        <p class="card-title fw-bold">{{ $ev->event_name }}</p>
                        <p class="card-text small">{{ $locationDisplay }}</p>
                        <p class="fw-bold">VNĐ {{ number_format($ev->price) }}+</p>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    @if($results->isEmpty())
    <p>Không tìm thấy sự kiện nào thuộc loại "{{ $eventTypeDisplay }}".</p>
    @endif
</div>
@endsection
