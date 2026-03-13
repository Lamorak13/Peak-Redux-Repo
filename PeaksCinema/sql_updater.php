<?php
    $servername = "localhost";
    $username = "root";
    $password = "";

    $conn = new mysqli($servername, $username, $password);

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $sql = <<<SQL
                SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
                START TRANSACTION;
                SET time_zone = "+00:00";

                /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
                /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
                /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
                /*!40101 SET NAMES utf8mb4 */;

                DROP DATABASE IF EXISTS `peakscinemadb`;
                CREATE DATABASE IF NOT EXISTS `peakscinemadb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
                USE `peakscinemadb`;

                CREATE TABLE `customer` (
                `Customer_ID` int(11) NOT NULL,
                `Name` varchar(100) NOT NULL,
                `Email` varchar(100) NOT NULL,
                `Password` varchar(255) NOT NULL,
                `PhoneNumber` varchar(10) NOT NULL,
                `CountryCode` varchar(4) NOT NULL,
                `PaymentMethod` tinytext NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                CREATE TABLE `daterange` (
                `DateRange_ID` int(11) NOT NULL,
                `Movie_ID` int(11) NOT NULL,
                `Theater_ID` int(11) NOT NULL,
                `StartDate` date NOT NULL,
                `EndDate` date NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                INSERT INTO `daterange` (`DateRange_ID`, `Movie_ID`, `Theater_ID`, `StartDate`, `EndDate`) VALUES
                (6, 1, 10, '2026-03-25', '2026-03-30');

                CREATE TABLE `e-receipt` (
                `Receipt_ID` int(11) NOT NULL,
                `PaymentID` int(11) NOT NULL,
                `DateIssued` date NOT NULL,
                `SentToEmail` varchar(100) NOT NULL,
                `ReceiptStatus` int(11) NOT NULL,
                `Status` int(11) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                CREATE TABLE `mall` (
                `Mall_ID` int(11) NOT NULL,
                `MallName` tinytext NOT NULL,
                `Location` tinytext NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                INSERT INTO `mall` (`Mall_ID`, `MallName`, `Location`) VALUES
                (1, 'SM Marikina', 'Marcos Highway, Calumpang, Marikina City, 1801, Marikina, Luzon Philippines');

                CREATE TABLE `movie` (
                `Movie_ID` int(11) NOT NULL,
                `MovieName` text NOT NULL,
                `MovieDescription` mediumtext NOT NULL,
                `Genre` tinytext NOT NULL,
                `Rating` varchar(10) NOT NULL,
                `Runtime` int(11) NOT NULL,
                `MoviePoster` text NOT NULL,
                `MovieAvailability` tinytext NOT NULL,
                `TrailerURL` text NOT NULL,
                `Price` int(11) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                INSERT INTO `movie` (`Movie_ID`, `MovieName`, `MovieDescription`, `Genre`, `Rating`, `Runtime`, `MoviePoster`, `MovieAvailability`, `TrailerURL`, `Price`) VALUES
                (1, 'Superman', 'Superman must reconcile his alien Kryptonian heritage with his human upbringing as reporter Clark Kent. As the embodiment of truth, justice and the human way he soon finds himself in a world that views these as old-fashioned.\n\n', 'Superhero, Action', 'PG', 129, 'PeaksCinema/MoviePosters/Superman.png', 'Now Showing', 'https://www.youtube.com/watch?v=Ox8ZLF6cGM0', 350);

                CREATE TABLE `payment` (
                `Payment_ID` int(11) NOT NULL,
                `Ticket_ID` int(11) NOT NULL,
                `PaymentMethod` varchar(50) NOT NULL,
                `AmountPaid` decimal(10,2) NOT NULL,
                `PaymentDate` date NOT NULL,
                `PaymentStatus` int(11) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                CREATE TABLE `seats` (
                `Seat_ID` int(11) NOT NULL,
                `Theater_ID` int(11) NOT NULL,
                `SeatRow` varchar(10) NOT NULL,
                `SeatColumn` varchar(10) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                INSERT INTO `seats` (`Seat_ID`, `Theater_ID`, `SeatRow`, `SeatColumn`) VALUES
                (5, 5, 'A', '10'),
                (6, 5, 'A', '9'),
                (7, 5, 'A', '0'),
                (9, 6, 'A', '10'),
                (10, 6, 'A', '9'),
                (11, 6, 'A', '0'),
                (13, 7, 'A', '10'),
                (14, 7, 'A', '9'),
                (15, 7, 'A', '0'),
                (17, 8, 'A', '10'),
                (18, 8, 'A', '9'),
                (19, 8, 'A', '0'),
                (21, 9, 'A', '10'),
                (22, 9, 'A', '9'),
                (23, 9, 'A', '0'),
                (24, 9, 'A', '0'),
                (25, 9, 'A', '8'),
                (26, 9, 'A', '7'),
                (27, 9, 'A', '6'),
                (28, 9, 'A', '5'),
                (29, 9, 'A', '4'),
                (30, 9, 'A', '3'),
                (31, 9, 'A', '0'),
                (32, 9, 'A', '0'),
                (33, 9, 'A', '2'),
                (34, 9, 'A', '1'),
                (35, 9, 'B', '10'),
                (36, 9, 'B', '9'),
                (37, 9, 'B', '0'),
                (38, 9, 'B', '0'),
                (39, 9, 'B', '8'),
                (40, 9, 'B', '7'),
                (41, 9, 'B', '6'),
                (42, 9, 'B', '5'),
                (43, 9, 'B', '4'),
                (44, 9, 'B', '3'),
                (45, 9, 'B', '0'),
                (46, 9, 'B', '0'),
                (47, 9, 'B', '2'),
                (48, 9, 'B', '1'),
                (49, 9, 'C', '10'),
                (50, 9, 'C', '9'),
                (51, 9, 'C', '0'),
                (52, 9, 'C', '0'),
                (53, 9, 'C', '8'),
                (54, 9, 'C', '7'),
                (55, 9, 'C', '6'),
                (56, 9, 'C', '5'),
                (57, 9, 'C', '4'),
                (58, 9, 'C', '3'),
                (59, 9, 'C', '0'),
                (60, 9, 'C', '0'),
                (61, 9, 'C', '2'),
                (62, 9, 'C', '1'),
                (63, 9, 'D', '10'),
                (64, 9, 'D', '9'),
                (65, 9, 'D', '0'),
                (66, 9, 'D', '0'),
                (67, 9, 'D', '8'),
                (68, 9, 'D', '7'),
                (69, 9, 'D', '6'),
                (70, 9, 'D', '5'),
                (71, 9, 'D', '4'),
                (72, 9, 'D', '3'),
                (73, 9, 'D', '0'),
                (74, 9, 'D', '0'),
                (75, 9, 'D', '2'),
                (76, 9, 'D', '1'),
                (77, 9, 'E', '10'),
                (78, 9, 'E', '9'),
                (79, 9, 'E', '0'),
                (80, 9, 'E', '0'),
                (81, 9, 'E', '8'),
                (82, 9, 'E', '7'),
                (83, 9, 'E', '6'),
                (84, 9, 'E', '5'),
                (85, 9, 'E', '4'),
                (86, 9, 'E', '3'),
                (87, 9, 'E', '0'),
                (88, 9, 'E', '0'),
                (89, 9, 'E', '2'),
                (90, 9, 'E', '1'),
                (91, 10, 'A', '10'),
                (92, 10, 'A', '9'),
                (93, 10, 'A', '0'),
                (94, 10, 'A', '0'),
                (95, 10, 'A', '8'),
                (96, 10, 'A', '7'),
                (97, 10, 'A', '6'),
                (98, 10, 'A', '5'),
                (99, 10, 'A', '4'),
                (100, 10, 'A', '3'),
                (101, 10, 'A', '0'),
                (102, 10, 'A', '0'),
                (103, 10, 'A', '2'),
                (104, 10, 'A', '1'),
                (105, 10, 'B', '10'),
                (106, 10, 'B', '9'),
                (107, 10, 'B', '0'),
                (108, 10, 'B', '0'),
                (109, 10, 'B', '8'),
                (110, 10, 'B', '7'),
                (111, 10, 'B', '6'),
                (112, 10, 'B', '5'),
                (113, 10, 'B', '4'),
                (114, 10, 'B', '3'),
                (115, 10, 'B', '0'),
                (116, 10, 'B', '0'),
                (117, 10, 'B', '2'),
                (118, 10, 'B', '1'),
                (119, 10, 'C', '10'),
                (120, 10, 'C', '9'),
                (121, 10, 'C', '0'),
                (122, 10, 'C', '0'),
                (123, 10, 'C', '8'),
                (124, 10, 'C', '7'),
                (125, 10, 'C', '6'),
                (126, 10, 'C', '5'),
                (127, 10, 'C', '4'),
                (128, 10, 'C', '3'),
                (129, 10, 'C', '0'),
                (130, 10, 'C', '0'),
                (131, 10, 'C', '2'),
                (132, 10, 'C', '1'),
                (133, 10, 'D', '10'),
                (134, 10, 'D', '9'),
                (135, 10, 'D', '0'),
                (136, 10, 'D', '0'),
                (137, 10, 'D', '8'),
                (138, 10, 'D', '7'),
                (139, 10, 'D', '6'),
                (140, 10, 'D', '5'),
                (141, 10, 'D', '4'),
                (142, 10, 'D', '3'),
                (143, 10, 'D', '0'),
                (144, 10, 'D', '0'),
                (145, 10, 'D', '2'),
                (146, 10, 'D', '1'),
                (147, 10, 'E', '10'),
                (148, 10, 'E', '9'),
                (149, 10, 'E', '0'),
                (150, 10, 'E', '0'),
                (151, 10, 'E', '8'),
                (152, 10, 'E', '7'),
                (153, 10, 'E', '6'),
                (154, 10, 'E', '5'),
                (155, 10, 'E', '4'),
                (156, 10, 'E', '3'),
                (157, 10, 'E', '0'),
                (158, 10, 'E', '0'),
                (159, 10, 'E', '2'),
                (160, 10, 'E', '1');

                CREATE TABLE `seat_timeslot` (
                `SeatTimeSlot_ID` int(11) NOT NULL,
                `Seat_ID` int(11) NOT NULL,
                `TimeSlot_ID` int(11) NOT NULL,
                `SeatPrice` decimal(10,2) NOT NULL,
                `SeatAvailability` tinyint(1) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                INSERT INTO `seat_timeslot` (`SeatTimeSlot_ID`, `Seat_ID`, `TimeSlot_ID`, `SeatPrice`, `SeatAvailability`) VALUES
                (1, 91, 1, 350.00, 1),
                (2, 92, 1, 350.00, 1),
                (3, 93, 1, 350.00, 1),
                (4, 94, 1, 350.00, 1),
                (5, 95, 1, 350.00, 1),
                (6, 96, 1, 350.00, 1),
                (7, 97, 1, 350.00, 1),
                (8, 98, 1, 350.00, 1),
                (9, 99, 1, 350.00, 1),
                (10, 100, 1, 350.00, 1),
                (11, 101, 1, 350.00, 1),
                (12, 102, 1, 350.00, 1),
                (13, 103, 1, 350.00, 1),
                (14, 104, 1, 350.00, 1),
                (15, 105, 1, 350.00, 1),
                (16, 106, 1, 350.00, 1),
                (17, 107, 1, 350.00, 1),
                (18, 108, 1, 350.00, 1),
                (19, 109, 1, 350.00, 1),
                (20, 110, 1, 350.00, 1),
                (21, 111, 1, 350.00, 1),
                (22, 112, 1, 350.00, 1),
                (23, 113, 1, 350.00, 1),
                (24, 114, 1, 350.00, 1),
                (25, 115, 1, 350.00, 1),
                (26, 116, 1, 350.00, 1),
                (27, 117, 1, 350.00, 1),
                (28, 118, 1, 350.00, 1),
                (29, 119, 1, 350.00, 1),
                (30, 120, 1, 350.00, 1),
                (31, 121, 1, 350.00, 1),
                (32, 122, 1, 350.00, 1),
                (33, 123, 1, 350.00, 1),
                (34, 124, 1, 350.00, 1),
                (35, 125, 1, 350.00, 1),
                (36, 126, 1, 350.00, 1),
                (37, 127, 1, 350.00, 1),
                (38, 128, 1, 350.00, 1),
                (39, 129, 1, 350.00, 1),
                (40, 130, 1, 350.00, 1),
                (41, 131, 1, 350.00, 1),
                (42, 132, 1, 350.00, 1),
                (43, 133, 1, 350.00, 1),
                (44, 134, 1, 350.00, 1),
                (45, 135, 1, 350.00, 1),
                (46, 136, 1, 350.00, 1),
                (47, 137, 1, 350.00, 1),
                (48, 138, 1, 350.00, 1),
                (49, 139, 1, 350.00, 1),
                (50, 140, 1, 350.00, 1),
                (51, 141, 1, 350.00, 1),
                (52, 142, 1, 350.00, 1),
                (53, 143, 1, 350.00, 1),
                (54, 144, 1, 350.00, 1),
                (55, 145, 1, 350.00, 1),
                (56, 146, 1, 350.00, 1),
                (57, 147, 1, 350.00, 1),
                (58, 148, 1, 350.00, 1),
                (59, 149, 1, 350.00, 1),
                (60, 150, 1, 350.00, 1),
                (61, 151, 1, 350.00, 1),
                (62, 152, 1, 350.00, 1),
                (63, 153, 1, 350.00, 1),
                (64, 154, 1, 350.00, 1),
                (65, 155, 1, 350.00, 1),
                (66, 156, 1, 350.00, 1),
                (67, 157, 1, 350.00, 1),
                (68, 158, 1, 350.00, 1),
                (69, 159, 1, 350.00, 1),
                (70, 160, 1, 350.00, 1),
                (71, 91, 2, 350.00, 1),
                (72, 92, 2, 350.00, 1),
                (73, 93, 2, 350.00, 1),
                (74, 94, 2, 350.00, 1),
                (75, 95, 2, 350.00, 1),
                (76, 96, 2, 350.00, 1),
                (77, 97, 2, 350.00, 1),
                (78, 98, 2, 350.00, 1),
                (79, 99, 2, 350.00, 1),
                (80, 100, 2, 350.00, 1),
                (81, 101, 2, 350.00, 1),
                (82, 102, 2, 350.00, 1),
                (83, 103, 2, 350.00, 1),
                (84, 104, 2, 350.00, 1),
                (85, 105, 2, 350.00, 1),
                (86, 106, 2, 350.00, 1),
                (87, 107, 2, 350.00, 1),
                (88, 108, 2, 350.00, 1),
                (89, 109, 2, 350.00, 1),
                (90, 110, 2, 350.00, 1),
                (91, 111, 2, 350.00, 1),
                (92, 112, 2, 350.00, 1),
                (93, 113, 2, 350.00, 1),
                (94, 114, 2, 350.00, 1),
                (95, 115, 2, 350.00, 1),
                (96, 116, 2, 350.00, 1),
                (97, 117, 2, 350.00, 1),
                (98, 118, 2, 350.00, 1),
                (99, 119, 2, 350.00, 1),
                (100, 120, 2, 350.00, 1),
                (101, 121, 2, 350.00, 1),
                (102, 122, 2, 350.00, 1),
                (103, 123, 2, 350.00, 1),
                (104, 124, 2, 350.00, 1),
                (105, 125, 2, 350.00, 1),
                (106, 126, 2, 350.00, 1),
                (107, 127, 2, 350.00, 1),
                (108, 128, 2, 350.00, 1),
                (109, 129, 2, 350.00, 1),
                (110, 130, 2, 350.00, 1),
                (111, 131, 2, 350.00, 1),
                (112, 132, 2, 350.00, 1),
                (113, 133, 2, 350.00, 1),
                (114, 134, 2, 350.00, 1),
                (115, 135, 2, 350.00, 1),
                (116, 136, 2, 350.00, 1),
                (117, 137, 2, 350.00, 1),
                (118, 138, 2, 350.00, 1),
                (119, 139, 2, 350.00, 1),
                (120, 140, 2, 350.00, 1),
                (121, 141, 2, 350.00, 1),
                (122, 142, 2, 350.00, 1),
                (123, 143, 2, 350.00, 1),
                (124, 144, 2, 350.00, 1),
                (125, 145, 2, 350.00, 1),
                (126, 146, 2, 350.00, 1),
                (127, 147, 2, 350.00, 1),
                (128, 148, 2, 350.00, 1),
                (129, 149, 2, 350.00, 1),
                (130, 150, 2, 350.00, 1),
                (131, 151, 2, 350.00, 1),
                (132, 152, 2, 350.00, 1),
                (133, 153, 2, 350.00, 1),
                (134, 154, 2, 350.00, 1),
                (135, 155, 2, 350.00, 1),
                (136, 156, 2, 350.00, 1),
                (137, 157, 2, 350.00, 1),
                (138, 158, 2, 350.00, 1),
                (139, 159, 2, 350.00, 1),
                (140, 160, 2, 350.00, 1),
                (141, 91, 3, 350.00, 1),
                (142, 92, 3, 350.00, 1),
                (143, 93, 3, 350.00, 1),
                (144, 94, 3, 350.00, 1),
                (145, 95, 3, 350.00, 1),
                (146, 96, 3, 350.00, 1),
                (147, 97, 3, 350.00, 1),
                (148, 98, 3, 350.00, 1),
                (149, 99, 3, 350.00, 1),
                (150, 100, 3, 350.00, 1),
                (151, 101, 3, 350.00, 1),
                (152, 102, 3, 350.00, 1),
                (153, 103, 3, 350.00, 1),
                (154, 104, 3, 350.00, 1),
                (155, 105, 3, 350.00, 1),
                (156, 106, 3, 350.00, 1),
                (157, 107, 3, 350.00, 1),
                (158, 108, 3, 350.00, 1),
                (159, 109, 3, 350.00, 1),
                (160, 110, 3, 350.00, 1),
                (161, 111, 3, 350.00, 1),
                (162, 112, 3, 350.00, 1),
                (163, 113, 3, 350.00, 1),
                (164, 114, 3, 350.00, 1),
                (165, 115, 3, 350.00, 1),
                (166, 116, 3, 350.00, 1),
                (167, 117, 3, 350.00, 1),
                (168, 118, 3, 350.00, 1),
                (169, 119, 3, 350.00, 1),
                (170, 120, 3, 350.00, 1),
                (171, 121, 3, 350.00, 1),
                (172, 122, 3, 350.00, 1),
                (173, 123, 3, 350.00, 1),
                (174, 124, 3, 350.00, 1),
                (175, 125, 3, 350.00, 1),
                (176, 126, 3, 350.00, 1),
                (177, 127, 3, 350.00, 1),
                (178, 128, 3, 350.00, 1),
                (179, 129, 3, 350.00, 1),
                (180, 130, 3, 350.00, 1),
                (181, 131, 3, 350.00, 1),
                (182, 132, 3, 350.00, 1),
                (183, 133, 3, 350.00, 1),
                (184, 134, 3, 350.00, 1),
                (185, 135, 3, 350.00, 1),
                (186, 136, 3, 350.00, 1),
                (187, 137, 3, 350.00, 1),
                (188, 138, 3, 350.00, 1),
                (189, 139, 3, 350.00, 1),
                (190, 140, 3, 350.00, 1),
                (191, 141, 3, 350.00, 1),
                (192, 142, 3, 350.00, 1),
                (193, 143, 3, 350.00, 1),
                (194, 144, 3, 350.00, 1),
                (195, 145, 3, 350.00, 1),
                (196, 146, 3, 350.00, 1),
                (197, 147, 3, 350.00, 1),
                (198, 148, 3, 350.00, 1),
                (199, 149, 3, 350.00, 1),
                (200, 150, 3, 350.00, 1),
                (201, 151, 3, 350.00, 1),
                (202, 152, 3, 350.00, 1),
                (203, 153, 3, 350.00, 1),
                (204, 154, 3, 350.00, 1),
                (205, 155, 3, 350.00, 1),
                (206, 156, 3, 350.00, 1),
                (207, 157, 3, 350.00, 1),
                (208, 158, 3, 350.00, 1),
                (209, 159, 3, 350.00, 1),
                (210, 160, 3, 350.00, 1),
                (211, 91, 4, 350.00, 1),
                (212, 92, 4, 350.00, 1),
                (213, 93, 4, 350.00, 1),
                (214, 94, 4, 350.00, 1),
                (215, 95, 4, 350.00, 1),
                (216, 96, 4, 350.00, 1),
                (217, 97, 4, 350.00, 1),
                (218, 98, 4, 350.00, 1),
                (219, 99, 4, 350.00, 1),
                (220, 100, 4, 350.00, 1),
                (221, 101, 4, 350.00, 1),
                (222, 102, 4, 350.00, 1),
                (223, 103, 4, 350.00, 1),
                (224, 104, 4, 350.00, 1),
                (225, 105, 4, 350.00, 1),
                (226, 106, 4, 350.00, 1),
                (227, 107, 4, 350.00, 1),
                (228, 108, 4, 350.00, 1),
                (229, 109, 4, 350.00, 1),
                (230, 110, 4, 350.00, 1),
                (231, 111, 4, 350.00, 1),
                (232, 112, 4, 350.00, 1),
                (233, 113, 4, 350.00, 1),
                (234, 114, 4, 350.00, 1),
                (235, 115, 4, 350.00, 1),
                (236, 116, 4, 350.00, 1),
                (237, 117, 4, 350.00, 1),
                (238, 118, 4, 350.00, 1),
                (239, 119, 4, 350.00, 1),
                (240, 120, 4, 350.00, 1),
                (241, 121, 4, 350.00, 1),
                (242, 122, 4, 350.00, 1),
                (243, 123, 4, 350.00, 1),
                (244, 124, 4, 350.00, 1),
                (245, 125, 4, 350.00, 1),
                (246, 126, 4, 350.00, 1),
                (247, 127, 4, 350.00, 1),
                (248, 128, 4, 350.00, 1),
                (249, 129, 4, 350.00, 1),
                (250, 130, 4, 350.00, 1),
                (251, 131, 4, 350.00, 1),
                (252, 132, 4, 350.00, 1),
                (253, 133, 4, 350.00, 1),
                (254, 134, 4, 350.00, 1),
                (255, 135, 4, 350.00, 1),
                (256, 136, 4, 350.00, 1),
                (257, 137, 4, 350.00, 1),
                (258, 138, 4, 350.00, 1),
                (259, 139, 4, 350.00, 1),
                (260, 140, 4, 350.00, 1),
                (261, 141, 4, 350.00, 1),
                (262, 142, 4, 350.00, 1),
                (263, 143, 4, 350.00, 1),
                (264, 144, 4, 350.00, 1),
                (265, 145, 4, 350.00, 1),
                (266, 146, 4, 350.00, 1),
                (267, 147, 4, 350.00, 1),
                (268, 148, 4, 350.00, 1),
                (269, 149, 4, 350.00, 1),
                (270, 150, 4, 350.00, 1),
                (271, 151, 4, 350.00, 1),
                (272, 152, 4, 350.00, 1),
                (273, 153, 4, 350.00, 1),
                (274, 154, 4, 350.00, 1),
                (275, 155, 4, 350.00, 1),
                (276, 156, 4, 350.00, 1),
                (277, 157, 4, 350.00, 1),
                (278, 158, 4, 350.00, 1),
                (279, 159, 4, 350.00, 1),
                (280, 160, 4, 350.00, 1),
                (281, 91, 5, 350.00, 1),
                (282, 92, 5, 350.00, 1),
                (283, 93, 5, 350.00, 1),
                (284, 94, 5, 350.00, 1),
                (285, 95, 5, 350.00, 1),
                (286, 96, 5, 350.00, 1),
                (287, 97, 5, 350.00, 1),
                (288, 98, 5, 350.00, 1),
                (289, 99, 5, 350.00, 1),
                (290, 100, 5, 350.00, 1),
                (291, 101, 5, 350.00, 1),
                (292, 102, 5, 350.00, 1),
                (293, 103, 5, 350.00, 1),
                (294, 104, 5, 350.00, 1),
                (295, 105, 5, 350.00, 1),
                (296, 106, 5, 350.00, 1),
                (297, 107, 5, 350.00, 1),
                (298, 108, 5, 350.00, 1),
                (299, 109, 5, 350.00, 1),
                (300, 110, 5, 350.00, 1),
                (301, 111, 5, 350.00, 1),
                (302, 112, 5, 350.00, 1),
                (303, 113, 5, 350.00, 1),
                (304, 114, 5, 350.00, 1),
                (305, 115, 5, 350.00, 1),
                (306, 116, 5, 350.00, 1),
                (307, 117, 5, 350.00, 1),
                (308, 118, 5, 350.00, 1),
                (309, 119, 5, 350.00, 1),
                (310, 120, 5, 350.00, 1),
                (311, 121, 5, 350.00, 1),
                (312, 122, 5, 350.00, 1),
                (313, 123, 5, 350.00, 1),
                (314, 124, 5, 350.00, 1),
                (315, 125, 5, 350.00, 1),
                (316, 126, 5, 350.00, 1),
                (317, 127, 5, 350.00, 1),
                (318, 128, 5, 350.00, 1),
                (319, 129, 5, 350.00, 1),
                (320, 130, 5, 350.00, 1),
                (321, 131, 5, 350.00, 1),
                (322, 132, 5, 350.00, 1),
                (323, 133, 5, 350.00, 1),
                (324, 134, 5, 350.00, 1),
                (325, 135, 5, 350.00, 1),
                (326, 136, 5, 350.00, 1),
                (327, 137, 5, 350.00, 1),
                (328, 138, 5, 350.00, 1),
                (329, 139, 5, 350.00, 1),
                (330, 140, 5, 350.00, 1),
                (331, 141, 5, 350.00, 1),
                (332, 142, 5, 350.00, 1),
                (333, 143, 5, 350.00, 1),
                (334, 144, 5, 350.00, 1),
                (335, 145, 5, 350.00, 1),
                (336, 146, 5, 350.00, 1),
                (337, 147, 5, 350.00, 1),
                (338, 148, 5, 350.00, 1),
                (339, 149, 5, 350.00, 1),
                (340, 150, 5, 350.00, 1),
                (341, 151, 5, 350.00, 1),
                (342, 152, 5, 350.00, 1),
                (343, 153, 5, 350.00, 1),
                (344, 154, 5, 350.00, 1),
                (345, 155, 5, 350.00, 1),
                (346, 156, 5, 350.00, 1),
                (347, 157, 5, 350.00, 1),
                (348, 158, 5, 350.00, 1),
                (349, 159, 5, 350.00, 1),
                (350, 160, 5, 350.00, 1),
                (351, 91, 6, 350.00, 1),
                (352, 92, 6, 350.00, 1),
                (353, 93, 6, 350.00, 1),
                (354, 94, 6, 350.00, 1),
                (355, 95, 6, 350.00, 1),
                (356, 96, 6, 350.00, 1),
                (357, 97, 6, 350.00, 1),
                (358, 98, 6, 350.00, 1),
                (359, 99, 6, 350.00, 1),
                (360, 100, 6, 350.00, 1),
                (361, 101, 6, 350.00, 1),
                (362, 102, 6, 350.00, 1),
                (363, 103, 6, 350.00, 1),
                (364, 104, 6, 350.00, 1),
                (365, 105, 6, 350.00, 1),
                (366, 106, 6, 350.00, 1),
                (367, 107, 6, 350.00, 1),
                (368, 108, 6, 350.00, 1),
                (369, 109, 6, 350.00, 1),
                (370, 110, 6, 350.00, 1),
                (371, 111, 6, 350.00, 1),
                (372, 112, 6, 350.00, 1),
                (373, 113, 6, 350.00, 1),
                (374, 114, 6, 350.00, 1),
                (375, 115, 6, 350.00, 1),
                (376, 116, 6, 350.00, 1),
                (377, 117, 6, 350.00, 1),
                (378, 118, 6, 350.00, 1),
                (379, 119, 6, 350.00, 1),
                (380, 120, 6, 350.00, 1),
                (381, 121, 6, 350.00, 1),
                (382, 122, 6, 350.00, 1),
                (383, 123, 6, 350.00, 1),
                (384, 124, 6, 350.00, 1),
                (385, 125, 6, 350.00, 1),
                (386, 126, 6, 350.00, 1),
                (387, 127, 6, 350.00, 1),
                (388, 128, 6, 350.00, 1),
                (389, 129, 6, 350.00, 1),
                (390, 130, 6, 350.00, 1),
                (391, 131, 6, 350.00, 1),
                (392, 132, 6, 350.00, 1),
                (393, 133, 6, 350.00, 1),
                (394, 134, 6, 350.00, 1),
                (395, 135, 6, 350.00, 1),
                (396, 136, 6, 350.00, 1),
                (397, 137, 6, 350.00, 1),
                (398, 138, 6, 350.00, 1),
                (399, 139, 6, 350.00, 1),
                (400, 140, 6, 350.00, 1),
                (401, 141, 6, 350.00, 1),
                (402, 142, 6, 350.00, 1),
                (403, 143, 6, 350.00, 1),
                (404, 144, 6, 350.00, 1),
                (405, 145, 6, 350.00, 1),
                (406, 146, 6, 350.00, 1),
                (407, 147, 6, 350.00, 1),
                (408, 148, 6, 350.00, 1),
                (409, 149, 6, 350.00, 1),
                (410, 150, 6, 350.00, 1),
                (411, 151, 6, 350.00, 1),
                (412, 152, 6, 350.00, 1),
                (413, 153, 6, 350.00, 1),
                (414, 154, 6, 350.00, 1),
                (415, 155, 6, 350.00, 1),
                (416, 156, 6, 350.00, 1),
                (417, 157, 6, 350.00, 1),
                (418, 158, 6, 350.00, 1),
                (419, 159, 6, 350.00, 1),
                (420, 160, 6, 350.00, 1);

                CREATE TABLE `theater` (
                `Theater_ID` int(11) NOT NULL,
                `Mall_ID` int(11) NOT NULL,
                `TheaterName` varchar(100) NOT NULL,
                `TotalSeats` int(11) NOT NULL,
                `TheaterType` varchar(50) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                INSERT INTO `theater` (`Theater_ID`, `Mall_ID`, `TheaterName`, `TotalSeats`, `TheaterType`) VALUES
                (10, 1, 'Regular 1', 50, 'Regular');

                CREATE TABLE `ticket` (
                `Ticket_ID` int(11) NOT NULL,
                `Seat_ID` int(11) NOT NULL,
                `Customer_ID` int(11) NOT NULL,
                `Movie_ID` int(11) NOT NULL,
                `TimeSlot_ID` int(11) NOT NULL,
                `Price` decimal(10,2) NOT NULL,
                `Status` int(11) NOT NULL,
                `DateTime` datetime NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                CREATE TABLE `timeslot` (
                `TimeSlot_ID` int(11) NOT NULL,
                `StartTime` time NOT NULL,
                `Date` date NOT NULL,
                `ScreeningType` varchar(10) NOT NULL,
                `Movie_ID` int(11) NOT NULL,
                `Theater_ID` int(11) NOT NULL,
                `DateRange_ID` int(11) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                INSERT INTO `timeslot` (`TimeSlot_ID`, `StartTime`, `Date`, `ScreeningType`, `Movie_ID`, `Theater_ID`, `DateRange_ID`) VALUES
                (1, '13:07:00', '2026-03-25', '2D', 1, 10, 6),
                (2, '13:07:00', '2026-03-26', '2D', 1, 10, 6),
                (3, '13:07:00', '2026-03-27', '2D', 1, 10, 6),
                (4, '13:07:00', '2026-03-28', '2D', 1, 10, 6),
                (5, '13:07:00', '2026-03-29', '2D', 1, 10, 6),
                (6, '13:07:00', '2026-03-30', '2D', 1, 10, 6);


                ALTER TABLE `customer`
                ADD PRIMARY KEY (`Customer_ID`);

                ALTER TABLE `daterange`
                ADD PRIMARY KEY (`DateRange_ID`),
                ADD KEY `Movie_ID` (`Movie_ID`),
                ADD KEY `Theater_ID` (`Theater_ID`);

                ALTER TABLE `e-receipt`
                ADD PRIMARY KEY (`Receipt_ID`);

                ALTER TABLE `mall`
                ADD PRIMARY KEY (`Mall_ID`),
                ADD UNIQUE KEY `MallName` (`MallName`) USING HASH;

                ALTER TABLE `movie`
                ADD PRIMARY KEY (`Movie_ID`);

                ALTER TABLE `payment`
                ADD PRIMARY KEY (`Payment_ID`);

                ALTER TABLE `seats`
                ADD PRIMARY KEY (`Seat_ID`);

                ALTER TABLE `seat_timeslot`
                ADD PRIMARY KEY (`SeatTimeSlot_ID`),
                ADD UNIQUE KEY `uniq_seat_timeslot` (`Seat_ID`,`TimeSlot_ID`),
                ADD KEY `TimeSlot_ID` (`TimeSlot_ID`);

                ALTER TABLE `theater`
                ADD PRIMARY KEY (`Theater_ID`);

                ALTER TABLE `ticket`
                ADD PRIMARY KEY (`Ticket_ID`);

                ALTER TABLE `timeslot`
                ADD PRIMARY KEY (`TimeSlot_ID`),
                ADD KEY `Movie_ID` (`Movie_ID`),
                ADD KEY `Theater_ID` (`Theater_ID`),
                ADD KEY `DateRange_ID` (`DateRange_ID`);


                ALTER TABLE `customer`
                MODIFY `Customer_ID` int(11) NOT NULL AUTO_INCREMENT;

                ALTER TABLE `daterange`
                MODIFY `DateRange_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

                ALTER TABLE `e-receipt`
                MODIFY `Receipt_ID` int(11) NOT NULL AUTO_INCREMENT;

                ALTER TABLE `mall`
                MODIFY `Mall_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

                ALTER TABLE `movie`
                MODIFY `Movie_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

                ALTER TABLE `payment`
                MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT;

                ALTER TABLE `seats`
                MODIFY `Seat_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

                ALTER TABLE `seat_timeslot`
                MODIFY `SeatTimeSlot_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=421;

                ALTER TABLE `theater`
                MODIFY `Theater_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

                ALTER TABLE `ticket`
                MODIFY `Ticket_ID` int(11) NOT NULL AUTO_INCREMENT;

                ALTER TABLE `timeslot`
                MODIFY `TimeSlot_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;


                ALTER TABLE `daterange`
                ADD CONSTRAINT `daterange_ibfk_1` FOREIGN KEY (`Movie_ID`) REFERENCES `movie` (`Movie_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT `daterange_ibfk_2` FOREIGN KEY (`Theater_ID`) REFERENCES `theater` (`Theater_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

                ALTER TABLE `seats`
                ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`Theater_ID`) REFERENCES `theater` (`Theater_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

                ALTER TABLE `seat_timeslot`
                ADD CONSTRAINT `seat_timeslot_ibfk_1` FOREIGN KEY (`Seat_ID`) REFERENCES `seats` (`Seat_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT `seat_timeslot_ibfk_2` FOREIGN KEY (`TimeSlot_ID`) REFERENCES `timeslot` (`TimeSlot_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

                ALTER TABLE `timeslot`
                ADD CONSTRAINT `timeslot_ibfk_1` FOREIGN KEY (`Movie_ID`) REFERENCES `movie` (`Movie_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT `timeslot_ibfk_2` FOREIGN KEY (`Theater_ID`) REFERENCES `theater` (`Theater_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT `timeslot_ibfk_3` FOREIGN KEY (`DateRange_ID`) REFERENCES `daterange` (`DateRange_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
                COMMIT;

                /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
                /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
                /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;





        SQL;

        if ($conn->multi_query($sql)) {
            echo "Updated Database Successfully.";
        } else {
            echo "Failed to update database.";
        }
    }
    
?>

<!DOCTYPE html>
<html>
    <body>
        <main>
            <form id="createDatabase" name="createDatabase" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <button type="submit" name="createDatabase" value="createDatabase">Update Database</button>
            </form>

        </main>
    </body>
</html>