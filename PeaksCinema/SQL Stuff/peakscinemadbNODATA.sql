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

-- --------------------------------------------------------

--
-- Table structure for table `mall`
--

CREATE TABLE `mall` (
  `Mall_ID` int(11) NOT NULL,
  `MallName` tinytext NOT NULL,
  `Location` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `Admin_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `Customer_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daterange`
--
ALTER TABLE `daterange`
  MODIFY `DateRange_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `e-receipt`
--
ALTER TABLE `e-receipt`
  MODIFY `Receipt_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mall`
--
ALTER TABLE `mall`
  MODIFY `Mall_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `movie`
--
ALTER TABLE `movie`
  MODIFY `Movie_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receipt`
--
ALTER TABLE `receipt`
  MODIFY `Receipt_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refund`
--
ALTER TABLE `refund`
  MODIFY `Refund_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `Seat_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seat_timeslot`
--
ALTER TABLE `seat_timeslot`
  MODIFY `SeatTimeSlot_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `theater`
--
ALTER TABLE `theater`
  MODIFY `Theater_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `Ticket_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `timeslot`
--
ALTER TABLE `timeslot`
  MODIFY `TimeSlot_ID` int(11) NOT NULL AUTO_INCREMENT;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
