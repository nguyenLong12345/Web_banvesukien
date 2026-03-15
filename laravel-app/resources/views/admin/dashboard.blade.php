@extends('layouts.admin')

@section('title', 'Bảng điều khiển')
@section('current_page', 'dashboard')

@section('content')
<h1 class="mb-4"><i class="bi bi-speedometer2"></i> Bảng điều khiển</h1>

<div class="row g-3">
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 card-icon"><i class="bi bi-people"></i></div>
                <div>
                    <h5 class="card-title mb-1"><a href="{{ route('admin.users.index') }}">Người dùng</a></h5>
                    <p class="card-text text-muted">{{ $totalUsers }} tài khoản</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 card-icon"><i class="bi bi-calendar-event"></i></div>
                <div>
                    <h5 class="card-title mb-1"><a href="{{ route('admin.events.index', ['status' => 'upcoming']) }}">Sự kiện</a></h5>
                    <p class="card-text text-muted">{{ $totalEvents }} sự kiện</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 card-icon"><i class="bi bi-ticket-perforated"></i></div>
                <div>
                    <h5 class="card-title mb-1"><a href="{{ route('admin.orders.index') }}">Vé đã bán</a></h5>
                    <p class="card-text text-muted">{{ $totalTickets }} vé</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 card-icon"><i class="bi bi-bar-chart-line"></i></div>
                <div>
                    <h5 class="card-title mb-1"><a href="{{ route('admin.history') }}">Tổng doanh thu</a></h5>
                    <p class="card-text text-muted">{{ number_format($totalPaids, 0, ',', '.') }} VND</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title">Doanh thu theo ngày</h5>
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="revenue_date" class="form-label">Chọn ngày</label>
                <input type="date" class="form-control" id="revenue_date" name="revenue_date" value="{{ $revenueDate }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Xem doanh thu</button>
            </div>
        </form>
        @if($dailyTotal > 0)
            <div class="alert alert-success mt-4">
                Tổng <strong>{{ \Carbon\Carbon::parse($revenueDate)->format('d/m/Y') }}</strong>:
                <strong>{{ number_format($dailyTotal, 0, ',', '.') }} VND</strong>
            </div>
        @else
            <div class="alert alert-success mt-4">
                Không có doanh thu nào vào ngày <strong>{{ \Carbon\Carbon::parse($revenueDate)->format('d/m/Y') }}</strong>.
            </div>
        @endif
    </div>
</div>

<div class="row mt-4 g-3">
    <!-- Biểu đồ doanh thu (Full width) -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Tổng quan doanh thu</h5>
                    <select id="chartFilter" class="form-select w-auto">
                        <option value="week">Theo tuần</option>
                        <option value="month">Theo tháng</option>
                        <option value="quarter">Theo quý</option>
                        <option value="year">Theo năm</option>
                    </select>
                </div>
                <!-- Fix Chart.js infinite height bug by providing a relative wrapper with fixed height -->
                <div style="height: 350px; position: relative; width: 100%;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4 g-3 mb-4">
    <!-- Biểu đồ loại sự kiện -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Tỉ lệ vé bán theo Loại sự kiện</h5>
                <div style="height: 300px; position: relative; width: 100%; display: flex; justify-content: center;">
                    <canvas id="eventTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ Top sự kiện bán chạy -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Top 5 Sự kiện bán chạy nhất</h5>
                <div style="height: 300px; position: relative; width: 100%;">
                    <canvas id="topEventsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let chart;
let chartData = {
    month: {
        labels: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"],
        data: @json(array_values($monthlyRevenue))
    },
    quarter: {
        labels: ["Q1", "Q2", "Q3", "Q4"],
        data: @json(array_values($quarterRevenue))
    },
    year: {
        labels: [@for($i = 4; $i >= 0; $i--) "{{ $currentYear - $i }}"{{ $i > 0 ? ',' : '' }} @endfor],
        data: [@for($i = 4; $i >= 0; $i--) {{ $yearRevenue[$currentYear - $i] ?? 0 }}{{ $i > 0 ? ',' : '' }} @endfor]
    },
    week: {
        labels: @json($weeklyRevenueLabels),
        data: @json($weeklyRevenueData)
    }
};

function renderRevenueChart(type) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    if (chart) chart.destroy();
    const total = chartData[type].data.reduce((a, b) => a + b, 0);
    chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData[type].labels,
            datasets: [{
                label: 'Doanh thu (VND)',
                data: chartData[type].data,
                backgroundColor: '#007bff',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top', align: 'end' },
                subtitle: {
                    display: true,
                    text: 'Tổng: ' + total.toLocaleString('vi-VN') + ' VND',
                    align: 'end', position: 'top', font: { size: 14, weight: 'normal' }, padding: { bottom: 10 }
                }
            },
            scales: {
                y: {
                    ticks: { callback: function(value) { return value.toLocaleString('vi-VN') + ' VND'; } }
                }
            }
        }
    });
}
document.getElementById('chartFilter').addEventListener('change', function() { renderRevenueChart(this.value); });
document.getElementById('chartFilter').value = 'week';
renderRevenueChart('week');

// Render Event Type Doughnut Chart
const eventTypeCtx = document.getElementById('eventTypeChart').getContext('2d');
new Chart(eventTypeCtx, {
    type: 'doughnut',
    data: {
        labels: @json($eventTypeLabels),
        datasets: [{
            data: @json($eventTypeData),
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' },
        }
    }
});

// Render Top Events Horizontal Bar Chart
const topEventsCtx = document.getElementById('topEventsChart').getContext('2d');
new Chart(topEventsCtx, {
    type: 'bar',
    data: {
        labels: @json($topEventLabels),
        datasets: [{
            label: 'Số vé bán được',
            data: @json($topEventData),
            backgroundColor: '#28a745',
            borderRadius: 4
        }]
    },
    options: {
        indexAxis: 'y', // Makes it a horizontal bar chart
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>
@endpush
@endsection
