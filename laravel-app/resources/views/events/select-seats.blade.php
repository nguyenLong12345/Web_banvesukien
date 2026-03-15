@extends('layouts.app')

@section('title', 'Chọn ghế')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center"><i class="bi bi-ticket-perforated-fill"></i> Chọn ghế cho sự kiện</h2>

    <div class="booking-info mb-4 text-center bg-light p-3 rounded">
        <p class="mb-0"><strong>Họ tên:</strong> {{ $booking['fullname'] ?? '' }} |
           <strong>Email:</strong> {{ $booking['email'] ?? '' }}<br>
           <strong>SĐT:</strong> {{ $booking['phone'] ?? '' }} |
           <strong>Phương thức thanh toán:</strong> {{ $booking['payment_method'] ?? '' }}</p>
    </div>

    <form id="seatForm" method="POST" action="{{ route('payment.vnpay.redirect') }}">
        @csrf
        <input type="hidden" name="total_amount" id="totalAmountInput">
        <input type="hidden" name="selected_seats" id="selectedSeatsInput">

        <div class="text-center mb-3 fw-bold">SƠ ĐỒ CHỖ NGỒI</div>

        <div class="seat-map mb-4">
            @php $current_row = ''; @endphp
            @foreach($seats as $seat)
                @php $seat_row = substr($seat->seat_number, 0, 1); @endphp
                @if($seat_row !== $current_row)
                    @if($current_row !== '')
                        </div>
                    @endif
                    <div class="seat-row d-flex flex-wrap gap-1 align-items-center justify-content-center mb-2">
                        <span class="badge bg-secondary me-2">{{ $seat_row }}</span>
                    @php $current_row = $seat_row; @endphp
                @endif
                @php
                    $seat_type = strtolower($seat->seat_type);
                    $is_booked = $seat->sStatus === 'Đã đặt';
                    $seat_class = $is_booked ? 'booked' : 'available ' . $seat_type;
                @endphp
                <div class="seat {{ $seat_class }} d-inline-block p-2 m-1 rounded text-center fw-bold"
                    style="min-width: 40px; cursor: {{ $is_booked ? 'not-allowed' : 'pointer' }};"
                    data-seat="{{ $seat->seat_id }}"
                    data-price="{{ $seat->seat_price }}"
                    @if(!$is_booked) role="button" @endif>
                    {{ $seat->seat_number }}
                </div>
            @endforeach
            </div>
        </div>

        <style>
            body { background-color: #fff3e0 !important; } /* Light orange background */
            .seat.normal { background-color: #198754; color: white; transition: all 0.2s; }
            .seat.normal:hover:not(.booked):not(.selected) { background-color: #157347; transform: scale(1.1); }
            
            .seat.vip { background-color: #d63384; color: white; transition: all 0.2s; }
            .seat.vip:hover:not(.booked):not(.selected) { background-color: #c02976; transform: scale(1.1); }
            
            .seat.booked { background-color: #dee2e6; color: #6c757d; opacity: 0.7; }
            
            .seat.selected { background-color: #ffc107 !important; color: black !important; border: 2px solid #333; transform: scale(1.1); }
        </style>

        <div class="mb-4 text-center">
            <p class="total-price fw-bold">Tổng tiền: <span id="totalPrice">0</span> VND</p>
        </div>

        <div class="text-center mb-5">
            <button type="submit" class="btn btn-primary btn-lg px-5">Xác nhận & Thanh toán</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selected = new Set();
    let totalPrice = 0;

    document.querySelectorAll('.seat.available').forEach(seat => {
        seat.addEventListener('click', function() {
            const seatId = this.getAttribute('data-seat');
            const price = parseFloat(this.getAttribute('data-price'));

            if (selected.has(seatId)) {
                selected.delete(seatId);
                this.classList.remove('selected', 'bg-warning');
                totalPrice -= price;
            } else {
                selected.add(seatId);
                this.classList.add('selected', 'bg-warning');
                totalPrice += price;
            }

            document.getElementById('selectedSeatsInput').value = JSON.stringify(Array.from(selected));
            document.getElementById('totalAmountInput').value = totalPrice;
            document.getElementById('totalPrice').innerText = totalPrice.toLocaleString();
        });
    });

    document.getElementById('seatForm').addEventListener('submit', function(e) {
        if (selected.size === 0) {
            e.preventDefault();
            alert('Vui lòng chọn ít nhất một ghế.');
        }
    });
});
</script>
@endpush
@endsection
