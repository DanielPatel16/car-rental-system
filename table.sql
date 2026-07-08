CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `year` int(11) NOT NULL,
  `fuel_type` enum('Petrol','Diesel','Electric','Hybrid') NOT NULL DEFAULT 'Petrol',
  `transmission` enum('Automatic','Manual') NOT NULL DEFAULT 'Automatic',
  `seats` int(11) NOT NULL DEFAULT 4,
  `price_per_day` decimal(10,2) NOT NULL DEFAULT 0.00,
  `registration_number` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Available','Rented','Maintenance') NOT NULL DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
);

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
);


INSERT INTO `categories` (`id`, `name`) VALUES
(6, 'Coupe'),
(4, 'Hatchback'),
(3, 'MPV/MUV'),
(7, 'Pickup Truck'),
(1, 'Sedan'),
(2, 'SUV'),
(5, 'Van/Minivan');

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
);

CREATE TABLE IF NOT EXISTS bookings (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    user_id          INT NOT NULL,
    car_id           INT NOT NULL,
    pickup_date      DATE NOT NULL,
    return_date      DATE NOT NULL,
    pickup_location  VARCHAR(255) NOT NULL,
    return_location  VARCHAR(255) NOT NULL,
    total_days       INT NOT NULL,
    total_amount     DECIMAL(10,2) NOT NULL,
    booking_status   ENUM('Pending', 'Confirmed', 'Cancelled', 'Completed') NOT NULL DEFAULT 'Pending',
    payment_status   ENUM('Pending', 'Paid', 'Failed', 'Refunded') NOT NULL DEFAULT 'Pending',

    -- Identity verification (required at booking time)
    aadhar_number    VARCHAR(20)  NOT NULL,
    license_number   VARCHAR(30)  NOT NULL,
    id_proof_image   VARCHAR(255) NOT NULL,

    created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_bookings_car  FOREIGN KEY (car_id)  REFERENCES cars(id)  ON DELETE CASCADE,

    INDEX idx_bookings_user (user_id),
    INDEX idx_bookings_car (car_id),
    INDEX idx_bookings_status (booking_status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payments (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    booking_id      INT NOT NULL,
    payment_method  ENUM('UPI', 'Card', 'Cash') NOT NULL,
    transaction_id  VARCHAR(100) DEFAULT NULL,
    amount          DECIMAL(10,2) NOT NULL,
    payment_date    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status          ENUM('Pending', 'Success', 'Failed', 'Refunded') NOT NULL DEFAULT 'Pending',

    CONSTRAINT fk_payments_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,

    INDEX idx_payments_booking (booking_id)
) ENGINE=InnoDB;