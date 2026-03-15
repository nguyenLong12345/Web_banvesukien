@extends('layouts.app')

@section('title', 'Mua vé - ' . $event->event_name)

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/payment.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/seat.css') }}">
@endpush

@section('content')
<div class="detail-wrapper">
    <div class="detail-left">
        <h1 class="event-title">{{ $event->event_name }}</h1>
        <div class="event-meta">
            <p><strong><i class="fa-solid fa-clock"></i> Thời gian:</strong> {{ $event->start_time->format('H:i d/m/Y') }}</p>
            <p><strong><i class="fa-solid fa-location-dot"></i> Địa điểm:</strong> {{ $event->location }}</p>
        </div>
        <div class="price-box">
            <p>🎟 Giá vé từ:</p>
            <h2>VNĐ {{ number_format($event->price) }}+</h2>
        </div>

        @auth
        <button type="button" class="btn w-100 openModalBuy" style="background-color: #ff5722; color: white;"
            data-id="{{ $event->event_id }}" data-type="{{ $event->event_type }}">
            Mua vé ngay
        </button>
        @else
        <a href="#" class="buy-ticket openLogin">MUA VÉ NGAY</a>
        @endauth
    </div>
    <div class="detail-right">
        <img src="{{ $event->image_url }}" alt="{{ $event->event_name }}">
    </div>
</div>

@auth
@include('events.partials.ticket-modal')
@endauth
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('infoModal');
    if (modal) {
        document.querySelectorAll('.openModalBuy').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('modalEventId').value = this.dataset.id;
                document.getElementById('modalEventType').value = this.dataset.type;
                document.getElementById('selectedSeatsInput').value = '';
                document.querySelectorAll('#infoForm input').forEach(i => i.classList.remove('is-invalid'));
                document.querySelectorAll('#infoForm .invalid-feedback').forEach(el => el.textContent = '');
                new bootstrap.Modal(modal).show();
            });
        });
    }
});
</script>
@endpush
