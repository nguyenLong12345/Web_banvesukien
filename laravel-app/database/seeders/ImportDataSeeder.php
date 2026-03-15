<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportDataSeeder extends Seeder
{
    /**
     * Seed the application's database with events data.
     */
    public function run(): void
    {
        // Admin
        DB::table('admins')->insert([
            'id' => 1,
            'username' => 'admin',
            'password' => '$2y$10$ZCOX7lzOZRGuh8WH5l/8XOUM9Tg4YYrrFb6mY0kJBvTCoGI3rOTrG'
        ]);

        // Events
        DB::table('events')->insert([
            ['event_id' => 'E01', 'event_name' => 'Giải cờ vua Hà Nội mở rộng GO-VCHESS 2025: Vietnam Chess Championship', 'start_time' => '2025-05-29 08:00:00', 'price' => 500000.00, 'event_img' => 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-2efa9abd87129325eea9f99579fabe10.jpg', 'location' => 'Hy Maxpro Coffee, Nhà F4, Ngõ 112 Trung Kính, Yên Hoà, Cầu Giấy, Hà Nội', 'total_seats' => 100, 'event_type' => 'tournament', 'eStatus' => 'Đã kết thúc', 'duration' => 2],
            ['event_id' => 'E010', 'event_name' => 'Nhà Hát Kịch IDECAF: Cái gì Vui Vẻ thì mình Ưu Tiên', 'start_time' => '2025-08-18 18:00:00', 'price' => 270000.00, 'event_img' => 'https://salt.tkbcdn.com/ts/ds/2b/1a/a5/371b379ac0bdd94e091cfc20ae2ce99d.jpg', 'location' => 'Nhà Hát Kịch IDECAFSố 28 Lê Thánh Tôn, Phường Bến Nghé, Quận 1, Thành Phố Hồ Chí Minh', 'total_seats' => 100, 'event_type' => 'music', 'eStatus' => 'Chưa diễn ra', 'duration' => 3],
            ['event_id' => 'E011', 'event_name' => 'MY MUSES - NAMTANFILM 1ST FANMEETING IN VIETNAM', 'start_time' => '2025-07-01 14:00:00', 'price' => 2000000.00, 'event_img' => 'https://salt.tkbcdn.com/ts/ds/21/5f/d7/92e9981cc46850451627316bfea4abd5.jpg', 'location' => 'Nhà Hát Bến Thành, Số 6 Mạc Đĩnh Chi, Phường Bến Nghé, Quận 1, Thành Phố Hồ Chí Minh', 'total_seats' => 100, 'event_type' => 'music', 'eStatus' => 'Chưa diễn ra', 'duration' => 3],
            ['event_id' => 'E012', 'event_name' => 'NOOS CHILL NIGHT THE CONCERT', 'start_time' => '2025-06-30 16:30:00', 'price' => 850000.00, 'event_img' => 'https://salt.tkbcdn.com/ts/ds/76/9b/d7/a8dff545a691b99731712b43da67556a.jpg', 'location' => 'Sân khấu Quảng trường Lavender Đà Lạt, Tiểu khu 157 Khu du lịch Tuyền Lâm, Phường 4, Thành Phố Đà Lạt, Tỉnh Lâm Đồng', 'total_seats' => 100, 'event_type' => 'music', 'eStatus' => 'Chưa diễn ra', 'duration' => 2],
            ['event_id' => 'E013', 'event_name' => '[Nhà Hát THANH NIÊN] Hài kịch: Thanh Xà Bạch Xà ngoại truyện', 'start_time' => '2025-08-07 19:00:00', 'price' => 250000.00, 'event_img' => 'https://salt.tkbcdn.com/ts/ds/72/00/b4/c3ee374b63d5baf3d0a27b18d13e99ce.jpg', 'location' => 'Nhà Văn hoá Thanh niên Thành phố Hồ Chí Minh, 4 Phạm Ngọc Thạch, Bến Nghé, Quận 1, Thành Phố Hồ Chí Minh', 'total_seats' => 50, 'event_type' => 'art', 'eStatus' => 'Chưa diễn ra', 'duration' => 4],
            ['event_id' => 'E014', 'event_name' => 'Swan Lake', 'start_time' => '2025-07-25 20:00:00', 'price' => 1000000.00, 'event_img' => 'https://salt.tkbcdn.com/ts/ds/64/84/6a/e9adbb6f7b0826db84c8821538ceaf9a.png', 'location' => 'Nhà hát Hồ Gươm, 40 P. Hàng Bài, Phường Hàng Bài, Quận Hoàn Kiếm, Thành Phố Hà Nội', 'total_seats' => 100, 'event_type' => 'art', 'eStatus' => 'Chưa diễn ra', 'duration' => 6],
            ['event_id' => 'E015', 'event_name' => 'GAI CONCERT IN HANOI', 'start_time' => '2025-06-25 19:30:00', 'price' => 850000.00, 'event_img' => 'https://salt.tkbcdn.com/ts/ds/d5/91/b9/d6d51e853d48514ec2a263cf50925d23.jpg', 'location' => 'Cung Điền Kinh Mỹ Đình, KĐT Mỹ Đình, Trần Hữu Dực, Quận Nam Từ Liêm, Thành Phố Hà Nội', 'total_seats' => 100, 'event_type' => 'music', 'eStatus' => 'Đã kết thúc', 'duration' => 3],
            ['event_id' => 'E016', 'event_name' => '[MINISHOW] B.U.I STORIES - Trung Quân & Bùi Anh Tuấn', 'start_time' => '2025-08-02 20:00:00', 'price' => 700000.00, 'event_img' => 'https://salt.tkbcdn.com/ts/ds/dc/a9/b1/bc86663d9ef3140e9d2393857b05a75c.jpg', 'location' => 'Nhà hát Bến Thành, Lầu 1, Số 6 Mạc Đĩnh Chi, Phường Bến Nghé, Quận 1, Thành Phố Hồ Chí Minh', 'total_seats' => 100, 'event_type' => 'music', 'eStatus' => 'Chưa diễn ra', 'duration' => 3],
            ['event_id' => 'E017', 'event_name' => 'Live-concert GIAI ĐIỆU HOÀNG HÔN 2025: Hà Nhi - Lân Nhã - Tăng Phúc', 'start_time' => '2025-08-06 19:30:00', 'price' => 4800000.00, 'event_img' => 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-b3234c764be98c2b243747a4d9d51db9.jpg', 'location' => 'Bãi Biển Phú Quốc - Quảng trường biển Grand World, Gành Dầu, đảo Phú Quốc, TP. Phú Quốc, Kiên Giang', 'total_seats' => 100, 'event_type' => 'music', 'eStatus' => 'Chưa diễn ra', 'duration' => 3],
            ['event_id' => 'E018', 'event_name' => 'Chamber Music Concert "Mon Amour" - Đêm nhạc thính phòng tại Sài Gòn', 'start_time' => '2025-06-29 19:00:00', 'price' => 350000.00, 'event_img' => 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-0334ff8fd9b5b9f34d18db242fb2cae4.jpg', 'location' => 'STEINGRAEBER Hall, 766/1 Sư Vạn Hạnh, Phường 12, Quận 10, Hồ Chí Minh', 'total_seats' => 100, 'event_type' => 'music', 'eStatus' => 'Chưa diễn ra', 'duration' => 4],
            ['event_id' => 'E019', 'event_name' => 'Vé vào cửa triển lãm Nghệ thuật Ánh sáng Metashow', 'start_time' => '2025-07-01 00:00:00', 'price' => 490000.00, 'event_img' => 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-d7396e37eb825935099081865c19af14.jpg', 'location' => 'L4-L10 Tầng 4, Thiso Mall Sala, 10 Mai Chí Thọ, P. Thủ Thiêm, Quận 2, Hồ Chí Minh', 'total_seats' => 50, 'event_type' => 'art', 'eStatus' => 'Chưa diễn ra', 'duration' => 6],
            ['event_id' => 'E02', 'event_name' => 'Madame de Đà Lạt', 'start_time' => '2025-10-16 07:30:00', 'price' => 700000.00, 'event_img' => 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-0d6a6fe2f50f4b6863b62db4d3046fe8.jpg', 'location' => 'Madame De Dalat (Biệt điện Trần Lệ Xuân), Số 2 Đường Yết Kiêu, phường 5, Lâm Đồng', 'total_seats' => 100, 'event_type' => 'visit', 'eStatus' => 'Chưa diễn ra', 'duration' => 4],
        ]);
    }
}
