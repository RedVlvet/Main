
CREATE DATABASE IF NOT EXISTS kmk_reservations
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_general_ci;

USE kmk_reservations;

-- stores user accounts
CREATE TABLE IF NOT EXISTS users (
    user_id     INT AUTO_INCREMENT PRIMARY KEY,
    full_name   VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    phone       VARCHAR(20)  NOT NULL,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Store driver bookings
CREATE TABLE IF NOT EXISTS drivers (
    driver_id    INT AUTO_INCREMENT PRIMARY KEY,
    reference    VARCHAR(12) NOT NULL UNIQUE,
    user_id      INT NULL,
    driver_name  VARCHAR(100) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NULL,
    return_date  DATE NULL,
    return_time  TIME NULL,
    adults       INT NOT NULL DEFAULT 1,
    children     INT NOT NULL DEFAULT 0,
    from_place   VARCHAR(100) NULL,
    to_place     VARCHAR(100) NULL,
    trip_type    ENUM('single', 'return') NULL DEFAULT 'single',
    status       ENUM('Pending', 'Confirmed', 'Cancelled') NOT NULL DEFAULT 'Pending',
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX driver_schedule (driver_name, booking_date, booking_time, status),
    INDEX driver_user (user_id, booking_date),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB;

-- Stores restaurant bookings
CREATE TABLE IF NOT EXISTS restaurants (
    restaurant_id   INT AUTO_INCREMENT PRIMARY KEY,
    reference       VARCHAR(12) NOT NULL UNIQUE,
    user_id         INT NULL,
    restaurant_name VARCHAR(150) NOT NULL,
    booking_date    DATE NOT NULL,
    booking_time    TIME NULL,
    adults          INT NOT NULL DEFAULT 1,
    children        INT NOT NULL DEFAULT 0,
    status          ENUM('Pending', 'Confirmed', 'Cancelled') NOT NULL DEFAULT 'Pending',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX restaurant_user (user_id, booking_date),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB;

-- Stores room bookings
CREATE TABLE IF NOT EXISTS rooms (
    room_id            INT AUTO_INCREMENT PRIMARY KEY,
    reference          VARCHAR(12) NOT NULL UNIQUE,
    user_id            INT NULL,
    accommodation_name VARCHAR(150) NOT NULL,
    checkin_date       DATE NOT NULL,
    checkout_date      DATE NULL,
    adults             INT NOT NULL DEFAULT 1,
    children           INT NOT NULL DEFAULT 0,
    room_count          INT NOT NULL DEFAULT 1,
    status             ENUM('Pending', 'Confirmed', 'Cancelled') NOT NULL DEFAULT 'Pending',
    created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX room_user (user_id, checkin_date),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB;

-- Copy bookings from the previous schema so the data is not deleted when schema is imported (AI assisted)
SET @legacy_service_tables = (
    SELECT COUNT(*) = 2
    FROM information_schema.tables
    WHERE table_schema = DATABASE()
      AND table_name IN ('bookings', 'services')
);

SET @copy_drivers = IF(@legacy_service_tables,
    'INSERT IGNORE INTO drivers
        (reference, user_id, driver_name, booking_date, booking_time, return_date, return_time,
         adults, children, from_place, to_place, trip_type, status, created_at)
     SELECT b.reference, b.user_id, s.name, b.booking_date, b.booking_time, b.end_date, b.end_time,
            b.adults, b.children, b.from_place, b.to_place, b.trip_type, b.status, b.created_at
     FROM bookings b JOIN services s ON b.service_id = s.service_id
     WHERE s.service_type = ''driver''',
    'SELECT 1');
PREPARE copy_statement FROM @copy_drivers;
EXECUTE copy_statement;
DEALLOCATE PREPARE copy_statement;

SET @copy_restaurants = IF(@legacy_service_tables,
    'INSERT IGNORE INTO restaurants
        (reference, user_id, restaurant_name, booking_date, booking_time, adults, children, status, created_at)
     SELECT b.reference, b.user_id, s.name, b.booking_date, b.booking_time, b.adults, b.children, b.status, b.created_at
     FROM bookings b JOIN services s ON b.service_id = s.service_id
     WHERE s.service_type = ''restaurant''',
    'SELECT 1');
PREPARE copy_statement FROM @copy_restaurants;
EXECUTE copy_statement;
DEALLOCATE PREPARE copy_statement;

SET @copy_rooms = IF(@legacy_service_tables,
    'INSERT IGNORE INTO rooms
        (reference, user_id, accommodation_name, checkin_date, checkout_date, adults, children, room_count, status, created_at)
     SELECT b.reference, b.user_id, s.name, b.booking_date, b.end_date, b.adults, b.children, COALESCE(b.rooms, 1), b.status, b.created_at
     FROM bookings b JOIN services s ON b.service_id = s.service_id
     WHERE s.service_type = ''room''',
    'SELECT 1');
PREPARE copy_statement FROM @copy_rooms;
EXECUTE copy_statement;
DEALLOCATE PREPARE copy_statement;

-- Copy older session reservations
SET @has_old_reservations = (
    SELECT COUNT(*) > 0
    FROM information_schema.tables
    WHERE table_schema = DATABASE()
      AND table_name = 'reservations'
);
SET @copy_old_drivers = IF(@has_old_reservations,
    'INSERT IGNORE INTO drivers
        (reference, user_id, driver_name, booking_date, booking_time, return_date, adults, children, created_at)
     SELECT CONCAT(''OLD-'', LPAD(id, 8, ''0'')), NULL, item_name, reservation_date, reservation_time,
            checkout_date, adults, children, created_at
     FROM reservations WHERE service = ''drivers''',
    'SELECT 1');
PREPARE copy_statement FROM @copy_old_drivers;
EXECUTE copy_statement;
DEALLOCATE PREPARE copy_statement;

SET @copy_old_restaurants = IF(@has_old_reservations,
    'INSERT IGNORE INTO restaurants
        (reference, user_id, restaurant_name, booking_date, booking_time, adults, children, created_at)
     SELECT CONCAT(''OLD-'', LPAD(id, 8, ''0'')), NULL, item_name, reservation_date, reservation_time,
            adults, children, created_at
     FROM reservations WHERE service = ''restaurants''',
    'SELECT 1');
PREPARE copy_statement FROM @copy_old_restaurants;
EXECUTE copy_statement;
DEALLOCATE PREPARE copy_statement;

SET @copy_old_rooms = IF(@has_old_reservations,
    'INSERT IGNORE INTO rooms
        (reference, user_id, accommodation_name, checkin_date, checkout_date, adults, children, room_count, created_at)
     SELECT CONCAT(''OLD-'', LPAD(id, 8, ''0'')), NULL, item_name, reservation_date, checkout_date,
            adults, children, COALESCE(rooms, 1), created_at
     FROM reservations WHERE service = ''rooms''',
    'SELECT 1');
PREPARE copy_statement FROM @copy_old_rooms;
EXECUTE copy_statement;
DEALLOCATE PREPARE copy_statement;

-- Remove legacy tables after copying
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS reservations;
SET FOREIGN_KEY_CHECKS = 1;

-- Ensure the admin account exists
INSERT IGNORE INTO users (full_name, email, phone, password, role, created_at) VALUES
('KMK Admin', 'admin@kmk.test', '876-555-0100', '$2y$10$bloXXGu04oMV5isQCdN8xOHOYjM9oX0WRqkaDdNXfa0K2bp2m.P5q', 'admin', '2026-10-01 09:00:00');
