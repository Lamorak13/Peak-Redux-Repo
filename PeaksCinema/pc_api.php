<?php 
    header("Content-Type: application/json");
    include 'peakscinemas_database.php';

    $method = $_SERVER['REQUEST_METHOD'];
    $request = $_GET['request'] ?? '';
    $parts = explode('/', trim($request, '/'));

    $resource = $parts[0] ?? null;
    $id = $parts[1] ?? null;
    
    $subResource = $parts[2] ?? null;
    $subId = $parts[3] ?? null;

    switch ($resource) {
        case 'customer':
            break;
        case 'daterange':
            
            break;
        case 'movie':
            if ($method == 'GET') {
                if ($id) {
                    $stmt = $conn->prepare('SELECT * FROM movie
                                            WHERE Movie_ID = ?');
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    echo json_encode($stmt->get_result()->fetch_assoc());
                } else {
                    $result = $conn->query("SELECT * FROM movie");
                    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
                }
            }

            if ($method == 'POST') {
                $movieName = $_POST['MovieName'];
                $movieDescription = $_POST['MovieDescription'];
                $genre = $_POST['Genre'];
                $rating = $_POST['Rating'];
                $runtime = $_POST['Runtime'];
                $trailerURL = $_POST['TrailerURL'];
                $uploadPath = null;
                
                if (isset($_FILES['MoviePoster']) && $_FILES['MoviePoster']['error'] == 0) {
                    $uploadDir = 'PeaksCinema/MoviePosters/';
                    $fileName = basename($_FILES['MoviePoster']['name']);
                    $uploadPath = $uploadDir . $fileName;

                    move_uploaded_file($_FILES['MoviePoster']['tmp_name'], $uploadPath);
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
?>