<?php 
    header("Content-Type: application/json");
    include 'peakscinemas_database.php';

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $method = $_SERVER['REQUEST_METHOD'];
    $request = $_GET['request'] ?? '';
    $parts = explode('/', trim($request, '/'));

    $resource = $parts[0] ?? null;
    $ID = $parts[1] ?? null;
    
    $subResource = $parts[2] ?? null;
    $subID = $parts[3] ?? null;
    $subResource2 = $parts[4] ?? null;
    $subID2 = $parts[5] ?? null;

    $date = $_GET['date'] ?? null;

    switch ($resource) {
        case 'customer':
            if ($method == 'GET') {
                if ($ID === null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $stmt = $conn->prepare('SELECT Customer_ID, FirstName, LastName, Email, PhoneNumber, CountryCode, PaymentMethod FROM customer');
                    
                    try {
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            echo json_encode(["error" => "There are no customers yet."]);
                        } else {
                            echo json_encode(["data" => $result->fetch_all(MYSQLI_ASSOC)]);
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }
            }
            break;
        case 'daterange':
            if ($method == 'GET') {
                if ($ID === null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $stmt = $conn->prepare('SELECT * FROM daterange');
                    
                    try {
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            echo json_encode(["error" => "There are no date ranges yet."]);
                        } else {
                            echo json_encode(["data" => $result->fetch_all(MYSQLI_ASSOC)]);
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                } else if ($ID !== null && $subResource === 'movie' && $subID !== null && $subResource2 === 'theater' && $subID2 !== null) { 
                    $stmt = $conn->prepare('SELECT DISTINCT daterange.DateRange_ID, daterange.StartDate, daterange.EndDate, timeslot.StartTime FROM daterange
                                            INNER JOIN timeslot
                                            ON timeslot.DateRange_ID = daterange.DateRange_ID
                                            WHERE daterange.Movie_ID = ? AND daterange.Theater_ID=?');
                            $stmt->bind_param('ii', $subID, $subID2);
                            
                            try {
                                $stmt->execute();
                                $result = $stmt->get_result();
                                if ($result->num_rows === 0) {
                                    echo json_encode(["error" => "There are no date ranges for this theater yet."]);
                                } else {
                                    $rows = $result->fetch_all(MYSQLI_ASSOC);
                                    $grouped = [];

                                    foreach ($rows as $row) {
                                        $id = $row['DateRange_ID'];
                                        if (!isset($grouped[$id])) {
                                            $grouped[$id] = [
                                                "DateRange_ID" => $row['DateRange_ID'],
                                                "StartDate" => $row['StartDate'],
                                                "EndDate" => $row['EndDate'],
                                                "Timeslots" => []
                                            ];
                                        }
                                        if (isset($row['StartTime']) && $row['StartTime'] !== null) { 
                                            $grouped[$id]['Timeslots'][] = [
                                                "StartTime" => $row['StartTime']
                                            ];
                                        }
                                    }
                                    echo json_encode(["data" => array_values($grouped)]);
                                }
                            } catch (mysqli_sql_exception $e) {
                                echo json_encode(["error" => $e->getMessage()]);
                            }
                            
                } else {
                    echo json_encode("Error with your request.");
                }                       
            }

            if ($method == 'POST') {
                if ($subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
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

                        $checkMovie = $conn->prepare("SELECT Movie_ID FROM movie WHERE Movie_ID = ?");
                        $checkMovie->bind_param("i", $Movie_ID);
                        $checkMovie->execute();
                        $movieResult = $checkMovie->get_result();
                        
                        $conn->begin_transaction();
                        try {
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

                                $TimeSlot_ID = $conn->insert_id;

                                $seats_stmt = $conn->prepare("SELECT Seat_ID FROM seats WHERE Theater_ID = ?");
                                $seats_stmt->bind_param("i", $Theater_ID);
                                $seats_stmt->execute();
                                $seatLayout = $seats_stmt->get_result();

                                $screeningSeatsToDb_stmt = $conn->prepare("INSERT INTO seat_timeslot (Seat_ID, TimeSlot_ID, SeatPrice, SeatAvailability)
                                                                            VALUES (?, ?, ?, ?)");
                                $SeatPrice = 350; // TEMP
                                $SeatAvailability = 1;

                                while ($row = $seatLayout->fetch_assoc()) {
                                    $screeningSeatsToDb_stmt->bind_param("iiii", $row['Seat_ID'], $TimeSlot_ID, $SeatPrice, $SeatAvailability);
                                    $screeningSeatsToDb_stmt->execute();
                                }

                                $conn->commit();
                                echo json_encode(["status" => "Success !", "DateRange_ID" => $DateRange_ID]);
                            }                        
                        } catch (mysqli_sql_exception $e) {
                            $conn->rollback();
                            echo json_encode(["error" => $e->getMessage()]);
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

                    $conn->begin_transaction();

                    try {
                        $stmt->execute();
                        if ($stmt->affected_rows > 0) {
                            $conn->commit();
                            http_response_code(200);
                            echo json_encode(["status" => "Success !"]);
                        } else {
                            $conn->rollback();
                            http_response_code(404);
                            echo json_encode(["error" => "That date range does not exist."]);
                        }                        
                    } catch (mysqli_sql_exception $e) {
                        $conn->rollback();
                        http_response_code(500);
                        echo json_encode(["status" => "Failed to delete daterange" . $e->getMessage()]);
                    }
                }
            }
            break;
        case 'movie':
            if ($method == 'GET') {
                if ($ID === null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $stmt = $conn->prepare('SELECT * FROM movie');                    
                    try {
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            echo json_encode(["error" => "There are no movies yet."]);
                        } else {
                            echo json_encode(["data" => $result->fetch_all(MYSQLI_ASSOC)]);
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }
                else if ($ID !== null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $stmt = $conn->prepare('SELECT * FROM movie WHERE Movie_ID = ?');
                    $stmt->bind_param("i", $ID);
                    try {
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            echo json_encode(["error" => "There are no movies yet."]);
                        } else {
                            echo json_encode(["data" => $result->fetch_all(MYSQLI_ASSOC)]);
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }
                else if ($ID !== null && $subResource === 'theaters' && $subID === null && $subResource2 === null && $subID2 === null && $date !== null) {
                    try {
                        $stmt = $conn->prepare('SELECT DISTINCT theater.Theater_ID, theater.TheaterName FROM theater
                                            INNER JOIN daterange ON daterange.Theater_ID = theater.Theater_ID
                                            INNER JOIN movie ON movie.Movie_ID = daterange.Movie_ID
                                            INNER JOIN timeslot ON timeslot.DateRange_ID = daterange.DateRange_ID
                                            WHERE movie.Movie_ID = ? AND timeslot.date = ?');
                        $stmt->bind_param('is', $ID, $date);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            echo json_encode(["data" => "No Available Theaters."]);
                        } else {
                            echo json_encode(["data" => $result->fetch_all(MYSQLI_ASSOC)]);
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => $e->getMessage()]);
                    }                    
                }
                else if ($ID !== null && $subResource === 'theater' && $subID !== null && $subResource2 === null && $subID2 === null) {
                    try {
                        $stmt = $conn->prepare('SELECT DISTINCT timeslot.TimeSlot_ID, timeslot.StartTime FROM timeslot
                                            INNER JOIN daterange ON daterange.DateRange_ID = timeslot.DateRange_ID
                                            INNER JOIN movie ON movie.Movie_ID = daterange.Movie_ID
                                            INNER JOIN theater ON theater.Theater_ID = daterange.Theater_ID
                                            WHERE movie.Movie_ID = ? AND theater.Theater_ID = ? AND date = ?');
                        $stmt->bind_param('iis', $ID, $subID, $date);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            echo json_encode(["data" => "No Available Timeslots."]);
                        } else {
                            echo json_encode(["data" => $result->fetch_all(MYSQLI_ASSOC)]);
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => $e->getMessage()]);
                    }                    
                }
                else if ($ID !== null && $subResource === 'theater' && $subID !== null && $subResource2 === 'timeslot' && $subID2 !== null) {
                    try {
                        $stmt = $conn->prepare('SELECT SeatTimeSlot_ID, seats.SeatRow, seats.SeatColumn, seat_timeslot.SeatPrice, seat_timeslot.SeatAvailability FROM seat_timeslot
                                            INNER JOIN seats ON seats.Seat_ID = seat_timeslot.Seat_ID
                                            INNER JOIN timeslot ON timeslot.TimeSlot_ID = seat_timeslot.TimeSlot_ID
                                            WHERE timeslot.TimeSlot_ID = ?');
                        $stmt->bind_param('i', $subID2);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            echo json_encode(["data" => "No Available Timeslots."]);
                        } else {
                            $rows = [];
                            foreach ($result as $seat) {
                                $rowIndex = $seat['SeatRow'];
                                $colIndex = $seat['SeatColumn'];
                                $rows[$rowIndex][] = [
                                    "SeatColumn" => $colIndex,
                                    "SeatTimeSlot_ID" => $seat['SeatTimeSlot_ID'],
                                    "SeatPrice" => $seat['SeatPrice'],
                                    "SeatAvailability" => $seat['SeatAvailability']
                                ];
                            }
                            echo json_encode(["data" => $rows]);
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => $e->getMessage()]);
                    }                    
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
            if ($method == 'GET') {
                if ($ID === null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $stmt = $conn->prepare('SELECT * FROM payment');
                    
                    try {
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            echo json_encode(["error" => "There are no customers yet."]);
                        } else {
                            echo json_encode(["data" => $result->fetch_all(MYSQLI_ASSOC)]);
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }
            }
            break;
        case 'seats':
            
            break;
        case 'seat_timeslot':
            if ($method == 'PUT') {
                $data = json_decode(file_get_contents("php://input"), true);

                if ($data) {
                    $seats = $data['seats'];
                    $SeatAvailability = 0;

                    $stmt = $conn->prepare("UPDATE seat_timeslot SET SeatAvailability = ? WHERE SeatTimeSlot_ID = ?");
                    
                    try {
                        foreach ($seats as $SeatTimeSlot_ID) {
                            $stmt->bind_param("ii", $SeatAvailability, $SeatTimeSlot_ID);
                            $stmt->execute();
                        }
                        echo json_encode(["status" => "Success!"]);
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }
            }

            break;
        case 'theater':
            if ($method == 'GET') {
                if (($ID === null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null)) {
                    $stmt = $conn->prepare('SELECT * FROM theater');                    
                    try {
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            echo json_encode(["error" => "There are no theaters yet."]);
                        } else {
                            echo json_encode(["data" => $result->fetch_all(MYSQLI_ASSOC)]);
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }
            }
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

    // if ($subResource === 'movie' && $subID && $subResource2 === 'theater' && $subID2) {
    //                     $data = json_decode(file_get_contents("php://input"), true);

    //                     if ($data) {
    //                         $Theater_ID = $data['Theater_ID'];
    //                         $Movie_ID   = $data['Movie_ID'];
    //                         $StartDate  = $data['StartDate'];
    //                         $EndDate    = $data['EndDate'];

    //                         $checkTheater = $conn->prepare("SELECT Theater_ID FROM theater WHERE Theater_ID = ?");
    //                         $checkTheater->bind_param("i", $Theater_ID);
    //                         $checkTheater->execute();
    //                         $checkResult = $checkTheater->get_result();
    //                         if ($checkResult->num_rows === 0) {
    //                             die("Invalid Theater_ID: " . $Theater_ID);
    //                         }

    //                         $checkMovie = $conn->prepare("SELECT Movie_ID FROM movie WHERE Movie_ID = ?");
    //                         $checkMovie->bind_param("i", $Movie_ID);
    //                         $checkMovie->execute();
    //                         $movieResult = $checkMovie->get_result();
    //                         if ($movieResult->num_rows === 0) {
    //                             die("Invalid Movie_ID: " . $Movie_ID);
    //                         }

    //                         $stmt = $conn->prepare("INSERT INTO daterange (Movie_ID, Theater_ID, StartDate, EndDate)
    //                                                 VALUES (?, ?, ?, ?)");
    //                         $stmt->bind_param("iiss", $Movie_ID, $Theater_ID, $StartDate, $EndDate);
    //                         $stmt->execute();

    //                         $DateRange_ID = $conn->insert_id;

    //                         $ScreeningType = "2D"; // TEMP

    //                         foreach ($data['timeslots'] as $timeslot) {
    //                             $date = $timeslot['date'];
    //                             $time = $timeslot['timeslot'];

    //                             $stmt2 = $conn->prepare("INSERT INTO timeslot (StartTime, Date, ScreeningType, Movie_ID, Theater_ID, DateRange_ID)
    //                                                     VALUES (?, ?, ?, ?, ?, ?)");
    //                             $stmt2->bind_param("sssiii", $time, $date, $ScreeningType, $Movie_ID, $Theater_ID, $DateRange_ID);
    //                             if (!$stmt2->execute()) {
    //                                 die("Error inserting timeslot: " . $stmt2->error);
    //                             }

    //                             $TimeSlot_ID = $conn->insert_id;

    //                             $seats_stmt = $conn->prepare("SELECT Seat_ID FROM seats WHERE Theater_ID = ?");
    //                             $seats_stmt->bind_param("i", $Theater_ID);
    //                             $seats_stmt->execute();
    //                             $seatLayout = $seats_stmt->get_result();

    //                             $SeatPrice = 350; // TEMP
    //                             $SeatAvailability = 1;

    //                             while ($row = $seatLayout->fetch_assoc()) {
    //                                 $screeningSeatsToDb_stmt = $conn->prepare("INSERT INTO seat_timeslot (Seat_ID, TimeSlot_ID, SeatPrice, SeatAvailability)
    //                                                                         VALUES (?, ?, ?, ?)");
    //                                 $screeningSeatsToDb_stmt->bind_param("iiii", $row['Seat_ID'], $TimeSlot_ID, $SeatPrice, $SeatAvailability);
    //                                 $screeningSeatsToDb_stmt->execute();
    //                             }
    //                         }
    //                     }
    //                 }
?>