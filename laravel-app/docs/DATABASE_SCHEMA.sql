CREATE TABLE `users` (
  `user_id` VARCHAR(20) PRIMARY KEY,
  `fullname` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `firebase_uid` VARCHAR(128) UNIQUE NULL,
  `reset_token` VARCHAR(255) NOT NULL DEFAULT '',
  `reset_expire` DATETIME NULL,
  `remember_token` VARCHAR(100) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `admins` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) UNIQUE NOT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `events` (
  `event_id` VARCHAR(10) PRIMARY KEY,
  `event_name` VARCHAR(255) NOT NULL,
  `start_time` DATETIME NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `event_img` VARCHAR(255) NOT NULL,
  `location` VARCHAR(255) NOT NULL,
  `total_seats` INT NOT NULL,
  `event_type` ENUM('music', 'art', 'visit', 'tournament') NOT NULL,
  `eStatus` ENUM('Chưa diễn ra', 'Đã kết thúc', 'Đang diễn ra', 'Đã bị hủy') DEFAULT 'Chưa diễn ra',
  `duration` INT NOT NULL,
  INDEX `idx_eStatus` (`eStatus`),
  INDEX `idx_event_type` (`event_type`),
  INDEX `idx_start_time` (`start_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payments` (
  `payment_id` VARCHAR(10) PRIMARY KEY,
  `user_id` VARCHAR(20) NOT NULL,
  `payment_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `method` ENUM('vnpay') DEFAULT 'vnpay',
  `amount` DECIMAL(10,2) NOT NULL,
  `fullname` VARCHAR(50) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `pStatus` ENUM('paid', 'pending', 'cancel') DEFAULT 'pending',
  `vnp_transaction_no` VARCHAR(50) NOT NULL,
  `meta_seats` TEXT NULL,
  `meta_event_id` VARCHAR(255) NULL,
  `payment_time` DATETIME NULL,
  INDEX `idx_pStatus` (`pStatus`),
  INDEX `idx_payment_at` (`payment_at`),
  INDEX `idx_payment_time` (`payment_time`),
  INDEX `idx_vnp_transaction_no` (`vnp_transaction_no`),
  CONSTRAINT `payments_user_id_foreign` 
    FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  `order_id` VARCHAR(10) PRIMARY KEY,
  `payment_id` VARCHAR(10) NOT NULL,
  `event_id` VARCHAR(10) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `quantity` INT NOT NULL,
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_event_id` (`event_id`),
  INDEX `idx_payment_id` (`payment_id`),
  CONSTRAINT `fk_event_id` 
    FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) 
    ON UPDATE CASCADE,
  CONSTRAINT `fk_payment_id` 
    FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`) 
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `seats` (
  `seat_id` VARCHAR(10) PRIMARY KEY,
  `event_id` VARCHAR(10) NOT NULL,
  `seat_type` VARCHAR(10) NOT NULL,
  `seat_number` VARCHAR(10) NOT NULL,
  `sStatus` ENUM('Đã đặt', 'Còn trống', 'Đang giữ') DEFAULT 'Còn trống',
  `seat_price` DOUBLE NOT NULL,
  INDEX `idx_seat_type` (`seat_type`),
  INDEX `idx_sStatus` (`sStatus`),
  INDEX `idx_event_id_sStatus` (`event_id`, `sStatus`),
  CONSTRAINT `seats_event_id_foreign` 
    FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tickets` (
  `ticket_id` VARCHAR(10) PRIMARY KEY,
  `order_id` VARCHAR(10) NOT NULL,
  `seat_id` VARCHAR(10) NOT NULL,
  `tStatus` ENUM('Thành công', 'Đã hủy') DEFAULT 'Thành công',
  INDEX `idx_tStatus` (`tStatus`),
  INDEX `idx_order_id` (`order_id`),
  INDEX `idx_seat_id` (`seat_id`),
  CONSTRAINT `fk_order` 
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  CONSTRAINT `fk_seat` 
    FOREIGN KEY (`seat_id`) REFERENCES `seats` (`seat_id`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  `key` VARCHAR(255) PRIMARY KEY,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` VARCHAR(255) PRIMARY KEY,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `uuid` VARCHAR(255) UNIQUE NOT NULL,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `queue` VARCHAR(255) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL,
  `reserved_at` INT UNSIGNED DEFAULT NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at` INT UNSIGNED NOT NULL,
  INDEX `idx_jobs_queue` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  `id` VARCHAR(255) PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `total_jobs` INT NOT NULL,
  `pending_jobs` INT NOT NULL,
  `failed_jobs` INT NOT NULL,
  `failed_job_ids` LONGTEXT NOT NULL,
  `options` MEDIUMTEXT NULL,
  `cancelled_at` INT NULL,
  `created_at` INT NOT NULL,
  `finished_at` INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
