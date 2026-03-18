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
                    (9, 1, 11, '2026-03-25', '2026-03-30');

                    CREATE TABLE `e-receipt` (
                    `Receipt_ID` int(11) NOT NULL,
                    `Payment_ID` int(11) NOT NULL,
                    `DateIssued` date NOT NULL,
                    `SentToEmail` varchar(100) NOT NULL,
                    `ReceiptStatus` int(11) NOT NULL,
                    `Status` int(11) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                    INSERT INTO `e-receipt` (`Receipt_ID`, `Payment_ID`, `DateIssued`, `SentToEmail`, `ReceiptStatus`, `Status`) VALUES
                    (1, 1, '2026-03-13', '', 1, 1),
                    (2, 2, '2026-03-13', '', 1, 1),
                    (3, 3, '2026-03-13', '', 1, 1);

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
                    `TrailerURL` text NOT NULL,
                    `MovieAvailability` varchar(100) NOT NULL DEFAULT 'Now Showing'
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

                    INSERT INTO `payment` (`Payment_ID`, `Ticket_ID`, `PaymentMethod`, `AmountPaid`, `PaymentDate`, `PaymentStatus`) VALUES
                    (1, 4, 'paymaya', 350.00, '2026-03-13', 1),
                    (2, 5, 'paymaya', 350.00, '2026-03-13', 1),
                    (3, 6, 'paymaya', 350.00, '2026-03-13', 1);

                    CREATE TABLE `seats` (
                    `Seat_ID` int(11) NOT NULL, 
                    `Theater_ID` int(11) NOT NULL,
                    `SeatRow` varchar(10) NOT NULL,
                    `SeatColumn` varchar(10) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                    INSERT INTO `seats` (`Seat_ID`, `Theater_ID`, `SeatRow`, `SeatColumn`) VALUES
                    (161, 11, 'A', '10'),
                    (162, 11, 'A', '9'),
                    (163, 11, 'A', '0'),
                    (164, 11, 'A', '0'),
                    (165, 11, 'A', '8'),
                    (166, 11, 'A', '7'),
                    (167, 11, 'A', '6'),
                    (168, 11, 'A', '5'),
                    (169, 11, 'A', '4'),
                    (170, 11, 'A', '3'),
                    (171, 11, 'A', '0'),
                    (172, 11, 'A', '0'),
                    (173, 11, 'A', '2'),
                    (174, 11, 'A', '1'),
                    (175, 11, 'B', '10'),
                    (176, 11, 'B', '9'),
                    (177, 11, 'B', '0'),
                    (178, 11, 'B', '0'),
                    (179, 11, 'B', '8'),
                    (180, 11, 'B', '7'),
                    (181, 11, 'B', '6'),
                    (182, 11, 'B', '5'),
                    (183, 11, 'B', '4'),
                    (184, 11, 'B', '3'),
                    (185, 11, 'B', '0'),
                    (186, 11, 'B', '0'),
                    (187, 11, 'B', '2'),
                    (188, 11, 'B', '1'),
                    (189, 11, 'C', '10'),
                    (190, 11, 'C', '9'),
                    (191, 11, 'C', '0'),
                    (192, 11, 'C', '0'),
                    (193, 11, 'C', '8'),
                    (194, 11, 'C', '7'),
                    (195, 11, 'C', '6'),
                    (196, 11, 'C', '5'),
                    (197, 11, 'C', '4'),
                    (198, 11, 'C', '3'),
                    (199, 11, 'C', '0'),
                    (200, 11, 'C', '0'),
                    (201, 11, 'C', '2'),
                    (202, 11, 'C', '1'),
                    (203, 11, 'D', '10'),
                    (204, 11, 'D', '9'),
                    (205, 11, 'D', '0'),
                    (206, 11, 'D', '0'),
                    (207, 11, 'D', '8'),
                    (208, 11, 'D', '7'),
                    (209, 11, 'D', '6'),
                    (210, 11, 'D', '5'),
                    (211, 11, 'D', '4'),
                    (212, 11, 'D', '3'),
                    (213, 11, 'D', '0'),
                    (214, 11, 'D', '0'),
                    (215, 11, 'D', '2'),
                    (216, 11, 'D', '1'),
                    (217, 11, 'E', '10'),
                    (218, 11, 'E', '9'),
                    (219, 11, 'E', '0'),
                    (220, 11, 'E', '0'),
                    (221, 11, 'E', '8'),
                    (222, 11, 'E', '7'),
                    (223, 11, 'E', '6'),
                    (224, 11, 'E', '5'),
                    (225, 11, 'E', '4'),
                    (226, 11, 'E', '3'),
                    (227, 11, 'E', '0'),
                    (228, 11, 'E', '0'),
                    (229, 11, 'E', '2'),
                    (230, 11, 'E', '1');

                    CREATE TABLE `seat_timeslot` (
                    `SeatTimeSlot_ID` int(11) NOT NULL,
                    `Seat_ID` int(11) NOT NULL,
                    `TimeSlot_ID` int(11) NOT NULL,
                    `SeatPrice` decimal(10,2) NOT NULL,
                    `SeatAvailability` tinyint(1) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                    INSERT INTO `seat_timeslot` (`SeatTimeSlot_ID`, `Seat_ID`, `TimeSlot_ID`, `SeatPrice`, `SeatAvailability`) VALUES
                    (1121, 161, 17, 350.00, 1),
                    (1122, 162, 17, 350.00, 1),
                    (1123, 163, 17, 350.00, 1),
                    (1124, 164, 17, 350.00, 1),
                    (1125, 165, 17, 350.00, 1),
                    (1126, 166, 17, 350.00, 1),
                    (1127, 167, 17, 350.00, 1),
                    (1128, 168, 17, 350.00, 1),
                    (1129, 169, 17, 350.00, 1),
                    (1130, 170, 17, 350.00, 1),
                    (1131, 171, 17, 350.00, 1),
                    (1132, 172, 17, 350.00, 1),
                    (1133, 173, 17, 350.00, 1),
                    (1134, 174, 17, 350.00, 1),
                    (1135, 175, 17, 350.00, 1),
                    (1136, 176, 17, 350.00, 1),
                    (1137, 177, 17, 350.00, 1),
                    (1138, 178, 17, 350.00, 1),
                    (1139, 179, 17, 350.00, 1),
                    (1140, 180, 17, 350.00, 1),
                    (1141, 181, 17, 350.00, 1),
                    (1142, 182, 17, 350.00, 1),
                    (1143, 183, 17, 350.00, 1),
                    (1144, 184, 17, 350.00, 1),
                    (1145, 185, 17, 350.00, 1),
                    (1146, 186, 17, 350.00, 1),
                    (1147, 187, 17, 350.00, 1),
                    (1148, 188, 17, 350.00, 1),
                    (1149, 189, 17, 350.00, 1),
                    (1150, 190, 17, 350.00, 1),
                    (1151, 191, 17, 350.00, 1),
                    (1152, 192, 17, 350.00, 1),
                    (1153, 193, 17, 350.00, 1),
                    (1154, 194, 17, 350.00, 1),
                    (1155, 195, 17, 350.00, 1),
                    (1156, 196, 17, 350.00, 1),
                    (1157, 197, 17, 350.00, 1),
                    (1158, 198, 17, 350.00, 1),
                    (1159, 199, 17, 350.00, 1),
                    (1160, 200, 17, 350.00, 1),
                    (1161, 201, 17, 350.00, 1),
                    (1162, 202, 17, 350.00, 1),
                    (1163, 203, 17, 350.00, 1),
                    (1164, 204, 17, 350.00, 1),
                    (1165, 205, 17, 350.00, 1),
                    (1166, 206, 17, 350.00, 1),
                    (1167, 207, 17, 350.00, 1),
                    (1168, 208, 17, 350.00, 1),
                    (1169, 209, 17, 350.00, 1),
                    (1170, 210, 17, 350.00, 1),
                    (1171, 211, 17, 350.00, 1),
                    (1172, 212, 17, 350.00, 1),
                    (1173, 213, 17, 350.00, 1),
                    (1174, 214, 17, 350.00, 1),
                    (1175, 215, 17, 350.00, 1),
                    (1176, 216, 17, 350.00, 1),
                    (1177, 217, 17, 350.00, 1),
                    (1178, 218, 17, 350.00, 1),
                    (1179, 219, 17, 350.00, 1),
                    (1180, 220, 17, 350.00, 1),
                    (1181, 221, 17, 350.00, 1),
                    (1182, 222, 17, 350.00, 1),
                    (1183, 223, 17, 350.00, 1),
                    (1184, 224, 17, 350.00, 1),
                    (1185, 225, 17, 350.00, 1),
                    (1186, 226, 17, 350.00, 1),
                    (1187, 227, 17, 350.00, 1),
                    (1188, 228, 17, 350.00, 1),
                    (1189, 229, 17, 350.00, 1),
                    (1190, 230, 17, 350.00, 1),
                    (1191, 161, 18, 350.00, 1),
                    (1192, 162, 18, 350.00, 1),
                    (1193, 163, 18, 350.00, 1),
                    (1194, 164, 18, 350.00, 1),
                    (1195, 165, 18, 350.00, 1),
                    (1196, 166, 18, 350.00, 1),
                    (1197, 167, 18, 350.00, 1),
                    (1198, 168, 18, 350.00, 1),
                    (1199, 169, 18, 350.00, 1),
                    (1200, 170, 18, 350.00, 1),
                    (1201, 171, 18, 350.00, 1),
                    (1202, 172, 18, 350.00, 1),
                    (1203, 173, 18, 350.00, 1),
                    (1204, 174, 18, 350.00, 1),
                    (1205, 175, 18, 350.00, 1),
                    (1206, 176, 18, 350.00, 1),
                    (1207, 177, 18, 350.00, 1),
                    (1208, 178, 18, 350.00, 1),
                    (1209, 179, 18, 350.00, 1),
                    (1210, 180, 18, 350.00, 1),
                    (1211, 181, 18, 350.00, 1),
                    (1212, 182, 18, 350.00, 1),
                    (1213, 183, 18, 350.00, 1),
                    (1214, 184, 18, 350.00, 1),
                    (1215, 185, 18, 350.00, 1),
                    (1216, 186, 18, 350.00, 1),
                    (1217, 187, 18, 350.00, 1),
                    (1218, 188, 18, 350.00, 1),
                    (1219, 189, 18, 350.00, 1),
                    (1220, 190, 18, 350.00, 1),
                    (1221, 191, 18, 350.00, 1),
                    (1222, 192, 18, 350.00, 1),
                    (1223, 193, 18, 350.00, 1),
                    (1224, 194, 18, 350.00, 1),
                    (1225, 195, 18, 350.00, 1),
                    (1226, 196, 18, 350.00, 1),
                    (1227, 197, 18, 350.00, 1),
                    (1228, 198, 18, 350.00, 1),
                    (1229, 199, 18, 350.00, 1),
                    (1230, 200, 18, 350.00, 1),
                    (1231, 201, 18, 350.00, 1),
                    (1232, 202, 18, 350.00, 1),
                    (1233, 203, 18, 350.00, 1),
                    (1234, 204, 18, 350.00, 1),
                    (1235, 205, 18, 350.00, 1),
                    (1236, 206, 18, 350.00, 1),
                    (1237, 207, 18, 350.00, 1),
                    (1238, 208, 18, 350.00, 1),
                    (1239, 209, 18, 350.00, 1),
                    (1240, 210, 18, 350.00, 1),
                    (1241, 211, 18, 350.00, 1),
                    (1242, 212, 18, 350.00, 1),
                    (1243, 213, 18, 350.00, 1),
                    (1244, 214, 18, 350.00, 1),
                    (1245, 215, 18, 350.00, 1),
                    (1246, 216, 18, 350.00, 1),
                    (1247, 217, 18, 350.00, 1),
                    (1248, 218, 18, 350.00, 1),
                    (1249, 219, 18, 350.00, 1),
                    (1250, 220, 18, 350.00, 1),
                    (1251, 221, 18, 350.00, 1),
                    (1252, 222, 18, 350.00, 1),
                    (1253, 223, 18, 350.00, 1),
                    (1254, 224, 18, 350.00, 1),
                    (1255, 225, 18, 350.00, 1),
                    (1256, 226, 18, 350.00, 1),
                    (1257, 227, 18, 350.00, 1),
                    (1258, 228, 18, 350.00, 1),
                    (1259, 229, 18, 350.00, 1),
                    (1260, 230, 18, 350.00, 1),
                    (1261, 161, 19, 350.00, 1),
                    (1262, 162, 19, 350.00, 1),
                    (1263, 163, 19, 350.00, 1),
                    (1264, 164, 19, 350.00, 1),
                    (1265, 165, 19, 350.00, 1),
                    (1266, 166, 19, 350.00, 1),
                    (1267, 167, 19, 350.00, 1),
                    (1268, 168, 19, 350.00, 1),
                    (1269, 169, 19, 350.00, 1),
                    (1270, 170, 19, 350.00, 1),
                    (1271, 171, 19, 350.00, 1),
                    (1272, 172, 19, 350.00, 1),
                    (1273, 173, 19, 350.00, 1),
                    (1274, 174, 19, 350.00, 1),
                    (1275, 175, 19, 350.00, 1),
                    (1276, 176, 19, 350.00, 1),
                    (1277, 177, 19, 350.00, 1),
                    (1278, 178, 19, 350.00, 1),
                    (1279, 179, 19, 350.00, 1),
                    (1280, 180, 19, 350.00, 1),
                    (1281, 181, 19, 350.00, 1),
                    (1282, 182, 19, 350.00, 1),
                    (1283, 183, 19, 350.00, 1),
                    (1284, 184, 19, 350.00, 1),
                    (1285, 185, 19, 350.00, 1),
                    (1286, 186, 19, 350.00, 1),
                    (1287, 187, 19, 350.00, 1),
                    (1288, 188, 19, 350.00, 1),
                    (1289, 189, 19, 350.00, 1),
                    (1290, 190, 19, 350.00, 1),
                    (1291, 191, 19, 350.00, 1),
                    (1292, 192, 19, 350.00, 1),
                    (1293, 193, 19, 350.00, 1),
                    (1294, 194, 19, 350.00, 1),
                    (1295, 195, 19, 350.00, 1),
                    (1296, 196, 19, 350.00, 1),
                    (1297, 197, 19, 350.00, 1),
                    (1298, 198, 19, 350.00, 1),
                    (1299, 199, 19, 350.00, 1),
                    (1300, 200, 19, 350.00, 1),
                    (1301, 201, 19, 350.00, 1),
                    (1302, 202, 19, 350.00, 1),
                    (1303, 203, 19, 350.00, 1),
                    (1304, 204, 19, 350.00, 1),
                    (1305, 205, 19, 350.00, 1),
                    (1306, 206, 19, 350.00, 1),
                    (1307, 207, 19, 350.00, 1),
                    (1308, 208, 19, 350.00, 1),
                    (1309, 209, 19, 350.00, 1),
                    (1310, 210, 19, 350.00, 1),
                    (1311, 211, 19, 350.00, 1),
                    (1312, 212, 19, 350.00, 1),
                    (1313, 213, 19, 350.00, 1),
                    (1314, 214, 19, 350.00, 1),
                    (1315, 215, 19, 350.00, 1),
                    (1316, 216, 19, 350.00, 1),
                    (1317, 217, 19, 350.00, 1),
                    (1318, 218, 19, 350.00, 1),
                    (1319, 219, 19, 350.00, 1),
                    (1320, 220, 19, 350.00, 1),
                    (1321, 221, 19, 350.00, 1),
                    (1322, 222, 19, 350.00, 1),
                    (1323, 223, 19, 350.00, 1),
                    (1324, 224, 19, 350.00, 1),
                    (1325, 225, 19, 350.00, 1),
                    (1326, 226, 19, 350.00, 1),
                    (1327, 227, 19, 350.00, 1),
                    (1328, 228, 19, 350.00, 1),
                    (1329, 229, 19, 350.00, 1),
                    (1330, 230, 19, 350.00, 1),
                    (1331, 161, 20, 350.00, 1),
                    (1332, 162, 20, 350.00, 1),
                    (1333, 163, 20, 350.00, 1),
                    (1334, 164, 20, 350.00, 1),
                    (1335, 165, 20, 350.00, 1),
                    (1336, 166, 20, 350.00, 1),
                    (1337, 167, 20, 350.00, 1),
                    (1338, 168, 20, 350.00, 1),
                    (1339, 169, 20, 350.00, 1),
                    (1340, 170, 20, 350.00, 1),
                    (1341, 171, 20, 350.00, 1),
                    (1342, 172, 20, 350.00, 1),
                    (1343, 173, 20, 350.00, 1),
                    (1344, 174, 20, 350.00, 1),
                    (1345, 175, 20, 350.00, 1),
                    (1346, 176, 20, 350.00, 1),
                    (1347, 177, 20, 350.00, 1),
                    (1348, 178, 20, 350.00, 1),
                    (1349, 179, 20, 350.00, 1),
                    (1350, 180, 20, 350.00, 1),
                    (1351, 181, 20, 350.00, 1),
                    (1352, 182, 20, 350.00, 1),
                    (1353, 183, 20, 350.00, 1),
                    (1354, 184, 20, 350.00, 1),
                    (1355, 185, 20, 350.00, 1),
                    (1356, 186, 20, 350.00, 1),
                    (1357, 187, 20, 350.00, 1),
                    (1358, 188, 20, 350.00, 1),
                    (1359, 189, 20, 350.00, 1),
                    (1360, 190, 20, 350.00, 1),
                    (1361, 191, 20, 350.00, 1),
                    (1362, 192, 20, 350.00, 1),
                    (1363, 193, 20, 350.00, 1),
                    (1364, 194, 20, 350.00, 1),
                    (1365, 195, 20, 350.00, 1),
                    (1366, 196, 20, 350.00, 1),
                    (1367, 197, 20, 350.00, 1),
                    (1368, 198, 20, 350.00, 1),
                    (1369, 199, 20, 350.00, 1),
                    (1370, 200, 20, 350.00, 1),
                    (1371, 201, 20, 350.00, 1),
                    (1372, 202, 20, 350.00, 1),
                    (1373, 203, 20, 350.00, 1),
                    (1374, 204, 20, 350.00, 1),
                    (1375, 205, 20, 350.00, 1),
                    (1376, 206, 20, 350.00, 1),
                    (1377, 207, 20, 350.00, 1),
                    (1378, 208, 20, 350.00, 1),
                    (1379, 209, 20, 350.00, 1),
                    (1380, 210, 20, 350.00, 1),
                    (1381, 211, 20, 350.00, 1),
                    (1382, 212, 20, 350.00, 1),
                    (1383, 213, 20, 350.00, 1),
                    (1384, 214, 20, 350.00, 1),
                    (1385, 215, 20, 350.00, 1),
                    (1386, 216, 20, 350.00, 1),
                    (1387, 217, 20, 350.00, 1),
                    (1388, 218, 20, 350.00, 1),
                    (1389, 219, 20, 350.00, 1),
                    (1390, 220, 20, 350.00, 1),
                    (1391, 221, 20, 350.00, 1),
                    (1392, 222, 20, 350.00, 1),
                    (1393, 223, 20, 350.00, 1),
                    (1394, 224, 20, 350.00, 1),
                    (1395, 225, 20, 350.00, 1),
                    (1396, 226, 20, 350.00, 1),
                    (1397, 227, 20, 350.00, 1),
                    (1398, 228, 20, 350.00, 1),
                    (1399, 229, 20, 350.00, 1),
                    (1400, 230, 20, 350.00, 1),
                    (1401, 161, 21, 350.00, 1),
                    (1402, 162, 21, 350.00, 1),
                    (1403, 163, 21, 350.00, 1),
                    (1404, 164, 21, 350.00, 1),
                    (1405, 165, 21, 350.00, 1),
                    (1406, 166, 21, 350.00, 1),
                    (1407, 167, 21, 350.00, 1),
                    (1408, 168, 21, 350.00, 1),
                    (1409, 169, 21, 350.00, 1),
                    (1410, 170, 21, 350.00, 1),
                    (1411, 171, 21, 350.00, 1),
                    (1412, 172, 21, 350.00, 1),
                    (1413, 173, 21, 350.00, 1),
                    (1414, 174, 21, 350.00, 1),
                    (1415, 175, 21, 350.00, 1),
                    (1416, 176, 21, 350.00, 1),
                    (1417, 177, 21, 350.00, 1),
                    (1418, 178, 21, 350.00, 1),
                    (1419, 179, 21, 350.00, 1),
                    (1420, 180, 21, 350.00, 1),
                    (1421, 181, 21, 350.00, 1),
                    (1422, 182, 21, 350.00, 1),
                    (1423, 183, 21, 350.00, 1),
                    (1424, 184, 21, 350.00, 1),
                    (1425, 185, 21, 350.00, 1),
                    (1426, 186, 21, 350.00, 1),
                    (1427, 187, 21, 350.00, 1),
                    (1428, 188, 21, 350.00, 1),
                    (1429, 189, 21, 350.00, 1),
                    (1430, 190, 21, 350.00, 1),
                    (1431, 191, 21, 350.00, 1),
                    (1432, 192, 21, 350.00, 1),
                    (1433, 193, 21, 350.00, 1),
                    (1434, 194, 21, 350.00, 1),
                    (1435, 195, 21, 350.00, 1),
                    (1436, 196, 21, 350.00, 1),
                    (1437, 197, 21, 350.00, 1),
                    (1438, 198, 21, 350.00, 1),
                    (1439, 199, 21, 350.00, 1),
                    (1440, 200, 21, 350.00, 1),
                    (1441, 201, 21, 350.00, 1),
                    (1442, 202, 21, 350.00, 1),
                    (1443, 203, 21, 350.00, 1),
                    (1444, 204, 21, 350.00, 1),
                    (1445, 205, 21, 350.00, 1),
                    (1446, 206, 21, 350.00, 1),
                    (1447, 207, 21, 350.00, 1),
                    (1448, 208, 21, 350.00, 1),
                    (1449, 209, 21, 350.00, 1),
                    (1450, 210, 21, 350.00, 1),
                    (1451, 211, 21, 350.00, 1),
                    (1452, 212, 21, 350.00, 1),
                    (1453, 213, 21, 350.00, 1),
                    (1454, 214, 21, 350.00, 1),
                    (1455, 215, 21, 350.00, 1),
                    (1456, 216, 21, 350.00, 1),
                    (1457, 217, 21, 350.00, 1),
                    (1458, 218, 21, 350.00, 1),
                    (1459, 219, 21, 350.00, 1),
                    (1460, 220, 21, 350.00, 1),
                    (1461, 221, 21, 350.00, 1),
                    (1462, 222, 21, 350.00, 1),
                    (1463, 223, 21, 350.00, 1),
                    (1464, 224, 21, 350.00, 1),
                    (1465, 225, 21, 350.00, 1),
                    (1466, 226, 21, 350.00, 1),
                    (1467, 227, 21, 350.00, 1),
                    (1468, 228, 21, 350.00, 1),
                    (1469, 229, 21, 350.00, 1),
                    (1470, 230, 21, 350.00, 1),
                    (1471, 161, 22, 350.00, 1),
                    (1472, 162, 22, 350.00, 1),
                    (1473, 163, 22, 350.00, 1),
                    (1474, 164, 22, 350.00, 1),
                    (1475, 165, 22, 350.00, 1),
                    (1476, 166, 22, 350.00, 1),
                    (1477, 167, 22, 350.00, 1),
                    (1478, 168, 22, 350.00, 1),
                    (1479, 169, 22, 350.00, 1),
                    (1480, 170, 22, 350.00, 1),
                    (1481, 171, 22, 350.00, 1),
                    (1482, 172, 22, 350.00, 1),
                    (1483, 173, 22, 350.00, 1),
                    (1484, 174, 22, 350.00, 1),
                    (1485, 175, 22, 350.00, 1),
                    (1486, 176, 22, 350.00, 1),
                    (1487, 177, 22, 350.00, 1),
                    (1488, 178, 22, 350.00, 1),
                    (1489, 179, 22, 350.00, 1),
                    (1490, 180, 22, 350.00, 1),
                    (1491, 181, 22, 350.00, 1),
                    (1492, 182, 22, 350.00, 1),
                    (1493, 183, 22, 350.00, 1),
                    (1494, 184, 22, 350.00, 1),
                    (1495, 185, 22, 350.00, 1),
                    (1496, 186, 22, 350.00, 1),
                    (1497, 187, 22, 350.00, 1),
                    (1498, 188, 22, 350.00, 1),
                    (1499, 189, 22, 350.00, 1),
                    (1500, 190, 22, 350.00, 1),
                    (1501, 191, 22, 350.00, 1),
                    (1502, 192, 22, 350.00, 1),
                    (1503, 193, 22, 350.00, 1),
                    (1504, 194, 22, 350.00, 1),
                    (1505, 195, 22, 350.00, 1),
                    (1506, 196, 22, 350.00, 1),
                    (1507, 197, 22, 350.00, 1),
                    (1508, 198, 22, 350.00, 1),
                    (1509, 199, 22, 350.00, 1),
                    (1510, 200, 22, 350.00, 1),
                    (1511, 201, 22, 350.00, 1),
                    (1512, 202, 22, 350.00, 1),
                    (1513, 203, 22, 350.00, 1),
                    (1514, 204, 22, 350.00, 1),
                    (1515, 205, 22, 350.00, 1),
                    (1516, 206, 22, 350.00, 1),
                    (1517, 207, 22, 350.00, 1),
                    (1518, 208, 22, 350.00, 1),
                    (1519, 209, 22, 350.00, 1),
                    (1520, 210, 22, 350.00, 1),
                    (1521, 211, 22, 350.00, 1),
                    (1522, 212, 22, 350.00, 1),
                    (1523, 213, 22, 350.00, 1),
                    (1524, 214, 22, 350.00, 1),
                    (1525, 215, 22, 350.00, 1),
                    (1526, 216, 22, 350.00, 1),
                    (1527, 217, 22, 350.00, 1),
                    (1528, 218, 22, 350.00, 1),
                    (1529, 219, 22, 350.00, 1),
                    (1530, 220, 22, 350.00, 1),
                    (1531, 221, 22, 350.00, 1),
                    (1532, 222, 22, 350.00, 1),
                    (1533, 223, 22, 350.00, 1),
                    (1534, 224, 22, 350.00, 1),
                    (1535, 225, 22, 350.00, 1),
                    (1536, 226, 22, 350.00, 1),
                    (1537, 227, 22, 350.00, 1),
                    (1538, 228, 22, 350.00, 1),
                    (1539, 229, 22, 350.00, 1),
                    (1540, 230, 22, 350.00, 1);

                    CREATE TABLE `theater` (
                    `Theater_ID` int(11) NOT NULL,
                    `Mall_ID` int(11) NOT NULL,
                    `TheaterName` varchar(100) NOT NULL,
                    `TotalSeats` int(11) NOT NULL,
                    `TheaterType` varchar(50) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

                    INSERT INTO `theater` (`Theater_ID`, `Mall_ID`, `TheaterName`, `TotalSeats`, `TheaterType`) VALUES
                    (11, 1, 'Regular 1', 50, 'Regular');

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

                    INSERT INTO `ticket` (`Ticket_ID`, `Seat_ID`, `Customer_ID`, `Movie_ID`, `TimeSlot_ID`, `Price`, `Status`, `DateTime`) VALUES
                    (4, 151, 1, 1, 9, 350.00, 1, '2026-03-13 13:27:15'),
                    (5, 148, 1, 1, 9, 350.00, 1, '2026-03-13 13:27:15'),
                    (6, 147, 1, 1, 9, 350.00, 1, '2026-03-13 13:27:15');

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
                    (17, '19:25:00', '2026-03-25', '2D', 1, 11, 9),
                    (18, '19:25:00', '2026-03-26', '2D', 1, 11, 9),
                    (19, '19:25:00', '2026-03-27', '2D', 1, 11, 9),
                    (20, '19:25:00', '2026-03-28', '2D', 1, 11, 9),
                    (21, '19:25:00', '2026-03-29', '2D', 1, 11, 9),
                    (22, '19:25:00', '2026-03-30', '2D', 1, 11, 9);


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
                    ADD PRIMARY KEY (`Seat_ID`),
                    ADD KEY `Theater_ID` (`Theater_ID`);

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
                    MODIFY `Customer_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

                    ALTER TABLE `daterange`
                    MODIFY `DateRange_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

                    ALTER TABLE `e-receipt`
                    MODIFY `Receipt_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

                    ALTER TABLE `mall`
                    MODIFY `Mall_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

                    ALTER TABLE `movie`
                    MODIFY `Movie_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

                    ALTER TABLE `payment`
                    MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

                    ALTER TABLE `seats`
                    MODIFY `Seat_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231;

                    ALTER TABLE `seat_timeslot`
                    MODIFY `SeatTimeSlot_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1541;

                    ALTER TABLE `theater`
                    MODIFY `Theater_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

                    ALTER TABLE `ticket`
                    MODIFY `Ticket_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

                    ALTER TABLE `timeslot`
                    MODIFY `TimeSlot_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;


                    ALTER TABLE `daterange`
                    ADD CONSTRAINT `daterange_ibfk_1` FOREIGN KEY (`Movie_ID`) REFERENCES `movie` (`Movie_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `daterange_ibfk_2` FOREIGN KEY (`Theater_ID`) REFERENCES `theater` (`Theater_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

                    ALTER TABLE `seats`
                    ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`Theater_ID`) REFERENCES `theater` (`Theater_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

                    ALTER TABLE `seat_timeslot`
                    ADD CONSTRAINT `seat_timeslot_ibfk_1` FOREIGN KEY (`TimeSlot_ID`) REFERENCES `timeslot` (`TimeSlot_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

                    ALTER TABLE `timeslot`
                    ADD CONSTRAINT `timeslot_ibfk_1` FOREIGN KEY (`DateRange_ID`) REFERENCES `daterange` (`DateRange_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `timeslot_ibfk_2` FOREIGN KEY (`Theater_ID`) REFERENCES `theater` (`Theater_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `timeslot_ibfk_3` FOREIGN KEY (`Movie_ID`) REFERENCES `movie` (`Movie_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
                    COMMIT;
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