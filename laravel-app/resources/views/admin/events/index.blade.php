@extends('layouts.admin')

@section('title', 'Quản lý sự kiện')
@section('current_page', 'events')

@section('content')
@include('admin.events.partials.event-modal')

@if($selectedEvent)
    @include('admin.events.partials.detail-modal', ['selectedEvent' => $selectedEvent, 'seats' => $seats, 'status' => $status])
@endif

<div class="container mt-4" style="margin-left: 20px;">
    <h2 class="mb-4"><i class="bi bi-easel2"></i> Danh sách sự kiện</h2>
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link {{ $status == 'upcoming' ? 'active' : '' }}" href="{{ route('admin.events.index', ['status' => 'upcoming'] + ($search ? ['search' => $search] : []) + ($filterDate ? ['filter_date' => $filterDate] : [])) }}">
                    <i class="bi bi-calendar-event"></i> Chưa diễn ra
                </a>
            </li>
            <li class="nav-item ms-2">
                <a class="nav-link {{ $status == 'active' ? 'active' : '' }}" href="{{ route('admin.events.index', ['status' => 'active'] + ($search ? ['search' => $search] : []) + ($filterDate ? ['filter_date' => $filterDate] : [])) }}">
                    <i class="bi bi-play-circle"></i> Đang diễn ra
                </a>
            </li>
            <li class="nav-item ms-2">
                <a class="nav-link {{ $status == 'ended' ? 'active' : '' }}" href="{{ route('admin.events.index', ['status' => 'ended'] + ($search ? ['search' => $search] : []) + ($filterDate ? ['filter_date' => $filterDate] : [])) }}">
                    <i class="bi bi-clock-history"></i> Đã kết thúc
                </a>
            </li>
        </ul>
        <form class="d-flex align-items-center gap-2" method="GET" action="{{ route('admin.events.index') }}" id="eventSearchForm">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="date" class="form-control flex-shrink-0" name="filter_date" value="{{ $filterDate }}" style="max-width: 150px;" id="eventFilterDate">
            <input type="text" class="form-control" name="search" style="max-width: 300px;" placeholder="Tìm kiếm tên sự kiện" value="{{ $search }}" id="eventSearchInput">
            <button class="btn btn-outline-primary flex-shrink-0" type="submit"><i class="bi bi-search"></i></button>
            <button type="button" class="btn btn-success flex-shrink-0" id="createEventBtn" data-bs-toggle="modal" data-bs-target="#editEventModal">
                <i class="bi bi-plus-circle"></i> Sự kiện
            </button>
        </form>
    </div>

    @if($events->isEmpty())
        <div class="alert alert-info">Không có sự kiện nào.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 7%;">Mã</th>
                        <th style="width: 33%;">Sự kiện</th>
                        <th style="width: 7%;">Thời gian</th>
                        <th style="width: 33%;">Địa điểm</th>
                        <th style="width: 10%;" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="event-body">
                    @foreach($events as $event)
                    <tr class="event-row">
                        <td>{{ $event->event_id }}</td>
                        <td>{{ $event->event_name }}</td>
                        <td>{{ $event->start_time->format('d/m/Y H:i') }}</td>
                        <td>{{ $event->location }}</td>
                        <td class="text-center">
                            @php $isBooked = in_array($event->event_id, $eventIdsWithBookedSeats); @endphp
                            <button class="btn btn-sm btn-warning edit-btn" data-bs-toggle="modal" data-bs-target="#editEventModal"
                                data-id="{{ $event->event_id }}"
                                data-name="{{ e($event->event_name) }}"
                                data-img="{{ $event->event_img }}"
                                data-start="{{ $event->start_time->format('Y-m-d\TH:i') }}"
                                data-price="{{ $event->price }}"
                                data-location="{{ e($event->location) }}"
                                data-seats="{{ $event->total_seats }}"
                                data-type="{{ e($event->event_type) }}"
                                data-duration="{{ $event->duration }}"
                                data-status="{{ $event->eStatus }}"
                                data-booked="{{ $isBooked ? '1' : '0' }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="d-inline" onsubmit="return confirm('Xác nhận xoá sự kiện này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            <a href="{{ route('admin.events.index', ['status' => $status, 'view' => $event->event_id]) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <nav><ul class="pagination justify-content-center" id="pagination-container"></ul></nav>
    @endif
</div>

@push('scripts')
<script>
const nextEventId = @json($nextEventId);
document.addEventListener('DOMContentLoaded', function() {
    const totalSeatsField = document.getElementById('totalSeats');
    const priceField = document.getElementById('price');
    const seatWarning = document.getElementById('seats-warning');

    const form = document.getElementById('eventForm');
    const storeUrl = form.dataset.storeUrl;
    const updateUrlTemplate = form.dataset.updateUrl;

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const d = this.dataset;
            form.action = updateUrlTemplate.replace('__ID__', d.id);
            if (form.querySelector('input[name="_method"]')) form.querySelector('input[name="_method"]').value = 'PUT';
            else {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);
            }
            document.getElementById('eventId').value = d.id;
            document.getElementById('eventName').value = d.name;
            document.getElementById('startTime').value = d.start;
            document.getElementById('price').value = d.price;
            document.getElementById('duration').value = d.duration;
            document.getElementById('location').value = d.location;
            document.getElementById('eStatus').value = d.status;
            document.getElementById('totalSeats').value = d.seats;
            document.getElementById('eventType').value = d.type;
            document.getElementById('eventIdDisplay').textContent = d.id;
            const imgPath = d.img.startsWith('http') ? d.img : '{{ asset("assets/images") }}/' + d.img;
            document.getElementById('eventImagePreview').src = imgPath;
            document.getElementById('eventImageLink').value = d.img.startsWith('http') ? d.img : '';
            document.getElementById('oldEventImg').value = d.img;

            seatWarning.innerHTML = '';
            // Remove any previously added hidden input for totalSeats
            const oldHidden = form.querySelector('input[name="total_seats"][type="hidden"]');
            if (oldHidden) oldHidden.remove();

            if (d.booked === '1') {
                totalSeatsField.disabled = true;
                priceField.readOnly = true;
                priceField.style.backgroundColor = '#e9ecef';
                // Add hidden input so total_seats value is still submitted
                const hiddenSeats = document.createElement('input');
                hiddenSeats.type = 'hidden';
                hiddenSeats.name = 'total_seats';
                hiddenSeats.value = d.seats;
                form.appendChild(hiddenSeats);
                seatWarning.innerHTML = '<div class="text-danger mt-1 fw-semibold">Không thể thay đổi số lượng ghế và giá vé vì đã có người đặt.</div>';
            } else {
                totalSeatsField.disabled = false;
                priceField.readOnly = false;
                priceField.style.backgroundColor = '';
            }
        });
    });

    document.getElementById('createEventBtn')?.addEventListener('click', function() {
        form.action = storeUrl;
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        document.querySelector('#editEventModal form')?.reset();
        document.getElementById('eventId').value = nextEventId;
        document.getElementById('eventIdDisplay').textContent = nextEventId;
        document.getElementById('oldEventImg').value = '';
        document.getElementById('eventImagePreview').src = '';
        document.getElementById('eventImageLink').value = '';
        if (totalSeatsField) totalSeatsField.disabled = false;
        if (priceField) { priceField.readOnly = false; priceField.style.backgroundColor = ''; }
        if (seatWarning) seatWarning.innerHTML = '';
        const oldHiddenCreate = form.querySelector('input[name="total_seats"][type="hidden"]');
        if (oldHiddenCreate) oldHiddenCreate.remove();
    });

    document.getElementById('eventImageInput')?.addEventListener('change', function(e) {
        const [file] = e.target.files;
        if (file) document.getElementById('eventImagePreview').src = URL.createObjectURL(file);
    });
});

const rows = document.querySelectorAll('.event-row');
const rowsPerPage = 15;
const totalPages = Math.ceil(rows.length / rowsPerPage);
const pagination = document.getElementById('pagination-container');

function showPage(page) {
    rows.forEach((row, i) => {
        row.style.display = (i >= (page - 1) * rowsPerPage && i < page * rowsPerPage) ? '' : 'none';
    });
    if (!pagination) return;
    pagination.innerHTML = '';
    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = 'page-item' + (i === page ? ' active' : '');
        li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        li.querySelector('a').addEventListener('click', (e) => { e.preventDefault(); showPage(i); });
        pagination.appendChild(li);
    }
}
if (rows.length) showPage(1);
</script>
@endpush
@endsection
