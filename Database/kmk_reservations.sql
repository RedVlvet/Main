

CREATE DATABASE IF NOT EXISTS kmk_reservations
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_general_ci;

USE kmk_reservations;

-- drops old tables so the code can be re-ran
DROP TABLE IF EXISTS bookings;      
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS users;


CREATE TABLE users (
    user_id     INT AUTO_INCREMENT PRIMARY KEY,
    full_name   VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    phone       VARCHAR(20)  NOT NULL,
    password    VARCHAR(255) NOT NULL,         
    role        ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


CREATE TABLE services (
    service_id    INT AUTO_INCREMENT PRIMARY KEY,
    service_type  ENUM('restaurant', 'room', 'driver') NOT NULL,
    name          VARCHAR(150) NOT NULL,
    location      VARCHAR(150) NOT NULL,
    UNIQUE (service_type, name)
) ENGINE=InnoDB;


CREATE TABLE bookings (
    booking_id    INT AUTO_INCREMENT PRIMARY KEY,
    reference     VARCHAR(12) NOT NULL UNIQUE,
    user_id       INT NOT NULL,
    service_id    INT NOT NULL,
    booking_date  DATE NOT NULL,                
    booking_time  TIME NULL,                    
    end_date      DATE NULL,                    
    end_time      TIME NULL,                    
    adults        INT NOT NULL DEFAULT 1,
    children      INT NOT NULL DEFAULT 0,
    rooms         INT NULL,                     
    from_place    VARCHAR(100) NULL,            
    to_place      VARCHAR(100) NULL,            
    trip_type     ENUM('single', 'return') NULL,
    status        ENUM('Pending', 'Confirmed', 'Cancelled') NOT NULL DEFAULT 'Pending',
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,   -- the date the booking was made

    FOREIGN KEY (user_id)    REFERENCES users(user_id),
    FOREIGN KEY (service_id) REFERENCES services(service_id)
) ENGINE=InnoDB;


-- admin@kmk.test password: Admin@2026      
INSERT INTO users (full_name, email, phone, password, role, created_at) VALUES
('KMK Admin',     'admin@kmk.test',  '876-555-0100', '$2y$10$bloXXGu04oMV5isQCdN8xOHOYjM9oX0WRqkaDdNXfa0K2bp2m.P5q', 'admin', '2026-10-01 09:00:00'),



