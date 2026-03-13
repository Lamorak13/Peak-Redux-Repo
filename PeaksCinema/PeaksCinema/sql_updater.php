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
                CREATE DATABASE IF NOT EXISTS `peakscinemadb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
                USE `peakscinemadb`;

                DROP TABLE IF EXISTS `customer`;
                CREATE TABLE IF NOT EXISTS `customer` (
                `Customer_ID` int(11) NOT NULL,
                `Name` varchar(100) NOT NULL,
                `Email` varchar(100) NOT NULL,
                `Password` varchar(255) NOT NULL,
                `PhoneNumber` varchar(10) NOT NULL,
                `CountryCode` varchar(4) NOT NULL,
                `PaymentMethod` tinytext NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                TRUNCATE TABLE `customer`;
                DROP TABLE IF EXISTS `daterange`;
                CREATE TABLE IF NOT EXISTS `daterange` (
                `DateRange_ID` int(11) NOT NULL AUTO_INCREMENT,
                `Movie_ID` int(11) NOT NULL,
                `Theater_ID` int(11) NOT NULL,
                `StartDate` date NOT NULL,
                `EndDate` date NOT NULL,
                PRIMARY KEY (`DateRange_ID`)
                ) ENGINE=InnoDB AUTO_INCREMENT=20923 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                TRUNCATE TABLE `daterange`;
                INSERT INTO `daterange` (`DateRange_ID`, `Movie_ID`, `Theater_ID`, `StartDate`, `EndDate`) VALUES
                (20922, 1, 13, '2026-03-25', '2026-03-30');

                DROP TABLE IF EXISTS `e-receipt`;
                CREATE TABLE IF NOT EXISTS `e-receipt` (
                `Receipt_ID` int(11) NOT NULL,
                `PaymentID` int(11) NOT NULL,
                `DateIssued` date NOT NULL,
                `SentToEmail` varchar(100) NOT NULL,
                `ReceiptStatus` int(11) NOT NULL,
                `Status` int(11) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                TRUNCATE TABLE `e-receipt`;
                DROP TABLE IF EXISTS `mall`;
                CREATE TABLE IF NOT EXISTS `mall` (
                `Mall_ID` int(11) NOT NULL AUTO_INCREMENT,
                `MallName` tinytext NOT NULL,
                `Location` tinytext NOT NULL,
                PRIMARY KEY (`Mall_ID`),
                UNIQUE KEY `MallName` (`MallName`) USING HASH
                ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                TRUNCATE TABLE `mall`;
                INSERT INTO `mall` (`Mall_ID`, `MallName`, `Location`) VALUES
                (1, 'SM Marikina', 'Marcos Highway, Calumpang, Marikina City, 1801, Marikina, Luzon Philippines');

                DROP TABLE IF EXISTS `movie`;
                CREATE TABLE IF NOT EXISTS `movie` (
                `Movie_ID` int(11) NOT NULL AUTO_INCREMENT,
                `MovieName` text NOT NULL,
                `MovieDescription` mediumtext NOT NULL,
                `Genre` tinytext NOT NULL,
                `Rating` varchar(10) NOT NULL,
                `Runtime` int(11) NOT NULL,
                `MoviePoster` text NOT NULL,
                `MovieAvailability` tinytext NOT NULL,
                `TrailerURL` text NOT NULL,
                `Price` int(11) NOT NULL,
                PRIMARY KEY (`Movie_ID`)
                ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                TRUNCATE TABLE `movie`;
                INSERT INTO `movie` (`Movie_ID`, `MovieName`, `MovieDescription`, `Genre`, `Rating`, `Runtime`, `MoviePoster`, `MovieAvailability`, `TrailerURL`, `Price`) VALUES
                (1, 'Superman', 'Superman must reconcile his alien Kryptonian heritage with his human upbringing as reporter Clark Kent. As the embodiment of truth, justice and the human way he soon finds himself in a world that views these as old-fashioned.\n\n', 'Superhero, Action', 'PG', 129, 'PeaksCinema/MoviePosters/Superman.png', 'Now Showing', 'https://www.youtube.com/watch?v=Ox8ZLF6cGM0', 350);

                DROP TABLE IF EXISTS `payment`;
                CREATE TABLE IF NOT EXISTS `payment` (
                `Payment_ID` int(11) NOT NULL AUTO_INCREMENT,
                `Ticket_ID` int(11) NOT NULL,
                `PaymentMethod` varchar(50) NOT NULL,
                `AmountPaid` decimal(10,2) NOT NULL,
                `PaymentDate` date NOT NULL,
                `PaymentStatus` int(11) NOT NULL,
                PRIMARY KEY (`Payment_ID`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                TRUNCATE TABLE `payment`;
                DROP TABLE IF EXISTS `seats`;
                CREATE TABLE IF NOT EXISTS `seats` (
                `Seat_ID` int(11) NOT NULL AUTO_INCREMENT,
                `SeatRow` varchar(10) NOT NULL,
                `SeatColumn` varchar(10) NOT NULL,
                `Theater_ID` int(11) NOT NULL,
                PRIMARY KEY (`Seat_ID`),
                KEY `Theater_ID` (`Theater_ID`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                TRUNCATE TABLE `seats`;
                DROP TABLE IF EXISTS `seat_timeslot`;
                CREATE TABLE IF NOT EXISTS `seat_timeslot` (
                `Seat_ID` int(11) NOT NULL,
                `TimeSlot_ID` int(11) NOT NULL,
                `SeatPrice` int(11) NOT NULL,
                `SeatAvailability` tinyint(4) NOT NULL DEFAULT 1,
                PRIMARY KEY (`Seat_ID`,`TimeSlot_ID`),
                KEY `TimeSlot_ID` (`TimeSlot_ID`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                TRUNCATE TABLE `seat_timeslot`;
                DROP TABLE IF EXISTS `theater`;
                CREATE TABLE IF NOT EXISTS `theater` (
                `Theater_ID` int(11) NOT NULL AUTO_INCREMENT,
                `Mall_ID` int(11) NOT NULL,
                `TheaterName` varchar(100) NOT NULL,
                `TotalSeats` int(11) NOT NULL,
                `TheaterType` varchar(50) NOT NULL,
                PRIMARY KEY (`Theater_ID`),
                KEY `Mall_ID` (`Mall_ID`)
                ) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                TRUNCATE TABLE `theater`;
                DROP TABLE IF EXISTS `ticket`;
                CREATE TABLE IF NOT EXISTS `ticket` (
                `Ticket_ID` int(11) NOT NULL AUTO_INCREMENT,
                `Seat_ID` int(11) NOT NULL,
                `Customer_ID` int(11) NOT NULL,
                `Movie_ID` int(11) NOT NULL,
                `TimeSlot_ID` int(11) NOT NULL,
                `Price` decimal(10,2) NOT NULL,
                `Status` int(11) NOT NULL,
                `DateTime` datetime NOT NULL,
                PRIMARY KEY (`Ticket_ID`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                TRUNCATE TABLE `ticket`;
                DROP TABLE IF EXISTS `timeslot`;
                CREATE TABLE IF NOT EXISTS `timeslot` (
                `TimeSlot_ID` int(11) NOT NULL AUTO_INCREMENT,
                `StartTime` time NOT NULL,
                `Date` date NOT NULL,
                `ScreeningType` varchar(5) NOT NULL,
                `Movie_ID` int(11) NOT NULL,
                `Theater_ID` int(11) NOT NULL,
                `DateRange_ID` int(11) NOT NULL,
                PRIMARY KEY (`TimeSlot_ID`),
                KEY `Movie_ID` (`Movie_ID`),
                KEY `Theater_ID` (`Theater_ID`),
                KEY `DateRange_ID` (`DateRange_ID`)
                ) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                TRUNCATE TABLE `timeslot`;

                ALTER TABLE `seats`
                ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`Theater_ID`) REFERENCES `theater` (`Theater_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

                ALTER TABLE `seat_timeslot`
                ADD CONSTRAINT `seat_timeslot_ibfk_1` FOREIGN KEY (`Seat_ID`) REFERENCES `seats` (`Seat_ID`) ON DELETE CASCADE,
                ADD CONSTRAINT `seat_timeslot_ibfk_2` FOREIGN KEY (`TimeSlot_ID`) REFERENCES `timeslot` (`TimeSlot_ID`) ON DELETE CASCADE;

                ALTER TABLE `theater`
                ADD CONSTRAINT `theater_ibfk_1` FOREIGN KEY (`Mall_ID`) REFERENCES `mall` (`Mall_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

                ALTER TABLE `timeslot`
                ADD CONSTRAINT `timeslot_ibfk_1` FOREIGN KEY (`Movie_ID`) REFERENCES `movie` (`Movie_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT `timeslot_ibfk_2` FOREIGN KEY (`Theater_ID`) REFERENCES `theater` (`Theater_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT `timeslot_ibfk_3` FOREIGN KEY (`DateRange_ID`) REFERENCES `daterange` (`DateRange_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
                COMMIT;




        SQL;

        if ($conn->multi_query($sql)) {
            echo "yahooo";
        } else {
            echo "uh oh...";
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