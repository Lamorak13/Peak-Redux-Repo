-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2026 at 03:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `peakscinemadb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_ID` int(11) NOT NULL,
  `Email` text NOT NULL,
  `AdminPassword` text NOT NULL,
  `AccessLevel` tinyint(1) NOT NULL,
  `LastName` text NOT NULL,
  `FirstName` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_ID`, `Email`, `AdminPassword`, `AccessLevel`, `LastName`, `FirstName`) VALUES
(1, 'super.admin.peakscinemas@peakscinemas.com', '$2y$10$N7Y8p/Yeilr3ja3Km9C81OMv39pBOaURy1/l7cQVJ6t2PwnPrPZRK', 2, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `Customer_ID` int(11) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` text NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `PhoneNumber` varchar(10) DEFAULT NULL,
  `CountryCode` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`Customer_ID`, `FirstName`, `LastName`, `Email`, `Password`, `PhoneNumber`, `CountryCode`) VALUES
(1, 'Guest', 'Account', NULL, NULL, NULL, NULL),
(5, 'Jerrell Nathan', 'Bantolino', 'jerrellnathan@gmail.com', '$2y$10$XcGbnv71gbciXiR6ktBBDuVyIcnLQwMySXrX2jlDXQGi8QvXoVp/q', '', ''),
(7, 'jerl', 'PixelatedCyan', 'jerllnathan@gmail.com', '$2y$10$3pUbUOoAy/.7RSmOQzPx3el2rnyjuNaRJhKA/Q13i67dnC2UMyfSC', '', ''),
(8, 'JERRELLLL', 'PixelatedCyan', 'jerrell@gmail.com', '$2y$10$ysd5h9uK3WlUpqLYPz.ZW.7994LNUnf3c9Lj6LbaIq1ODfP6mn8vK', '', ''),
(9, 'Jerrell', 'PixelatedCyan', 'whaturvy@gmail.com', '$2y$10$qwAQACeiXr1gzrogPbO6a.kzKJrkIgVjCJM35k6DxzNf6ssMV2sW2', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `daterange`
--

CREATE TABLE `daterange` (
  `DateRange_ID` int(11) NOT NULL,
  `Movie_ID` int(11) NOT NULL,
  `Theater_ID` int(11) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daterange`
--

INSERT INTO `daterange` (`DateRange_ID`, `Movie_ID`, `Theater_ID`, `StartDate`, `EndDate`) VALUES
(34, 31, 19, '2026-04-15', '2026-04-20'),
(35, 31, 20, '2026-04-25', NULL),
(36, 30, 19, '2026-04-09', '2026-04-10');

-- --------------------------------------------------------

--
-- Table structure for table `e-receipt`
--

CREATE TABLE `e-receipt` (
  `Receipt_ID` int(11) NOT NULL,
  `Payment_ID` int(11) NOT NULL,
  `DateIssued` date NOT NULL,
  `SentToEmail` varchar(100) NOT NULL,
  `ReceiptStatus` int(11) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `e-receipt`
--

INSERT INTO `e-receipt` (`Receipt_ID`, `Payment_ID`, `DateIssued`, `SentToEmail`, `ReceiptStatus`, `Status`) VALUES
(1, 1, '2026-03-13', '', 1, 1),
(2, 2, '2026-03-13', '', 1, 1),
(3, 3, '2026-03-13', '', 1, 1),
(4, 4, '2026-03-14', '', 1, 1),
(5, 5, '2026-03-14', '', 1, 1),
(6, 6, '2026-03-14', '', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `mall`
--

CREATE TABLE `mall` (
  `Mall_ID` int(11) NOT NULL,
  `MallName` tinytext NOT NULL,
  `Location` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mall`
--

INSERT INTO `mall` (`Mall_ID`, `MallName`, `Location`) VALUES
(1, 'SM Marikina', 'Marcos Highway, Calumpang, Marikina City, 1801, Marikina, Luzon Philippines');

-- --------------------------------------------------------

--
-- Table structure for table `movie`
--

CREATE TABLE `movie` (
  `Movie_ID` int(11) NOT NULL,
  `MovieName` text NOT NULL,
  `MovieDescription` mediumtext NOT NULL,
  `Genre` tinytext NOT NULL,
  `Rating` varchar(10) NOT NULL,
  `Runtime` int(11) NOT NULL,
  `MoviePoster` text NOT NULL,
  `TrailerURL` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movie`
--

INSERT INTO `movie` (`Movie_ID`, `MovieName`, `MovieDescription`, `Genre`, `Rating`, `Runtime`, `MoviePoster`, `TrailerURL`) VALUES
(30, 'It\'s TV Time!!!!!!', 'The Lord of Screens Cleaved Red by Blade', 'Comedy, Horror', 'R-13', 400, 'PeaksCinema/MoviePosters/It_s_TV_Time______.png', 'https://www.youtube.com/watch?v=F2PJbTuZlTU'),
(31, 'Caine TADC', 'Uh.. Wait', 'Adventure, Fantasy', 'R-13', 578, 'PeaksCinema/MoviePosters/Caine_TADC.png', 'https://www.youtube.com/watch?v=aAg3bwzSuLk&pp=ygUKY2FpbmUgc29uZw%3D%3D');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `Payment_ID` int(11) NOT NULL,
  `Ticket_ID` int(11) NOT NULL,
  `PaymentMethod` varchar(50) NOT NULL,
  `AmountPaid` decimal(10,2) NOT NULL,
  `PaymentDate` date NOT NULL,
  `PaymentStatus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`Payment_ID`, `Ticket_ID`, `PaymentMethod`, `AmountPaid`, `PaymentDate`, `PaymentStatus`) VALUES
(1, 4, 'paymaya', 350.00, '2026-03-13', 1),
(2, 5, 'paymaya', 350.00, '2026-03-13', 1),
(3, 6, 'paymaya', 350.00, '2026-03-13', 1),
(4, 7, 'gcash', 350.00, '2026-03-14', 1),
(5, 8, 'gcash', 350.00, '2026-03-14', 1),
(6, 9, 'gcash', 350.00, '2026-03-14', 1);

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `Receipt_ID` int(11) NOT NULL,
  `PaymentDate` date NOT NULL DEFAULT current_timestamp(),
  `Customer_ID` int(11) NOT NULL,
  `PaymentMethod` varchar(255) NOT NULL,
  `AmountPaid` int(11) NOT NULL,
  `Status` varchar(15) NOT NULL DEFAULT 'Paid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipt`
--

INSERT INTO `receipt` (`Receipt_ID`, `PaymentDate`, `Customer_ID`, `PaymentMethod`, `AmountPaid`, `Status`) VALUES
(1, '2026-04-16', 9, 'paymaya', 350, 'Paid'),
(2, '2026-04-16', 9, 'paymaya', 350, 'Paid'),
(3, '2026-04-16', 9, 'paypal', 350, 'Paid');

--
-- Triggers `receipt`
--
DELIMITER $$
CREATE TRIGGER `ReceiptNullSetAvailabilityToOne` BEFORE DELETE ON `receipt` FOR EACH ROW BEGIN
UPDATE seat_timeslot
SET SeatAvailability = 1
WHERE SeatTimeSlot_ID IN (
    SELECT SeatTimeSlot_ID
    FROM ticket
    WHERE Receipt_ID = OLD.Receipt_ID
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `refund`
--

CREATE TABLE `refund` (
  `Refund_ID` int(11) NOT NULL,
  `Customer_ID` int(11) DEFAULT NULL,
  `Receipt_ID` int(11) NOT NULL,
  `RefundReason` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `Seat_ID` int(11) NOT NULL,
  `Theater_ID` int(11) NOT NULL,
  `SeatRow` varchar(10) NOT NULL,
  `SeatColumn` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`Seat_ID`, `Theater_ID`, `SeatRow`, `SeatColumn`) VALUES
(721, 19, 'A', '10'),
(722, 19, 'A', '9'),
(723, 19, 'A', '0'),
(724, 19, 'A', '0'),
(725, 19, 'A', '8'),
(726, 19, 'A', '7'),
(727, 19, 'A', '6'),
(728, 19, 'A', '5'),
(729, 19, 'A', '4'),
(730, 19, 'A', '3'),
(731, 19, 'A', '0'),
(732, 19, 'A', '0'),
(733, 19, 'A', '2'),
(734, 19, 'A', '1'),
(735, 19, 'B', '10'),
(736, 19, 'B', '9'),
(737, 19, 'B', '0'),
(738, 19, 'B', '0'),
(739, 19, 'B', '8'),
(740, 19, 'B', '7'),
(741, 19, 'B', '6'),
(742, 19, 'B', '5'),
(743, 19, 'B', '4'),
(744, 19, 'B', '3'),
(745, 19, 'B', '0'),
(746, 19, 'B', '0'),
(747, 19, 'B', '2'),
(748, 19, 'B', '1'),
(749, 19, 'C', '10'),
(750, 19, 'C', '9'),
(751, 19, 'C', '0'),
(752, 19, 'C', '0'),
(753, 19, 'C', '8'),
(754, 19, 'C', '7'),
(755, 19, 'C', '6'),
(756, 19, 'C', '5'),
(757, 19, 'C', '4'),
(758, 19, 'C', '3'),
(759, 19, 'C', '0'),
(760, 19, 'C', '0'),
(761, 19, 'C', '2'),
(762, 19, 'C', '1'),
(763, 19, 'D', '10'),
(764, 19, 'D', '9'),
(765, 19, 'D', '0'),
(766, 19, 'D', '0'),
(767, 19, 'D', '8'),
(768, 19, 'D', '7'),
(769, 19, 'D', '6'),
(770, 19, 'D', '5'),
(771, 19, 'D', '4'),
(772, 19, 'D', '3'),
(773, 19, 'D', '0'),
(774, 19, 'D', '0'),
(775, 19, 'D', '2'),
(776, 19, 'D', '1'),
(777, 19, 'E', '10'),
(778, 19, 'E', '9'),
(779, 19, 'E', '0'),
(780, 19, 'E', '0'),
(781, 19, 'E', '8'),
(782, 19, 'E', '7'),
(783, 19, 'E', '6'),
(784, 19, 'E', '5'),
(785, 19, 'E', '4'),
(786, 19, 'E', '3'),
(787, 19, 'E', '0'),
(788, 19, 'E', '0'),
(789, 19, 'E', '2'),
(790, 19, 'E', '1'),
(791, 20, 'A', '10'),
(792, 20, 'A', '9'),
(793, 20, 'A', '0'),
(794, 20, 'A', '0'),
(795, 20, 'A', '8'),
(796, 20, 'A', '7'),
(797, 20, 'A', '6'),
(798, 20, 'A', '5'),
(799, 20, 'A', '4'),
(800, 20, 'A', '3'),
(801, 20, 'A', '0'),
(802, 20, 'A', '0'),
(803, 20, 'A', '2'),
(804, 20, 'A', '1'),
(805, 20, 'B', '10'),
(806, 20, 'B', '9'),
(807, 20, 'B', '0'),
(808, 20, 'B', '0'),
(809, 20, 'B', '8'),
(810, 20, 'B', '7'),
(811, 20, 'B', '6'),
(812, 20, 'B', '5'),
(813, 20, 'B', '4'),
(814, 20, 'B', '3'),
(815, 20, 'B', '0'),
(816, 20, 'B', '0'),
(817, 20, 'B', '2'),
(818, 20, 'B', '1'),
(819, 20, 'C', '10'),
(820, 20, 'C', '9'),
(821, 20, 'C', '0'),
(822, 20, 'C', '0'),
(823, 20, 'C', '8'),
(824, 20, 'C', '7'),
(825, 20, 'C', '6'),
(826, 20, 'C', '5'),
(827, 20, 'C', '4'),
(828, 20, 'C', '3'),
(829, 20, 'C', '0'),
(830, 20, 'C', '0'),
(831, 20, 'C', '2'),
(832, 20, 'C', '1'),
(833, 20, 'D', '10'),
(834, 20, 'D', '9'),
(835, 20, 'D', '0'),
(836, 20, 'D', '0'),
(837, 20, 'D', '8'),
(838, 20, 'D', '7'),
(839, 20, 'D', '6'),
(840, 20, 'D', '5'),
(841, 20, 'D', '4'),
(842, 20, 'D', '3'),
(843, 20, 'D', '0'),
(844, 20, 'D', '0'),
(845, 20, 'D', '2'),
(846, 20, 'D', '1'),
(847, 20, 'E', '10'),
(848, 20, 'E', '9'),
(849, 20, 'E', '0'),
(850, 20, 'E', '0'),
(851, 20, 'E', '8'),
(852, 20, 'E', '7'),
(853, 20, 'E', '6'),
(854, 20, 'E', '5'),
(855, 20, 'E', '4'),
(856, 20, 'E', '3'),
(857, 20, 'E', '0'),
(858, 20, 'E', '0'),
(859, 20, 'E', '2'),
(860, 20, 'E', '1');

-- --------------------------------------------------------

--
-- Table structure for table `seat_timeslot`
--

CREATE TABLE `seat_timeslot` (
  `SeatTimeSlot_ID` int(11) NOT NULL,
  `Seat_ID` int(11) NOT NULL,
  `TimeSlot_ID` int(11) NOT NULL,
  `SeatPrice` decimal(10,2) NOT NULL,
  `SeatAvailability` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seat_timeslot`
--

INSERT INTO `seat_timeslot` (`SeatTimeSlot_ID`, `Seat_ID`, `TimeSlot_ID`, `SeatPrice`, `SeatAvailability`) VALUES
(6791, 721, 98, 350.00, 1),
(6792, 722, 98, 350.00, 1),
(6793, 723, 98, 350.00, 1),
(6794, 724, 98, 350.00, 1),
(6795, 725, 98, 350.00, 1),
(6796, 726, 98, 350.00, 1),
(6797, 727, 98, 350.00, 1),
(6798, 728, 98, 350.00, 1),
(6799, 729, 98, 350.00, 1),
(6800, 730, 98, 350.00, 1),
(6801, 731, 98, 350.00, 1),
(6802, 732, 98, 350.00, 1),
(6803, 733, 98, 350.00, 1),
(6804, 734, 98, 350.00, 1),
(6805, 735, 98, 350.00, 1),
(6806, 736, 98, 350.00, 1),
(6807, 737, 98, 350.00, 1),
(6808, 738, 98, 350.00, 1),
(6809, 739, 98, 350.00, 1),
(6810, 740, 98, 350.00, 1),
(6811, 741, 98, 350.00, 1),
(6812, 742, 98, 350.00, 1),
(6813, 743, 98, 350.00, 1),
(6814, 744, 98, 350.00, 1),
(6815, 745, 98, 350.00, 1),
(6816, 746, 98, 350.00, 1),
(6817, 747, 98, 350.00, 1),
(6818, 748, 98, 350.00, 1),
(6819, 749, 98, 350.00, 1),
(6820, 750, 98, 350.00, 1),
(6821, 751, 98, 350.00, 1),
(6822, 752, 98, 350.00, 1),
(6823, 753, 98, 350.00, 1),
(6824, 754, 98, 350.00, 1),
(6825, 755, 98, 350.00, 1),
(6826, 756, 98, 350.00, 1),
(6827, 757, 98, 350.00, 1),
(6828, 758, 98, 350.00, 1),
(6829, 759, 98, 350.00, 1),
(6830, 760, 98, 350.00, 1),
(6831, 761, 98, 350.00, 1),
(6832, 762, 98, 350.00, 1),
(6833, 763, 98, 350.00, 1),
(6834, 764, 98, 350.00, 1),
(6835, 765, 98, 350.00, 1),
(6836, 766, 98, 350.00, 1),
(6837, 767, 98, 350.00, 1),
(6838, 768, 98, 350.00, 1),
(6839, 769, 98, 350.00, 1),
(6840, 770, 98, 350.00, 1),
(6841, 771, 98, 350.00, 1),
(6842, 772, 98, 350.00, 1),
(6843, 773, 98, 350.00, 1),
(6844, 774, 98, 350.00, 1),
(6845, 775, 98, 350.00, 1),
(6846, 776, 98, 350.00, 1),
(6847, 777, 98, 350.00, 1),
(6848, 778, 98, 350.00, 1),
(6849, 779, 98, 350.00, 1),
(6850, 780, 98, 350.00, 1),
(6851, 781, 98, 350.00, 1),
(6852, 782, 98, 350.00, 1),
(6853, 783, 98, 350.00, 1),
(6854, 784, 98, 350.00, 1),
(6855, 785, 98, 350.00, 1),
(6856, 786, 98, 350.00, 0),
(6857, 787, 98, 350.00, 1),
(6858, 788, 98, 350.00, 1),
(6859, 789, 98, 350.00, 0),
(6860, 790, 98, 350.00, 0),
(6861, 721, 99, 350.00, 1),
(6862, 722, 99, 350.00, 1),
(6863, 723, 99, 350.00, 1),
(6864, 724, 99, 350.00, 1),
(6865, 725, 99, 350.00, 1),
(6866, 726, 99, 350.00, 1),
(6867, 727, 99, 350.00, 1),
(6868, 728, 99, 350.00, 1),
(6869, 729, 99, 350.00, 1),
(6870, 730, 99, 350.00, 1),
(6871, 731, 99, 350.00, 1),
(6872, 732, 99, 350.00, 1),
(6873, 733, 99, 350.00, 1),
(6874, 734, 99, 350.00, 1),
(6875, 735, 99, 350.00, 1),
(6876, 736, 99, 350.00, 1),
(6877, 737, 99, 350.00, 1),
(6878, 738, 99, 350.00, 1),
(6879, 739, 99, 350.00, 1),
(6880, 740, 99, 350.00, 1),
(6881, 741, 99, 350.00, 1),
(6882, 742, 99, 350.00, 1),
(6883, 743, 99, 350.00, 1),
(6884, 744, 99, 350.00, 1),
(6885, 745, 99, 350.00, 1),
(6886, 746, 99, 350.00, 1),
(6887, 747, 99, 350.00, 1),
(6888, 748, 99, 350.00, 1),
(6889, 749, 99, 350.00, 1),
(6890, 750, 99, 350.00, 1),
(6891, 751, 99, 350.00, 1),
(6892, 752, 99, 350.00, 1),
(6893, 753, 99, 350.00, 1),
(6894, 754, 99, 350.00, 1),
(6895, 755, 99, 350.00, 1),
(6896, 756, 99, 350.00, 1),
(6897, 757, 99, 350.00, 1),
(6898, 758, 99, 350.00, 1),
(6899, 759, 99, 350.00, 1),
(6900, 760, 99, 350.00, 1),
(6901, 761, 99, 350.00, 1),
(6902, 762, 99, 350.00, 1),
(6903, 763, 99, 350.00, 1),
(6904, 764, 99, 350.00, 1),
(6905, 765, 99, 350.00, 1),
(6906, 766, 99, 350.00, 1),
(6907, 767, 99, 350.00, 1),
(6908, 768, 99, 350.00, 1),
(6909, 769, 99, 350.00, 1),
(6910, 770, 99, 350.00, 1),
(6911, 771, 99, 350.00, 1),
(6912, 772, 99, 350.00, 1),
(6913, 773, 99, 350.00, 1),
(6914, 774, 99, 350.00, 1),
(6915, 775, 99, 350.00, 1),
(6916, 776, 99, 350.00, 1),
(6917, 777, 99, 350.00, 1),
(6918, 778, 99, 350.00, 1),
(6919, 779, 99, 350.00, 1),
(6920, 780, 99, 350.00, 1),
(6921, 781, 99, 350.00, 1),
(6922, 782, 99, 350.00, 1),
(6923, 783, 99, 350.00, 1),
(6924, 784, 99, 350.00, 1),
(6925, 785, 99, 350.00, 1),
(6926, 786, 99, 350.00, 1),
(6927, 787, 99, 350.00, 1),
(6928, 788, 99, 350.00, 1),
(6929, 789, 99, 350.00, 1),
(6930, 790, 99, 350.00, 1),
(6931, 721, 100, 350.00, 1),
(6932, 722, 100, 350.00, 1),
(6933, 723, 100, 350.00, 1),
(6934, 724, 100, 350.00, 1),
(6935, 725, 100, 350.00, 1),
(6936, 726, 100, 350.00, 1),
(6937, 727, 100, 350.00, 1),
(6938, 728, 100, 350.00, 1),
(6939, 729, 100, 350.00, 1),
(6940, 730, 100, 350.00, 1),
(6941, 731, 100, 350.00, 1),
(6942, 732, 100, 350.00, 1),
(6943, 733, 100, 350.00, 1),
(6944, 734, 100, 350.00, 1),
(6945, 735, 100, 350.00, 1),
(6946, 736, 100, 350.00, 1),
(6947, 737, 100, 350.00, 1),
(6948, 738, 100, 350.00, 1),
(6949, 739, 100, 350.00, 1),
(6950, 740, 100, 350.00, 1),
(6951, 741, 100, 350.00, 1),
(6952, 742, 100, 350.00, 1),
(6953, 743, 100, 350.00, 1),
(6954, 744, 100, 350.00, 1),
(6955, 745, 100, 350.00, 1),
(6956, 746, 100, 350.00, 1),
(6957, 747, 100, 350.00, 1),
(6958, 748, 100, 350.00, 1),
(6959, 749, 100, 350.00, 1),
(6960, 750, 100, 350.00, 1),
(6961, 751, 100, 350.00, 1),
(6962, 752, 100, 350.00, 1),
(6963, 753, 100, 350.00, 1),
(6964, 754, 100, 350.00, 1),
(6965, 755, 100, 350.00, 1),
(6966, 756, 100, 350.00, 1),
(6967, 757, 100, 350.00, 1),
(6968, 758, 100, 350.00, 1),
(6969, 759, 100, 350.00, 1),
(6970, 760, 100, 350.00, 1),
(6971, 761, 100, 350.00, 1),
(6972, 762, 100, 350.00, 1),
(6973, 763, 100, 350.00, 1),
(6974, 764, 100, 350.00, 1),
(6975, 765, 100, 350.00, 1),
(6976, 766, 100, 350.00, 1),
(6977, 767, 100, 350.00, 1),
(6978, 768, 100, 350.00, 1),
(6979, 769, 100, 350.00, 1),
(6980, 770, 100, 350.00, 1),
(6981, 771, 100, 350.00, 1),
(6982, 772, 100, 350.00, 1),
(6983, 773, 100, 350.00, 1),
(6984, 774, 100, 350.00, 1),
(6985, 775, 100, 350.00, 1),
(6986, 776, 100, 350.00, 1),
(6987, 777, 100, 350.00, 1),
(6988, 778, 100, 350.00, 1),
(6989, 779, 100, 350.00, 1),
(6990, 780, 100, 350.00, 1),
(6991, 781, 100, 350.00, 1),
(6992, 782, 100, 350.00, 1),
(6993, 783, 100, 350.00, 1),
(6994, 784, 100, 350.00, 1),
(6995, 785, 100, 350.00, 1),
(6996, 786, 100, 350.00, 1),
(6997, 787, 100, 350.00, 1),
(6998, 788, 100, 350.00, 1),
(6999, 789, 100, 350.00, 1),
(7000, 790, 100, 350.00, 1),
(7001, 721, 101, 350.00, 1),
(7002, 722, 101, 350.00, 1),
(7003, 723, 101, 350.00, 1),
(7004, 724, 101, 350.00, 1),
(7005, 725, 101, 350.00, 1),
(7006, 726, 101, 350.00, 1),
(7007, 727, 101, 350.00, 1),
(7008, 728, 101, 350.00, 1),
(7009, 729, 101, 350.00, 1),
(7010, 730, 101, 350.00, 1),
(7011, 731, 101, 350.00, 1),
(7012, 732, 101, 350.00, 1),
(7013, 733, 101, 350.00, 1),
(7014, 734, 101, 350.00, 1),
(7015, 735, 101, 350.00, 1),
(7016, 736, 101, 350.00, 1),
(7017, 737, 101, 350.00, 1),
(7018, 738, 101, 350.00, 1),
(7019, 739, 101, 350.00, 1),
(7020, 740, 101, 350.00, 1),
(7021, 741, 101, 350.00, 1),
(7022, 742, 101, 350.00, 1),
(7023, 743, 101, 350.00, 1),
(7024, 744, 101, 350.00, 1),
(7025, 745, 101, 350.00, 1),
(7026, 746, 101, 350.00, 1),
(7027, 747, 101, 350.00, 1),
(7028, 748, 101, 350.00, 1),
(7029, 749, 101, 350.00, 1),
(7030, 750, 101, 350.00, 1),
(7031, 751, 101, 350.00, 1),
(7032, 752, 101, 350.00, 1),
(7033, 753, 101, 350.00, 1),
(7034, 754, 101, 350.00, 1),
(7035, 755, 101, 350.00, 1),
(7036, 756, 101, 350.00, 1),
(7037, 757, 101, 350.00, 1),
(7038, 758, 101, 350.00, 1),
(7039, 759, 101, 350.00, 1),
(7040, 760, 101, 350.00, 1),
(7041, 761, 101, 350.00, 1),
(7042, 762, 101, 350.00, 1),
(7043, 763, 101, 350.00, 1),
(7044, 764, 101, 350.00, 1),
(7045, 765, 101, 350.00, 1),
(7046, 766, 101, 350.00, 1),
(7047, 767, 101, 350.00, 1),
(7048, 768, 101, 350.00, 1),
(7049, 769, 101, 350.00, 1),
(7050, 770, 101, 350.00, 1),
(7051, 771, 101, 350.00, 1),
(7052, 772, 101, 350.00, 1),
(7053, 773, 101, 350.00, 1),
(7054, 774, 101, 350.00, 1),
(7055, 775, 101, 350.00, 1),
(7056, 776, 101, 350.00, 1),
(7057, 777, 101, 350.00, 1),
(7058, 778, 101, 350.00, 1),
(7059, 779, 101, 350.00, 1),
(7060, 780, 101, 350.00, 1),
(7061, 781, 101, 350.00, 1),
(7062, 782, 101, 350.00, 1),
(7063, 783, 101, 350.00, 1),
(7064, 784, 101, 350.00, 1),
(7065, 785, 101, 350.00, 1),
(7066, 786, 101, 350.00, 1),
(7067, 787, 101, 350.00, 1),
(7068, 788, 101, 350.00, 1),
(7069, 789, 101, 350.00, 1),
(7070, 790, 101, 350.00, 1),
(7071, 791, 102, 349.00, 1),
(7072, 792, 102, 349.00, 1),
(7073, 793, 102, 349.00, 1),
(7074, 794, 102, 349.00, 1),
(7075, 795, 102, 349.00, 1),
(7076, 796, 102, 349.00, 1),
(7077, 797, 102, 349.00, 1),
(7078, 798, 102, 349.00, 1),
(7079, 799, 102, 349.00, 1),
(7080, 800, 102, 349.00, 1),
(7081, 801, 102, 349.00, 1),
(7082, 802, 102, 349.00, 1),
(7083, 803, 102, 349.00, 1),
(7084, 804, 102, 349.00, 1),
(7085, 805, 102, 349.00, 1),
(7086, 806, 102, 349.00, 1),
(7087, 807, 102, 349.00, 1),
(7088, 808, 102, 349.00, 1),
(7089, 809, 102, 349.00, 1),
(7090, 810, 102, 349.00, 1),
(7091, 811, 102, 349.00, 1),
(7092, 812, 102, 349.00, 1),
(7093, 813, 102, 349.00, 1),
(7094, 814, 102, 349.00, 1),
(7095, 815, 102, 349.00, 1),
(7096, 816, 102, 349.00, 1),
(7097, 817, 102, 349.00, 1),
(7098, 818, 102, 349.00, 1),
(7099, 819, 102, 349.00, 1),
(7100, 820, 102, 349.00, 1),
(7101, 821, 102, 349.00, 1),
(7102, 822, 102, 349.00, 1),
(7103, 823, 102, 349.00, 1),
(7104, 824, 102, 349.00, 1),
(7105, 825, 102, 349.00, 1),
(7106, 826, 102, 349.00, 1),
(7107, 827, 102, 349.00, 1),
(7108, 828, 102, 349.00, 1),
(7109, 829, 102, 349.00, 1),
(7110, 830, 102, 349.00, 1),
(7111, 831, 102, 349.00, 1),
(7112, 832, 102, 349.00, 1),
(7113, 833, 102, 349.00, 1),
(7114, 834, 102, 349.00, 1),
(7115, 835, 102, 349.00, 1),
(7116, 836, 102, 349.00, 1),
(7117, 837, 102, 349.00, 1),
(7118, 838, 102, 349.00, 1),
(7119, 839, 102, 349.00, 1),
(7120, 840, 102, 349.00, 1),
(7121, 841, 102, 349.00, 1),
(7122, 842, 102, 349.00, 1),
(7123, 843, 102, 349.00, 1),
(7124, 844, 102, 349.00, 1),
(7125, 845, 102, 349.00, 1),
(7126, 846, 102, 349.00, 1),
(7127, 847, 102, 349.00, 1),
(7128, 848, 102, 349.00, 1),
(7129, 849, 102, 349.00, 1),
(7130, 850, 102, 349.00, 1),
(7131, 851, 102, 349.00, 1),
(7132, 852, 102, 349.00, 1),
(7133, 853, 102, 349.00, 1),
(7134, 854, 102, 349.00, 1),
(7135, 855, 102, 349.00, 1),
(7136, 856, 102, 349.00, 1),
(7137, 857, 102, 349.00, 1),
(7138, 858, 102, 349.00, 1),
(7139, 859, 102, 349.00, 1),
(7140, 860, 102, 349.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `theater`
--

CREATE TABLE `theater` (
  `Theater_ID` int(11) NOT NULL,
  `TheaterName` varchar(100) NOT NULL,
  `TotalSeats` int(11) NOT NULL,
  `TheaterType` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theater`
--

INSERT INTO `theater` (`Theater_ID`, `TheaterName`, `TotalSeats`, `TheaterType`) VALUES
(19, 'Director\'s Club 1', 50, 'Director\'s Club'),
(20, 'Regular 1', 50, 'Regular');

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `Ticket_ID` int(11) NOT NULL,
  `Customer_ID` int(11) NOT NULL,
  `SeatTimeSlot_ID` int(11) NOT NULL,
  `Receipt_ID` int(11) NOT NULL,
  `Price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket`
--

INSERT INTO `ticket` (`Ticket_ID`, `Customer_ID`, `SeatTimeSlot_ID`, `Receipt_ID`, `Price`) VALUES
(2, 9, 6860, 0, 350),
(4, 9, 6860, 1, 350),
(5, 9, 6859, 2, 350),
(6, 9, 6856, 3, 350);

-- --------------------------------------------------------

--
-- Table structure for table `timeslot`
--

CREATE TABLE `timeslot` (
  `TimeSlot_ID` int(11) NOT NULL,
  `StartTime` time NOT NULL,
  `Date` date NOT NULL,
  `ScreeningType` varchar(10) NOT NULL,
  `Movie_ID` int(11) NOT NULL,
  `Theater_ID` int(11) NOT NULL,
  `DateRange_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timeslot`
--

INSERT INTO `timeslot` (`TimeSlot_ID`, `StartTime`, `Date`, `ScreeningType`, `Movie_ID`, `Theater_ID`, `DateRange_ID`) VALUES
(98, '12:25:00', '2026-04-17', '2D', 31, 19, 34),
(99, '12:25:00', '2026-04-18', '2D', 31, 19, 34),
(100, '12:25:00', '2026-04-19', '2D', 31, 19, 34),
(101, '12:25:00', '2026-04-20', '2D', 31, 19, 34),
(102, '12:30:00', '2026-04-25', '2D', 31, 20, 35);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_ID`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`Customer_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `daterange`
--
ALTER TABLE `daterange`
  ADD PRIMARY KEY (`DateRange_ID`),
  ADD KEY `Movie_ID` (`Movie_ID`),
  ADD KEY `Theater_ID` (`Theater_ID`);

--
-- Indexes for table `e-receipt`
--
ALTER TABLE `e-receipt`
  ADD PRIMARY KEY (`Receipt_ID`);

--
-- Indexes for table `mall`
--
ALTER TABLE `mall`
  ADD PRIMARY KEY (`Mall_ID`),
  ADD UNIQUE KEY `MallName` (`MallName`) USING HASH;

--
-- Indexes for table `movie`
--
ALTER TABLE `movie`
  ADD PRIMARY KEY (`Movie_ID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`Payment_ID`);

--
-- Indexes for table `receipt`
--
ALTER TABLE `receipt`
  ADD PRIMARY KEY (`Receipt_ID`) USING BTREE,
  ADD KEY `Customer_ID` (`Customer_ID`);

--
-- Indexes for table `refund`
--
ALTER TABLE `refund`
  ADD PRIMARY KEY (`Refund_ID`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`Seat_ID`),
  ADD KEY `Theater_ID` (`Theater_ID`);

--
-- Indexes for table `seat_timeslot`
--
ALTER TABLE `seat_timeslot`
  ADD PRIMARY KEY (`SeatTimeSlot_ID`),
  ADD UNIQUE KEY `uniq_seat_timeslot` (`Seat_ID`,`TimeSlot_ID`),
  ADD KEY `TimeSlot_ID` (`TimeSlot_ID`);

--
-- Indexes for table `theater`
--
ALTER TABLE `theater`
  ADD PRIMARY KEY (`Theater_ID`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`Ticket_ID`),
  ADD KEY `Customer_ID` (`Customer_ID`),
  ADD KEY `Receipt_ID` (`Receipt_ID`),
  ADD KEY `SeatTimeSlot_ID` (`SeatTimeSlot_ID`);

--
-- Indexes for table `timeslot`
--
ALTER TABLE `timeslot`
  ADD PRIMARY KEY (`TimeSlot_ID`),
  ADD KEY `Movie_ID` (`Movie_ID`),
  ADD KEY `Theater_ID` (`Theater_ID`),
  ADD KEY `DateRange_ID` (`DateRange_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `Admin_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `Customer_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `daterange`
--
ALTER TABLE `daterange`
  MODIFY `DateRange_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `e-receipt`
--
ALTER TABLE `e-receipt`
  MODIFY `Receipt_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `mall`
--
ALTER TABLE `mall`
  MODIFY `Mall_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `movie`
--
ALTER TABLE `movie`
  MODIFY `Movie_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `receipt`
--
ALTER TABLE `receipt`
  MODIFY `Receipt_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `refund`
--
ALTER TABLE `refund`
  MODIFY `Refund_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `Seat_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1001;

--
-- AUTO_INCREMENT for table `seat_timeslot`
--
ALTER TABLE `seat_timeslot`
  MODIFY `SeatTimeSlot_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7281;

--
-- AUTO_INCREMENT for table `theater`
--
ALTER TABLE `theater`
  MODIFY `Theater_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `Ticket_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `timeslot`
--
ALTER TABLE `timeslot`
  MODIFY `TimeSlot_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `daterange`
--
ALTER TABLE `daterange`
  ADD CONSTRAINT `daterange_ibfk_1` FOREIGN KEY (`Movie_ID`) REFERENCES `movie` (`Movie_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `daterange_ibfk_2` FOREIGN KEY (`Theater_ID`) REFERENCES `theater` (`Theater_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `receipt`
--
ALTER TABLE `receipt`
  ADD CONSTRAINT `receipt_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`Theater_ID`) REFERENCES `theater` (`Theater_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seat_timeslot`
--
ALTER TABLE `seat_timeslot`
  ADD CONSTRAINT `seat_timeslot_ibfk_1` FOREIGN KEY (`TimeSlot_ID`) REFERENCES `timeslot` (`TimeSlot_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`Receipt_ID`) REFERENCES `receipt` (`Receipt_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_ibfk_3` FOREIGN KEY (`SeatTimeSlot_ID`) REFERENCES `seat_timeslot` (`SeatTimeSlot_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `timeslot`
--
ALTER TABLE `timeslot`
  ADD CONSTRAINT `timeslot_ibfk_1` FOREIGN KEY (`DateRange_ID`) REFERENCES `daterange` (`DateRange_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `timeslot_ibfk_2` FOREIGN KEY (`Theater_ID`) REFERENCES `theater` (`Theater_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `timeslot_ibfk_3` FOREIGN KEY (`Movie_ID`) REFERENCES `movie` (`Movie_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `DeleteOldDates` ON SCHEDULE EVERY 1 DAY STARTS '2026-04-16 08:21:15' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM timeslot WHERE TIMESTAMP(Date, StartTime) < NOW()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
