# Event Bookings - Laravel Architecture Design

Mapping from plain PHP to Laravel structure.

## 1. Controllers Structure

### Web (User-facing)

| Controller | Methods | PHP Source |
|------------|---------|------------|
| `App\Http\Controllers\Web\EventController` | `home`, `search`, `eventType`, `payment` | `index.php`, `pages/home.php`, `pages/search.php`, `pages/event_type.php`, `pages/payment.php` |
| `App\Http\Controllers\Web\SeatController` | `selectSeats`, `getSeats` | `pages/select_seats.php`, `assets/ajax/get_seats.php` |
| `App\Http\Controllers\Web\BookingController` | `confirm` | `process/confirm_booking.php` |
| `App\Http\Controllers\Web\PaymentController` | `vnpayRedirect`, `vnpayReturn` | `pages/vn_pay_redirect.php`, `pages/vnpay_return.php` |
| `App\Http\Controllers\Web\TicketController` | `index` | `pages/my_tickets.php` |
| `App\Http\Controllers\Web\ProfileController` | `show`, `updateInfo`, `changePassword` | [NEW] Người dùng quản lý thông tin cá nhân |
| `App\Http\Controllers\ChatbotController` | `sendMessage` | [NEW] AI Chatbot Support |
| `App\Http\Controllers\SubscribeController` | `store` | `subscribe.php` |

### Auth (User)

| Controller | Methods | PHP Source |
|------------|---------|------------|
| `App\Http\Controllers\Auth\LoginController` | `store`, `destroy` | `auth/login.php`, `auth/logout.php` |
| `App\Http\Controllers\Auth\RegisterController` | `store` | `auth/register.php` |
| `App\Http\Controllers\Auth\PasswordResetController` | `showResetForm`, `reset`, `sendResetLink` | `auth/reset_password.php`, `auth/send_reset_link.php` |
| `App\Http\Controllers\Auth\FirebaseAuthController` | `verify` | `auth/firebase_verify.php` |

### Admin

| Controller | Methods | PHP Source |
|------------|---------|------------|
| `App\Http\Controllers\Admin\AuthController` | `showLoginForm`, `login`, `logout` | `admin/login.php`, `admin/logout.php` |
| `App\Http\Controllers\Admin\DashboardController` | `index` | `admin/dashboard.php` |
| `App\Http\Controllers\Admin\EventController` | `index`, `store`, `update`, `destroy`, `generateSeats` | `admin/events.php`, `admin/update_event.php`, `admin/delete_event.php`, `process/generate_seats.php` |
| `App\Http\Controllers\Admin\UserController` | `index`, `update`, `destroy` | `admin/users.php`, `admin/update_user.php`, `admin/delete_user.php` |
| `App\Http\Controllers\Admin\OrderController` | `index`, `updateTicket`, `history` | `admin/orders.php`, `admin/update_ticket.php`, `admin/history.php` |

## 2. Services

| Service | Purpose | PHP Source |
|---------|---------|------------|
| `App\Services\Payment\VNPayService` | Create payment URL, verify checksum | `includes/vnpay_config.php`, logic in `vn_pay_redirect.php`, `vnpay_return.php` |
| `App\Services\Auth\FirebaseService` | Verify idToken, find/create user | `auth/firebase_verify.php` |
| `App\Services\Booking\BookingService` | Session booking, pending payment, complete/cancel | `process/confirm_booking.php`, `vn_pay_redirect.php`, `vnpay_return.php` |
| `App\Services\Event\EventStatusService` | Update event status based on time | [NEW] Logic đồng bộ trạng thái sự kiện |

## 3. Routes Mapping

### Public

| Method | Route | Controller@Action | PHP Source |
|--------|-------|-------------------|------------|
| GET | `/` | EventController@home | index.php, pages/home.php |
| GET | `/home` | EventController@home | - |
| GET | `/search` | EventController@search | pages/search.php |
| GET | `/api/events/search-suggestions` | EventController@searchSuggestions | [NEW] AJAX gợi ý tìm kiếm |
| GET | `/events/type/{type}` | EventController@eventType | pages/event_type.php |
| GET | `/events/{event}/payment` | EventController@payment | pages/payment.php |
| POST | `/chatbot/message` | ChatbotController@sendMessage | [NEW] Giao tiếp với AI Chatbot |
| POST | `/subscribe` | SubscribeController@store | subscribe.php |

### Auth (User)

| Method | Route | Controller@Action | PHP Source |
|--------|-------|-------------------|------------|
| POST | `/login` | LoginController@store | auth/login.php |
| POST | `/logout` | LoginController@destroy | auth/logout.php |
| POST | `/register` | RegisterController@store | auth/register.php |
| POST | `/auth/firebase/verify` | FirebaseAuthController@verify | auth/firebase_verify.php |
| POST | `/password/email` | PasswordResetController@sendResetLink | auth/send_reset_link.php |
| GET | `/password/reset/{token}` | PasswordResetController@showResetForm | auth/reset_password.php |
| POST | `/password/reset` | PasswordResetController@reset | auth/reset_password.php |

### Protected (auth)

| Method | Route | Controller@Action | PHP Source |
|--------|-------|-------------------|------------|
| GET | `/my-tickets` | TicketController@index | pages/my_tickets.php |
| GET | `/profile` | ProfileController@show | [NEW] Trang cá nhân |
| POST | `/profile/info` | ProfileController@updateInfo | [NEW] Cập nhật thông tin |
| POST | `/profile/password` | ProfileController@changePassword | [NEW] Đổi mật khẩu |
| GET | `/events/{event}/select-seats` | SeatController@selectSeats | pages/select_seats.php |
| GET | `/ajax/events/{event}/seats` | SeatController@getSeats | assets/ajax/get_seats.php |
| POST | `/booking/confirm` | BookingController@confirm | process/confirm_booking.php |
| POST | `/payment/vnpay/redirect` | PaymentController@vnpayRedirect | pages/vn_pay_redirect.php |

### Payment Callback

| Method | Route | Controller@Action | PHP Source |
|--------|-------|-------------------|------------|
| GET | `/payment/vnpay/return` | PaymentController@vnpayReturn | pages/vnpay_return.php |

### Admin

| Method | Route | Controller@Action | PHP Source |
|--------|-------|-------------------|------------|
| GET | `/admin/login` | Admin\AuthController@showLoginForm | admin/login.php |
| POST | `/admin/login` | Admin\AuthController@login | admin/login.php |
| POST | `/admin/logout` | Admin\AuthController@logout | admin/logout.php |
| GET | `/admin` | DashboardController@index | admin/dashboard.php |
| GET | `/admin/events` | Admin\EventController@index | admin/events.php |
| POST | `/admin/events` | Admin\EventController@store | admin/update_event.php |
| PUT | `/admin/events/{event}` | Admin\EventController@update | admin/update_event.php |
| DELETE | `/admin/events/{event}` | Admin\EventController@destroy | admin/delete_event.php |
| POST | `/admin/events/{event}/seats/generate` | Admin\EventController@generateSeats | process/generate_seats.php |
| GET | `/admin/users` | UserController@index | admin/users.php |
| PUT | `/admin/users/{user}` | UserController@update | admin/update_user.php |
| DELETE | `/admin/users/{user}` | UserController@destroy | admin/delete_user.php |
| GET | `/admin/orders` | OrderController@index | admin/orders.php |
| POST | `/admin/orders/update-ticket` | OrderController@updateTicket | admin/update_ticket.php |
| GET | `/admin/history` | OrderController@history | admin/history.php |

## 4. Configuration

- **VNPay**: `config/vnpay.php` + `.env` (`VNPAY_TMNCODE`, `VNPAY_HASH_SECRET`, `VNPAY_RETURNURL`, `VNPAY_URL`)
- **Firebase**: `config/services.php` + `.env` (`FIREBASE_CREDENTIALS` path)
- **Auth**: `config/auth.php` — guards: `web` (users), `admin` (admins)

## 5. Request Validation (To Implement)

- `LoginRequest`, `RegisterRequest` — auth forms
- `ConfirmBookingRequest` — booking/confirm
- `EventStoreRequest`, `EventUpdateRequest` — admin events
- `UpdateUserRequest` — admin users
