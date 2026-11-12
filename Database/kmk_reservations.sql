-- =========================================================
-- KMK Reservations database
-- Import this file in phpMyAdmin (Import tab) or run it in the SQL tab.
-- It creates the database, the 3 tables and the sample data.
-- =========================================================

CREATE DATABASE IF NOT EXISTS kmk_reservations
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_general_ci;

USE kmk_reservations;

-- Drop old tables first so the file can be imported again
-- (bookings first, because it links to the other two)
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS users;


-- ---------------------------------------------------------
-- 1. USERS - everyone who has an account
--    role is 'user' (customer) or 'admin' (KMK staff)
-- ---------------------------------------------------------
CREATE TABLE users (
    user_id     INT AUTO_INCREMENT PRIMARY KEY,
    full_name   VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    phone       VARCHAR(20)  NOT NULL,
    password    VARCHAR(255) NOT NULL,          -- saved as a password_hash(), never plain text
    role        ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- ---------------------------------------------------------
-- 2. SERVICES - the restaurants, accommodations and drivers
--    a user can book
-- ---------------------------------------------------------
CREATE TABLE services (
    service_id    INT AUTO_INCREMENT PRIMARY KEY,
    service_type  ENUM('restaurant', 'room', 'driver') NOT NULL,
    name          VARCHAR(150) NOT NULL,
    location      VARCHAR(150) NOT NULL,
    UNIQUE (service_type, name)
) ENGINE=InnoDB;


-- ---------------------------------------------------------
-- 3. BOOKINGS - every reservation made
--    links to users (who booked) and services (what was booked)
--    A booking is never deleted, it is set to 'Cancelled'
-- ---------------------------------------------------------
CREATE TABLE bookings (
    booking_id    INT AUTO_INCREMENT PRIMARY KEY,
    reference     VARCHAR(12) NOT NULL UNIQUE,
    user_id       INT NOT NULL,
    service_id    INT NOT NULL,
    booking_date  DATE NOT NULL,                -- reservation date / check-in / journey date
    booking_time  TIME NULL,                    -- restaurant or journey time (rooms have none)
    end_date      DATE NULL,                    -- check-out date or return date
    end_time      TIME NULL,                    -- return time (drivers only)
    adults        INT NOT NULL DEFAULT 1,
    children      INT NOT NULL DEFAULT 0,
    rooms         INT NULL,                     -- accommodations only
    from_place    VARCHAR(100) NULL,            -- drivers only
    to_place      VARCHAR(100) NULL,            -- drivers only
    trip_type     ENUM('single', 'return') NULL,-- drivers only
    status        ENUM('Pending', 'Confirmed', 'Cancelled') NOT NULL DEFAULT 'Pending',
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,   -- the date the booking was made

    FOREIGN KEY (user_id)    REFERENCES users(user_id),
    FOREIGN KEY (service_id) REFERENCES services(service_id)
) ENGINE=InnoDB;


-- =========================================================
-- SAMPLE DATA (made up people, for testing)
-- =========================================================

-- Passwords:  admin@kmk.test = Admin@2026    everyone else = Password1
INSERT INTO users (full_name, email, phone, password, role, created_at) VALUES
('KMK Admin',     'admin@kmk.test',  '876-555-0100', '$2y$10$bloXXGu04oMV5isQCdN8xOHOYjM9oX0WRqkaDdNXfa0K2bp2m.P5q', 'admin', '2026-10-01 09:00:00'),
('Kayla Morgan',  'kayla@test.com',  '876-555-0111', '$2y$10$mwVq00o7SubJIqQffez3D.kBhHfrZgD7jfYPpdNpap9xk0yXachtW', 'user',  '2026-10-02 10:15:00'),
('Devon Clarke',  'devon@test.com',  '876-555-0122', '$2y$10$mwVq00o7SubJIqQffez3D.kBhHfrZgD7jfYPpdNpap9xk0yXachtW', 'user',  '2026-10-05 14:30:00'),
('Shanice Brown', 'shanice@test.com','876-555-0133', '$2y$10$mwVq00o7SubJIqQffez3D.kBhHfrZgD7jfYPpdNpap9xk0yXachtW', 'user',  '2026-10-09 18:45:00');

-- The names must match the options in the booking forms
INSERT INTO services (service_type, name, location) VALUES
('restaurant', 'The Jerk Centre',                               'Ocho Rios'),
('restaurant', 'Pier 1 Restaurant',                             'Howard Cooke Boulevard'),
('restaurant', 'Food to go',                                    'Ocho Rios'),
('restaurant', 'Sally''s at Sunset',                            'Negril'),
('restaurant', 'Cabarita Inn',                                  'Port Maria'),
('restaurant', 'Roots 21 | Bar & Kitchen',                      'Port Antonio'),
('room',       'Bahia Principe Explore Jamaica',                'Runaway Bay'),
('room',       'The Blue House Boutique Bed & Breakfast Villa', '30 Marcliff Drive, Ocho Rios'),
('room',       'Royalton Blue Waters',                          'Trelawny'),
('room',       'Golden Eye',                                    'Race Course, Oracabessa'),
('room',       'Couples Sans Souci',                            'White River, St. Ann'),
('room',       'Sandals Jamaica Montego Bay',                   'Kent Ave, Montego Bay'),
('driver',     'Marcus',                                        'Islandwide'),
('driver',     'John',                                          'Islandwide'),
('driver',     'Phillip',                                       'Islandwide'),
('driver',     'Martin',                                        'Islandwide'),
('driver',     'Francis',                                       'Islandwide');

-- 12 sample bookings (some previous, some upcoming, one cancelled)
-- service_id: 1-6 restaurants, 7-12 accommodations, 13-17 drivers
INSERT INTO bookings (reference, user_id, service_id, booking_date, booking_time, end_date, end_time, adults, children, rooms, from_place, to_place, trip_type, status, created_at) VALUES
('KMK-A1B2C3', 2,  1, '2026-10-12', '19:00:00', NULL,         NULL,       2, 0, NULL, NULL,         NULL,          NULL,     'Confirmed', '2026-10-03 11:20:00'),
('KMK-D4E5F6', 2,  8, '2026-10-20', NULL,       '2026-10-23', NULL,       2, 1, 1,    NULL,         NULL,          NULL,     'Confirmed', '2026-10-04 09:05:00'),
('KMK-G7H8J9', 2, 13, '2026-11-20', '08:30:00', NULL,         NULL,       2, 0, NULL, 'Kingston',   'Ocho Rios',   'single', 'Confirmed', '2026-11-01 16:40:00'),
('KMK-K2L3M4', 2,  4, '2026-11-28', '18:30:00', NULL,         NULL,       4, 2, NULL, NULL,         NULL,          NULL,     'Pending',   '2026-11-05 13:10:00'),
('KMK-N5P6Q7', 3,  2, '2026-10-30', '20:00:00', NULL,         NULL,       2, 0, NULL, NULL,         NULL,          NULL,     'Confirmed', '2026-10-15 10:00:00'),
('KMK-R8S9T2', 3, 12, '2026-12-18', NULL,       '2026-12-22', NULL,       2, 0, 1,    NULL,         NULL,          NULL,     'Pending',   '2026-11-06 19:25:00'),
('KMK-U3V4W5', 3, 15, '2026-12-18', '07:00:00', '2026-12-22', '12:00:00', 2, 0, NULL, 'May Pen',    'Montego Bay', 'return', 'Pending',   '2026-11-06 19:40:00'),
('KMK-X6Y7Z8', 3,  6, '2026-11-15', '13:00:00', NULL,         NULL,       3, 0, NULL, NULL,         NULL,          NULL,     'Cancelled', '2026-11-02 08:15:00'),
('KMK-B9C2D3', 4,  9, '2026-11-25', NULL,       '2026-11-29', NULL,       2, 2, 2,    NULL,         NULL,          NULL,     'Confirmed', '2026-11-03 12:00:00'),
('KMK-E4F5G6', 4, 17, '2026-11-25', '09:00:00', NULL,         NULL,       2, 2, NULL, 'Spanish Town','Falmouth',   'single', 'Confirmed', '2026-11-03 12:10:00'),
('KMK-H7J8K9', 4,  3, '2026-10-08', '12:30:00', NULL,         NULL,       1, 0, NULL, NULL,         NULL,          NULL,     'Confirmed', '2026-10-07 09:45:00'),
('KMK-L2M3N4', 4, 11, '2026-12-05', NULL,       '2026-12-07', NULL,       2, 0, 1,    NULL,         NULL,          NULL,     'Pending',   '2026-11-08 17:30:00');
