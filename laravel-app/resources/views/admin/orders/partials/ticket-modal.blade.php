<div class="modal fade show" id="ticketModal" style="display:block; background: rgba(0,0,0,0.5);" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết đơn hàng: {{ $orderId }}</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn-close"></a>
            </div>
            <div class="modal-body">
                @foreach($ticketDetails as $ticket)
                @php
                    $event = $ticket->order->event ?? null;
                    $payment = $ticket->order->payment ?? null;
                    $imgUrl = $event ? $event->image_url : '';
                    $badgeClass = match($ticket->tStatus) {
                        'Thành công' => 'bg-info',
                        'Đã hủy' => 'bg-danger',
                        default => 'bg-secondary',
                    };
                @endphp
                <div class="card mb-3 shadow-sm border">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="{{ $imgUrl }}" class="img-fluid rounded-start" alt="event">
                        </div>
                        <div class="col-md-8 position-relative">
                            <div class="card-body">
                                <h5 class="card-title">{{ $event?->event_name ?? '-' }}</h5>
                                <p class="card-text">
                                    <strong>Ngày tổ chức:</strong> {{ $event?->start_time?->format('d/m/Y H:i') ?? '-' }}<br>
                                    <strong>Trạng thái sự kiện:</strong> {{ $event?->eStatus ?? '-' }}<br>
                                    <strong>Ghế:</strong> {{ $ticket->seat->seat_number ?? '-' }}<br>
                                    <strong>Người mua:</strong> {{ $payment?->fullname ?? '-' }}<br>
                                    <strong>Email:</strong> {{ $payment?->email ?? '-' }} |
                                    <strong>SDT:</strong> {{ $payment?->phone ?? '-' }}
                                </p>

                                <form method="POST" action="{{ route('admin.orders.update-ticket') }}" class="d-flex align-items-center gap-2 mt-2">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $orderId }}">
                                    <input type="hidden" name="ticket_id" value="{{ $ticket->ticket_id }}">
                                    <input type="hidden" name="seat_id" value="{{ $ticket->seat_id }}">
                                    <select name="new_status" class="form-select form-select-sm" style="width: auto;">
                                        <option value="Thành công" {{ $ticket->tStatus == 'Thành công' ? 'selected' : '' }}>Thành công</option>
                                        <option value="Đã hủy" {{ $ticket->tStatus == 'Đã hủy' ? 'selected' : '' }}>Đã hủy</option>
                                    </select>
                                    <button class="btn btn-sm btn-primary" type="submit">Cập nhật</button>
                                </form>
                            </div>
                            <div class="position-absolute top-0 end-0 p-2">
                                <span class="badge {{ $badgeClass }} px-3 py-2">{{ $ticket->tStatus }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
