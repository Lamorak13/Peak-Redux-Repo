<?php 
    header("Content-Type: application/json");
    include 'peakscinemas_database.php';

    $method = $_SERVER['REQUEST_METHOD'];
    $request = $_GET['request'] ?? '';
    $parts = explode('/', trim($request, '/'));

    $resource = $parts[0] ?? null;
    $ID = $parts[1] ?? null;
    
    $subResource = $parts[2] ?? null;
    $subID = $parts[3] ?? null;
    $subResource2 = $parts[4] ?? null;
    $subID2 = $parts[5] ?? null;

    switch ($resource) {
        case 'customer':
            break;
        case 'daterange':
            if ($method == 'GET') { 
                if (!is_numeric($ID)) { // e.g. PeaksCinema/pc_api.php?request=daterange/all/movie/1/theater/11
                    if ($subResource === 'movie' && $subID && $subResource2 === 'theater' && $subID2) { 
                        $stmt = $conn->prepare('SELECT * FROM daterange
                                                        WHERE Movie_ID = ? AND Theater_ID=?');
                                $stmt->bind_param('ii', $subID, $subID2);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                if ($result->num_rows === 0) {
                                    echo json_encode("There are no date ranges for this theater yet.");
                                } else {
                                    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
                                }
                    } else {
                        echo json_encode("Error with your request.");
                    }
                } else {
                    // other queries
                }                           
            }

            if ($method == 'POST') {
                if (!is_numeric($ID)) {
                    if ($subResource === 'movie' && $subID && $subResource2 === 'theater' && $subID2) {
                        $data = json_decode(file_get_contents("php://input"), true);

                        if ($data) {
                            $Theater_ID = $data['Theater_ID'];
                            $Movie_ID   = $data['Movie_ID'];
                            $StartDate  = $data['StartDate'];
                            $EndDate    = $data['EndDate'];

                            $checkTheater = $conn->prepare("SELECT Theater_ID FROM theater WHERE Theater_ID = ?");
                            $checkTheater->bind_param("i", $Theater_ID);
                            $checkTheater->execute();
                            $checkResult = $checkTheater->get_result();
                            if ($checkResult->num_rows === 0) {
                                die("Invalid Theater_ID: " . $Theater_ID);
                            }

                            $checkMovie = $conn->prepare("SELECT Movie_ID FROM movie WHERE Movie_ID = ?");
                            $checkMovie->bind_param("i", $Movie_ID);
                            $checkMovie->execute();
                            $movieResult = $checkMovie->get_result();
                            if ($movieResult->num_rows === 0) {
                                die("Invalid Movie_ID: " . $Movie_ID);
                            }

                            $stmt = $conn->prepare("INSERT INTO daterange (Movie_ID, Theater_ID, StartDate, EndDate)
                                                    VALUES (?, ?, ?, ?)");
                            $stmt->bind_param("iiss", $Movie_ID, $Theater_ID, $StartDate, $EndDate);
                            $stmt->execute();

                            $DateRange_ID = $conn->insert_id;

                            $ScreeningType = "2D"; // TEMP

                            foreach ($data['timeslots'] as $timeslot) {
                                $date = $timeslot['date'];
                                $time = $timeslot['timeslot'];

                                $stmt2 = $conn->prepare("INSERT INTO timeslot (StartTime, Date, ScreeningType, Movie_ID, Theater_ID, DateRange_ID)
                                                        VALUES (?, ?, ?, ?, ?, ?)");
                                $stmt2->bind_param("sssiii", $time, $date, $ScreeningType, $Movie_ID, $Theater_ID, $DateRange_ID);
                                if (!$stmt2->execute()) {
                                    die("Error inserting timeslot: " . $stmt2->error);
                                }

                                $TimeSlot_ID = $conn->insert_id;

                                $seats_stmt = $conn->prepare("SELECT Seat_ID FROM seats WHERE Theater_ID = ?");
                                $seats_stmt->bind_param("i", $Theater_ID);
                                $seats_stmt->execute();
                                $seatLayout = $seats_stmt->get_result();

                                $SeatPrice = 350; // TEMP
                                $SeatAvailability = 1;

                                while ($row = $seatLayout->fetch_assoc()) {
                                    $screeningSeatsToDb_stmt = $conn->prepare("INSERT INTO seat_timeslot (Seat_ID, TimeSlot_ID, SeatPrice, SeatAvailability)
                                                                            VALUES (?, ?, ?, ?)");
                                    $screeningSeatsToDb_stmt->bind_param("iiii", $row['Seat_ID'], $TimeSlot_ID, $SeatPrice, $SeatAvailability);
                                    $screeningSeatsToDb_stmt->execute();
                                }
                            }
                        }
                    }
                }                
            }

            if ($method == 'PUT') {
                // future update stuff
            }

            if ($method == 'DELETE') {
                if ($ID !== null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $stmt = $conn->prepare("DELETE FROM daterange WHERE DateRange_ID = ?");
                    $stmt->bind_param("i", $ID);

                    if ($stmt->execute()) {
                        echo json_encode("Successfully deleted date range.");
                    } else {
                        echo json_encode("Error with your request.");
                    }
                }
            }
            break;
        case 'movie':
            if ($method == 'GET') {
                if ($ID) {
                    $stmt = $conn->prepare('SELECT * FROM movie
                                            WHERE Movie_ID = ?');
                    $stmt->bind_param('i', $ID);
                    $stmt->execute();
                    echo json_encode($stmt->get_result()->fetch_assoc());
                } else {
                    $result = $conn->query("SELECT * FROM movie");
                    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
                }
            }

            if ($method == 'POST') {
                $movieName = trim($_POST['MovieName'] ?? '');
                $movieDescription = trim($_POST['MovieDescription'] ?? '');
                $genre = trim($_POST['Genre'] ?? '');
                $rating = trim($_POST['Rating'] ?? '');
                $runtime = trim($_POST['Runtime'] ?? '');
                $trailerURL = trim($_POST['TrailerURL'] ?? '');

                $uploadPath = null;
                if (isset($_FILES['MoviePoster']) && $_FILES['MoviePoster']['error'] == 0) {
                    $uploadDir = __DIR__ . '/MoviePosters/';
                    $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $movieName) . '.' . pathinfo($_FILES['MoviePoster']['name'], PATHINFO_EXTENSION);
                    $uploadPath = $uploadDir . $fileName;

                    move_uploaded_file($_FILES['MoviePoster']['tmp_name'], $uploadPath);
                }

                if (empty($movieName) || empty($movieDescription) || empty($genre) || empty($rating) || empty($runtime) || empty($uploadPath)) {
                    die(json_encode("All fields are required, please input everything correctly."));
                }
                if (!is_numeric($runtime)) {
                    die(json_encode("Runtime should be a number."));
                }

                $stmt = $conn->prepare('INSERT INTO movie (
                                        MovieName,
                                        MovieDescription,
                                        Genre,
                                        Rating,
                                        Runtime,
                                        MoviePoster,
                                        TrailerURL)
                                        VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('ssssiss',
                                    $movieName,
                                    $movieDescription,
                                    $genre,
                                    $rating,
                                    $runtime,
                                    $uploadPath,
                                    $trailerURL);
                $stmt->execute();
            }
            
            break;
        case 'payment':
            break;
        case 'seats':
            break;
        case 'seat_timeslot':
            break;
        case 'theater':
            break;
        case 'ticket':
            break;
        case 'timeslot':
            break;
        default:
            break;
    }

    // function dateRangeHandler($method, $ID, $subResource, $subID, $subResource2, $subID2) {
    //     global $conn;
    //     if ($method == 'GET') {
    //         if ($subResource === 'movie' && $subID && $subResource2 === 'theater' && $subID2) {
    //             $stmt = $conn->prepare('SELECT * FROM daterange
    //                                             WHERE Movie_ID = ? AND Theater_ID=?');
    //                     $stmt->bind_param('ii', $subID, $subID2);
    //                     $stmt->execute();
    //                     $result = $stmt->get_result();
    //                     if ($result->num_rows === 0) {
    //                         echo json_encode("There are no date ranges for this theater yet.");
    //                     } else {
    //                         echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    //                     }
                        
    //         } else {
    //             echo json_encode("Error with your request.");
    //         }
    //     }

    //     if ($method == 'POST') {
    //         if ($subResource === 'movie' && $subID && $subResource2 === 'theater' && $subID2) {
    //             // $stmt = $conn->prepare('SELECT * FROM daterange
    //             //                                 WHERE Movie_ID = ? AND Theater_ID=?');
    //             //         $stmt->bind_param('ii', $subID, $subID2);
    //             //         $stmt->execute();
    //             //         $result = $stmt->get_result();
    //             //         if ($result->num_rows === 0) {
    //             //             echo json_encode("There are no date ranges for this theater yet.");
    //             //         } else {
    //             //             echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    //             //         }
                        
    //         } else {
    //             echo json_encode("Error with your request.");
    //         }
    //     }
        
    // }
?>