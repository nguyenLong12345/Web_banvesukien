<nav class="navbar navbar-expand-lg">
    <div class="container d-flex align-items-center justify-content-between">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" height="50" width="210">
        </a>

        <div class="search-container d-flex">
            <form class="d-flex w-100" id="searchForm" action="{{ route('search') }}" method="GET">
                <input class="form-control search-input" id="searchInput" type="search" name="query" placeholder="Tìm kiếm sự kiện..." value="{{ request('query') }}" aria-label="Search" autocomplete="off">
                <button class="btn btn-outline-light search-btn" type="submit"><i class="fas fa-search"></i></button>
            </form>
            <div id="searchDropdown" class="search-dropdown"></div>
        </div>



        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list"></i>
        </button>

        <div class="collapse navbar-collapse justify-content-end text-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('tickets.index') }}" title="Vé của tôi">
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                </li>
                @auth
                    <li class="nav-item dropdown px-2 d-flex align-items-center">
                        <a class="nav-link dropdown-toggle text-white fw-bold cursor-pointer" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                            {{ Auth::user()->fullname ?? 'Người dùng' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown" style="border-radius: 10px; min-width: 130px;">
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('profile.show') }}">
                                    Thông tin cá nhân
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item dropdown px-2 d-flex align-items-center">
                        <a class="nav-link dropdown-toggle text-white cursor-pointer" id="guestDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                            Tài khoản
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="guestDropdown" style="border-radius: 10px; min-width: 130px;">
                            <li>
                                <a class="dropdown-item py-2 openLogin cursor-pointer" style="cursor: pointer;">
                                    Đăng nhập
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 cursor-pointer" id="openRegister" style="cursor: pointer;">
                                    Đăng ký
                                </a>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
<div class="sub-navbar">
    <div class="container d-flex justify-content-between align-items-center">
        <ul class="sub-nav-list mb-0">
            <li><a href="{{ route('events.type', 'all') }}">Tất cả</a></li>
            <li><a href="{{ route('events.type', 'music') }}">Âm nhạc</a></li>
            <li><a href="{{ route('events.type', 'art') }}">Văn hóa nghệ thuật</a></li>
            <li><a href="{{ route('events.type', 'visit') }}">Tham quan</a></li>
            <li><a href="{{ route('events.type', 'tournament') }}">Giải đấu</a></li>
        </ul>
        <div class="location-filter">
            <select class="form-select custom-location-select" id="locationSelect" aria-label="Chọn địa điểm">
                <option value="" {{ !request('location') ? 'selected' : '' }}>Chọn địa điểm</option>
                <option value="HN" {{ request('location') == 'HN' ? 'selected' : '' }}>Hà Nội</option>
                <option value="HCM" {{ request('location') == 'HCM' ? 'selected' : '' }}>Hồ Chí Minh</option>
                <option value="DL" {{ request('location') == 'DL' ? 'selected' : '' }}>Đà Lạt</option>
                <option value="QN" {{ request('location') == 'QN' ? 'selected' : '' }}>Quảng Ninh</option>
                <option value="HUE" {{ request('location') == 'HUE' ? 'selected' : '' }}>Huế</option>
                <option value="QNA" {{ request('location') == 'QNA' ? 'selected' : '' }}>Quảng Nam</option>
                <option value="DN" {{ request('location') == 'DN' ? 'selected' : '' }}>Đà Nẵng</option>
            </select>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchDropdown = document.getElementById('searchDropdown');
    let debounceTimer;

    if (!searchInput || !searchDropdown) return;

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(debounceTimer);

        if (query.length === 0) {
            searchDropdown.style.display = 'none';
            searchDropdown.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/api/events/search-suggestions?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        let html = '';
                        data.forEach(item => {
                            html += `
                                <a href="${item.url}" class="search-suggestion-item">
                                    <img src="${item.image}" alt="${item.name}" class="search-suggestion-img">
                                    <div class="search-suggestion-details">
                                        <h6 class="search-suggestion-title">${item.name}</h6>
                                        <p class="search-suggestion-date"><i class="far fa-calendar-alt"></i> ${item.date}</p>
                                    </div>
                                </a>
                            `;
                        });
                        searchDropdown.innerHTML = html;
                        searchDropdown.style.display = 'block';
                    } else {
                        searchDropdown.innerHTML = '<div class="p-3 text-center text-muted">Không tìm thấy sự kiện nào</div>';
                        searchDropdown.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error fetching search suggestions:', error);
                    searchDropdown.style.display = 'none';
                });
        }, 300); // 300ms debounce
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target) && !searchDropdown.contains(event.target)) {
            searchDropdown.style.display = 'none';
        }
    });

    const locationSelect = document.getElementById('locationSelect');
    if (locationSelect) {
        locationSelect.addEventListener('change', function() {
            const loc = this.value;
            const currentPath = window.location.pathname;

            // If on home page, redirect to "all events"
            if (currentPath === '/' || currentPath === '/home') {
                let allEventsUrl = '{{ route("events.type", "all") }}';
                if (loc) {
                    allEventsUrl += '?location=' + loc;
                }
                window.location.href = allEventsUrl;
            } else {
                // Otherwise build URL for current page
                const url = new URL(window.location.href);
                if (loc) {
                    url.searchParams.set('location', loc);
                } else {
                    url.searchParams.delete('location');
                }
                // Reset page to 1 if pagination exists so it doesn't break
                url.searchParams.delete('page');
                window.location.href = url.toString();
            }
        });
    }

    // Show dropdown again if input is clicked and has text
    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length > 0 && searchDropdown.innerHTML !== '') {
            searchDropdown.style.display = 'block';
        }
    });

    @if(session('showLogin'))
        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
    @endif

    @if(session('showRegister'))
        const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
        registerModal.show();
    @endif
});
</script>
@endpush
