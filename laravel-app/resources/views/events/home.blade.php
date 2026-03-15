@extends('layouts.app')

@section('title', 'Trang chủ - Bán vé sự kiện')

@section('content')
<div class="container mt-3">
    @if($sliderEvents->isNotEmpty())
    <div id="eventSlider" class="carousel slide mx-auto" data-bs-ride="carousel">
        <div class="carousel-indicators">
            @foreach($sliderEvents as $i => $row)
            <button type="button" data-bs-target="#eventSlider" data-bs-slide-to="{{ $i }}" class="{{ $i == 0 ? 'active' : '' }}"></button>
            @endforeach
        </div>
        <div class="carousel-inner">
            @foreach($sliderEvents as $i => $row)
            <div class="carousel-item {{ $i == 0 ? 'active' : '' }}">
                <a href="{{ route('events.payment', $row->event_id) }}">
                    <img src="{{ $row->image_url }}" class="d-block w-100" alt="{{ $row->event_name }}" style="max-height: 400px; object-fit: cover;">
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="container">
        <div class="col-md-6 col-lg-6 col-sm-9 col-xs-9 event_type_list mt-5">
            <h5>SỰ KIỆN NỔI BẬT</h5>
        </div>
        @if($featuredEvents->isNotEmpty())
        <div class="top-row">
            @foreach($featuredEvents->take(2) as $ev)
            <a href="{{ route('events.payment', $ev->event_id) }}">
                <img src="{{ $ev->image_url }}" alt="{{ $ev->event_name }}">
            </a>
            @endforeach
        </div>
        <div class="bottom-row mt-2">
            @foreach($featuredEvents->skip(2)->take(4) as $ev)
            <a href="{{ route('events.payment', $ev->event_id) }}">
                <img src="{{ $ev->image_url }}" alt="{{ $ev->event_name }}">
            </a>
            @endforeach
        </div>
        @else
        <p class="text-center mt-3">Không có sự kiện nổi bật nào.</p>
        @endif
    </div>

    @if($musicEvents->isNotEmpty())
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mt-5">
            <div class="col-md-6 col-lg-6 col-sm-9 col-xs-9 event_type_list">
                <h5>CA NHẠC</h5>
            </div>
            <a href="{{ route('events.type', 'music') }}" class="view-more">Xem thêm →</a>
        </div>
        <div class="event-slider">
            @foreach($musicEvents as $ev)
            <div class="event-item">
                <a href="{{ route('events.payment', $ev->event_id) }}" style="text-decoration: none; color: inherit;">
                    <img src="{{ $ev->image_url }}" alt="{{ $ev->event_name }}">
                    <p>{{ $ev->event_name }}</p>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($visitEvents->isNotEmpty())
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mt-5">
            <div class="col-md-6 col-lg-6 col-sm-9 col-xs-9 event_type_list">
                <h5>THAM QUAN</h5>
            </div>
            <a href="{{ route('events.type', 'visit') }}" class="view-more">Xem thêm →</a>
        </div>
        <div class="event-slider">
            @foreach($visitEvents as $ev)
            <div class="event-item">
                <a href="{{ route('events.payment', $ev->event_id) }}" style="text-decoration: none; color: inherit;">
                    <img src="{{ $ev->image_url }}" alt="{{ $ev->event_name }}">
                    <p>{{ $ev->event_name }}</p>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($artEvents->isNotEmpty())
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mt-5">
            <div class="col-md-6 col-lg-6 col-sm-9 col-xs-9 event_type_list">
                <h5>VĂN HÓA NGHỆ THUẬT</h5>
            </div>
            <a href="{{ route('events.type', 'art') }}" class="view-more">Xem thêm →</a>
        </div>
        <div class="event-slider">
            @foreach($artEvents as $ev)
            <div class="event-item">
                <a href="{{ route('events.payment', $ev->event_id) }}" style="text-decoration: none; color: inherit;">
                    <img src="{{ $ev->image_url }}" alt="{{ $ev->event_name }}">
                    <p>{{ $ev->event_name }}</p>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($tournamentEvents->isNotEmpty())
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mt-5">
            <div class="col-md-6 col-lg-6 col-sm-9 col-xs-9 event_type_list">
                <h5>GIẢI ĐẤU</h5>
            </div>
            <a href="{{ route('events.type', 'tournament') }}" class="view-more">Xem thêm →</a>
        </div>
        <div class="event-slider">
            @foreach($tournamentEvents as $ev)
            <div class="event-item">
                <a href="{{ route('events.payment', $ev->event_id) }}" style="text-decoration: none; color: inherit;">
                    <img src="{{ $ev->image_url }}" alt="{{ $ev->event_name }}">
                    <p>{{ $ev->event_name }}</p>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sliders = document.querySelectorAll('.event-slider');

    sliders.forEach(slider => {
        let isDown = false;
        let startX;
        let scrollLeft;
        let isDragging = false;

        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            isDragging = false;
            slider.classList.add('active');
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });

        slider.addEventListener('mouseleave', () => {
            isDown = false;
            slider.classList.remove('active');
        });

        slider.addEventListener('mouseup', () => {
            isDown = false;
            slider.classList.remove('active');
        });

        // Prevent native image dragging from interfering with our custom scroll
        slider.addEventListener('dragstart', (e) => {
            e.preventDefault();
        });

        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault(); // Prevent text selection/image dragging
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 2; // Scroll-fast multiplier
            
            // If moved more than 5px, consider it a drag
            if (Math.abs(walk) > 5) {
                isDragging = true;
            }
            
            slider.scrollLeft = scrollLeft - walk;
        });

        // Prevent clicking links if we were dragging
        slider.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', (e) => {
                if (isDragging) {
                    e.preventDefault();
                }
            });
        });
    });

    // Add mouse drag support to Bootstrap Carousels
    const carousels = document.querySelectorAll('.carousel');
    carousels.forEach(carouselEl => {
        let isDownCarousel = false;
        let startXCarousel;
        let isDraggingCarousel = false;
        
        // Disable native selection/dragging on carousel images
        carouselEl.addEventListener('dragstart', (e) => e.preventDefault());

        carouselEl.addEventListener('mousedown', (e) => {
            isDownCarousel = true;
            isDraggingCarousel = false;
            startXCarousel = e.pageX;
            carouselEl.style.cursor = 'grabbing';
        });

        carouselEl.addEventListener('mouseleave', () => {
            if(isDownCarousel) {
                isDownCarousel = false;
                carouselEl.style.cursor = 'default';
            }
        });

        carouselEl.addEventListener('mousemove', (e) => {
            if(!isDownCarousel) return;
            const diff = Math.abs(startXCarousel - e.pageX);
            if(diff > 10) {
                isDraggingCarousel = true;
            }
        });

        carouselEl.addEventListener('mouseup', (e) => {
            if(!isDownCarousel) return;
            isDownCarousel = false;
            carouselEl.style.cursor = 'default';
            
            const endX = e.pageX;
            const diff = startXCarousel - endX;

            // If dragged enough (e.g. 50px)
            if (Math.abs(diff) > 50) {
                const bsCarousel = bootstrap.Carousel.getInstance(carouselEl) || new bootstrap.Carousel(carouselEl);
                if (diff > 0) {
                    bsCarousel.next(); // Dragged left -> show next
                } else {
                    bsCarousel.prev(); // Dragged right -> show prev
                }
            }
        });
        
        // Reset cursor for carousel hover
        carouselEl.addEventListener('mouseenter', () => {
            if(!isDownCarousel) {
               carouselEl.style.cursor = 'grab';
            }
        });

        // Prevent clicking links if we were dragging
        carouselEl.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', (e) => {
                if (isDraggingCarousel) {
                    e.preventDefault();
                }
            });
        });
    });
});
</script>
@endpush

@endsection
