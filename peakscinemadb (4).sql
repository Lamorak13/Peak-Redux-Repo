-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2026 at 09:53 AM
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
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE `customer` (
  `Customer_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `PhoneNumber` varchar(10) NOT NULL,
  `CountryCode` varchar(4) NOT NULL,
  `PaymentMethod` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `customer`
--

TRUNCATE TABLE `customer`;
-- --------------------------------------------------------

--
-- Table structure for table `daterange`
--

DROP TABLE IF EXISTS `daterange`;
CREATE TABLE `daterange` (
  `DateRange_ID` int(11) NOT NULL,
  `Movie_ID` int(11) NOT NULL,
  `Theater_ID` int(11) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `daterange`
--

TRUNCATE TABLE `daterange`;
--
-- Dumping data for table `daterange`
--

INSERT INTO `daterange` (`DateRange_ID`, `Movie_ID`, `Theater_ID`, `StartDate`, `EndDate`) VALUES
(20922, 1, 13, '2026-03-25', '2026-03-30');

-- --------------------------------------------------------

--
-- Table structure for table `e-receipt`
--

DROP TABLE IF EXISTS `e-receipt`;
CREATE TABLE `e-receipt` (
  `Receipt_ID` int(11) NOT NULL,
  `PaymentID` int(11) NOT NULL,
  `DateIssued` date NOT NULL,
  `SentToEmail` varchar(100) NOT NULL,
  `ReceiptStatus` int(11) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `e-receipt`
--

TRUNCATE TABLE `e-receipt`;
-- --------------------------------------------------------

--
-- Table structure for table `mall`
--

DROP TABLE IF EXISTS `mall`;
CREATE TABLE `mall` (
  `Mall_ID` int(11) NOT NULL,
  `MallName` tinytext NOT NULL,
  `Location` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `mall`
--

TRUNCATE TABLE `mall`;
--
-- Dumping data for table `mall`
--

INSERT INTO `mall` (`Mall_ID`, `MallName`, `Location`) VALUES
(1, 'SM Marikina', 'Marcos Highway, Calumpang, Marikina City, 1801, Marikina, Luzon Philippines');

-- --------------------------------------------------------

--
-- Table structure for table `movie`
--

DROP TABLE IF EXISTS `movie`;
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

--
-- Truncate table before insert `movie`
--

TRUNCATE TABLE `movie`;
--
-- Dumping data for table `movie`
--

INSERT INTO `movie` (`Movie_ID`, `MovieName`, `MovieDescription`, `Genre`, `Rating`, `Runtime`, `MoviePoster`, `MovieAvailability`, `TrailerURL`, `Price`) VALUES
(1, 'Superman', 'Superman must reconcile his alien Kryptonian heritage with his human upbringing as reporter Clark Kent. As the embodiment of truth, justice and the human way he soon finds himself in a world that views these as old-fashioned.\n\n', 'Superhero, Action', 'PG', 129, 'PeaksCinema/MoviePosters/Superman.png', 'Now Showing', 'https://www.youtube.com/watch?v=Ox8ZLF6cGM0', 350);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment` (
  `Payment_ID` int(11) NOT NULL,
  `Ticket_ID` int(11) NOT NULL,
  `PaymentMethod` varchar(50) NOT NULL,
  `AmountPaid` decimal(10,2) NOT NULL,
  `PaymentDate` date NOT NULL,
  `PaymentStatus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `payment`
--

TRUNCATE TABLE `payment`;
-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

DROP TABLE IF EXISTS `seats`;
CREATE TABLE `seats` (
  `Seat_ID` int(11) NOT NULL,
  `SeatRow` varchar(10) NOT NULL,
  `SeatColumn` varchar(10) NOT NULL,
  `Theater_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `seats`
--

TRUNCATE TABLE `seats`;
-- --------------------------------------------------------

--
-- Table structure for table `seat_timeslot`
--

DROP TABLE IF EXISTS `seat_timeslot`;
CREATE TABLE `seat_timeslot` (
  `Seat_ID` int(11) NOT NULL,
  `TimeSlot_ID` int(11) NOT NULL,
  `SeatPrice` int(11) NOT NULL,
  `SeatAvailability` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `seat_timeslot`
--

TRUNCATE TABLE `seat_timeslot`;
-- --------------------------------------------------------

--
-- Table structure for table `theater`
--

DROP TABLE IF EXISTS `theater`;
CREATE TABLE `theater` (
  `Theater_ID` int(11) NOT NULL,
  `Mall_ID` int(11) NOT NULL,
  `TheaterName` varchar(100) NOT NULL,
  `TotalSeats` int(11) NOT NULL,
  `TheaterType` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `theater`
--

TRUNCATE TABLE `theater`;
-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

DROP TABLE IF EXISTS `ticket`;
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

--
-- Truncate table before insert `ticket`
--

TRUNCATE TABLE `ticket`;
-- --------------------------------------------------------

--
-- Table structure for table `timeslot`
--

DROP TABLE IF EXISTS `timeslot`;
CREATE TABLE `timeslot` (
  `TimeSlot_ID` int(11) NOT NULL,
  `StartTime` time NOT NULL,
  `Date` date NOT NULL,
  `ScreeningType` varchar(5) NOT NULL,
  `Movie_ID` int(11) NOT NULL,
  `Theater_ID` int(11) NOT NULL,
  `DateRange_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `timeslot`
--

TRUNCATE TABLE `timeslot`;
--
-- Indexes for dumped tables
--

--
-- Indexes for table `daterange`
--
ALTER TABLE `daterange`
  ADD PRIMARY KEY (`DateRange_ID`);

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
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`Seat_ID`),
  ADD KEY `Theater_ID` (`Theater_ID`);

--
-- Indexes for table `seat_timeslot`
--
ALTER TABLE `seat_timeslot`
  ADD PRIMARY KEY (`Seat_ID`,`TimeSlot_ID`),
  ADD KEY `TimeSlot_ID` (`TimeSlot_ID`);

--
-- Indexes for table `theater`
--
ALTER TABLE `theater`
  ADD PRIMARY KEY (`Theater_ID`),
  ADD KEY `Mall_ID` (`Mall_ID`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`Ticket_ID`);

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
-- AUTO_INCREMENT for table `daterange`
--
ALTER TABLE `daterange`
  MODIFY `DateRange_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20923;

--
-- AUTO_INCREMENT for table `mall`
--
ALTER TABLE `mall`
  MODIFY `Mall_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `movie`
--
ALTER TABLE `movie`
  MODIFY `Movie_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `Seat_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `theater`
--
ALTER TABLE `theater`
  MODIFY `Theater_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `Ticket_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `timeslot`
--
ALTER TABLE `timeslot`
  MODIFY `TimeSlot_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`Theater_ID`) REFERENCES `theater` (`Theater_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seat_timeslot`
--
ALTER TABLE `seat_timeslot`
  ADD CONSTRAINT `seat_timeslot_ibfk_1` FOREIGN KEY (`Seat_ID`) REFERENCES `seats` (`Seat_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `seat_timeslot_ibfk_2` FOREIGN KEY (`TimeSlot_ID`) REFERENCES `timeslot` (`TimeSlot_ID`) ON DELETE CASCADE;

--
-- Constraints for table `theater`
--
ALTER TABLE `theater`
  ADD CONSTRAINT `theater_ibfk_1` FOREIGN KEY (`Mall_ID`) REFERENCES `mall` (`Mall_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `timeslot`
--
ALTER TABLE `timeslot`
  ADD CONSTRAINT `timeslot_ibfk_1` FOREIGN KEY (`Movie_ID`) REFERENCES `movie` (`Movie_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `timeslot_ibfk_2` FOREIGN KEY (`Theater_ID`) REFERENCES `theater` (`Theater_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `timeslot_ibfk_3` FOREIGN KEY (`DateRange_ID`) REFERENCES `daterange` (`DateRange_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
