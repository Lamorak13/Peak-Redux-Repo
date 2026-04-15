<?php
include("peakscinemas_database.php");
session_start();
$profile_link = "profile_edit.php";

$Movie_ID = filter_input(INPUT_GET, 'movie_id', FILTER_VALIDATE_INT);

if (!$Movie_ID) {
    header("Location: home.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM movie WHERE Movie_ID = ?");
$stmt->bind_param("i", $Movie_ID);
$stmt->execute();
$movieDetails = ($stmt->get_result())->fetch_assoc();

if (!$movieDetails) {
    header("Location: home.php");
    exit;
}

$trailerURL = $movieDetails['TrailerURL'] ?? '';

// ====================== POST HANDLER FOR MALLS ======================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['screeningDate'])) {
    $screeningDate = $_POST['screeningDate'];
    $mallData = [];

    $stmt = $conn->prepare("SELECT DISTINCT mall.* 
                            FROM mall 
                            INNER JOIN theater ON mall.Mall_ID = theater.Mall_ID 
                            INNER JOIN timeslot ON theater.Theater_ID = timeslot.Theater_ID 
                            WHERE timeslot.Movie_ID = ? AND timeslot.Date = ?");
    $stmt->bind_param("is", $Movie_ID, $screeningDate);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($mall = $result->fetch_assoc()) {
        $Mall_ID = $mall['Mall_ID'];

        // Theater Types
        $t_stmt = $conn->prepare("SELECT DISTINCT TheaterType FROM theater WHERE Mall_ID = ?");
        $t_stmt->bind_param("i", $Mall_ID);
        $t_stmt->execute();
        $theaterTypes = [];
        $res = $t_stmt->get_result();
        while ($row = $res->fetch_assoc()) $theaterTypes[] = $row['TheaterType'];

        // Screening Types
        $s_stmt = $conn->prepare("SELECT DISTINCT ScreeningType 
                                  FROM timeslot 
                                  INNER JOIN theater ON timeslot.Theater_ID = theater.Theater_ID 
                                  WHERE theater.Mall_ID = ? AND timeslot.Movie_ID = ? AND timeslot.Date = ?");
        $s_stmt->bind_param("iis", $Mall_ID, $Movie_ID, $screeningDate);
        $s_stmt->execute();
        $screeningTypes = [];
        $res = $s_stmt->get_result();
        while ($row = $res->fetch_assoc()) $screeningTypes[] = $row['ScreeningType'];

        $mall['TheaterTypes'] = $theaterTypes;
        $mall['ScreeningTypes'] = $screeningTypes;
        $mallData[] = $mall;
    }

    echo json_encode($mallData);
    exit;
}
?>