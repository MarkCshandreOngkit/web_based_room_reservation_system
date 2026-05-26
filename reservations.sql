-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2026 at 05:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;

--
-- Database: `try_his`
--

-- --------------------------------------------------------

-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
    `reservation_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `room_name` varchar(100) NOT NULL,
    `floor_location` varchar(100) DEFAULT NULL,
    `subject_activity` varchar(100) DEFAULT NULL,
    `section_organization` varchar(100) DEFAULT NULL,
    `reservation_type` varchar(100) DEFAULT NULL,
    `reservation_date` date NOT NULL,
    `start_time` time NOT NULL,
    `end_time` time NOT NULL,
    `notes` text DEFAULT NULL,
    `room_type` varchar(50) DEFAULT NULL,
    `capacity` int(11) DEFAULT NULL,
    `facilities` text DEFAULT NULL,
    `reservation_code` varchar(50) DEFAULT NULL,
    `status` varchar(50) DEFAULT 'Ongoing',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `image` varchar(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO
    `reservations` (
        `reservation_id`,
        `user_id`,
        `room_name`,
        `floor_location`,
        `subject_activity`,
        `section_organization`,
        `reservation_type`,
        `reservation_date`,
        `start_time`,
        `end_time`,
        `notes`,
        `room_type`,
        `capacity`,
        `facilities`,
        `reservation_code`,
        `status`,
        `created_at`,
        `image`
    )
VALUES (
        1,
        1,
        'EE Lab 2B',
        '3rd Floor - Electrical Engineering',
        'Circuit Laboratory',
        'BSEE 3-1',
        'Laboratory Activity',
        '2026-05-23',
        '13:30:00',
        '16:30:00',
        'Please prepare the multimeters and breadboards.',
        'Laboratory',
        24,
        'Computers, Projector, Whiteboard, Multimeters, Power Supply',
        'RES-051524-0147',
        'Cancelled',
        '2026-05-23 09:32:14',
        'classroom1.jpg'
    ),
    (
        2,
        1,
        'EE Lab 1A',
        '3rd Floor - Electrical Engineering',
        'Instrumentation Laboratory',
        'BSEE 3-1',
        'Laboratory Activity',
        '2026-05-10',
        '00:00:00',
        '10:00:00',
        'Instrumentation practice.',
        'Laboratory',
        20,
        'Computers, Oscilloscope',
        'RES-051024-0321',
        'Cancelled',
        '2026-05-23 09:32:14',
        'EElab1A.jpg'
    ),
    (
        3,
        1,
        'Seminar Room A',
        '2nd Floor - Electrical Engineering',
        'Thesis Advising',
        'BSEE 4-1',
        'Meeting',
        '2026-05-02',
        '09:00:00',
        '12:00:00',
        'Thesis consultation meeting.',
        'Seminar Room',
        15,
        'Projector, Aircon',
        'RES-050224-0874',
        'Completed',
        '2026-05-23 09:32:14',
        'SeminarRoomA.jpg'
    ),
    (
        4,
        1,
        'EE Lab 1B',
        '3rd Floor - Electrical Engineering',
        'Power Systems Laboratory',
        'BSEE 3-1',
        'Laboratory Activity',
        '2026-04-25',
        '14:00:00',
        '17:00:00',
        'Power systems experiment.',
        'Laboratory',
        24,
        'Power Supply, Computers',
        'RES-042524-0122',
        'Completed',
        '2026-05-23 09:32:14',
        'EElab1B.jpg'
    ),
    (
        5,
        1,
        'Seminar Room B',
        '2nd Floor - Electrical Engineering',
        'Department Meeting',
        'Faculty',
        'Meeting',
        '2026-04-20',
        '08:00:00',
        '11:00:00',
        'Monthly faculty meeting.',
        'Seminar Room',
        30,
        'Projector, Whiteboard',
        'RES-042024-0671',
        'Completed',
        '2026-05-23 09:32:14',
        'SeminarRoomB.jpg'
    );

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations` ADD PRIMARY KEY (`reservation_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 6;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;