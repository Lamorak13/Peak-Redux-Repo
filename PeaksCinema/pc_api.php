<?php 
    header("Content-Type: application/json");
    include 'peakscinemas_database.php';

    require_once 'vendor/autoload.php';

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

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
        case 'customer_signup':
            if ($method == 'POST') {            
                function input_cleanup($data) {
                    return stripslashes(trim($data));
                }

                $lastName      = input_cleanup($_POST["lastName"] ?? "");
                $firstName     = input_cleanup($_POST["firstName"] ?? "");
                $email         = input_cleanup($_POST["email"] ?? "");
                $passwordPlain = input_cleanup($_POST["password"] ?? "");
                $confirmPassword = input_cleanup($_POST["confirmPassword"] ?? "");
                $countryCode   = input_cleanup($_POST["countryCode"] ?? "");
                $phoneNumber   = input_cleanup($_POST["phoneNumber"] ?? "");

                // Name validation
                if (!preg_match("/^[a-zA-Z-' ]+$/", $lastName)) {
                    echo json_encode(["error" => "Invalid last name"]);
                    exit();
                }
                if (!preg_match("/^[a-zA-Z-' ]+$/", $firstName)) {
                    echo json_encode(["error" => "Invalid first name"]);
                    exit();
                }

                // Email validation
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {                    
                    echo json_encode(["error" => "Invalid email."]);
                    exit();
                }

                // Password match check
                if ($passwordPlain !== $confirmPassword) {                
                    echo json_encode(["error" => "Passwords do not match."]);
                    exit();
                }

                // ==================== PASSWORD VALIDATION (FIXED) ====================
                if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,64}$/', $passwordPlain)) {                    
                    echo json_encode(["error" => "Password must be 8-64 characters long and include at least one uppercase letter, one lowercase letter, one number (0-9), and one symbol from: @$!%*?&"]);
                    exit();
                }
                // ===================================================================

                $password = password_hash($passwordPlain, PASSWORD_DEFAULT);

                if (!empty($phoneNumber)) {
                    if (!preg_match("/^\+[0-9]{1,4}$/", $countryCode)) {
                        echo json_encode(["error" => "Invalid country code."]);
                        exit();
                    }
                    // Phone number validation
                    if (!preg_match("/^9[0-9]{9}$/", $phoneNumber)) {
                        echo json_encode(["error" => "Invalid phone number. Must start with 9 and be exactly 10 digits."]);
                        exit();
                    }
                } else {
                    $countryCode = "";
                }            

                // Check if email already exists
                $check = $conn->prepare("SELECT Customer_ID FROM customer WHERE Email = ?");
                $check->bind_param("s", $email);
                $check->execute();
                $checkResult = $check->get_result();

                if ($checkResult->num_rows > 0) {
                    http_response_code(409);
                    echo json_encode(["error" => "Email already exists. Please log in instead using that email."]);
                } else {
                    $conn->begin_transaction();       
                    $sql = $conn->prepare("
                        INSERT INTO customer (LastName, FirstName, Email, Password, CountryCode, PhoneNumber)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");

                    $sql->bind_param("ssssss", $lastName, $firstName, $email, $password, $countryCode, $phoneNumber);

                    if ($sql->execute()) {
                        $conn->commit();
                        http_response_code(200);
                        echo json_encode(["status" => "Sign up successful! Please log in."]);
                        // echo "<script>
                        //     document.getElementById('signupForm').style.display = 'none';
                        //     document.getElementById('loginForm').style.display = 'block';
                        // </script>";
                    } else {
                        $conn->rollback();
                        http_response_code(400);
                        echo json_encode(["error" => "An error occurred. Please try again."]);
                    }

                }
            }
            break;
        case 'customer_login':
            if ($method == 'POST') {
                $data = json_decode(file_get_contents("php://input"), true);

                $email = $data['loginEmail'] ?? '';
                $password = $data['loginPassword'] ?? '';

                if (empty($email) || empty($password)) {
                    http_response_code(400);
                    die(json_encode(["error" => "Email and password are required."]));
                }

                $stmt = $conn->prepare("SELECT Customer_ID, Password FROM customer WHERE Email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute(); 
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $customer = $result->fetch_assoc();

                    if (password_verify($password, $customer['Password'])) {
                        $payload = [
                            'iss' => 'http://localhost/Peak-Redux-Repo/PeaksCinema',
                            'iat' => time(),
                            'exp' => time() + (60 * 60 * 12),
                            'id' => $customer['Customer_ID'],
                            'role' => 'customer'
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
                        // Force password reset while the temp password is still in use.
                        // This is checked against the stored hash (not the entered password),
                        // so it works for newly created admins and after admin-initiated resets.
                        $must_reset = password_verify('admin1234', $admin['AdminPassword']);

                        $payload = [
                            'iss' => 'http://localhost/Peak-Redux-Repo/PeaksCinema',
                            'iat' => time(),
                            'exp' => time() + (60 * 60 * 12),
                            'id' => $admin['Admin_ID'],
                            'role' => 'admin',
                            'access_level' => $admin['AccessLevel'],
                            'must_reset' => $must_reset
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
        case 'admin_password':
            // Allows an authenticated admin to set their own password.
            if ($method === 'PUT') {
                $decoded = admin_surely($jwt_secret, 0);
                $adminId = intval($decoded['id'] ?? 0);

                if ($adminId <= 0) {
                    http_response_code(401);
                    die(json_encode(["error" => "Invalid session."]));
                }

                $data = json_decode(file_get_contents("php://input"), true);
                $newPassword = $data['newPassword'] ?? '';

                if (!is_string($newPassword)) {
                    http_response_code(400);
                    die(json_encode(["error" => "Invalid password."]));
                }

                $newPassword = trim($newPassword);

                if (strlen($newPassword) < 8) {
                    http_response_code(400);
                    die(json_encode(["error" => "Password must be at least 8 characters."]));
                }

                if (preg_match('/\s/', $newPassword)) {
                    http_response_code(400);
                    die(json_encode(["error" => "Password cannot contain spaces."]));
                }

                if ($newPassword === 'admin1234') {
                    http_response_code(400);
                    die(json_encode(["error" => "Please choose a password different from the temporary password."]));
                }

                // Strong password policy: uppercase + lowercase + number + symbol
                if (!preg_match('/[A-Z]/', $newPassword)) {
                    http_response_code(400);
                    die(json_encode(["error" => "Password must include at least 1 uppercase letter."]));
                }
                if (!preg_match('/[a-z]/', $newPassword)) {
                    http_response_code(400);
                    die(json_encode(["error" => "Password must include at least 1 lowercase letter."]));
                }
                if (!preg_match('/[0-9]/', $newPassword)) {
                    http_response_code(400);
                    die(json_encode(["error" => "Password must include at least 1 number."]));
                }
                if (!preg_match('/[^A-Za-z0-9]/', $newPassword)) {
                    http_response_code(400);
                    die(json_encode(["error" => "Password must include at least 1 symbol (example: ! @ # $)."]));
                }

                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                try {
                    $conn->begin_transaction();

                    $stmt = $conn->prepare("UPDATE admin SET AdminPassword = ? WHERE Admin_ID = ?");
                    $stmt->bind_param("si", $hashedPassword, $adminId);

                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $conn->commit();
                        http_response_code(200);
                        echo json_encode(["status" => "Password updated successfully"]);
                    } else {
                        $conn->rollback();
                        http_response_code(404);
                        echo json_encode(["error" => "Admin not found"]);
                    }
                } catch (mysqli_sql_exception $e) {
                    $conn->rollback();
                    http_response_code(400);
                    echo json_encode(["error" => $e->getMessage()]);
                }
            }
            break;
        case 'customer':
            if ($method == 'GET') {
                if ($ID !== null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $stmt = $conn->prepare('SELECT Customer_ID, FirstName, LastName, Email, PhoneNumber, CountryCode FROM customer WHERE Customer_ID = ?');
                    
                    try {
                        $stmt->bind_param("i", $ID);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            echo json_encode(["error" => "Null for a customer."]);
                        } else {
                            echo json_encode(["data" => $result->fetch_all(MYSQLI_ASSOC)]);
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }
            }
            if ($method == 'PUT') {
                if ($ID !== null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $data = json_decode(file_get_contents('php://input'), true);

                    $FirstName = $data['FirstName'] ?? null;
                    $LastName = $data['LastName'] ?? null;
                    $Email = $data['Email'] ?? null;
                    $PhoneNumber = $data['PhoneNumber'] ?? null;
                    $CountryCode = $data['CountryCode'] ?? null;
                    $Password = $data['Password'] ?? null;
                    $hashedPassword = null;

                    if (!$FirstName) {
                        echo json_encode(["error_ln" => "First name is required."]);
                        exit();
                    }

                    if (!$LastName) {
                        echo json_encode(["error_fn" => "Last name is required."]);
                        exit();
                    }

                    if (!$Email) {
                        echo json_encode(["error_e" => "Email is required."]);
                        exit();
                    }

                    if ($Password) {
                        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,64}$/', $Password)) {                    
                            echo json_encode(["error" => "Password must be 8-64 characters long and include at least one uppercase letter, one lowercase letter, one number (0-9), and one symbol from: @$!%*?&"]);
                            exit();
                        }

                        $hashedPassword = password_hash($Password, PASSWORD_DEFAULT);                        
                    } 
                    

                    if (!empty($PhoneNumber)) {
                        if (!preg_match("/^\+[0-9]{1,4}$/", $CountryCode)) {
                            echo json_encode(["error" => "Invalid country code."]);
                            exit();
                        }
                        // Phone number validation
                        if (!preg_match("/^9[0-9]{9}$/", $PhoneNumber)) {
                            echo json_encode(["error" => "Invalid phone number. Must start with 9 and be exactly 10 digits."]);
                            exit();
                        }
                    } else {
                        $CountryCode = "";
                    }  

                    $stmt = $conn->prepare('UPDATE customer SET 
                                                                FirstName = COALESCE(NULLIF(?, ""), FirstName), 
                                                                LastName = COALESCE(NULLIF(?, ""), LastName),
                                                                Email = COALESCE(NULLIF(?, ""), Email), 
                                                                PhoneNumber = COALESCE(NULLIF(?, ""), PhoneNumber), 
                                                                CountryCode = COALESCE(NULLIF(?, ""), CountryCode), 
                                                                Password = COALESCE(NULLIF(?, ""), Password) 
                                                            WHERE Customer_ID = ?');
                    $conn->begin_transaction();
                    
                    try {
                        $stmt->bind_param("ssssssi", $FirstName, $LastName, $Email, $PhoneNumber, $CountryCode, $hashedPassword, $ID);
                        $stmt->execute();                        
                        $conn->commit();

                        echo json_encode(["status" => "SUCCESS! WOOO"]);
                    } catch (mysqli_sql_exception $e) {
                        $conn->rollback();
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
                } else if ($ID !== null && $subResource === 'theater' && $subID !== null && $subResource2 === null && $subID2 === null) {
                    $excludeMovieID = $_GET['exclude_movie'] ?? null;
                    if ($excludeMovieID !== null && $excludeMovieID !== '') {
                        $excludeMovieID = (int)$excludeMovieID;
                        $stmt = $conn->prepare('SELECT DateRange_ID, Movie_ID, StartDate, EndDate FROM daterange WHERE Theater_ID = ? AND Movie_ID <> ?');
                        $stmt->bind_param('ii', $subID, $excludeMovieID);
                    } else {
                        $stmt = $conn->prepare('SELECT DateRange_ID, Movie_ID, StartDate, EndDate FROM daterange WHERE Theater_ID = ?');
                        $stmt->bind_param('i', $subID);
                    }

                    try {
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows === 0) {
                            echo json_encode(["data" => []]);
                        } else {
                            echo json_encode(["data" => $result->fetch_all(MYSQLI_ASSOC)]);
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
                    $stmt = $conn->prepare("SELECT movie.*, MIN(daterange.StartDate) AS EarliestDate, MAX(daterange.EndDate) AS LatestDate,
                                            CASE 
                                                WHEN CURRENT_DATE BETWEEN MIN(daterange.StartDate) AND MAX(daterange.EndDate) THEN 'Now Showing'
                                                WHEN CURRENT_DATE < MIN(daterange.StartDate) THEN 'Coming Soon'
                                                ELSE 'Ended'
                                            END AS MovieAvailability
                                            FROM `movie`
                                            INNER JOIN daterange ON daterange.Movie_ID = movie.Movie_ID
                                            INNER JOIN timeslot ON timeslot.DateRange_ID = daterange.DateRange_ID
                                            GROUP BY movie.Movie_ID");                    
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
                        $stmt = $conn->prepare('SELECT theater.Theater_ID, theater.TheaterName, timeslot.TimeSlot_ID, timeslot.ScreeningType, timeslot.StartTime FROM theater
                                            INNER JOIN daterange ON daterange.Theater_ID = theater.Theater_ID
                                            INNER JOIN movie ON movie.Movie_ID = daterange.Movie_ID
                                            INNER JOIN timeslot ON timeslot.DateRange_ID = daterange.DateRange_ID
                                            WHERE movie.Movie_ID = ? AND timeslot.Date = ?
                                            AND TIMESTAMP(timeslot.Date, timeslot.StartTime) >= NOW()
                                            ORDER BY theater.TheaterName ASC, timeslot.StartTime ASC');
                        $stmt->bind_param('is', $ID, $date);
                        $stmt->execute();
                        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                        $theaters = [];

                        foreach ($result as $row) {
                            $Theater_ID = $row['Theater_ID'];
                            if (!isset($theaters[$Theater_ID])) {
                                $theaters[$Theater_ID] = [
                                    'TheaterName' => $row['TheaterName'],
                                    'Timeslots' => []
                                ];
                            }

                            $theaters[$Theater_ID]['Timeslots'][] = [
                                'TimeSlot_ID' => $row['TimeSlot_ID'],
                                'ScreeningType' => $row['ScreeningType'],
                                'StartTime' => date("g:i A", strtotime($row['StartTime']))
                            ];
                        }

                        echo json_encode(['data' => array_values($theaters)]);
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => $e->getMessage()]);
                    }                    
                }
                else if ($ID !== null && $subResource === 'timeslots' && $subID === null && $subResource2 === null && $subID2 === null && $date === null) {
                    try {
                        $stmt = $conn->prepare('SELECT timeslot.Date FROM timeslot
                                            INNER JOIN daterange ON daterange.DateRange_ID = timeslot.DateRange_ID
                                            INNER JOIN movie ON movie.Movie_ID = daterange.Movie_ID
                                            WHERE movie.Movie_ID = ? AND timeslot.Date >= CURRENT_DATE');
                        $stmt->bind_param('i', $ID);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            echo json_encode(["error" => "No Available dates."]);
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

                        $result->free();
                        $stmt->close();
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
                        $result->free();
                        $stmt->close();
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

                // if ($data) {
                //     $seats = $data['seats'];
                //     $SeatAvailability = 0;

                //     $stmt = $conn->prepare("UPDATE seat_timeslot SET SeatAvailability = ? WHERE SeatTimeSlot_ID = ?");
                    
                //     try {
                //         foreach ($seats as $SeatTimeSlot_ID) {
                //             $stmt->bind_param("ii", $SeatAvailability, $SeatTimeSlot_ID);
                //             $stmt->execute();
                //         }
                //         echo json_encode(["status" => "Success!"]);
                //     } catch (mysqli_sql_exception $e) {
                //         echo json_encode(["error" => $e->getMessage()]);
                //     }
                // }
                if ($ID !== null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {                    
                    $data = json_decode(file_get_contents("php://input"), true);


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
                        $result->free();
                        $stmt->close();
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
            if ($method == 'GET') {
                if ($ID === 'all' && $subResource === 'theater' && $subID !== null && $subResource2 === null && $subID2 === null) {
                    $startDate = $_GET['start_date'] ?? null;
                    $endDate = $_GET['end_date'] ?? null;
                    $excludeMovieID = $_GET['exclude_movie'] ?? null;

                    if ($startDate === null || $endDate === null) {
                        echo json_encode(["error" => "Missing required date range."]);
                        break;
                    }

                    try {
                        if ($excludeMovieID !== null && $excludeMovieID !== '') {
                            $excludeMovieID = (int)$excludeMovieID;
                            $stmt = $conn->prepare('SELECT timeslot.Date, timeslot.StartTime, timeslot.Movie_ID, movie.Runtime
                                                    FROM timeslot
                                                    INNER JOIN movie ON movie.Movie_ID = timeslot.Movie_ID
                                                    WHERE timeslot.Theater_ID = ?
                                                    AND timeslot.Date BETWEEN ? AND ?
                                                    AND timeslot.Movie_ID <> ?');
                            $stmt->bind_param('issi', $subID, $startDate, $endDate, $excludeMovieID);
                        } else {
                            $stmt = $conn->prepare('SELECT timeslot.Date, timeslot.StartTime, timeslot.Movie_ID, movie.Runtime
                                                    FROM timeslot
                                                    INNER JOIN movie ON movie.Movie_ID = timeslot.Movie_ID
                                                    WHERE timeslot.Theater_ID = ?
                                                    AND timeslot.Date BETWEEN ? AND ?');
                            $stmt->bind_param('iss', $subID, $startDate, $endDate);
                        }

                        $stmt->execute();
                        $result = $stmt->get_result();
                        echo json_encode(["data" => $result->fetch_all(MYSQLI_ASSOC)]);
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }
                else if ($ID !== null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    try {
                        $stmt = $conn->prepare('SELECT SeatTimeSlot_ID, seats.SeatRow, seats.SeatColumn, seat_timeslot.SeatPrice, seat_timeslot.SeatAvailability, timeslot.Date FROM seat_timeslot
                                            INNER JOIN seats ON seats.Seat_ID = seat_timeslot.Seat_ID
                                            INNER JOIN timeslot ON timeslot.TimeSlot_ID = seat_timeslot.TimeSlot_ID
                                            WHERE timeslot.TimeSlot_ID = ?');
                        $stmt->bind_param('i', $ID);
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
                            echo json_encode(["data" => $rows, "Date" => $seat['Date']]);
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => $e->getMessage()]);
                    }                    
                }
            }
            break;
        case 'receipt':
            if ($method == 'GET') {
                if ($ID !== null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    // Receipt details (one row), including ordered seat list and ticket count
                    $stmt = $conn->prepare("
                        SELECT
                            r.Receipt_ID,
                            r.Customer_ID,
                            r.PaymentMethod,
                            r.AmountPaid,
                            r.PaymentDate,
                            r.Status,
                            c.LastName,
                            c.FirstName,
                            m.MovieName,
                            th.TheaterName,
                            ts.Date,
                            ts.StartTime,
                            ts.ScreeningType,
                            COUNT(t.Ticket_ID) AS Ticket_Count,
                            GROUP_CONCAT(CONCAT(s.SeatRow, s.SeatColumn) ORDER BY s.SeatRow, s.SeatColumn SEPARATOR ', ') AS Seat_List
                        FROM receipt r
                        INNER JOIN ticket t ON t.Receipt_ID = r.Receipt_ID
                        INNER JOIN customer c ON c.Customer_ID = r.Customer_ID
                        INNER JOIN seat_timeslot st ON st.SeatTimeSlot_ID = t.SeatTimeSlot_ID
                        INNER JOIN seats s ON s.Seat_ID = st.Seat_ID
                        INNER JOIN timeslot ts ON ts.TimeSlot_ID = st.TimeSlot_ID
                        INNER JOIN daterange dr ON dr.DateRange_ID = ts.DateRange_ID
                        INNER JOIN movie m ON m.Movie_ID = dr.Movie_ID
                        INNER JOIN theater th ON th.Theater_ID = dr.Theater_ID
                        WHERE r.Receipt_ID = ?
                        GROUP BY r.Receipt_ID
                    ");
                    try {
                        $stmt->bind_param("i", $ID);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        echo json_encode(["data" => $result->fetch_all(MYSQLI_ASSOC)]);

                        $result->free();
                        $stmt->close();
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => "An error occurred upon retrieving this receipt. Please try again." . $e]);
                    }
                }
                else if ($ID === 'customer' && $subResource !== null && $subID === null && $subResource2 === null && $subID2 === null) {
                   $stmt = $conn->prepare("
                                            SELECT 
                                                receipt.Receipt_ID, 
                                                receipt.AmountPaid, 
                                                receipt.PaymentDate, 
                                                receipt.Status,
                                                movie.MovieName, 
                                                theater.TheaterName,
                                                /* Concatenating Row and Column from the 'seats' table */
                                                GROUP_CONCAT(CONCAT(seats.SeatRow, seats.SeatColumn) SEPARATOR ', ') as Seat_List
                                            FROM receipt
                                            INNER JOIN ticket ON ticket.Receipt_ID = receipt.Receipt_ID
                                            INNER JOIN seat_timeslot ON ticket.SeatTimeSlot_ID = seat_timeslot.SeatTimeSlot_ID
                                            INNER JOIN seats ON seat_timeslot.Seat_ID = seats.Seat_ID
                                            INNER JOIN timeslot ON seat_timeslot.TimeSlot_ID = timeslot.TimeSlot_ID
                                            INNER JOIN daterange ON timeslot.DateRange_ID = daterange.DateRange_ID
                                            INNER JOIN movie ON daterange.Movie_ID = movie.Movie_ID
                                            INNER JOIN theater ON daterange.Theater_ID = theater.Theater_ID
                                            WHERE receipt.Customer_ID = ?
                                            GROUP BY receipt.Receipt_ID
                                        ");
                    try {
                        $stmt->bind_param("i", $subResource);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        echo json_encode(["data" => $result->fetch_all(MYSQLI_ASSOC)]);

                        $result->free();
                        $stmt->close();
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => "An error occurred upon retrieving this receipt. Please try again." . $e]);
                    }
                } 
            }            
            
            if ($method == 'POST') {            
                if ($ID === null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $data = json_decode(file_get_contents('php://input'), true);

                    $Customer_ID = $data['Customer_ID'];
                    $PaymentMethod = $data['PaymentMethod'];
                    $AmountPaid = $data['totalPrice'];
                    $selectedSeats = $data['selectedSeats'];

                    $emailStmt = $conn->prepare("SELECT Email FROM customer WHERE Customer_ID = ?");
                    $emailStmt->bind_param('i', $Customer_ID);
                    $emailStmt->execute();
                    $result = $emailStmt->get_result();
                    $row = $result->fetch_assoc();

                    $address = $row['Email'] ?? null;

                    $conn->begin_transaction();
                    try {
                        $stmt = $conn->prepare("INSERT INTO Receipt (Customer_ID, PaymentMethod, AmountPaid)
                                                VALUES (?, ?, ?)");
                        $stmt->bind_param("isd", $Customer_ID, $PaymentMethod, $AmountPaid);
                        $stmt->execute();

                        $Receipt_ID = $conn->insert_id;

                        $stmt2 = $conn->prepare("INSERT INTO Ticket(Customer_ID, SeatTimeSlot_ID, Receipt_ID, Price)
                                                VALUES (?, ?, ?, ?)");
                        foreach ($selectedSeats as $seat) {
                            $stmt2->bind_param("iiid", $Customer_ID, $seat['SeatTimeSlot_ID'], $Receipt_ID, $seat['SeatPrice']);
                            $stmt2->execute();

                            $stmt3 = $conn->prepare("UPDATE seat_timeslot SET SeatAvailability = 0 WHERE SeatTimeSlot_ID = ?");
                            $stmt3->bind_param("i", $seat['SeatTimeSlot_ID']);
                            $stmt3->execute();
                        }
                        
                        $detail_stmt = $conn->prepare("
                            SELECT m.MovieName, t.Date, t.StartTime, th.TheaterName, c.FirstName, c.LastName, r.PaymentMethod, r.PaymentDate,
                                s.SeatRow, s.SeatColumn, tk.Price
                            FROM ticket tk
                            JOIN seat_timeslot st ON tk.SeatTimeSlot_ID = st.SeatTimeSlot_ID
                            JOIN timeslot t ON st.TimeSlot_ID = t.TimeSlot_ID
                            JOIN movie m ON t.Movie_ID = m.Movie_ID
                            JOIN theater th ON t.Theater_ID = th.Theater_ID
                            JOIN seats s ON st.Seat_ID = s.Seat_ID
                            JOIN customer c ON tk.Customer_ID = c.Customer_ID
                            JOIN receipt r ON tk.Receipt_ID = r.Receipt_ID
                            WHERE tk.Receipt_ID = ?
                        ");
                        $detail_stmt->bind_param("i", $Receipt_ID);
                        $detail_stmt->execute();
                        $results = $detail_stmt->get_result();

                        $seatList = [];
                        $totalTickets = 0;
                        $totalPrice = 0;
                        $firstRow = null;

                        while ($row = $results->fetch_assoc()) {
                            if (!$firstRow) $firstRow = $row; // Keep one row for header info
                            $seatList[] = $row['SeatRow'] . $row['SeatColumn'];
                            $totalPrice += $row['Price'];
                            $totalTickets++;
                        }

                        $seatsString = implode(", ", $seatList);

                        $paymentMethodDisplay = strtoupper($firstRow['PaymentMethod']);
                        $formattedTime = date("h:i A", strtotime($firstRow['StartTime']));

                        $year = date("Y", strtotime($firstRow['PaymentDate']));

                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'jerrellnathan@gmail.com';
                        $mail->Password = 'kzmg pbko flhr xwhp';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        $mail->setFrom('jerrellnathan@gmail.com', 'PeaksCinemas');
                        $mail->addAddress($address);
                        $mail->isHTML(true);
                        $mail->Subject = 'Your PeaksCinemas Booking Receipt';

                        $mail->Body = <<<HTML
                        <div style="font-family: sans-serif; background: #071018; color: #ffffff; padding: 30px; border-radius: 15px;">
                            <h2 style="color: #2dd4bf; text-align: center;">Booking Confirmation</h2>
                            <p style="text-align: center; color: #9ca3af;">Thank you for your purchase!</p>
                            
                            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                                <tr style="border-bottom: 1px solid #334e68;"><td style="padding: 15px; color: #8a9bad;">Movie:</td><td style="padding: 15px; text-align: right; font-weight: bold;">{$firstRow['MovieName']}</td></tr>
                                <tr style="border-bottom: 1px solid #334e68;"><td style="padding: 15px; color: #8a9bad;">Cinema:</td><td style="padding: 15px; text-align: right; font-weight: bold;">{$firstRow['TheaterName']}</td></tr>
                                <tr style="border-bottom: 1px solid #334e68;"><td style="padding: 15px; color: #8a9bad;">Date/Time:</td><td style="padding: 15px; text-align: right; font-weight: bold;">{$firstRow['Date']} | {$formattedTime}</td></tr>
                                <tr style="border-bottom: 1px solid #334e68;"><td style="padding: 15px; color: #8a9bad;">Seats:</td><td style="padding: 15px; text-align: right; font-weight: bold;">{$seatsString}</td></tr>
                                <tr style="border-bottom: 1px solid #334e68;"><td style="padding: 15px; color: #8a9bad;">Tickets:</td><td style="padding: 15px; text-align: right; font-weight: bold;">{$totalTickets}</td></tr>
                                <tr style="border-bottom: 1px solid #334e68;"><td style="padding: 15px; color: #8a9bad;">Customer:</td><td style="padding: 15px; text-align: right; font-weight: bold;">{$firstRow['LastName']}, {$firstRow['FirstName']}</td></tr>
                                <tr style="border-bottom: 1px solid #334e68;"><td style="padding: 15px; color: #8a9bad;">Payment:</td><td style="padding: 15px; text-align: right; font-weight: bold;">{$paymentMethodDisplay}</td></tr>
                                <tr style="border-bottom: 1px solid #2dd4bf;"><td style="padding: 15px; color: #ffffff; font-weight: bold;">Total:</td><td style="padding: 15px; text-align: right; font-weight: bold; color: #2dd4bf; font-size: 1.2em;">₱{$totalPrice}</td></tr>
                            </table>
                            
                            <div style="margin-top: 30px; text-align: center; background: #112233; padding: 20px; border-radius: 10px;">
                                <p style="margin: 0; color: #8a9bad;">Booking Reference</p>
                                <h2 style="color: #2dd4bf; margin: 10px 0 0 0; letter-spacing: 2px;">PC{$Receipt_ID}{$year}</h2>
                                <div>If you did not book this, please immediately refund through the website or contact us using our contact information below:</div>
                                <div>Phone Number: +63 9202520720</div>
                                <div>Email: peakscinemas@gmail.com</div>
                            </div>
                        </div>
                        HTML;

                        $mail->send();
                        $conn->commit();
                        echo json_encode(["status" => "Success!!!!", "Receipt_ID" => $Receipt_ID]);
                    } catch (mysqli_sql_exception $e) {
                        $conn->rollback();
                        echo json_encode(["error" => "There has been an error with submitting. Please try again."]);
                    }
                }
            }
            break;
        case 'refund':
            if ($method == 'GET') {
                if ($ID === null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    try {
                        $stmt = $conn->prepare("SELECT refund.*, receipt.*, customer.FirstName, customer.LastName FROM refund
                                                INNER JOIN receipt ON receipt.Receipt_ID = refund.Receipt_ID
                                                LEFT JOIN customer ON customer.Customer_ID = receipt.Customer_ID
                                                ORDER BY refund.Refund_ID DESC");
                        $stmt->execute();
                        $result = $stmt->get_result();

                        echo json_encode(["data" => $result->fetch_all(MYSQLI_ASSOC)]);
                    } catch (mysqli_sql_exception $e) {
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }
            }
            if ($method == 'POST') {
                if ($ID === null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $data = json_decode(file_get_contents('php://input'), true);

                    $Customer_ID = $data['Customer_ID'];
                    $Receipt_ID = $data['Receipt_ID'];
                    $RefundReason = $data['RefundReason'];

                    $stmt = $conn->prepare("INSERT INTO refund (Customer_ID, Receipt_ID, RefundReason) VALUES (?, ?, ?)");                    
                    $stmt2 = $conn->prepare("UPDATE receipt SET Status = 'Pending Refund' WHERE Receipt_ID = ?");
                    $conn->begin_transaction();
                    try {
                        $stmt->bind_param("iis", $Customer_ID, $Receipt_ID, $RefundReason);
                        $stmt2->bind_param("i", $Receipt_ID);
                        $stmt->execute();
                        $stmt2->execute();
                        echo json_encode(["status" => "Success."]);
                        $conn->commit();
                    } catch (mysqli_sql_exception $e) {
                        $conn->rollback();
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }
            }
            if ($method == 'PUT') {
                if ($ID !== null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $data = json_decode(file_get_contents('php://input'), true);
                    $action = $data['action'] ?? null;

                    if ($action !== 'accept' && $action !== 'deny') {
                        http_response_code(400);
                        echo json_encode(["error" => "Invalid refund action."]);
                        break;
                    }

                    $conn->begin_transaction();
                    try {
                        $lookupStmt = $conn->prepare("SELECT refund.Receipt_ID, receipt.AmountPaid, receipt.PaymentDate, customer.Email, customer.FirstName, customer.LastName, PaymentDate
                                                      FROM refund
                                                      INNER JOIN receipt ON receipt.Receipt_ID = refund.Receipt_ID
                                                      LEFT JOIN customer ON customer.Customer_ID = receipt.Customer_ID
                                                      WHERE refund.Refund_ID = ?");
                        $lookupStmt->bind_param("i", $ID);
                        $lookupStmt->execute();
                        $lookupResult = $lookupStmt->get_result();
                        if ($lookupResult->num_rows === 0) {
                            throw new \Exception("Refund request not found.");
                        }
                        $refundRow = $lookupResult->fetch_assoc();
                        $Receipt_ID = $refundRow['Receipt_ID'];
                        $customerEmail = $refundRow['Email'] ?? null;
                        $firstName = $refundRow['FirstName'] ?? "Customer";
                        $lastName = $refundRow['LastName'] ?? "";
                        $amountPaid = $refundRow['AmountPaid'] ?? 0;
                        $paymentDate = $refundRow['PaymentDate'] ?? null;
                        $year = date("Y", strtotime($paymentDate));

                        if ($action === 'accept') {
                            $deleteReceiptStmt = $conn->prepare("DELETE FROM receipt WHERE Receipt_ID = ?");
                            $deleteReceiptStmt->bind_param("i", $Receipt_ID);
                            $deleteReceiptStmt->execute();
                        } else {
                            $statusStmt = $conn->prepare("UPDATE receipt SET Status = 'Paid' WHERE Receipt_ID = ?");
                            $statusStmt->bind_param("i", $Receipt_ID);
                            $statusStmt->execute();
                        }

                        $deleteStmt = $conn->prepare("DELETE FROM refund WHERE Refund_ID = ?");
                        $deleteStmt->bind_param("i", $ID);
                        $deleteStmt->execute();

                        $conn->commit();

                        // Refund decisions are email-notified using the same mail setup used by receipts.
                        if (!empty($customerEmail)) {
                            try {
                                $decisionLabel = $action === 'accept' ? 'Approved' : 'Denied';
                                $decisionText = $action === 'accept'
                                    ? 'Your refund request has been approved. The related receipt has been cancelled in our system.'
                                    : 'Your refund request has been denied. The related receipt remains active.';
                                $formattedDate = $paymentDate ? date("F d, Y", strtotime($paymentDate)) : "N/A";

                                $mail = new PHPMailer(true);
                                $mail->isSMTP();
                                $mail->Host = 'smtp.gmail.com';
                                $mail->SMTPAuth = true;
                                $mail->Username = 'jerrellnathan@gmail.com';
                                $mail->Password = 'kzmg pbko flhr xwhp';
                                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                                $mail->Port = 587;

                                $mail->setFrom('jerrellnathan@gmail.com', 'PeaksCinemas');
                                $mail->addAddress($customerEmail);
                                $mail->isHTML(true);
                                $mail->Subject = 'Your PeaksCinemas Refund Request Update';
                                $mail->Body = <<<HTML
                                <div style="font-family: sans-serif; background: #071018; color: #ffffff; padding: 30px; border-radius: 15px;">
                                    <h2 style="color: #2dd4bf; text-align: center;">Refund Request {$decisionLabel}</h2>
                                    <p style="text-align: center; color: #9ca3af;">Hello {$firstName} {$lastName},</p>
                                    <p style="text-align: center;">{$decisionText}</p>
                                    
                                    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                                        <tr style="border-bottom: 1px solid #334e68;"><td style="padding: 12px; color: #8a9bad;">Receipt ID:</td><td style="padding: 12px; text-align: right; font-weight: bold;">PC{$Receipt_ID}{$year}</td></tr>
                                        <tr style="border-bottom: 1px solid #334e68;"><td style="padding: 12px; color: #8a9bad;">Original Payment Date:</td><td style="padding: 12px; text-align: right; font-weight: bold;">{$formattedDate}</td></tr>
                                        <tr style="border-bottom: 1px solid #334e68;"><td style="padding: 12px; color: #8a9bad;">Original Amount:</td><td style="padding: 12px; text-align: right; font-weight: bold;">₱{$amountPaid}</td></tr>
                                        <tr style="border-bottom: 1px solid #2dd4bf;"><td style="padding: 12px; color: #ffffff; font-weight: bold;">Decision:</td><td style="padding: 12px; text-align: right; font-weight: bold; color: #2dd4bf;">{$decisionLabel}</td></tr>
                                    </table>
                                    <div style="margin-top: 30px; text-align: center; background: #112233; padding: 20px; border-radius: 10px;">
                                        <div>If you want to talk further with Peak's Cinemas staff, please contact us through these:</div>
                                        <div>Phone Number: +63 9202520720</div>
                                        <div>Email: peakscinemas@gmail.com</div>
                                    </div>
                                </div>
                                HTML;
                                $mail->send();
                            } catch (\Exception $mailError) {
                                echo json_encode(["status" => "Success.", "action" => $action, "warning" => "Refund processed but email could not be sent."]);
                                break;
                            }
                        }

                        echo json_encode(["status" => "Success.", "action" => $action]);
                    } catch (\Exception $e) {
                        $conn->rollback();
                        http_response_code(400);
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }
            }
            break;
        case 'top_movies':
            if ($method == 'GET') {
                try {
                    $stmt = $conn->prepare('SELECT
                                                movie.Movie_ID,
                                                movie.MovieName,
                                                COALESCE(SUM(seat_timeslot.SeatPrice), 0) AS TotalRevenue
                                            FROM movie
                                            LEFT JOIN daterange ON daterange.Movie_ID = movie.Movie_ID
                                            LEFT JOIN timeslot ON timeslot.DateRange_ID = daterange.DateRange_ID
                                            LEFT JOIN seat_timeslot ON seat_timeslot.TimeSlot_ID = timeslot.TimeSlot_ID
                                            WHERE seat_timeslot.SeatAvailability = 0
                                            GROUP BY movie.Movie_ID, movie.MovieName
                                            ORDER BY TotalRevenue DESC
                                            LIMIT 10');
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $movies = [];
                    while ($row = $result->fetch_assoc()) {
                        $movies[] = $row;
                    }

                    // Pad with empty entries if less than 10
                    while (count($movies) < 10) {
                        $movies[] = [
                            'MovieName' => '--blank (for now)--',
                            'TotalRevenue' => '--blank (for now)--'
                        ];
                    }

                    echo json_encode(["data" => $movies]);
                } catch (mysqli_sql_exception $e) {
                    http_response_code(400);
                    echo json_encode(["error" => $e->getMessage()]);
                }
            }
            break;
        case 'theater_analytics':
            if ($method == 'GET') {
                if ($ID !== null && $subResource === null && $subID === null && $subResource2 === null && $subID2 === null) {
                    $year = $ID;
                    try {
                        $stmt = $conn->prepare('SELECT
                                                    theater.TheaterName,
                                                    MONTHNAME(timeslot.Date) AS Month_Name,
                                                    MONTH(timeslot.Date) AS Month_Num,
                                                    SUM(seat_timeslot.SeatPrice) AS Total
                                                FROM `seat_timeslot`
                                                INNER JOIN timeslot ON timeslot.TimeSlot_ID = seat_timeslot.TimeSlot_ID
                                                INNER JOIN theater ON theater.Theater_ID = timeslot.Theater_ID
                                                WHERE YEAR(timeslot.Date) = ? AND seat_timeslot.SeatAvailability = 0
                                                GROUP BY theater.Theater_ID, MONTH(timeslot.Date)
                                                ORDER BY Month_Num ASC');
                        $stmt->bind_param('i', $year);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $theaterData = [];
                        while ($row = $result->fetch_assoc()) {
                            $theater = $row['TheaterName'];
                            if (!isset($theaterData[$theater])) {
                                $theaterData[$theater] = [];
                            }
                            $theaterData[$theater][$row['Month_Num']] = (int)$row['Total'];
                        }

                        // Fill in missing months with 0
                        foreach ($theaterData as &$data) {
                            for ($i = 1; $i <= 12; $i++) {
                                if (!isset($data[$i])) {
                                    $data[$i] = 0;
                                }
                            }
                            ksort($data);
                        }

                        echo json_encode(["data" => $theaterData]);
                    } catch (mysqli_sql_exception $e) {
                        http_response_code(400);
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }
            }
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
        case 'staff':
            if ($method == 'GET') {
                if ($ID === null && $subResource === null) {
                    try {
                        $stmt = $conn->prepare("SELECT Admin_ID, FirstName, LastName, Email, AccessLevel FROM admin ORDER BY Admin_ID DESC");
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            echo json_encode(["data" => []]);
                        } else {
                            echo json_encode(["data" => $result->fetch_all(MYSQLI_ASSOC)]);
                        }
                    } catch (mysqli_sql_exception $e) {
                        http_response_code(400);
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                } elseif ($ID !== null && $subResource === null) {
                    try {
                        $stmt = $conn->prepare("SELECT Admin_ID, FirstName, LastName, Email, AccessLevel FROM admin WHERE Admin_ID = ?");
                        $stmt->bind_param("i", $ID);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            http_response_code(404);
                            echo json_encode(["error" => "Staff member not found"]);
                        } else {
                            echo json_encode(["data" => $result->fetch_assoc()]);
                        }
                    } catch (mysqli_sql_exception $e) {
                        http_response_code(400);
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }
            }

            if ($method == 'POST') {
                admin_surely($jwt_secret, 2);

                $staffName = trim($_POST['staffName'] ?? '');
                $staffEmail = trim($_POST['staffEmail'] ?? '');
                $staffLevel = trim($_POST['staffLevel'] ?? '');

                if (empty($staffName) || empty($staffEmail) || $staffLevel === '') {
                    http_response_code(400);
                    die(json_encode(["error" => "All fields are required"]));
                }

                if (!filter_var($staffEmail, FILTER_VALIDATE_EMAIL)) {
                    http_response_code(400);
                    die(json_encode(["error" => "Invalid email format"]));
                }

                // Check if email exists
                $checkStmt = $conn->prepare("SELECT Admin_ID FROM admin WHERE Email = ?");
                $checkStmt->bind_param("s", $staffEmail);
                $checkStmt->execute();
                if ($checkStmt->get_result()->num_rows > 0) {
                    http_response_code(409);
                    die(json_encode(["error" => "Email already exists"]));
                }

                // Set default password
                $tempPassword = 'admin1234';
                $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);

                // Extract first and last name
                $nameParts = explode(' ', $staffName, 2);
                $firstName = $nameParts[0];
                $lastName = $nameParts[1] ?? '';

                try {
                    $conn->begin_transaction();

                    $stmt = $conn->prepare("INSERT INTO admin (FirstName, LastName, Email, AdminPassword, AccessLevel) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssi", $firstName, $lastName, $staffEmail, $hashedPassword, $staffLevel);

                    if ($stmt->execute()) {
                        $conn->commit();
                        http_response_code(201);
                        echo json_encode(["status" => "Staff member created successfully", "tempPassword" => $tempPassword]);
                    } else {
                        $conn->rollback();
                        http_response_code(400);
                        echo json_encode(["error" => "Failed to create staff member"]);
                    }
                } catch (mysqli_sql_exception $e) {
                    $conn->rollback();
                    http_response_code(400);
                    echo json_encode(["error" => $e->getMessage()]);
                }
            }

            if ($method == 'PUT') {
                admin_surely($jwt_secret, 2);

                $data = json_decode(file_get_contents('php://input'), true);

                if ($ID === null) {
                    http_response_code(400);
                    die(json_encode(["error" => "Staff ID is required"]));
                }

                if ($subResource === 'reset-password') {
                    // Handle password reset
                    $newPassword = 'admin1234';
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    try {
                        $conn->begin_transaction();

                        $stmt = $conn->prepare("UPDATE admin SET AdminPassword = ? WHERE Admin_ID = ?");
                        $stmt->bind_param("si", $hashedPassword, $ID);

                        if ($stmt->execute() && $stmt->affected_rows > 0) {
                            $conn->commit();
                            http_response_code(200);
                            echo json_encode(["status" => "Password reset successfully"]);
                        } else {
                            $conn->rollback();
                            http_response_code(404);
                            echo json_encode(["error" => "Staff member not found"]);
                        }
                    } catch (mysqli_sql_exception $e) {
                        $conn->rollback();
                        http_response_code(400);
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                } else {
                    // Handle access level update
                    $accessLevel = $data['accessLevel'] ?? null;

                    if ($accessLevel === null || !in_array($accessLevel, [0, 1, 2])) {
                        http_response_code(400);
                        die(json_encode(["error" => "Invalid access level"]));
                    }

                    try {
                        $conn->begin_transaction();

                        $stmt = $conn->prepare("UPDATE admin SET AccessLevel = ? WHERE Admin_ID = ?");
                        $stmt->bind_param("ii", $accessLevel, $ID);

                        if ($stmt->execute() && $stmt->affected_rows > 0) {
                            $conn->commit();
                            http_response_code(200);
                            echo json_encode(["status" => "Staff member updated successfully"]);
                        } else {
                            $conn->rollback();
                            http_response_code(404);
                            echo json_encode(["error" => "Staff member not found"]);
                        }
                    } catch (mysqli_sql_exception $e) {
                        $conn->rollback();
                        http_response_code(400);
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                }
            }

            if ($method == 'DELETE') {
                admin_surely($jwt_secret, 2);

                if ($ID === null) {
                    http_response_code(400);
                    die(json_encode(["error" => "Staff ID is required"]));
                }

                try {
                    $conn->begin_transaction();

                    $stmt = $conn->prepare("DELETE FROM admin WHERE Admin_ID = ?");
                    $stmt->bind_param("i", $ID);

                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $conn->commit();
                        http_response_code(200);
                        echo json_encode(["status" => "Staff member deleted successfully"]);
                    } else {
                        $conn->rollback();
                        http_response_code(404);
                        echo json_encode(["error" => "Staff member not found"]);
                    }
                } catch (mysqli_sql_exception $e) {
                    $conn->rollback();
                    http_response_code(400);
                    echo json_encode(["error" => $e->getMessage()]);
                }
            }
            break;
        case 'customer_email':
            if ($method == 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);               
                
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