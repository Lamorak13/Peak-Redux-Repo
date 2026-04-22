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
    <link rel="manifest" href="manifest.json">
    <link rel="stylesheet" href="site.css">
    <script src="customer_gate.js"></script>
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
            <a href="home.php">Back to Home</a>
        </nav>
    </header>

    <main>
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
                    <span id="receiptMovieName"></span>
                </div>
                <div class="receipt-row">
                    <span>Cinema:</span>
                    <span id="receiptCinemaName"></span>
                </div>
                <div class="receipt-row">
                    <span>Date & Time:</span>
                    <span id="receiptDateTime">
                        
                    </span>
                </div>
                <div class="receipt-row">
                    <span>Seats:</span>
                    <span id="selectedSeatsReceipt">
                        
                    </span>
                </div>
                <div class="receipt-row">
                    <span>Tickets:</span>
                    <span id="ticketCountReceipt"></span>
                </div>
                <div class="receipt-row">
                    <span>Customer Name:</span>
                    <span id="customerNameReceipt"></span>
                </div>
                <div class="receipt-row">
                    <span>Payment Method:</span>
                    <span id="paymentMethodReceipt">
                        
                    </span>
                </div>
                <div class="receipt-row receipt-total">
                    <span>Total:</span>
                    <span>₱<span id="totalReceipt"></span></span>
                </div>
            </div>
            
            <div class="booking-box">
                <p>Booking Reference</p>
                <div id="bookingReference"></div>
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
            const buttons = document.querySelector('.button-container');

            const originalBodyBg = document.body.style.background;
            const originalHeaderDisplay = headerEl.style.display;
            const originalBtnDisplay = buttons.style.display;

            headerEl.style.display = 'none';
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
                    scale: 2,
                    useCORS: true,
                    backgroundColor: '#071018',
                    allowTaint: true,
                    logging: false,
                    scrollX: 0,
                    scrollY: 0,
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
                        buttons.style.display = originalBtnDisplay;
                        document.body.style.background = originalBodyBg;
                    });
            }, 500);
        }

        function goHome() {
            window.location.href = 'home.php';
        }

        const urlParams = new URLSearchParams(window.location.search);
        const Receipt_ID = urlParams.get('receipt_id');

        window.onload = function() {
            fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=receipt/${Receipt_ID}`, {
                method: 'GET'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Failed to load receipt (HTTP ${response.status})`);
                }
                return response.json();
            })
            .then(data => {
                const receipt = data.data?.[0];
                if (!receipt) {
                    throw new Error("Receipt not found.");
                }

                document.getElementById('receiptMovieName').textContent = receipt.MovieName;                
                document.getElementById('receiptCinemaName').textContent = receipt.TheaterName;
                document.getElementById('receiptDateTime').textContent = receipt.Date + " | " + receipt.ScreeningType + " - " 
                                                                        + new Date(`1970-01-01T${receipt.StartTime}`).toLocaleTimeString('en-US', {
                                                                            hour: 'numeric',
                                                                            minute: 'numeric',
                                                                            hour12: true
                                                                        });
                document.getElementById('customerNameReceipt').textContent = receipt.LastName + ", " + receipt.FirstName;
                document.getElementById('selectedSeatsReceipt').textContent = receipt.Seat_List || "";
                document.getElementById('ticketCountReceipt').textContent = receipt.Ticket_Count || "";

                const paymentMethodReceipt = document.getElementById('paymentMethodReceipt');

                switch (receipt.PaymentMethod) {
                    case 'credit':
                        paymentMethodReceipt.textContent = "Credit/Debit Card";
                        break;
                    case 'paypal':
                        paymentMethodReceipt.textContent = "PayPal";
                        break;
                    case 'gcash':
                        paymentMethodReceipt.textContent = "GCash";
                        break;
                    case 'paymaya':
                        paymentMethodReceipt.textContent = "PayMaya";
                        break;
                }

                document.getElementById('totalReceipt').textContent = receipt.AmountPaid;

                const paymentYear = receipt.PaymentDate ? new Date(receipt.PaymentDate).getFullYear() : new Date().getFullYear();
                document.getElementById('bookingReference').textContent = "PC" + receipt.Receipt_ID + paymentYear;
            })
            .catch(err => {
                alert(err.message || "Error loading receipt.");
            });
        }
    </script>    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</body>
</html>