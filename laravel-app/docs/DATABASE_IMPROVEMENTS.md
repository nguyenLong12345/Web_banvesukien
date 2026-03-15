# Cải Thiện Database - TicketBox Application

## Tổng Quan
Database đã được phân tích, tối ưu hóa và tạo lại với các cải thiện về hiệu suất và tính toàn vẹn dữ liệu.

## Cấu Trúc Database

### 1. **users** - Bảng người dùng
- `user_id` (PK): Mã người dùng (PKA + 4 số)
- `fullname`: Họ tên
- `email`: Email (unique)
- `password`: Mật khẩu (hashed)
- `reset_token`: Token reset mật khẩu
- `reset_expire`: Thời gian hết hạn token
- `firebase_uid`: Firebase UID (unique, nullable)
- `remember_token`: Token duy trì đăng nhập (nullable)

### 2. **admins** - Bảng quản trị viên
- `id` (PK): ID tự tăng
- `username`: Tên đăng nhập (unique)
- `password`: Mật khẩu (hashed)

### 3. **events** - Bảng sự kiện
- `event_id` (PK): Mã sự kiện (E0 + số)
- `event_name`: Tên sự kiện
- `start_time`: Thời gian bắt đầu
- `price`: Giá vé
- `event_img`: Hình ảnh
- `location`: Địa điểm
- `total_seats`: Tổng số ghế (50 hoặc 100)
- `event_type`: Loại sự kiện (music, art, visit, tournament)
- `eStatus`: Trạng thái (Chưa diễn ra, Đang diễn ra, Đã kết thúc, Đã bị hủy)
- `duration`: Thời lượng (giờ)

**Indexes:**
- `eStatus` - Tìm kiếm theo trạng thái
- `event_type` - Lọc theo loại sự kiện
- `start_time` - Sắp xếp theo thời gian

### 4. **payments** - Bảng thanh toán
- `payment_id` (PK): Mã thanh toán (P0 + số)
- `user_id` (FK): Mã người dùng
- `payment_at`: Thời gian tạo
- `method`: Phương thức (vnpay)
- `amount`: Số tiền
- `fullname`: Họ tên người thanh toán
- `email`: Email
- `phone`: Số điện thoại
- `pStatus`: Trạng thái (paid, pending, cancel)
- `vnp_transaction_no`: Mã giao dịch VNPay
- `meta_seats`: Lưu danh sách ghế đã đặt (JSON/text)
- `meta_event_id`: Lưu ID sự kiện (backup)
- `payment_time`: Thời gian thanh toán thực tế

**Indexes:**
- `pStatus` - Lọc theo trạng thái thanh toán
- `payment_at` - Sắp xếp theo thời gian
- `payment_time` - Thống kê doanh thu
- `vnp_transaction_no` - Tra cứu giao dịch

**Foreign Keys:**
- `user_id` → `users.user_id` (CASCADE DELETE)

### 5. **orders** - Bảng đơn hàng
- `order_id` (PK): Mã đơn hàng (O0 + số)
- `payment_id` (FK): Mã thanh toán
- `event_id` (FK): Mã sự kiện
- `created_at`: Thời gian tạo
- `quantity`: Số lượng vé

**Indexes:**
- `created_at` - Sắp xếp theo thời gian
- `event_id` - Lọc theo sự kiện
- `payment_id` - Tra cứu thanh toán

**Foreign Keys:**
- `event_id` → `events.event_id` (RESTRICT DELETE, CASCADE UPDATE)
- `payment_id` → `payments.payment_id` (RESTRICT DELETE, CASCADE UPDATE)

### 6. **seats** - Bảng ghế ngồi
- `seat_id` (PK): Mã ghế (S + 8 ký tự)
- `event_id` (FK): Mã sự kiện
- `seat_type`: Loại ghế (normal, vip)
- `seat_number`: Số ghế (N1, V1, S1...)
- `sStatus`: Trạng thái (Còn trống, Đã đặt)
- `seat_price`: Giá ghế

**Indexes:**
- `seat_type` - Lọc theo loại ghế
- `sStatus` - Tìm ghế trống
- `(event_id, sStatus)` - Composite index cho query phổ biến

**Foreign Keys:**
- `event_id` → `events.event_id` (CASCADE DELETE, CASCADE UPDATE)

### 7. **tickets** - Bảng vé
- `ticket_id` (PK): Mã vé (T0 + số)
- `order_id` (FK): Mã đơn hàng
- `seat_id` (FK): Mã ghế
- `tStatus`: Trạng thái (Thành công, Đã hủy)

**Indexes:**
- `tStatus` - Lọc theo trạng thái
- `order_id` - Tra cứu theo đơn hàng
- `seat_id` - Tra cứu theo ghế

**Foreign Keys:**
- `order_id` → `orders.order_id` (RESTRICT DELETE)
- `seat_id` → `seats.seat_id` (RESTRICT DELETE)

## Các Cải Thiện Đã Thực Hiện

### 1. **Thêm Indexes**
- Thêm indexes cho các cột thường xuyên được query: `eStatus`, `event_type`, `start_time`, `pStatus`, `sStatus`
- Thêm composite index `(event_id, sStatus)` cho bảng seats để tối ưu query tìm ghế trống theo sự kiện
- Thêm indexes cho các foreign keys để tăng tốc JOIN operations

### 2. **Sửa Enum Values**
- Thêm trạng thái "Đã bị hủy" vào enum `eStatus` của bảng events (đã có trong validation nhưng thiếu trong migration)

### 3. **Cải Thiện Foreign Key Constraints**
- Đổi `CASCADE DELETE` thành `RESTRICT DELETE` cho orders và tickets để tránh mất dữ liệu quan trọng
- Giữ `CASCADE DELETE` cho seats khi xóa event (hợp lý vì ghế thuộc về sự kiện)
- Thêm `onDelete('cascade')` cho payments → users
- Thêm các trường `meta_seats`, `meta_event_id` vào bảng `payments` để dự phòng và đối soát dữ liệu ghế.
- Thêm `remember_token` vào bảng `users` để hỗ trợ tính năng "Ghi nhớ đăng nhập".
- Cập nhật enum `sStatus` trong bảng `seats` để bao gồm trạng thái "Đang giữ".

### 4. **Tối Ưu Hóa Data Integrity**
- Đảm bảo thứ tự migration đúng (payments trước orders)
- Thêm các ràng buộc foreign key đầy đủ
- Sử dụng RESTRICT để bảo vệ dữ liệu lịch sử

### 5. **Cải Thiện Seeder**
- Sửa DatabaseSeeder để tạo dữ liệu mẫu đúng cấu trúc
- Tạo user test: `test@example.com` / `password`
- Tạo admin test: `admin` / `admin123`

## Quan Hệ Giữa Các Bảng

```
users (1) ──→ (n) payments
                    │
                    │ (1)
                    ↓
events (1) ──→ (n) orders (n) ←── (1) payments
   │                 │
   │ (1)             │ (1)
   ↓                 ↓
seats (n)        tickets (n)
   │                 │
   └─────────────────┘
         (1:n)
```

## Logic Nghiệp Vụ Quan Trọng

### 1. **Booking Flow**
1. User chọn event → lưu thông tin vào session
2. User chọn seats → tạo payment (pending)
3. Redirect đến VNPay
4. Callback thành công → payment (paid), seats (Đã đặt), tạo order + tickets
5. Callback thất bại → payment (cancel)

### 2. **Event Status Auto-Update**
- Dựa vào `start_time` và `duration`
- "Chưa diễn ra": now < start_time
- "Đang diễn ra": start_time ≤ now ≤ end_time
- "Đã kết thúc": now > end_time

### 3. **ID Generation Strategy**
- Users: PKA + 4 số random
- Payments: P0 + sequential
- Orders: O0 + sequential
- Tickets: T0 + sequential
- Seats: S + 8 ký tự unique
- Events: E0 + 2 số

## Lệnh Quản Lý Database

```bash
# Tạo lại database
php artisan migrate:fresh

# Tạo lại database với dữ liệu mẫu
php artisan migrate:fresh --seed

# Rollback migration cuối
php artisan migrate:rollback

# Kiểm tra trạng thái migration
php artisan migrate:status
```

## Dữ Liệu Test

### Admin Test
- Username: `admin`
- Password: `admin123`

## Khuyến Nghị Tiếp Theo

1. **Soft Deletes**: Cân nhắc thêm soft deletes cho events, orders để giữ lịch sử
2. **Audit Trail**: Thêm bảng audit logs để theo dõi thay đổi quan trọng
3. **Caching**: Cache danh sách events, seats để giảm tải database
4. **Queue**: Sử dụng queue cho việc gửi email xác nhận, cập nhật trạng thái
5. **Backup**: Thiết lập backup tự động cho database
6. **Monitoring**: Theo dõi slow queries và tối ưu hóa

## Ghi Chú Bảo Mật

- Tất cả passwords đều được hash bằng bcrypt
- Firebase UID được lưu để tích hợp social login
- VNPay transaction sử dụng HMAC-SHA512 để xác thực
- Session-based authentication với regeneration
