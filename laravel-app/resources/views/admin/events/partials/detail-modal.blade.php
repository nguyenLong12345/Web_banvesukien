<div class="modal-backdrop fade show" style="z-index: 1040; background-color: rgba(0, 0, 0, 0.5);"></div>

<div class="modal fade show" id="eventDetailModal" tabindex="-1" style="display: block; z-index: 1050;" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-ticket-perforated"></i> Chi tiết vé & sơ đồ ghế</h5>
                <a href="{{ route('admin.events.index', ['status' => $status]) }}" class="btn-close btn-close-white"></a>
            </div>
            <div class="modal-body row g-4">
                <div class="col-md-6">
                    @php $imgUrl = $selectedEvent->image_url; @endphp
                    <img src="{{ $imgUrl }}" class="img-fluid rounded mb-3" alt="Event Image">
                    <h5>{{ $selectedEvent->event_name }}</h5>
                    <p><i class="bi bi-calendar-check"></i> {{ $selectedEvent->start_time->format('H:i d/m/Y') }}</p>
                    <p><i class="bi bi-geo-alt"></i> {{ $selectedEvent->location }}</p>
                    <p><i class="bi bi-clock"></i> Thời gian diễn ra: {{ number_format($selectedEvent->duration) }} h</p>
                    <p><i class="bi bi-cash-coin"></i> Giá vé: {{ number_format($selectedEvent->price) }} đ</p>
                    <p><i class="bi bi-person-lines-fill"></i> Ghế trống:
                        {{ $seats->where('sStatus', 'Còn trống')->count() }} / {{ $seats->count() }}
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="border p-3 rounded d-flex flex-wrap bg-light" style="min-height: 250px;">
                        @php $i = 0; @endphp
                        @foreach($seats as $seat)
                            @php
                                $class = 'seat ';
                                $class .= $seat->sStatus === 'Đã đặt' ? 'booked' : ($seat->seat_type === 'vip' ? 'vip' : 'normal');
                            @endphp
                            <div class="{{ $class }}">{{ $seat->seat_number }}</div>
                            @php
                                $i++;
                                if ($i % 10 === 0) echo '<div class="w-100"></div>';
                            @endphp
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.modal-backdrop.show { opacity: 0.7; background-color: rgba(0, 0, 0, 0.5); }
.seat { width: 40px; height: 40px; margin: 5px; border-radius: 10px; text-align: center; line-height: 45px; font-weight: bold; color: white; transition: 0.2s ease; }
.seat.normal { background-color: #198754; }
.seat.vip { background-color: #d63384; }
.seat.booked { background-color: #6c757d; }
</style>
