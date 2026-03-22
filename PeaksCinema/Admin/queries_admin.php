<?php 
    include("peakscinemas_database.php");

    $q = $_GET['q'] ?? '';
    $Movie_ID = filter_input(INPUT_GET, 'movie_id', FILTER_VALIDATE_INT);

    if ($q == 'movies') { // DONE
        $stmt = $conn->prepare("SELECT DISTINCT movie.Movie_ID, movie.MovieName, movie.MoviePoster
                                FROM movie");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo '<p>No movies are in the system. Please add a movie to see it here.</p>';
        } else {
            while($row = $result->fetch_assoc()) {
                echo '<div class="movieContainer" id="', htmlspecialchars($row['Movie_ID']), '">';
                echo '<img src=../../', htmlspecialchars($row['MoviePoster']), ' class="moviePoster">';
                echo '<div class="movieName">', htmlspecialchars($row['MovieName']), '</div>';
                echo '</div>';
            }
        }

        $stmt->free_result();
    }

    if ($q == 'theaterdatetimes') {
        $id = intval($_GET['id']);
        $movie_id = intval($_GET['movie_id']);
        
        $stmt = $conn->prepare("
            SELECT DISTINCT daterange.DateRange_ID, daterange.StartDate, daterange.EndDate
            FROM daterange
            INNER JOIN theater
            ON daterange.Theater_ID = theater.Theater_ID
            INNER JOIN movie
            ON daterange.Movie_ID = movie.Movie_ID
            WHERE daterange.Theater_ID = ? AND movie.Movie_ID = ?
        ");
        $stmt->bind_param("ii", $id, $movie_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            echo '<div class="currentDatesContainer" id=' . $row['DateRange_ID'] .'>';
            echo '<div class="daterangeContainer">';
            echo '<div class="daterange">' .
                date("F j Y", strtotime(htmlspecialchars($row['StartDate']))) .
                '---' . date("F j Y", strtotime(htmlspecialchars($row['EndDate']))) .
                '</div>';
            echo '<div class="daterangeOptions">';
            echo '<div class="daterangeEdit">Edit</div>';
            echo '<div class="daterangeDelete" onclick="deleteDateTime(' . $row['DateRange_ID'] . ')">Delete</div>';
            echo '</div>';
            echo '</div>';
            
            $stmt2 = $conn->prepare("SELECT DISTINCT timeslot.StartTime 
                                    FROM timeslot 
                                    INNER JOIN daterange 
                                    ON timeslot.DateRange_ID = daterange.DateRange_ID 
                                    WHERE daterange.DateRange_ID = ?");
            $stmt2->bind_param("i", $row['DateRange_ID']);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            echo '<div class="timeslotForDateRange">';
            while ($timeslots = $result2->fetch_assoc()) {
                echo '<div class="timeslots">' . date("g:i A", strtotime($timeslots['StartTime'])) . '</div>';
            }
            echo '</div></div>';
        }
    }


    if ($q == 'datetimesent') {
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


    if ($q == 'datedeletion') {
        if (isset($_POST['id'])) {
            $DateRange_ID = intval($_POST['id']);

            $stmt = $conn->prepare("DELETE FROM daterange WHERE DateRange_ID = ?");
            $stmt->bind_param("i", $DateRange_ID);

            if ($stmt->execute()) {
                echo "true";
            } else {
                echo "Error deleting: " . $stmt->error;
            }
        } else {
            echo "No ID provided";
        }
    }

    if ($q == 'movieupload') {
        $posterFolder = dirname(__DIR__) . '/MoviePosters';
        if (!is_dir($posterFolder)) {
            mkdir($posterFolder, 0755, true);
        }

        function input_cleanup($data) {
            $data = trim($data);
            $data = stripslashes($data);
            return $data;
        }

        // prepared statement for later use
        $stmt = $conn -> prepare("INSERT INTO movie(MovieName, MovieDescription, Genre, Rating, Runtime, MoviePoster, TrailerURL)
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt -> bind_param("ssssiss", $safeMovieName, $MovieDescription, $Genre, $Rating, $Runtime, $MoviePoster, $TrailerURL);

        function getYoutubeID($url) {
            if (preg_match('/youtu\.be\/([^\?]+)/', $url, $matches)) {
                return $matches[1];
            }
            if (preg_match('/v=([^&]+)/', $url, $matches)) {
                return $matches[1];
            }
            return $url; // fallback if admin pastes just the ID dito
        }
        
        // more input cleanup
        $MovieName = input_cleanup($_POST['movieName']);
        $safeMovieName = preg_replace('/[\\\\\/:\*\?"<>\|]/', '', $MovieName);
        $safeMovieName = str_replace(' ', '_', $safeMovieName); 

        $MovieDescription = input_cleanup($_POST['movieDesc']);
        $Genre = input_cleanup($_POST['movieGenre']);
        $Rating = input_cleanup($_POST['movieRating']);
        $Runtime = input_cleanup($_POST['movieRuntime']);
        $TrailerURL = input_cleanup($_POST['TrailerURL']);
        $TrailerURL = getYoutubeID($TrailerInput);

        // this makes a "path" to the uploaded file
        $temp = $_FILES['moviePosterUp']['tmp_name'];
        // this cuts off the file type from the image name
        $fileType = pathinfo($_FILES['moviePosterUp']['name'], PATHINFO_EXTENSION);

        // FIXED: Remove characters that are invalid in Windows file paths
        // Colons, slashes, asterisks, quotes, etc. cause upload to fail on Windows/XAMPP
        $safeMovieName = preg_replace('/[\\\\\/:\*\?"<>\|]/', '', $MovieName);
        $safeMovieName = trim($safeMovieName);

        // rename the file to match the (sanitized) movie name
        $fileName = $safeMovieName . "." . $fileType;
        // create the full path where the file will be saved
        $endPath = $posterFolder . "/" . $fileName;

        // Move the temporary file to the actual folder
        if (move_uploaded_file($temp, $endPath)) {
            $MoviePoster = 'PeaksCinema/MoviePosters/' . $fileName;
        } else {
            echo "There was an error with uploading the poster. Please try again. ";
        }

        // actual execution of the prepared statement
        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
        }
    }

    if ($q == 'movieedit') {
        $Movie_ID = intval($_GET['id']);
        $posterFolder = dirname(__DIR__) . '/MoviePosters';
        if (!is_dir($posterFolder)) {
            mkdir($posterFolder, 0755, true);
        }

        function input_cleanup($data) {
            $data = trim($data);
            $data = stripslashes($data);
            return $data;
        }

        // prepared statement for later use
        $stmt = $conn -> prepare("UPDATE movie
                                  SET MovieName = ?, MovieDescription = ?, Genre = ?, Rating = ?, Runtime = ?, MoviePoster = ?, TrailerURL = ?
                                  WHERE Movie_ID = ?");
        $stmt -> bind_param("ssssissi", $safeMovieName, $MovieDescription, $Genre, $Rating, $Runtime, $MoviePoster, $TrailerURL, $Movie_ID);

        function getYoutubeID($url) {
            if (preg_match('/youtu\.be\/([^\?]+)/', $url, $matches)) {
                return $matches[1];
            }
            if (preg_match('/v=([^&]+)/', $url, $matches)) {
                return $matches[1];
            }
            return $url; // fallback if admin pastes just the ID dito
        }
        
        // more input cleanup
        $MovieName = input_cleanup($_POST['movieName']);
        $safeMovieName = preg_replace('/[\\\\\/:\*\?"<>\|]/', '', $MovieName);
        $safeMovieName = str_replace(' ', '_', $safeMovieName); 

        $MovieDescription = input_cleanup($_POST['movieDesc']);
        $Genre = input_cleanup($_POST['movieGenre']);
        $Rating = input_cleanup($_POST['movieRating']);
        $Runtime = input_cleanup($_POST['movieRuntime']);
        $TrailerURL = input_cleanup($_POST['TrailerURL']);

        // this makes a "path" to the uploaded file
        $temp = $_FILES['moviePosterUp']['tmp_name'];
        // this cuts off the file type from the image name
        $fileType = pathinfo($_FILES['moviePosterUp']['name'], PATHINFO_EXTENSION);

        // FIXED: Remove characters that are invalid in Windows file paths
        // Colons, slashes, asterisks, quotes, etc. cause upload to fail on Windows/XAMPP
        $safeMovieName = preg_replace('/[\\\\\/:\*\?"<>\|]/', '', $MovieName);
        $safeMovieName = trim($safeMovieName);

        // rename the file to match the (sanitized) movie name
        $fileName = $safeMovieName . "." . $fileType;
        // create the full path where the file will be saved
        $endPath = $posterFolder . "/" . $fileName;

        // Move the temporary file to the actual folder
        if (move_uploaded_file($temp, $endPath)) {
            $MoviePoster = 'PeaksCinema/MoviePosters/' . $fileName;
        } else {
            echo "There was an error with uploading the poster. Please try again. ";
        }

        // actual execution of the prepared statement
        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
        }
    }

    if ($q == 'theaters') {
        $stmt = $conn->prepare("SELECT DISTINCT Theater_ID, TheaterName 
                                FROM theater 
                                ORDER BY TheaterName;");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo '<p>No theaters are in the system. Please add a theater to see it here.</p>';
        } else {
            while($row = $result->fetch_assoc()) {
                echo '<div class="theaterContainer" id="', htmlspecialchars($row['Theater_ID']), '" data-id="', htmlspecialchars($row['Theater_ID']), '">';
                echo '<div class="theaterName">', htmlspecialchars($row['TheaterName']), '</div>';
                echo '</div>';
            }
        }

        $stmt->free_result();
    }

    // SELECT DISTINCT Theater_ID, TheaterName FROM theater ORDER BY TheaterName;
?>

