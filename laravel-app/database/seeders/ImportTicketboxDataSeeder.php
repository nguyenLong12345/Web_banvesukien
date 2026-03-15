<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportTicketboxDataSeeder extends Seeder
{
    /**
     * Import data from ticketbox.sql file
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data
        DB::table('tickets')->truncate();
        DB::table('orders')->truncate();
        DB::table('payments')->truncate();
        DB::table('seats')->truncate();
        DB::table('events')->truncate();
        DB::table('users')->truncate();
        DB::table('admins')->truncate();

        // Import admins
        DB::table('admins')->insert([
            ['id' => 1, 'username' => 'admin', 'password' => '$2y$10$ZCOX7lzOZRGuh8WH5l/8XOUM9Tg4YYrrFb6mY0kJBvTCoGI3rOTrG'],
        ]);

        // Import users
        DB::table('users')->insert([
            ['user_id' => 'PKA001', 'fullname' => 'Hà', 'email' => 'ha@gmail.com', 'password' => '$2y$10$dT0HQpO/852/x9pFctaJcOhNFQ3GqA95EHetHcaaYHZg8xVLDMMxe', 'reset_token' => '', 'reset_expire' => null, 'firebase_uid' => null],
            ['user_id' => 'PKA02', 'fullname' => 'Huy', 'email' => 'huy@gmail.com', 'password' => '$2y$10$g7wbZB3GrNsLylns8J7Ow.0cMZeWfAo5QPP.9B339iNiROnbkXljq', 'reset_token' => '', 'reset_expire' => null, 'firebase_uid' => null],
            ['user_id' => 'PKA03', 'fullname' => 'Như', 'email' => 'halun05062004@gmail.com', 'password' => '$2y$10$n5XbUFc.wlj5mVonR/3RTuJbOgqj/RYE9Zm1BIcth4BoQi9sERKu.', 'reset_token' => 'c9f64649b75d213fa07442d574594513', 'reset_expire' => '2025-06-23 10:31:05', 'firebase_uid' => null],
        ]);

        // Import payments
        DB::table('payments')->insert([
            ['payment_id' => 'P01', 'user_id' => 'PKA02', 'payment_at' => '2025-06-22 08:34:10', 'method' => 'vnpay', 'amount' => 400000.00, 'fullname' => 'thanhha', 'email' => 'halun05062004@gmail.com', 'phone' => '0932396059', 'pStatus' => 'cancel', 'vnp_transaction_no' => '250622_153410_9926', 'payment_time' => null],
            ['payment_id' => 'P02', 'user_id' => 'PKA001', 'payment_at' => '2025-06-23 03:20:55', 'method' => 'vnpay', 'amount' => 1750000.00, 'fullname' => 'Trần Thanh Hà', 'email' => 'hacute35762k4@gmail.com', 'phone' => '0932386058', 'pStatus' => 'paid', 'vnp_transaction_no' => '15032239', 'payment_time' => '2025-06-23 10:21:21'],
            ['payment_id' => 'P03', 'user_id' => 'PKA001', 'payment_at' => '2025-06-23 06:14:59', 'method' => 'vnpay', 'amount' => 1747500.00, 'fullname' => 'Trần Thanh Hà', 'email' => 'hacute35762k4@gmail.com', 'phone' => '0932386058', 'pStatus' => 'paid', 'vnp_transaction_no' => '15032599', 'payment_time' => '2025-06-23 13:15:28'],
            ['payment_id' => 'P04', 'user_id' => 'PKA03', 'payment_at' => '2025-06-23 07:51:19', 'method' => 'vnpay', 'amount' => 500000.00, 'fullname' => 'Như', 'email' => 'nhu@gmail.com', 'phone' => '1234567899', 'pStatus' => 'paid', 'vnp_transaction_no' => '15032808', 'payment_time' => '2025-06-23 14:51:39'],
            ['payment_id' => 'P05', 'user_id' => 'PKA03', 'payment_at' => '2025-06-23 07:55:13', 'method' => 'vnpay', 'amount' => 1050000.00, 'fullname' => 'Như', 'email' => 'nhu@gmail.com', 'phone' => '1234567899', 'pStatus' => 'paid', 'vnp_transaction_no' => '15032820', 'payment_time' => '2025-06-23 14:55:59'],
            ['payment_id' => 'P06', 'user_id' => 'PKA001', 'payment_at' => '2025-06-25 19:14:28', 'method' => 'vnpay', 'amount' => 100000.00, 'fullname' => 'Hằng', 'email' => 'ha@gmail.com', 'phone' => '1111111111', 'pStatus' => 'paid', 'vnp_transaction_no' => '15038507', 'payment_time' => '2025-06-26 02:14:54'],
        ]);

        // Import orders
        DB::table('orders')->insert([
            ['order_id' => 'O01', 'payment_id' => 'P02', 'event_id' => 'E016', 'created_at' => '2025-06-23 10:21:21', 'quantity' => 2],
            ['order_id' => 'O02', 'payment_id' => 'P03', 'event_id' => 'E038', 'created_at' => '2025-06-23 13:15:28', 'quantity' => 2],
            ['order_id' => 'O03', 'payment_id' => 'P04', 'event_id' => 'E037', 'created_at' => '2025-06-23 14:51:39', 'quantity' => 2],
            ['order_id' => 'O04', 'payment_id' => 'P05', 'event_id' => 'E016', 'created_at' => '2025-06-23 14:55:59', 'quantity' => 1],
            ['order_id' => 'O05', 'payment_id' => 'P06', 'event_id' => 'E036', 'created_at' => '2025-06-26 02:14:54', 'quantity' => 1],
        ]);

        echo "✓ Imported users, admins, payments, orders\n";
        echo "→ Now importing events and seats from SQL file...\n";

        // Read and execute the SQL file for events and seats
        $sqlFile = base_path('../ticketbox.sql');
        
        if (!file_exists($sqlFile)) {
            $sqlFile = base_path('ticketbox.sql');
        }
        
        if (!file_exists($sqlFile)) {
            echo "✗ Error: ticketbox.sql not found\n";
            echo "  Tried: " . base_path('../ticketbox.sql') . "\n";
            echo "  Tried: " . base_path('ticketbox.sql') . "\n";
            return;
        }
        
        echo "→ Reading SQL file from: $sqlFile\n";

        $sql = file_get_contents($sqlFile);
        
        // Extract only INSERT statements for events and seats
        preg_match_all("/INSERT INTO `events`.*?;/s", $sql, $eventMatches);
        preg_match_all("/INSERT INTO `seats`.*?;/s", $sql, $seatMatches);
        
        // Execute events inserts
        if (!empty($eventMatches[0])) {
            foreach ($eventMatches[0] as $insert) {
                try {
                    DB::statement($insert);
                } catch (\Exception $e) {
                    echo "Warning: " . $e->getMessage() . "\n";
                }
            }
            echo "✓ Imported events\n";
        }
        
        // Execute seats inserts (in chunks to avoid memory issues)
        if (!empty($seatMatches[0])) {
            foreach ($seatMatches[0] as $insert) {
                try {
                    DB::statement($insert);
                } catch (\Exception $e) {
                    echo "Warning: " . $e->getMessage() . "\n";
                }
            }
            echo "✓ Imported seats\n";
        }

        // Import tickets
        DB::table('tickets')->insert([
            ['ticket_id' => 'T01', 'order_id' => 'O01', 'seat_id' => 'S1316', 'tStatus' => 'Đã hủy'],
            ['ticket_id' => 'T02', 'order_id' => 'O01', 'seat_id' => 'S1334', 'tStatus' => 'Thành công'],
            ['ticket_id' => 'T03', 'order_id' => 'O02', 'seat_id' => 'S3364', 'tStatus' => 'Thành công'],
            ['ticket_id' => 'T04', 'order_id' => 'O02', 'seat_id' => 'S3318', 'tStatus' => 'Thành công'],
            ['ticket_id' => 'T05', 'order_id' => 'O03', 'seat_id' => 'S3284', 'tStatus' => 'Thành công'],
            ['ticket_id' => 'T06', 'order_id' => 'O03', 'seat_id' => 'S3257', 'tStatus' => 'Thành công'],
            ['ticket_id' => 'T07', 'order_id' => 'O04', 'seat_id' => 'S1320', 'tStatus' => 'Thành công'],
            ['ticket_id' => 'T08', 'order_id' => 'O05', 'seat_id' => 'S3184', 'tStatus' => 'Thành công'],
        ]);

        echo "✓ Imported tickets\n";

        // Update seat status for booked seats
        DB::statement("UPDATE seats SET sStatus = 'Đã đặt' WHERE seat_id IN ('S1316', 'S1334', 'S3364', 'S3318', 'S3284', 'S3257', 'S1320', 'S3184')");

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        echo "\n✓ Data import completed successfully!\n";
        echo "→ Total users: " . DB::table('users')->count() . "\n";
        echo "→ Total events: " . DB::table('events')->count() . "\n";
        echo "→ Total seats: " . DB::table('seats')->count() . "\n";
        echo "→ Total payments: " . DB::table('payments')->count() . "\n";
        echo "→ Total orders: " . DB::table('orders')->count() . "\n";
        echo "→ Total tickets: " . DB::table('tickets')->count() . "\n";
    }
}
