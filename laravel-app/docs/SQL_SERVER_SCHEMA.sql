-- SQL Server Schema for Ticket Events
-- Converted from MySQL schema

-- 1. Create table users
CREATE TABLE [users] (
  [user_id] NVARCHAR(20) PRIMARY KEY,
  [fullname] NVARCHAR(255) NOT NULL,
  [email] NVARCHAR(255) UNIQUE NOT NULL,
  [password] NVARCHAR(255) NOT NULL,
  [firebase_uid] NVARCHAR(128) UNIQUE NULL,
  [reset_token] NVARCHAR(255) NOT NULL DEFAULT '',
  [reset_expire] DATETIME NULL,
  [remember_token] NVARCHAR(100) NULL
);

-- 2. Create table admins
CREATE TABLE [admins] (
  [id] INT IDENTITY(1,1) PRIMARY KEY,
  [username] NVARCHAR(100) UNIQUE NOT NULL,
  [email] NVARCHAR(255) DEFAULT NULL,
  [password] NVARCHAR(255) NOT NULL,
  [created_at] DATETIME NULL DEFAULT NULL,
  [updated_at] DATETIME NULL DEFAULT NULL
);

-- 3. Create table events
CREATE TABLE [events] (
  [event_id] NVARCHAR(10) PRIMARY KEY,
  [event_name] NVARCHAR(255) NOT NULL,
  [start_time] DATETIME NOT NULL,
  [price] DECIMAL(10,2) NOT NULL,
  [event_img] NVARCHAR(255) NOT NULL,
  [location] NVARCHAR(255) NOT NULL,
  [total_seats] INT NOT NULL,
  [event_type] NVARCHAR(20) NOT NULL,
  [eStatus] NVARCHAR(50) DEFAULT 'Chưa diễn ra',
  [duration] INT NOT NULL,
  CONSTRAINT [CK_events_type] CHECK ([event_type] IN ('music', 'art', 'visit', 'tournament')),
  CONSTRAINT [CK_events_status] CHECK ([eStatus] IN ('Chưa diễn ra', 'Đã kết thúc', 'Đang diễn ra', 'Đã bị hủy'))
);

CREATE INDEX [idx_eStatus] ON [events] ([eStatus]);
CREATE INDEX [idx_event_type] ON [events] ([event_type]);
CREATE INDEX [idx_start_time] ON [events] ([start_time]);

-- 4. Create table payments
CREATE TABLE [payments] (
  [payment_id] NVARCHAR(10) PRIMARY KEY,
  [user_id] NVARCHAR(20) NOT NULL,
  [payment_at] DATETIME DEFAULT GETDATE(),
  [method] NVARCHAR(20) DEFAULT 'vnpay',
  [amount] DECIMAL(10,2) NOT NULL,
  [fullname] NVARCHAR(50) NOT NULL,
  [email] NVARCHAR(50) NOT NULL,
  [phone] NVARCHAR(20) NOT NULL,
  [pStatus] NVARCHAR(20) DEFAULT 'pending',
  [vnp_transaction_no] NVARCHAR(50) NOT NULL,
  [meta_seats] NVARCHAR(MAX) NULL,
  [meta_event_id] NVARCHAR(255) NULL,
  [payment_time] DATETIME NULL,
  CONSTRAINT [CK_payments_method] CHECK ([method] IN ('vnpay')),
  CONSTRAINT [CK_payments_status] CHECK ([pStatus] IN ('paid', 'pending', 'cancel')),
  CONSTRAINT [payments_user_id_foreign] FOREIGN KEY ([user_id]) REFERENCES [users] ([user_id]) ON DELETE CASCADE
);

CREATE INDEX [idx_pStatus] ON [payments] ([pStatus]);
CREATE INDEX [idx_payment_at] ON [payments] ([payment_at]);
CREATE INDEX [idx_payment_time] ON [payments] ([payment_time]);
CREATE INDEX [idx_vnp_transaction_no] ON [payments] ([vnp_transaction_no]);

-- 5. Create table orders
CREATE TABLE [orders] (
  [order_id] NVARCHAR(10) PRIMARY KEY,
  [payment_id] NVARCHAR(10) NOT NULL,
  [event_id] NVARCHAR(10) NOT NULL,
  [created_at] DATETIME NOT NULL,
  [quantity] INT NOT NULL,
  CONSTRAINT [fk_event_id] FOREIGN KEY ([event_id]) REFERENCES [events] ([event_id]),
  CONSTRAINT [fk_payment_id] FOREIGN KEY ([payment_id]) REFERENCES [payments] ([payment_id])
);

CREATE INDEX [idx_created_at_orders] ON [orders] ([created_at]);
CREATE INDEX [idx_event_id_orders] ON [orders] ([event_id]);
CREATE INDEX [idx_payment_id_orders] ON [orders] ([payment_id]);

-- 6. Create table seats
CREATE TABLE [seats] (
  [seat_id] NVARCHAR(10) PRIMARY KEY,
  [event_id] NVARCHAR(10) NOT NULL,
  [seat_type] NVARCHAR(10) NOT NULL,
  [seat_number] NVARCHAR(10) NOT NULL,
  [sStatus] NVARCHAR(20) DEFAULT 'Còn trống',
  [seat_price] FLOAT NOT NULL,
  CONSTRAINT [CK_seats_status] CHECK ([sStatus] IN ('Đã đặt', 'Còn trống', 'Đang giữ')),
  CONSTRAINT [seats_event_id_foreign] FOREIGN KEY ([event_id]) REFERENCES [events] ([event_id]) ON DELETE CASCADE
);

CREATE INDEX [idx_seat_type] ON [seats] ([seat_type]);
CREATE INDEX [idx_sStatus] ON [seats] ([sStatus]);
CREATE INDEX [idx_event_id_sStatus] ON [seats] ([event_id], [sStatus]);

-- 7. Create table tickets
CREATE TABLE [tickets] (
  [ticket_id] NVARCHAR(10) PRIMARY KEY,
  [order_id] NVARCHAR(10) NOT NULL,
  [seat_id] NVARCHAR(10) NOT NULL,
  [tStatus] NVARCHAR(20) DEFAULT 'Thành công',
  CONSTRAINT [CK_tickets_status] CHECK ([tStatus] IN ('Thành công', 'Đã hủy')),
  CONSTRAINT [fk_order] FOREIGN KEY ([order_id]) REFERENCES [orders] ([order_id]),
  CONSTRAINT [fk_seat] FOREIGN KEY ([seat_id]) REFERENCES [seats] ([seat_id])
);

CREATE INDEX [idx_tStatus] ON [tickets] ([tStatus]);
CREATE INDEX [idx_order_id_tickets] ON [tickets] ([order_id]);
CREATE INDEX [idx_seat_id_tickets] ON [tickets] ([seat_id]);

-- 8. Laravel internal tables: cache
CREATE TABLE [cache] (
  [key] NVARCHAR(255) PRIMARY KEY,
  [value] NVARCHAR(MAX) NOT NULL,
  [expiration] INT NOT NULL
);

-- 9. Laravel internal tables: cache_locks
CREATE TABLE [cache_locks] (
  [key] NVARCHAR(255) PRIMARY KEY,
  [owner] NVARCHAR(255) NOT NULL,
  [expiration] INT NOT NULL
);

-- 10. Laravel internal tables: failed_jobs
CREATE TABLE [failed_jobs] (
  [id] BIGINT IDENTITY(1,1) PRIMARY KEY,
  [uuid] NVARCHAR(255) UNIQUE NOT NULL,
  [connection] NVARCHAR(MAX) NOT NULL,
  [queue] NVARCHAR(MAX) NOT NULL,
  [payload] NVARCHAR(MAX) NOT NULL,
  [exception] NVARCHAR(MAX) NOT NULL,
  [failed_at] DATETIME NOT NULL DEFAULT GETDATE()
);

-- 11. Laravel internal tables: jobs
CREATE TABLE [jobs] (
  [id] BIGINT IDENTITY(1,1) PRIMARY KEY,
  [queue] NVARCHAR(255) NOT NULL,
  [payload] NVARCHAR(MAX) NOT NULL,
  [attempts] TINYINT NOT NULL,
  [reserved_at] INT NULL,
  [available_at] INT NOT NULL,
  [created_at] INT NOT NULL
);

CREATE INDEX [idx_jobs_queue] ON [jobs] ([queue]);

-- 12. Laravel internal tables: job_batches
CREATE TABLE [job_batches] (
  [id] NVARCHAR(255) PRIMARY KEY,
  [name] NVARCHAR(255) NOT NULL,
  [total_jobs] INT NOT NULL,
  [pending_jobs] INT NOT NULL,
  [failed_jobs] INT NOT NULL,
  [failed_job_ids] NVARCHAR(MAX) NOT NULL,
  [options] NVARCHAR(MAX) NULL,
  [cancelled_at] INT NULL,
  [created_at] INT NOT NULL,
  [finished_at] INT NULL
);
