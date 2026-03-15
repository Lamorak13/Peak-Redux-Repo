<?php
    $servername = "localhost";
    $username = "root";
    $password = "";

    // Create connection without selecting a database first
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        // First, drop and create the database
        $conn->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'");
        $conn->query("START TRANSACTION");
        $conn->query("SET time_zone = '+00:00'");
        $conn->query("DROP DATABASE IF EXISTS `peakscinemadb`");
        $conn->query("CREATE DATABASE IF NOT EXISTS `peakscinemadb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        $conn->query("USE `peakscinemadb`");

        // Create customer table
        $conn->query("CREATE TABLE `customer` (
            `Customer_ID` int(11) NOT NULL,
            `Name` varchar(100) NOT NULL,
            `Email` varchar(100) NOT NULL,
            `Password` varchar(255) NOT NULL,
            `PhoneNumber` varchar(10) NOT NULL,
            `CountryCode` varchar(4) NOT NULL,
            `PaymentMethod` tinytext NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Create daterange table
        $conn->query("CREATE TABLE `daterange` (
            `DateRange_ID` int(11) NOT NULL,
            `Movie_ID` int(11) NOT NULL,
            `Theater_ID` int(11) NOT NULL,
            `StartDate` date NOT NULL,
            `EndDate` date NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Insert into daterange
        $conn->query("INSERT INTO `daterange` (`DateRange_ID`, `Movie_ID`, `Theater_ID`, `StartDate`, `EndDate`) VALUES
            (9, 1, 11, '2026-03-25', '2026-03-30')");

        // Create e-receipt table
        $conn->query("CREATE TABLE `e-receipt` (
            `Receipt_ID` int(11) NOT NULL,
            `Payment_ID` int(11) NOT NULL,
            `DateIssued` date NOT NULL,
            `SentToEmail` varchar(100) NOT NULL,
            `ReceiptStatus` int(11) NOT NULL,
            `Status` int(11) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Insert into e-receipt
        $conn->query("INSERT INTO `e-receipt` (`Receipt_ID`, `Payment_ID`, `DateIssued`, `SentToEmail`, `ReceiptStatus`, `Status`) VALUES
            (1, 1, '2026-03-13', '', 1, 1),
            (2, 2, '2026-03-13', '', 1, 1),
            (3, 3, '2026-03-13', '', 1, 1)");

        // Create mall table
        $conn->query("CREATE TABLE `mall` (
            `Mall_ID` int(11) NOT NULL,
            `MallName` tinytext NOT NULL,
            `Location` tinytext NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Insert into mall
        $conn->query("INSERT INTO `mall` (`Mall_ID`, `MallName`, `Location`) VALUES
            (1, 'SM Marikina', 'Marcos Highway, Calumpang, Marikina City, 1801, Marikina, Luzon Philippines')");

        // Create movie table
        $conn->query("CREATE TABLE `movie` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Insert into movie
        $conn->query("INSERT INTO `movie` (`Movie_ID`, `MovieName`, `MovieDescription`, `Genre`, `Rating`, `Runtime`, `MoviePoster`, `MovieAvailability`, `TrailerURL`, `Price`) VALUES
            (1, 'Superman', 'Superman must reconcile his alien Kryptonian heritage with his human upbringing as reporter Clark Kent. As the embodiment of truth, justice and the human way he soon finds himself in a world that views these as old-fashioned.\n\n', 'Superhero, Action', 'PG', 129, 'PeaksCinema/MoviePosters/Superman.png', 'Now Showing', 'https://www.youtube.com/watch?v=Ox8ZLF6cGM0', 350)");

        // Create payment table
        $conn->query("CREATE TABLE `payment` (
            `Payment_ID` int(11) NOT NULL,
            `Ticket_ID` int(11) NOT NULL,
            `PaymentMethod` varchar(50) NOT NULL,
            `AmountPaid` decimal(10,2) NOT NULL,
            `PaymentDate` date NOT NULL,
            `PaymentStatus` int(11) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Insert into payment
        $conn->query("INSERT INTO `payment` (`Payment_ID`, `Ticket_ID`, `PaymentMethod`, `AmountPaid`, `PaymentDate`, `PaymentStatus`) VALUES
            (1, 4, 'paymaya', 350.00, '2026-03-13', 1),
            (2, 5, 'paymaya', 350.00, '2026-03-13', 1),
            (3, 6, 'paymaya', 350.00, '2026-03-13', 1)");

        // Create seats table
        $conn->query("CREATE TABLE `seats` (
            `Seat_ID` int(11) NOT NULL, 
            `Theater_ID` int(11) NOT NULL,
            `SeatRow` varchar(10) NOT NULL,
            `SeatColumn` varchar(10) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Insert into seats - batch insert for better performance
        $seatData = [
            [161, 11, 'A', '10'], [162, 11, 'A', '9'], [163, 11, 'A', '0'], [164, 11, 'A', '0'], [165, 11, 'A', '8'],
            [166, 11, 'A', '7'], [167, 11, 'A', '6'], [168, 11, 'A', '5'], [169, 11, 'A', '4'], [170, 11, 'A', '3'],
            [171, 11, 'A', '0'], [172, 11, 'A', '0'], [173, 11, 'A', '2'], [174, 11, 'A', '1'], [175, 11, 'B', '10'],
            [176, 11, 'B', '9'], [177, 11, 'B', '0'], [178, 11, 'B', '0'], [179, 11, 'B', '8'], [180, 11, 'B', '7'],
            [181, 11, 'B', '6'], [182, 11, 'B', '5'], [183, 11, 'B', '4'], [184, 11, 'B', '3'], [185, 11, 'B', '0'],
            [186, 11, 'B', '0'], [187, 11, 'B', '2'], [188, 11, 'B', '1'], [189, 11, 'C', '10'], [190, 11, 'C', '9'],
            [191, 11, 'C', '0'], [192, 11, 'C', '0'], [193, 11, 'C', '8'], [194, 11, 'C', '7'], [195, 11, 'C', '6'],
            [196, 11, 'C', '5'], [197, 11, 'C', '4'], [198, 11, 'C', '3'], [199, 11, 'C', '0'], [200, 11, 'C', '0'],
            [201, 11, 'C', '2'], [202, 11, 'C', '1'], [203, 11, 'D', '10'], [204, 11, 'D', '9'], [205, 11, 'D', '0'],
            [206, 11, 'D', '0'], [207, 11, 'D', '8'], [208, 11, 'D', '7'], [209, 11, 'D', '6'], [210, 11, 'D', '5'],
            [211, 11, 'D', '4'], [212, 11, 'D', '3'], [213, 11, 'D', '0'], [214, 11, 'D', '0'], [215, 11, 'D', '2'],
            [216, 11, 'D', '1'], [217, 11, 'E', '10'], [218, 11, 'E', '9'], [219, 11, 'E', '0'], [220, 11, 'E', '0'],
            [221, 11, 'E', '8'], [222, 11, 'E', '7'], [223, 11, 'E', '6'], [224, 11, 'E', '5'], [225, 11, 'E', '4'],
            [226, 11, 'E', '3'], [227, 11, 'E', '0'], [228, 11, 'E', '0'], [229, 11, 'E', '2'], [230, 11, 'E', '1']
        ];
        
        foreach ($seatData as $seat) {
            $conn->query("INSERT INTO `seats` (`Seat_ID`, `Theater_ID`, `SeatRow`, `SeatColumn`) VALUES ($seat[0], $seat[1], '$seat[2]', '$seat[3]')");
        }

        // Create seat_timeslot table
        $conn->query("CREATE TABLE `seat_timeslot` (
            `SeatTimeSlot_ID` int(11) NOT NULL,
            `Seat_ID` int(11) NOT NULL,
            `TimeSlot_ID` int(11) NOT NULL,
            `SeatPrice` decimal(10,2) NOT NULL,
            `SeatAvailability` tinyint(1) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Insert seat_timeslot data - create entries for each seat and timeslot
        $seatTimeSlotId = 1121;
        $timeSlots = [17, 18, 19, 20, 21, 22];
        
        foreach ($timeSlots as $timeSlotId) {
            for ($seatId = 161; $seatId <= 230; $seatId++) {
                $conn->query("INSERT INTO `seat_timeslot` (`SeatTimeSlot_ID`, `Seat_ID`, `TimeSlot_ID`, `SeatPrice`, `SeatAvailability`) VALUES ($seatTimeSlotId, $seatId, $timeSlotId, 350.00, 1)");
                $seatTimeSlotId++;
            }
        }

        // Create theater table
        $conn->query("CREATE TABLE `theater` (
            `Theater_ID` int(11) NOT NULL,
            `Mall_ID` int(11) NOT NULL,
            `TheaterName` varchar(100) NOT NULL,
            `TotalSeats` int(11) NOT NULL,
            `TheaterType` varchar(50) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Insert into theater
        $conn->query("INSERT INTO `theater` (`Theater_ID`, `Mall_ID`, `TheaterName`, `TotalSeats`, `TheaterType`) VALUES
            (11, 1, 'Regular 1', 50, 'Regular')");

        // Create ticket table
        $conn->query("CREATE TABLE `ticket` (
            `Ticket_ID` int(11) NOT NULL,
            `Seat_ID` int(11) NOT NULL,
            `Customer_ID` int(11) NOT NULL,
            `Movie_ID` int(11) NOT NULL,
            `TimeSlot_ID` int(11) NOT NULL,
            `Price` decimal(10,2) NOT NULL,
            `Status` int(11) NOT NULL,
            `DateTime` datetime NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Insert into ticket
        $conn->query("INSERT INTO `ticket` (`Ticket_ID`, `Seat_ID`, `Customer_ID`, `Movie_ID`, `TimeSlot_ID`, `Price`, `Status`, `DateTime`) VALUES
            (4, 151, 1, 1, 9, 350.00, 1, '2026-03-13 13:27:15'),
            (5, 148, 1, 1, 9, 350.00, 1, '2026-03-13 13:27:15'),
            (6, 147, 1, 1, 9, 350.00, 1, '2026-03-13 13:27:15')");

        // Create timeslot table
        $conn->query("CREATE TABLE `timeslot` (
            `TimeSlot_ID` int(11) NOT NULL,
            `StartTime` time NOT NULL,
            `Date` date NOT NULL,
            `ScreeningType` varchar(10) NOT NULL,
            `Movie_ID` int(11) NOT NULL,
            `Theater_ID` int(11) NOT NULL,
            `DateRange_ID` int(11) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Insert into timeslot
        $conn->query("INSERT INTO `timeslot` (`TimeSlot_ID`, `StartTime`, `Date`, `ScreeningType`, `Movie_ID`, `Theater_ID`, `DateRange_ID`) VALUES
            (17, '19:25:00', '2026-03-25', '2D', 1, 11, 9),
            (18, '19:25:00', '2026-03-26', '2D', 1, 11, 9),
            (19, '19:25:00', '2026-03-27', '2D', 1, 11, 9),
            (20, '19:25:00', '2026-03-28', '2D', 1, 11, 9),
            (21, '19:25:00', '2026-03-29', '2D', 1, 11, 9),
            (22, '19:25:00', '2026-03-30', '2D', 1, 11, 9)");

        // Add PRIMARY KEYs
        $conn->query("ALTER TABLE `customer` ADD PRIMARY KEY (`Customer_ID`)");
        $conn->query("ALTER TABLE `daterange` ADD PRIMARY KEY (`DateRange_ID`), ADD KEY `Movie_ID` (`Movie_ID`), ADD KEY `Theater_ID` (`Theater_ID`)");
        $conn->query("ALTER TABLE `e-receipt` ADD PRIMARY KEY (`Receipt_ID`)");
        $conn->query("ALTER TABLE `mall` ADD PRIMARY KEY (`Mall_ID`), ADD UNIQUE KEY `MallName` (`MallName`) USING HASH");
        $conn->query("ALTER TABLE `movie` ADD PRIMARY KEY (`Movie_ID`)");
        $conn->query("ALTER TABLE `payment` ADD PRIMARY KEY (`Payment_ID`)");
        $conn->query("ALTER TABLE `seats` ADD PRIMARY KEY (`Seat_ID`), ADD KEY `Theater_ID` (`Theater_ID`)");
        $conn->query("ALTER TABLE `seat_timeslot` ADD PRIMARY KEY (`SeatTimeSlot_ID`), ADD UNIQUE KEY `uniq_seat_timeslot` (`Seat_ID`,`TimeSlot_ID`), ADD KEY `TimeSlot_ID` (`TimeSlot_ID`)");
        $conn->query("ALTER TABLE `theater` ADD PRIMARY KEY (`Theater_ID`)");
        $conn->query("ALTER TABLE `ticket` ADD PRIMARY KEY (`Ticket_ID`)");
        $conn->query("ALTER TABLE `timeslot` ADD PRIMARY KEY (`TimeSlot_ID`), ADD KEY `Movie_ID` (`Movie_ID`), ADD KEY `Theater_ID` (`Theater_ID`), ADD KEY `DateRange_ID` (`DateRange_ID`)");

        // Set AUTO_INCREMENT
        $conn->query("ALTER TABLE `customer` MODIFY `Customer_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2");
        $conn->query("ALTER TABLE `daterange` MODIFY `DateRange_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10");
        $conn->query("ALTER TABLE `e-receipt` MODIFY `Receipt_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4");
        $conn->query("ALTER TABLE `mall` MODIFY `Mall_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2");
        $conn->query("ALTER TABLE `movie` MODIFY `Movie_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2");
        $conn->query("ALTER TABLE `payment` MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4");
        $conn->query("ALTER TABLE `seats` MODIFY `Seat_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231");
        $conn->query("ALTER TABLE `seat_timeslot` MODIFY `SeatTimeSlot_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1541");
        $conn->query("ALTER TABLE `theater` MODIFY `Theater_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12");
        $conn->query("ALTER TABLE `ticket` MODIFY `Ticket_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7");
        $conn->query("ALTER TABLE `timeslot` MODIFY `TimeSlot_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23");

        // Add foreign keys
        $conn->query("ALTER TABLE `daterange` ADD CONSTRAINT `daterange_ibfk_1` FOREIGN KEY (`Movie_ID`) REFERENCES `movie` (`Movie_ID`) ON DELETE CASCADE ON UPDATE CASCADE, ADD CONSTRAINT `daterange_ibfk_2` FOREIGN KEY (`Theater_ID`) REFERENCES `theater` (`Theater_ID`) ON DELETE CASCADE ON UPDATE CASCADE");
        $conn->query("ALTER TABLE `seats` ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`Theater_ID`) REFERENCES `theater` (`Theater_ID`) ON DELETE CASCADE ON UPDATE CASCADE");
        $conn->query("ALTER TABLE `seat_timeslot` ADD CONSTRAINT `seat_timeslot_ibfk_1` FOREIGN KEY (`TimeSlot_ID`) REFERENCES `timeslot` (`TimeSlot_ID`) ON DELETE CASCADE ON UPDATE CASCADE");
        $conn->query("ALTER TABLE `timeslot` ADD CONSTRAINT `timeslot_ibfk_1` FOREIGN KEY (`DateRange_ID`) REFERENCES `daterange` (`DateRange_ID`) ON DELETE CASCADE ON UPDATE CASCADE, ADD CONSTRAINT `timeslot_ibfk_2` FOREIGN KEY (`Theater_ID`) REFERENCES `theater` (`Theater_ID`) ON DELETE CASCADE ON UPDATE CASCADE, ADD CONSTRAINT `timeslot_ibfk_3` FOREIGN KEY (`Movie_ID`) REFERENCES `movie` (`Movie_ID`) ON DELETE CASCADE ON UPDATE CASCADE");

        $conn->query("COMMIT");

        echo "Updated Database Successfully.";
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
