<?php 
    header("Content-Type: application/json");
    include 'peakscinemas_database.php';

    require_once 'vendor/autoload.php';

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    $jwt_secret = '6bcfd225e5e2a38f3682734905a6984c5b1883abbe56128ef1d8d5729e428dab';

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

    function admin_surely($secret, $level) {
        $headers = apache_request_headers();
        $auth_header = $headers['Authorization'] ?? '';

        if (!$auth_header) {
            http_response_code(401);
            die(json_encode(["error" => "Missing authorization."]));
        }

        $token = str_replace('Bearer ', '', $auth_header);

        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));

            $decoded_array = (array)$decoded;

            if ($decoded_array['role'] !== 'admin') {
                http_response_code(403);
                die(json_encode(["error" => "You are not an admin, I'm afraid."]));
            } else {
                if ($decoded_array['access_level'] < $level) {
                    http_response_code(403);
                    die(json_encode(["error" => "You do not have sufficient credentials to perform this task, I'm afraid."]));
                }
            }

            return $decoded_array;
        } catch (\Firebase\JWT\ExpiredException $e) {
            http_response_code(401);
            die(json_encode(["error" => "Session has expired, please log in again."]));
        } catch (Exception $e) {
            http_response_code(401);
            die(json_encode(["error" => "Insufficient credentials."]));
        }
    }

    switch ($resource) {
        case 'customer_login':
            break;
        case 'admin_login':
            if ($method == 'POST') {
                $data = json_decode(file_get_contents("php://input"), true);

                $email = $data['Email'] ?? '';
                $password = $data['Password'] ?? '';

                if (empty($email) || empty($password)) {
                    http_response_code(400);
                    die(json_encode(["error" => "Email and password are required."]));
                }

                $stmt = $conn->prepare("SELECT Admin_ID, Email, AdminPassword, AccessLevel FROM admin WHERE Email = ?");
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $admin = $result->fetch_assoc();

                    if (password_verify($password, $admin['AdminPassword'])) {
                        $payload = [
                            'iss' => 'http://localhost/Peak-Redux-Repo/PeaksCinema',
                            'iat' => time(),
                            'exp' => time() + (60 * 60 * 12),
                            'id' => $admin['Admin_ID'],
                            'role' => 'admin',
                            'access_level' => $admin['AccessLevel']
                        ];

                        $jwt = JWT::encode($payload, $jwt_secret, 'HS256');

                        echo json_encode(["status" => "Success!!!!!", "token" => $jwt]);
                        exit();
                    } else {
                        http_response_code(404);
                        echo json_encode(["error" => "Wrong Email or Password."]);
                        exit();
                    }
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Wrong Email or Password."]);
                    exit();
                }
            }
            break;
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
                if ($ID !== null && $subResource === 'theater' && $subID !== null && $subResource2 === 'movie' && $subID2 !== null) {
                    $data = json_decode(file_get_contents('php://input'), true);

                    $Theater_ID = $subID;
                    $Movie_ID = $subID2;
                    $StartDate = $data['StartDate'];
                    $EndDate = $data['EndDate'] ?? null;
                    $timeslots = $data['timeslots'];
                    
                    $conn->begin_transaction();
                    try {
                        $stmt = $conn->prepare("INSERT INTO daterange (Movie_ID, Theater_ID, StartDate, EndDate)
                                            VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("iiss", $Movie_ID, $Theater_ID, $StartDate, $EndDate);
                        $stmt->execute();

                        $DateRange_ID = $conn->insert_id;

                        $ScreeningType = $data['ScreeningType'];

                        $seats_stmt = $conn->prepare("SELECT Seat_ID FROM seats WHERE Theater_ID = ?");
                        $seats_stmt->bind_param("i", $Theater_ID);
                        $seats_stmt->execute();
                        $seatLayout = $seats_stmt->get_result();
                        $seats = $seatLayout->fetch_all(MYSQLI_ASSOC);

                        $stmt2 = $conn->prepare("INSERT INTO timeslot (StartTime, Date, ScreeningType, Movie_ID, Theater_ID, DateRange_ID)
                                                 VALUES (?, ?, ?, ?, ?, ?)");

                        $screeningSeatsToDb_stmt = $conn->prepare("INSERT INTO seat_timeslot (Seat_ID, TimeSlot_ID, SeatPrice, SeatAvailability)
                                                                   VALUES (?, ?, ?, ?)");

                        foreach ($timeslots as $timeslot) {                            
                            $date = $timeslot['date'];
                            $time = $timeslot['timeslot'];
                            
                            $stmt2->bind_param("sssiii", $time, $date, $ScreeningType, $Movie_ID, $Theater_ID, $DateRange_ID);
                            $stmt2->execute();

                            $TimeSlot_ID = $conn->insert_id;
                            
                            $SeatPrice = $data['SeatPrice'];
                            $SeatAvailability = 1;

                            foreach ($seats as $row) {
                                $screeningSeatsToDb_stmt->bind_param("iidi", $row['Seat_ID'], $TimeSlot_ID, $SeatPrice, $SeatAvailability);
                                $screeningSeatsToDb_stmt->execute();
                            }
                        }                        
                        $conn->commit();
                        echo json_encode(["status" => "Success !", "DateRange_ID" => $DateRange_ID]);
                    } catch (mysqli_sql_exception $e) {
                        $conn->rollback();
                        echo json_encode(["error" => $e->getMessage()]);
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
                admin_surely($jwt_secret, 1);
                if ($ID === null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $movieName = trim($_POST['MovieName'] ?? '');
                    $movieDescription = trim($_POST['MovieDescription'] ?? '');
                    $genre = trim($_POST['Genre'] ?? '');
                    $rating = trim($_POST['Rating'] ?? '');
                    $runtime = trim($_POST['Runtime'] ?? '');
                    $trailerURL = trim($_POST['TrailerURL'] ?? '');

                    $uploadPath = null;
                    if (isset($_FILES['MoviePoster']) && $_FILES['MoviePoster']['error'] == 0) {
                        $posterFolder = $_SERVER['DOCUMENT_ROOT'] . '/PeaksCinema/MoviePosters';
                        if (!is_dir($posterFolder)) {
                            mkdir($posterFolder, 0755, true);
                        }
                        $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $movieName) . '.' . pathinfo($_FILES['MoviePoster']['name'], PATHINFO_EXTENSION);
                        $uploadPath = $posterFolder . "/" . $fileName;

                        move_uploaded_file($_FILES['MoviePoster']['tmp_name'], $uploadPath);

                        $relativePath = 'PeaksCinema/MoviePosters/' . $fileName; 
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

                                            
                    $conn->begin_transaction();
                    try {
                        $stmt->bind_param('ssssiss',
                                        $movieName,
                                        $movieDescription,
                                        $genre,
                                        $rating,
                                        $runtime,
                                        $relativePath,
                                        $trailerURL);
                        $stmt->execute();
                        $conn->commit();
                        http_response_code(200);
                        echo json_encode(["status" => "Success !"]);
                    } catch (mysqli_sql_exception $e) {
                        $conn->rollback();
                        http_response_code(404);
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }

                if ($ID !== null && $subResource === 'poster' && $subID === null && $subResource2 === null && $subID2 === null) {
                    $movieName = $_POST['MovieName'];
                    $uploadPath = null;       
                    $relativePath = null;             
                    
                    if (isset($_FILES['MoviePoster']) && $_FILES['MoviePoster']['error'] == 0) {
                        $posterFolder = $_SERVER['DOCUMENT_ROOT'] . '/PeaksCinema/MoviePosters';
                        if (!is_dir($posterFolder)) {
                            mkdir($posterFolder, 0755, true);
                        }
                        $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $movieName) . '.' . pathinfo($_FILES['MoviePoster']['name'], PATHINFO_EXTENSION);
                        $uploadPath = $posterFolder . "/" . $fileName;

                        if (file_exists($uploadPath)) {
                            unlink($uploadPath);                            
                        }
                        move_uploaded_file($_FILES['MoviePoster']['tmp_name'], $uploadPath);
                        $relativePath = 'PeaksCinema/MoviePosters/' . $fileName;
                    } else {
                        exit;
                    }

                    $stmt = $conn->prepare('UPDATE movie
                                            SET 
                                            MoviePoster = ?
                                            WHERE Movie_ID = ?');
                                            
                    $conn->begin_transaction();
                    try {
                        $stmt->bind_param('si', $relativePath, $ID);
                        $stmt->execute();
                        $conn->commit();
                        http_response_code(200);
                        echo json_encode(["status" => "Success !"]);
                    } catch (mysqli_sql_exception $e) {
                        $conn->rollback();
                        http_response_code(404);
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }
            }

            if ($method == 'PUT') {
                if ($ID !== null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $data = json_decode(file_get_contents('php://input'), true);

                    $Movie_ID = $ID;
                    $movieName = trim($data['MovieName'] ?? '');
                    $movieDescription = trim($data['MovieDescription'] ?? '');
                    $genre = trim($data['Genre'] ?? '');
                    $rating = trim($data['Rating'] ?? '');
                    $runtime = trim($data['Runtime'] ?? '');
                    $runtime = (int)$runtime;
                    $trailerURL = trim($data['TrailerURL'] ?? '');

                    $stmt = $conn->prepare('UPDATE movie
                                            SET 
                                            MovieName = ?,
                                            MovieDescription = ?,
                                            Genre = ?,
                                            Rating = ?,
                                            Runtime = ?,
                                            TrailerURL = ?
                                            WHERE Movie_ID = ?');
                                            
                    $conn->begin_transaction();
                    try {
                        $stmt->bind_param('ssssisi',
                                        $movieName,
                                        $movieDescription,
                                        $genre,
                                        $rating,
                                        $runtime,
                                        $trailerURL,
                                        $Movie_ID);
                        $stmt->execute();
                        $conn->commit();
                        http_response_code(200);
                        echo json_encode(["status" => "Success !"]);
                    } catch (mysqli_sql_exception $e) {
                        $conn->rollback();
                        http_response_code(404);
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }                
            }

            if ($method == 'DELETE') {
                if ($ID !== null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $stmt = $conn->prepare("DELETE FROM movie WHERE Movie_ID = ?");
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
                            echo json_encode(["error" => "That movie does not exist."]);
                        }                        
                    } catch (mysqli_sql_exception $e) {
                        $conn->rollback();
                        http_response_code(500);
                        echo json_encode(["status" => "Failed to delete movie" . $e->getMessage()]);
                    }
                }
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
                else if (($ID !== null && $subResource === "seats" && $subID === null && $subResource2 === null && $subID2 === null)) {
                    try {
                        $stmt = $conn->prepare('SELECT theater.Theater_ID, theater.TheaterName, theater.TheaterType, seats.SeatRow, seats.SeatColumn FROM theater
                                            INNER JOIN seats ON seats.Theater_ID= theater.Theater_ID
                                            WHERE theater.Theater_ID = ?');
                        $stmt->bind_param('i', $ID);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            echo json_encode(["error" => "No Theater exists for that"]);
                        } else {
                            $theaterInfo = null;
                            $rows = [];
                            foreach ($result as $row) {
                                $theaterInfo = [
                                    "Theater_ID" => $row['Theater_ID'],
                                    "TheaterName" => $row['TheaterName'],
                                    "TheaterType" => $row['TheaterType']
                                ];
                                $rowIndex = $row['SeatRow'];
                                $colIndex = $row['SeatColumn'];
                                $rows[$rowIndex][] = [
                                    "SeatColumn" => $colIndex
                                ];
                            }
                            echo json_encode(["data" => ["theater" => $theaterInfo, "seats" => $rows]]);
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => $e->getMessage()]);
                    }                    
                }                
            }

            if ($method == 'POST') {
                $jsonLocation = file_get_contents($_FILES["theaterLayoutUp"]["tmp_name"]);
                $seatLayout = json_decode($jsonLocation, true);
                
                // input cleanup so that the inputted data will be clean (unless the admin themself spams random letters. cant do anything about that... (i mean you can its just another can of worms))
                function input_cleanup($data) {
                    $data = trim($data);
                    $data = stripslashes($data);
                    return $data;
                }

                // prepared statement for the theater table. basically it prepares the insertion of values to the table so that it's safe to upload
                $theater_to_db_stmt = $conn -> prepare("INSERT INTO theater(TheaterName, TheaterType, TotalSeats)
                                                        VALUES ( ?, ?, ?)");
                $theater_to_db_stmt -> bind_param("ssi", $TheaterName, $TheaterType, $TotalSeats);
                
                $TheaterName = input_cleanup($_POST['theaterName']);
                $TheaterType = input_cleanup($_POST['theaterType']);
                $TotalSeats = 50;
                
                $conn->begin_transaction();
                try {
                    $theater_to_db_stmt -> execute();
                    $Theater_ID = $conn -> insert_id;

                    $seats_to_db_stmt = $conn -> prepare("INSERT INTO seats(SeatRow, SeatColumn, Theater_ID)
                                                        VALUES (?, ?, ?)");
                    $seats_to_db_stmt -> bind_param("sii", $SeatRow, $SeatColumn, $Theater_ID);

                    // this is where the seats table gets inserted
                    foreach ($seatLayout['seats'] as $SeatRow => $cols) {
                        foreach ($cols as $seat) {
                            $SeatColumn = $seat['SeatColumn'];

                            $seats_to_db_stmt -> execute();
                        }
                    }
                    $conn->commit();
                    http_response_code(200);
                    echo json_encode(["status" => "Success !"]);
                } catch (mysqli_sql_exception $e) {
                    $conn->rollback();
                    http_response_code(404);
                    echo json_encode(["error" => "Failed to add theater."]);
                }                
            }

            if ($method == 'DELETE') {
                if ($ID !== null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $stmt = $conn->prepare("DELETE FROM theater WHERE Theater_ID = ?");
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
                            echo json_encode(["error" => "That theater does not exist."]);
                        }                        
                    } catch (mysqli_sql_exception $e) {
                        $conn->rollback();
                        http_response_code(500);
                        echo json_encode([
                            "status" => "Failed to delete theater",
                            "error" => $e->getMessage()
                        ]);
                    }
                }
            }
            
            break;
        case 'ticket':
            break;
        case 'timeslot':
            break;
        case 'monthly_sales':
            if ($method == 'GET') {
                if ($ID !== null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $emptyMonths = [
                        "January" => 0, "February" => 0, "March" => 0, "April" => 0, "May" => 0, "June" => 0, 
                        "July" => 0, "August" => 0, "September" => 0, "October" => 0, "November" => 0, "December" => 0
                    ];
                    $stmt = $conn->prepare('SELECT
                                                MONTHNAME(timeslot.Date) AS Month_Name,
                                                SUM(seat_timeslot.SeatPrice) AS Total FROM `seat_timeslot` 
                                            INNER JOIN timeslot ON timeslot.TimeSlot_ID = seat_timeslot.TimeSlot_ID 
                                            WHERE seat_timeslot.SeatAvailability = 0 
                                            AND YEAR(timeslot.Date) = ? 
                                            GROUP BY Month_Name
                                            ORDER BY MONTH(timeslot.Date) ASC');
                    try {
                        $stmt->bind_param('i', $ID);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $final_result = [];
                        while ($row = mysqli_fetch_assoc($result)) {
                            $emptyMonths[$row["Month_Name"]] = (float)$row["Total"];
                        }

                        foreach ($emptyMonths as $month => $total) {
                            $final_result[] = ["month" => $month, "revenue" => $total];
                        }

                        http_response_code(200);
                        echo json_encode(["data" => $final_result]);

                    } catch (mysqli_sql_exception $e) {
                        http_response_code(400);
                        echo json_encode(["error" => "An error has been made processing your request."]);
                    }

                }
            }
            break;
        case 'theater_sales':
            if ($method == 'GET') {
                if ($ID !== null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $emptyMonths = [
                        "January" => 0, "February" => 0, "March" => 0, "April" => 0, "May" => 0, "June" => 0, 
                        "July" => 0, "August" => 0, "September" => 0, "October" => 0, "November" => 0, "December" => 0
                    ];                
                    
                    $theater_final = [];

                    try {
                        $type_stmt = $conn->prepare('SELECT DISTINCT TheaterType FROM theater');
                        $type_stmt->execute();
                        $type_result = $type_stmt->get_result();
                        $stmt = $conn->prepare('SELECT MONTHNAME(timeslot.Date) AS Month_Name,
                                                        theater.TheaterType AS TheaterType,
                                                        SUM(seat_timeslot.SeatPrice) AS Total FROM `seat_timeslot`
                                                INNER JOIN timeslot ON timeslot.TimeSlot_ID = seat_timeslot.TimeSlot_ID
                                                INNER JOIN daterange ON daterange.DateRange_ID = timeslot.DateRange_ID
                                                INNER JOIN theater ON theater.Theater_ID = daterange.Theater_ID
                                                WHERE theater.TheaterType = ? AND seat_timeslot.SeatAvailability = 0
                                                AND YEAR(timeslot.Date) = ?  
                                                GROUP BY Month_Name
                                                ORDER BY MONTH(timeslot.Date) ASC');                            

                        foreach ($type_result as $type) {
                            $monthlySales = $emptyMonths;
                            $stmt->bind_param('si', $type['TheaterType'], $ID);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while ($row = $result->fetch_assoc()) {
                                $monthlySales[$row['Month_Name']] = (float)$row['Total'];
                            }
                            
                            $final_result = [];

                            foreach ($monthlySales as $month => $total) {
                                $final_result[] = ["month" => $month, "revenue" => $total];
                            }

                            $theater_final[] = ["type" => $type['TheaterType'], "year" => $final_result];
                        }

                        http_response_code(200);
                        echo json_encode(["data" => $theater_final]);

                    } catch (mysqli_sql_exception $e) {
                        http_response_code(400);
                        echo json_encode(["error" => "An error has been made processing your request."]);
                    }
                }              
            }            
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