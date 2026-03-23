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
                dateRangeHandler($method, $ID, $subResource, $subID, $subResource2, $subID2);
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

    function dateRangeHandler($method, $ID, $subResource, $subID, $subResource2, $subID2) {
        global $conn;
        if ($method == 'GET') {
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
        }

        if ($method == 'POST') {
            if ($subResource === 'movie' && $subID && $subResource2 === 'theater' && $subID2) {
                // $stmt = $conn->prepare('SELECT * FROM daterange
                //                                 WHERE Movie_ID = ? AND Theater_ID=?');
                //         $stmt->bind_param('ii', $subID, $subID2);
                //         $stmt->execute();
                //         $result = $stmt->get_result();
                //         if ($result->num_rows === 0) {
                //             echo json_encode("There are no date ranges for this theater yet.");
                //         } else {
                //             echo json_encode($result->fetch_all(MYSQLI_ASSOC));
                //         }
                        
            } else {
                echo json_encode("Error with your request.");
            }
        }
        
    }
?>