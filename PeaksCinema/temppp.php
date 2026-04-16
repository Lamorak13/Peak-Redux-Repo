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

<?php
    include("peakscinemas_database.php");
    session_start();
    $profile_link = "personal_info_form.php";
      
    $Movie_ID = filter_input(INPUT_GET, 'movie_id', FILTER_VALIDATE_INT);
    $Mall_ID = filter_input(INPUT_GET, 'mall_id', FILTER_VALIDATE_INT);
    $Date = filter_input(INPUT_GET, 'date');
    $TimeSlot_ID = filter_input(INPUT_GET, 'timeslot_id', FILTER_VALIDATE_INT);

    if (!$Movie_ID || !$Mall_ID || !$Date || !$TimeSlot_ID) {
        header("Location: home.php");
        exit;
    }

    $movie_stmt = $conn -> prepare("SELECT * FROM movie WHERE Movie_ID = ?");
    $movie_stmt -> bind_param("i", $Movie_ID);
    $movie_stmt -> execute();
    $movieDetails = ($movie_stmt -> get_result()) -> fetch_assoc();

    $mall_stmt = $conn -> prepare("SELECT * FROM mall WHERE Mall_ID = ?");
    $mall_stmt -> bind_param("i", $Mall_ID);
    $mall_stmt -> execute();
    $mallDetails = ($mall_stmt -> get_result()) -> fetch_assoc();

    $timeslot_stmt = $conn -> prepare("SELECT * FROM timeslot
                                       INNER JOIN theater ON timeslot.Theater_ID=theater.Theater_ID
                                       WHERE TimeSlot_ID = ?");
    $timeslot_stmt -> bind_param("i", $TimeSlot_ID);
    $timeslot_stmt -> execute();
    $timeslotDetails = ($timeslot_stmt -> get_result()) -> fetch_assoc();

    if (!$timeslotDetails) {
        header("Location: home.php");
        exit;
    }

    $seats_stmt = $conn -> prepare("SELECT * FROM seats WHERE TimeSlot_ID = ?");
    $seats_stmt -> bind_param("i", $TimeSlot_ID);
    $seats_stmt -> execute();
    $seatLayout = $seats_stmt -> get_result();

    if ($seatLayout) {
        $layoutProper = [];

        while ($seat = $seatLayout -> fetch_assoc()) {
            $Seat_ID = $seat['Seat_ID'];
            $rows = $seat['SeatRow'];
            $cols = $seat['SeatColumn'];
            $type = $seat['SeatType'];
            $price = $seat['SeatPrice'];
            $availability = $seat['SeatAvailability'];
            $layoutProper[$rows][] = [
                'Seat_ID' => $Seat_ID,
                'SeatType' => $type,
                'SeatPrice' => $price,
                'SeatAvailability' => $availability,
                'SeatColumn' => $cols
            ];
        }
    }
    
    if (!$movieDetails || !$mallDetails) {
        header("Location: home.php");
        exit;
    }

    function input_cleanup($data) {
        $data = trim($data);
        $data = stripslashes($data);
        return $data;
    }
?>

<?php
// session_start();

// $Movie_ID = isset($_POST['movie_id']) ? $_POST['movie_id'] : '';
// $Mall_ID = isset($_POST['mall_id']) ? $_POST['mall_id'] : '';
// $Date = isset($_POST['date']) ? $_POST['date'] : '';
// $TimeSlot_ID = isset($_POST['timeslot_id']) ? $_POST['timeslot_id'] : '';
// $selectedSeats = isset($_POST['selectedSeats']) ? $_POST['selectedSeats'] : [];
// $totalPrice = isset($_POST['totalPrice']) ? $_POST['totalPrice'] : 0;
// $paymentMethod = isset($_POST['paymentMethod']) ? $_POST['paymentMethod'] : '';
// $Customer_ID = $_SESSION['user_id'];

// $customerName = '';
// if ($paymentMethod === 'credit') {
//     $customerName = isset($_POST['cardFirstName']) ? $_POST['cardFirstName'] . ' ' . (isset($_POST['cardLastName']) ? $_POST['cardLastName'] : '') : '';
// } elseif ($paymentMethod === 'paypal') {
//     $customerName = isset($_POST['paypalFirstName']) ? $_POST['paypalFirstName'] . ' ' . (isset($_POST['paypalLastName']) ? $_POST['paypalLastName'] : '') : '';
// } elseif ($paymentMethod === 'gcash') {
//     $customerName = isset($_POST['gcashFirstName']) ? $_POST['gcashFirstName'] . ' ' . (isset($_POST['gcashLastName']) ? $_POST['gcashLastName'] : '') : '';
// } elseif ($paymentMethod === 'paymaya') {
//     $customerName = isset($_POST['paymayaFirstName']) ? $_POST['paymayaFirstName'] . ' ' . (isset($_POST['paymayaLastName']) ? $_POST['paymayaLastName'] : '') : '';
// }

// if (empty($paymentMethod)) {
//     header("Location: payment.php");
//     exit;
// }

// include("peakscinemas_database.php");

// $email_stmt = $conn->prepare("SELECT Email FROM customer WHERE Customer_ID = ?");
// $email_stmt->bind_param("i", $Customer_ID);
// $email_stmt->execute();
// $emailResult = $email_stmt->get_result()->fetch_assoc();
// $customerEmail = $emailResult['Email'];

// $movie_stmt = $conn->prepare("SELECT * FROM movie WHERE Movie_ID = ?");
// $movie_stmt->bind_param("i", $Movie_ID);
// $movie_stmt->execute();
// $movieDetails = ($movie_stmt->get_result())->fetch_assoc();

// $mall_stmt = $conn->prepare("SELECT * FROM mall WHERE Mall_ID = ?");
// $mall_stmt->bind_param("i", $Mall_ID);
// $mall_stmt->execute();
// $mallDetails = ($mall_stmt->get_result())->fetch_assoc();

// $timeslot_stmt = $conn->prepare("SELECT * FROM timeslot WHERE TimeSlot_ID = ?");
// $timeslot_stmt->bind_param("i", $TimeSlot_ID);
// $timeslot_stmt->execute();
// $timeslotDetails = ($timeslot_stmt->get_result())->fetch_assoc();

// $theater_stmt = $conn->prepare("SELECT TheaterName FROM theater WHERE Theater_ID = ?");
// $theater_stmt->bind_param("i", $timeslotDetails['Theater_ID']);
// $theater_stmt->execute();
// $theaterDetails = ($theater_stmt->get_result())->fetch_assoc();

// $seatPositions = [];
// if (!empty($selectedSeats)) {
//     $placeholders = str_repeat('?,', count($selectedSeats) - 1) . '?';
//     $seat_stmt = $conn->prepare("SELECT Seat_ID, SeatRow, SeatColumn FROM seats WHERE Seat_ID IN ($placeholders)");
    
//     $types = str_repeat('i', count($selectedSeats));
//     $seat_stmt->bind_param($types, ...$selectedSeats);
//     $seat_stmt->execute();
//     $seatResult = $seat_stmt->get_result();
    
//     while ($seat = $seatResult->fetch_assoc()) {
//         $seatPositions[] = $seat['SeatRow'] . $seat['SeatColumn'];
//     }
    
//     sort($seatPositions);
// }

// $bookingRef = 'PC-' . date('Ymd') . '-' . rand(1000, 9999);

// if (empty($selectedSeats)) {
//     die("No seats selected.");
// }

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $conn -> begin_transaction();

//     try {
//         $seatUpdate_stmt = $conn -> prepare("UPDATE seats SET SeatAvailability = 0 WHERE Seat_ID = ?");

//         foreach ($selectedSeats as $Seat_ID) {
//             $seatUpdate_stmt -> bind_param("i", $Seat_ID);
//             $seatUpdate_stmt -> execute();
//         }

//         $ticketIDs = [];
//         $ticket_stmt = $conn -> prepare("INSERT INTO ticket(Seat_ID, Customer_ID, Movie_ID, TimeSlot_ID, Price, Status, DateTime)
//                                         VALUES (?, ?, ?, ?, ?, ?, ?)");

//         $Status = 1;
//         $dateTime = date('Y-m-d H:i:s');    
//         $price = $totalPrice / count($selectedSeats);

//         foreach ($selectedSeats as $Seat_ID) {
//             $ticket_stmt -> bind_param("iiiidis", $Seat_ID, $Customer_ID, $Movie_ID, $TimeSlot_ID, $price, $Status, $dateTime);
//             $ticket_stmt -> execute();
//             $ticketIDs[] = $conn -> insert_id;
//         }

//         $payment_stmt = $conn -> prepare("INSERT INTO payment(Ticket_ID, PaymentMethod, AmountPaid, PaymentDate, PaymentStatus)
//                                         VALUES (?, ?, ?, ?, ?)");    

//         $receipt_stmt = $conn -> prepare("INSERT INTO `e-receipt`(Payment_ID, DateIssued, ReceiptStatus, Status)
//                                         VALUES (?, ?, ?, ?)");

//         foreach ($ticketIDs as $Ticket_ID) {
//             $payment_stmt -> bind_param("isdsi", $Ticket_ID, $paymentMethod, $price, $dateTime, $Status);
//             $payment_stmt -> execute();
//             $Payment_ID = $conn -> insert_id;

//             $receipt_stmt -> bind_param("isii", $Payment_ID, $dateTime, $Status, $Status);
//             $receipt_stmt -> execute();
//         }

//         $conn -> commit();
//     } catch (Exception $e) {
//         $conn -> rollback();
//         throw $e;
//     }    
// }
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Segoe UI',sans-serif;
        }

        body{
            background:linear-gradient(to bottom,#071018,#0d1b2a);
            color:white;
            min-height:100vh;
            overflow-x:hidden;
        }

        header{
            position:fixed;
            width:100%;
            top:0;
            padding:20px 60px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            z-index:1000;
            background:linear-gradient(to bottom,rgba(7,16,24,0.95),transparent);
            transition:0.3s;
        }

        header.scrolled{
            background:#071018;
            box-shadow:0 4px 25px rgba(0,0,0,0.6);
        }

        .logo img{
            height:45px;
        }

        nav{
            display:flex;
            gap:10px;
        }

        nav a{
            background:rgba(255,255,255,0.08);
            padding:8px 15px;
            border-radius:20px;
            text-decoration:none;
            color:white;
            transition:0.3s;
        }

        nav a:hover,
        nav a.active{
            background:#2dd4bf;
            color:#071018;
        }

        main{
            margin-top:130px;
            padding:0 60px;
        }

        #topLinkSection{
            margin-bottom:30px;
        }

        .topLink{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            align-items:center;
        }

        .topLink a{
            background:rgba(255,255,255,0.08);
            padding:8px 15px;
            border-radius:20px;
            text-decoration:none;
            color:white;
            transition:0.3s;
        }

        .topLink a:hover,
        .topLink a#active{
            background:#2dd4bf;
            color:#071018;
        }

        #receiptSection{
            background:rgba(255,255,255,0.06);
            backdrop-filter:blur(10px);
            border-radius:15px;
            padding:30px;
            box-shadow:0 10px 30px rgba(0,0,0,0.4);
            margin-bottom:40px;
        }

        .receipt-header{
            text-align:center;
            margin-bottom:20px;
        }

        .receipt-header h2{
            font-size:2.6rem;
            color:#2dd4bf;
            letter-spacing:1px;
        }

        .thank-you{
            font-size:1.15rem;
            color:#9ca3af;
            margin-top:8px;
        }

        .receipt-details{
            display:flex;
            flex-direction:column;
            gap:10px;
        }

        .receipt-row{
            display:flex;
            justify-content:space-between;
            padding:14px 18px;
            border-radius:12px;
            background:rgba(255,255,255,0.05);
        }

        .receipt-row span:first-child{
            color:#8a9bad;
            font-weight:500;
        }

        .receipt-row span:last-child{
            font-weight:bold;
            color:white;
        }

        .receipt-total{
            background:rgba(45,212,191,0.15);
            border:1px solid #2dd4bf;
            font-size:18px;
            padding:16px 20px;
            border-radius:12px;
        }

        .booking-box{
            text-align:center;
            padding:20px;
            border-radius:15px;
            background:rgba(255,255,255,0.06);
            margin-top:18px;
        }

        .booking-box p{
            opacity:0.7;
            margin-bottom:8px;
        }

        #bookingReference{
            font-size:1.5rem;
            color:#2dd4bf;
            font-weight:bold;
            letter-spacing:2px;
        }

        .receipt-footer{
            text-align:center;
            margin-top:18px;
            font-size:0.9rem;
            color:#64748b;
            opacity:0.75;
        }

        .button-container{
            display:flex;
            justify-content:center;
            gap:10px;
            margin-top:25px;
            flex-wrap:wrap;
        }

        .btn{
            background:rgba(255,255,255,0.08);
            color:white;
            border:none;
            padding:10px 20px;
            border-radius:20px;
            cursor:pointer;
            transition:0.3s;
        }

        .btn:hover{
            background:#2dd4bf;
            color:#071018;
        }

        .btn-primary{
            background:#2dd4bf;
            color:#071018;
            font-weight:bold;
        }

        .pdf-mode {
            background: #071018 !important;
            color: white !important;
            backdrop-filter: none !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.6) !important;
            padding: 35px !important;
            border-radius: 15px !important;
            width: 100% !important;
            max-width: 800px !important;
            margin: 0 auto !important;
        }

        .pdf-mode * {
            color: white !important;
        }

        .pdf-mode .receipt-row {
            background: rgba(255,255,255,0.08) !important;
            border: 1px solid rgba(255,255,255,0.15) !important;
        }

        .pdf-mode .receipt-total {
            background: rgba(45,212,191,0.18) !important;
            border: 1px solid #2dd4bf !important;
            font-size: 20px !important;
        }

        .pdf-mode .receipt-header h2 {
            color: #2dd4bf !important;
            font-size: 2.8rem !important;
        }

        .pdf-mode .thank-you {
            color: #9ca3af !important;
        }

        .pdf-mode #bookingReference {
            color: #2dd4bf !important;
            font-size: 1.8rem !important;
        }

        .pdf-mode .booking-box {
            background: rgba(255,255,255,0.08) !important;
            border: 1px solid rgba(255,255,255,0.15) !important;
        }

        .receipt-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 18px;
        }
        .receipt-logo img {
            height: 52px;
        }
        .logo-text {
            font-size: 2.1rem;
            font-weight: bold;
            color: #2dd4bf;
            letter-spacing: 3px;
        }

        .pdf-mode .receipt-footer {
            margin-top: 15px !important;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="peakscinemastransparent.png" alt="PeaksCinemas Logo">
        </div>
        <nav>
            <a href="home.php">Home</a>
            <a href="home.php">Back to Home</a>
        </nav>
    </header>

    <main>
        <div id="topLinkSection">
            <nav class="topLink">
                <a href="home.php">Home</a><p>&nbsp/&nbsp</p>
                <a href="movie.php?movie_id=<?= htmlspecialchars($Movie_ID) ?>">Malls with "<?= htmlspecialchars($movieDetails['MovieName']) ?>"</a><p>&nbsp/&nbsp</p>
                <a href="mall.php?movie_id=<?= htmlspecialchars($Movie_ID) ?>&mall_id=<?= htmlspecialchars($Mall_ID) ?>&date=<?= htmlspecialchars($Date) ?>">Available theaters in "<?= htmlspecialchars($mallDetails['MallName']) ?>"</a><p>&nbsp/&nbsp</p>
                <a href="seat_selection.php?movie_id=<?= htmlspecialchars($Movie_ID) ?>&mall_id=<?= htmlspecialchars($Mall_ID) ?>&date=<?= htmlspecialchars($Date) ?>&timeslot_id=<?= htmlspecialchars($TimeSlot_ID) ?>">Seats Selection in <?= htmlspecialchars($theaterDetails['TheaterName']) ?></a><p>&nbsp/&nbsp</p>
                <a href="payment.php">Payment</a><p>&nbsp/&nbsp</p>
                <a id="active">Receipt</a> 
            </nav>
        </div>

        <section id="receiptSection">
            <div class="receipt-logo">
                <img src="peakscinemastransparent.png" alt="PeaksCinemas">
            </div>

            <div class="receipt-header">
                <h2>Booking Confirmation</h2>
                <p class="thank-you">Thank you for your purchase!</p>
            </div>
            
            <div class="receipt-details">
                <div class="receipt-row">
                    <span>Movie:</span>
                    <span id="receiptMovieName"><?= htmlspecialchars($movieDetails['MovieName']) ?></span>
                </div>
                <div class="receipt-row">
                    <span>Cinema:</span>
                    <span id="receiptCinemaName"><?= htmlspecialchars($mallDetails['MallName']) ?> - <?= htmlspecialchars($theaterDetails['TheaterName']) ?></span>
                </div>
                <div class="receipt-row">
                    <span>Date & Time:</span>
                    <span id="receiptDateTime">
                        <?= htmlspecialchars($Date) ?> - 
                        <?php 
                        if (isset($timeslotDetails['ScreeningType']) && isset($timeslotDetails['StartTime'])) {
                            echo htmlspecialchars($timeslotDetails['ScreeningType'] . ' - ' . date("g:i A", strtotime($timeslotDetails['StartTime'])));
                        } else {
                            echo 'Time not available';
                        }
                        ?>
                    </span>
                </div>
                <div class="receipt-row">
                    <span>Seats:</span>
                    <span id="selectedSeatsReceipt">
                        <?php 
                        if (!empty($seatPositions)) {
                            echo implode(", ", $seatPositions);
                        } else {
                            echo 'No seats selected';
                        }
                        ?>
                    </span>
                </div>
                <div class="receipt-row">
                    <span>Tickets:</span>
                    <span id="ticketCountReceipt"><?= count($selectedSeats) ?></span>
                </div>
                <div class="receipt-row">
                    <span>Customer Name:</span>
                    <span id="customerNameReceipt"><?= !empty($customerName) ? htmlspecialchars($customerName) : '-' ?></span>
                </div>
                <div class="receipt-row">
                    <span>Payment Method:</span>
                    <span id="paymentMethodReceipt">
                        <?php 
                        switch($paymentMethod) {
                            case 'credit': echo 'Credit/Debit Card'; break;
                            case 'paypal': echo 'PayPal'; break;
                            case 'gcash': echo 'GCash'; break;
                            case 'paymaya': echo 'PayMaya'; break;
                            default: echo '-';
                        }
                        ?>
                    </span>
                </div>
                <div class="receipt-row receipt-total">
                    <span>Total:</span>
                    <span>₱<span id="totalReceipt"><?= number_format($totalPrice, 2) ?></span></span>
                </div>
            </div>
            
            <div class="booking-box">
                <p>Booking Reference</p>
                <div id="bookingReference"><?= htmlspecialchars($bookingRef) ?></div>
            </div>

            <div class="receipt-footer">
                Valid for one-time use only • PeaksCinemas © 2026
            </div>
            
            <div class="button-container">
                <button class="btn btn-primary" onclick="downloadReceipt()">Download Receipt</button>
                <button class="btn" onclick="goHome()">Back to Home</button>
            </div>
        </section>
    </main>

    <script>
        function downloadReceipt() {
            const receipt = document.getElementById('receiptSection');
            const headerEl = document.querySelector('header');
            const topLinkEl = document.getElementById('topLinkSection');
            const buttons = document.querySelector('.button-container');

            const originalBodyBg = document.body.style.background;
            const originalHeaderDisplay = headerEl.style.display;
            const originalTopDisplay = topLinkEl.style.display;
            const originalBtnDisplay = buttons.style.display;

            headerEl.style.display = 'none';
            topLinkEl.style.display = 'none';
            buttons.style.display = 'none';
            document.body.style.background = '#071018';
            receipt.classList.add('pdf-mode');

            receipt.offsetHeight;

            const bookingRef = document.getElementById('bookingReference').textContent.trim();

            const opt = {
                margin: [25, 25, 25, 25],
                filename: `receipt_${bookingRef}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: {
                    scale: 3.5,
                    useCORS: true,
                    backgroundColor: '#071018',
                    allowTaint: true,
                    logging: false,
                    scrollX: 0,
                    scrollY: 0
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };

            setTimeout(() => {
                html2pdf()
                    .set(opt)
                    .from(receipt)
                    .save()
                    .then(() => {
                        receipt.classList.remove('pdf-mode');
                        headerEl.style.display = originalHeaderDisplay;
                        topLinkEl.style.display = originalTopDisplay;
                        buttons.style.display = originalBtnDisplay;
                        document.body.style.background = originalBodyBg;
                    });
            }, 500);
        }

        function goHome() {
            window.location.href = 'home.php';
        }
    </script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
include("peakscinemas_database.php");

$message = "";


// ==================== FETCH USER INFO ====================
$stmt = $conn->prepare("SELECT LastName, FirstName, Email, PhoneNumber, Password FROM customer WHERE Customer_ID = ?");
$stmt->bind_param("i", $Customer_ID);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "User not found.";
    exit;
}
$user = $result->fetch_assoc();

// ==================== HANDLE PROFILE UPDATE ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['tab'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $user['Password'];

    $updateStmt = $conn->prepare("UPDATE customer SET Name = ?, Email = ?, PhoneNumber = ?, Password = ? WHERE Customer_ID = ?");
    $updateStmt->bind_param("ssssi", $name, $email, $phone, $hashedPassword, $Customer_ID);

    if ($updateStmt->execute()) {
        $message = "✅ Your profile has been updated successfully!";
        $user['Name'] = $name;
        $user['Email'] = $email;
        $user['PhoneNumber'] = $phone;
    } else {
        $message = "❌ Error updating profile. Please try again.";
    }
}

// ==================== FETCH PURCHASE HISTORY ====================
$history_stmt = $conn->prepare("SELECT 
    Purchase_ID, MovieName, MallName, TheaterName, Seats, 
    TotalPrice, PurchaseDate, Status 
    FROM purchases 
    WHERE Customer_ID = ? 
    ORDER BY PurchaseDate DESC");

$history_stmt->bind_param("i", $Customer_ID);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile - PeaksCinemas</title>
<style>
:root {
    --bg-dark: #141414;
    --bg-glass: rgba(255, 255, 255, 0.05);
    --accent: #2dd4bf;
    --accent-soft: rgba(45,212,191,0.2);
    --text-light: #ffffff;
    --text-muted: #9ca3af;
    --border-color: rgba(255,255,255,0.1);
    --success: #00c853;
    --error: #ff4b4b;
}

* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Poppins', sans-serif;
    background: radial-gradient(circle at top, #1f1f1f 0%, #0d0d0d 100%);
    color: var(--text-light);
    min-height: 100vh;
    padding-top: 110px;
}

/* HEADER */
header {
    backdrop-filter: blur(10px);
    background: rgba(0,0,0,0.6);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 50px;
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 10;
    border-bottom: 1px solid var(--border-color);
}
.logo img { height: 48px; cursor: pointer; transition: 0.3s ease; }
.logo img:hover { transform: scale(1.08); }

.header-actions { display: flex; align-items: center; gap: 12px; }
.profile-btn, .logout-btn { /* styles same as before */ }

/* TABS */
.tabs {
    display: flex;
    background: var(--bg-glass);
    border-radius: 12px;
    padding: 6px;
    margin-bottom: 30px;
    border: 1px solid var(--border-color);
}
.tab {
    flex: 1;
    padding: 12px;
    text-align: center;
    border-radius: 10px;
    cursor: pointer;
    transition: 0.3s;
    font-weight: 500;
}
.tab.active {
    background: var(--accent);
    color: #071018;
    font-weight: 600;
}

/* MAIN CARD */
.main-container {
    max-width: 800px;
    margin: auto;
    padding: 40px;
    border-radius: 20px;
    background: var(--bg-glass);
    backdrop-filter: blur(18px);
    border: 1px solid var(--border-color);
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
}

/* Form styles (same as your original) */
label { font-size:14px; color: var(--text-muted); margin-bottom: 6px; display: block; }
input[type="text"], input[type="email"], input[type="tel"], input[type="password"] {
    width: 100%; padding: 12px 14px; margin-bottom: 18px;
    border-radius: 10px; border: 1px solid var(--border-color);
    background: rgba(255,255,255,0.04); color: white;
}
input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft); outline: none; }

input[type="submit"] {
    width: 100%; padding: 14px; border-radius: 12px; border: none;
    background: linear-gradient(135deg, #2dd4bf, #06b3a8);
    font-weight: 600; color: white; cursor: pointer;
}
input[type="submit"]:hover { transform: translateY(-3px) scale(1.02); }

/* History Table */
.history-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
.history-table th, .history-table td {
    padding: 14px 12px;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}
.history-table th {
    background: rgba(45, 212, 191, 0.15);
    color: var(--accent);
}
.history-table tr:hover {
    background: rgba(255,255,255,0.03);
}
.status {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: bold;
}
.status.Paid { background: #2dd4bf; color: #071018; }

.refund-btn {
    margin-left: 10px;
    padding: 6px 12px;
    border-radius: 20px;
    border: none;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    background: #ff4b4b;
    color: white;
    transition: 0.2s;
}
.refund-btn:hover {
    transform: scale(1.05);
}

.message { padding: 12px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-weight: 500; }
.message.success { background: rgba(0,200,83,0.1); border: 1px solid var(--success); color: var(--success); }
.message.error { background: rgba(255,75,75,0.1); border: 1px solid var(--error); color: var(--error); }

.no-history {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-muted);
    font-size: 1.1rem;
}
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="peakscinemastransparent.png" alt="PeaksCinemas Logo" onclick="window.location.href='home.php'">
    </div>
    <div class="header-actions">
        <button class="profile-btn" onclick="window.location.href='<?= $profile_link ?>'" title="Profile">👤</button>
        <a href="?logout=1"><button class="logout-btn">Logout</button></a>
    </div>
</header>

<div class="main-container">

    <div class="tabs">
        <div class="tab active" onclick="switchTab(0)">Account Settings</div>
        <div class="tab" onclick="switchTab(1)">Booking History</div>
    </div>

    <!-- ==================== ACCOUNT SETTINGS TAB ==================== -->
    <div id="tab0" class="tab-content">
        <div class="profile-header">
            <h2>Account Settings</h2>
            <p>Manage your PeaksCinemas profile information</p>
        </div>

        <?php if (!empty($message)) : ?>
            <div class="message <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="tab" value="0">

            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required value="<?= htmlspecialchars($user['Name']) ?>">

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($user['Email']) ?>">

            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" required pattern="[0-9]{10}" 
                   value="<?= htmlspecialchars($user['PhoneNumber']) ?>">

            <label for="password">New Password (leave blank to keep current)</label>
            <div class="password-container" style="position:relative;">
                <input type="password" id="password" name="password" placeholder="Enter new password">
                <button type="button" id="togglePassword" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#999;cursor:pointer;">Show</button>
            </div>

            <input type="submit" value="Save Changes">
        </form>
    </div>

    <!-- ==================== BOOKING HISTORY TAB ==================== -->
    <div id="tab1" class="tab-content" style="display:none;">
        <h2 style="margin-bottom:25px; text-align:center;">My Booking History</h2>

        <?php if ($history_result->num_rows > 0): ?>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Movie</th>
                        <th>Mall</th>
                        <th>Theater</th>
                        <th>Seats</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $history_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= date("M d, Y • h:i A", strtotime($row['PurchaseDate'])) ?></td>
                            <td><?= htmlspecialchars($row['MovieName']) ?></td>
                            <td><?= htmlspecialchars($row['MallName']) ?></td>
                            <td><?= htmlspecialchars($row['TheaterName']) ?></td>
                            <td><?= htmlspecialchars(str_replace(',', ', ', $row['Seats'])) ?></td>
                            <td>₱<?= number_format($row['TotalPrice'], 2) ?></td>
                            <td>
                                <span class="status <?= htmlspecialchars($row['Status']) ?>">
                                <?= htmlspecialchars($row['Status']) ?>
                                </span>

                                <?php if ($row['Status'] === 'Paid'): ?>
                                <button class="refund-btn" onclick="window.location.href='home.php'">
                                Refund
                            </button>
                            <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-history">
                <p>You don't have any booking history yet.</p>
                <button onclick="window.location.href='movie.php'" style="margin-top:20px; padding:12px 24px; background:var(--accent); color:#071018; border:none; border-radius:30px; cursor:pointer;">
                    Browse Movies Now
                </button>
            </div>
        <?php endif; ?>
    </div>

</div>

<script>
// Tab switching
function switchTab(tabIndex) {
    document.querySelectorAll('.tab').forEach((tab, index) => {
        tab.classList.toggle('active', index === tabIndex);
    });
    
    document.querySelectorAll('.tab-content').forEach((content, index) => {
        content.style.display = (index === tabIndex) ? 'block' : 'none';
    });
}

// Password toggle (for Account Settings tab)
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.textContent = 'Hide';
            } else {
                passwordInput.type = 'password';
                toggleBtn.textContent = 'Show';
            }
        });
    }
});
</script>

</body>
</html>


            <table class="history-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Movie</th>
                        <th>Mall</th>
                        <th>Theater</th>
                        <th>Seats</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                        <tr>
                            <td class="PurchaseDate"></td>
                            <td class="MovieName"></td>
                            <td class="TheaterName"></td>
                            <td class="seats"></td>
                            <td class="TotalPrice">₱</td>
                            <td>
                                <span class="status"></span>

                                
                                <button class="refund-btn">
                                Refund
                                </button>
                            </td>
                        </tr>
                </tbody>
            </table>