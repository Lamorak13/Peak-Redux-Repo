<?php
    include("peakscinemas_database.php");
    session_start();
    $profile_link = "personal_info_form.php";


$Movie_ID = isset($_POST['movie_id']) ? $_POST['movie_id'] : '';
$Mall_ID = isset($_POST['mall_id']) ? $_POST['mall_id'] : '';
$Date = isset($_POST['date']) ? $_POST['date'] : '';
$TimeSlot_ID = isset($_POST['timeslot_id']) ? $_POST['timeslot_id'] : '';
$selectedSeats = isset($_POST['selectedSeats']) ? $_POST['selectedSeats'] : [];
$totalPrice = isset($_POST['priceTotal']) ? $_POST['priceTotal'] : 0;


if (empty($selectedSeats) || $totalPrice <= 0) {
    header("Location: seat_selection.php?movie_id=" . $Movie_ID . "&mall_id=" . $Mall_ID . "&date=" . $Date . "&timeslot_id=" . $TimeSlot_ID);
    exit;
}


$_SESSION['booking_data'] = [
    'movie_id' => $Movie_ID,
    'mall_id' => $Mall_ID,
    'date' => $Date,
    'timeslot_id' => $TimeSlot_ID,
    'selectedSeats' => $selectedSeats,
    'totalPrice' => $totalPrice
];


include("peakscinemas_database.php");


$movie_stmt = $conn->prepare("SELECT * FROM movie WHERE Movie_ID = ?");
$movie_stmt->bind_param("i", $Movie_ID);
$movie_stmt->execute();
$movieDetails = ($movie_stmt->get_result())->fetch_assoc();


$mall_stmt = $conn->prepare("SELECT * FROM mall WHERE Mall_ID = ?");
$mall_stmt->bind_param("i", $Mall_ID);
$mall_stmt->execute();
$mallDetails = ($mall_stmt->get_result())->fetch_assoc();


$timeslot_stmt = $conn->prepare("SELECT * FROM timeslot INNER JOIN theater ON timeslot.Theater_ID=theater.Theater_ID WHERE TimeSlot_ID = ?");
$timeslot_stmt->bind_param("i", $TimeSlot_ID);
$timeslot_stmt->execute();
$timeslotDetails = ($timeslot_stmt->get_result())->fetch_assoc();


$seatPositions = [];
if (!empty($selectedSeats)) {
  
    $placeholders = str_repeat('?,', count($selectedSeats) - 1) . '?';
    $seat_stmt = $conn->prepare("SELECT Seat_ID, SeatRow, SeatColumn FROM seats WHERE Seat_ID IN ($placeholders)");
    
 
    $types = str_repeat('i', count($selectedSeats));
    $seat_stmt->bind_param($types, ...$selectedSeats);
    $seat_stmt->execute();
    $seatResult = $seat_stmt->get_result();
    
    while ($seat = $seatResult->fetch_assoc()) {
        $seatPositions[] = $seat['SeatRow'] . $seat['SeatColumn'];
    }
    

    sort($seatPositions);
}
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        header {
            background-color: #6A7F3F;
            background: linear-gradient(90deg,rgba(106, 127, 63, 1) 0%, rgba(74, 106, 90, 1) 100%);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 30px;
            border-bottom: 1px solid #ffffffff;
            }
         
            body {
                font-family: 'Segoe UI', Arial, sans-serif;
                color: white;
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                background: #5C4033;
                background: linear-gradient(360deg, rgba(92, 64, 51, 1) 0%, rgba(51, 17, 0, 1) 100%);
      
                }

            body::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(to bottom, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 11%, transparent 100%);
                pointer-events: none;
            }
       
                
            .logo img {
               height: 50px;
               width: auto;
               cursor: pointer;
               transition: transform 0.3s ease;
            }

            .logo img:hover {
               transform: scale(1.05);
            }

            
             .profile-btn {
                background-color: #4b4b4b;
                background: linear-gradient(90deg,rgba(75, 75, 75, 1) 0%, rgba(43, 43, 43, 1) 100%);
                border: 1px solid #CCCCCC;
                border-radius: 50%;
                width: 45px;
                height: 45px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                margin-left: auto;
                box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            }

            .profile-btn svg {
                width: 24px;
                height: 24px;
                transition: transform 0.3s ease;
            }

            .profile-btn:hover {
                background: #ffffff;
                background: linear-gradient(90deg,rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
                transform: scale(1.1);
                border: 1px solid #4b4b4b;
                box-shadow: 0 0 8px rgba(255,255,255,0.3);
            }

            .profile-btn:hover svg {
                transform: scale(1.05);
            }


        nav {
            display: flex;
            gap: 10px;
        }

        nav a {
            background-color: #4b4b4b;
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 10px;
            border: 1px solid #a3c2b1;
            transition: 0.3s;
        }

        nav a:hover,
        nav a.active {
            background-color: #a3c2b1;
            color: #2b2b2b;
        }

        .main-container {
            background-color: #a3c2b1;
            margin: 40px auto;
            width: 85%;
            padding: 30px;
            border-radius: 10px;
            border: 3px solid #4b4b4b;
        }

        .tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .tab {
            background-color: #4b4b4b;
            color: white;
            padding: 10px 25px;
            border-radius: 10px 10px 0 0;
            margin: 0 3px;
            cursor: pointer;
            font-weight: bold;
            border: 1px solid #4b4b4b;
        }

        .tab.active {
            background-color: #a3c2b1;
            color: #2b2b2b;
            border-bottom: none;
        }

        /* Top Link Stuff */
        #topLinkSection {
            width: 50%;
            margin: 10px auto;
            display: flex;
            align-items: center;
            gap: 20px;
            border-radius: 10px;
        }

        .topLink {
            border-radius: 10px;
            width: auto;
            align-items: center;
            text-align: center;
        }

        .topLink a {
                color: #FFFFFF;
                background-color: #4b4b4b;
                background: linear-gradient(90deg,rgba(75, 75, 75, 1) 0%, rgba(43, 43, 43, 1) 100%);
                text-decoration: none;
                padding: 8px 15px;
                border-radius: 10px;
                border: 1px solid #CCCCCC;
                transition: 0.3s;
                margin-top: 10px;
                font-size: 18px;
                font-weight: 900;
                font-family: 'Poppins', sans-serif;
                font-weight: 600;
                text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
            }

            .topLink a#active {
                background: #ffffff;
                background: linear-gradient(360deg,rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
                color: white;
                text-decoration: none;
                padding: 8px 15px;
                border-radius: 10px;
                border: 1px solid #4b4b4b;
                font-weight: bold;
                color: #363635;
                font-family: 'Poppins', sans-serif;
                font-weight: 900;
                
                transition: 0.3s ease;
            }

            
        

            .topLink a:hover,
            .topLink a.active {
                background-color: #ffffffff;
                background: linear-gradient(90deg,rgba(245, 245, 245, 1) 0%, rgba(255, 255, 255, 1) 100%);
                color: #2b2b2b;
                border: 1px solid #4b4b4b;
                box-shadow: 0 0 8px rgba(255,255,255,0.3);
                transition: 0.3s ease;
            }

        /* Available seats stuff */

         .glassbox {
            background: rgba(255, 255, 255, 0.3); /* semi-transparent white */
            padding: 20px;
            border: 2px solid white;
            width: 50%;
            margin: 5px auto;
            margin-top: 30px;
            margin-bottom: 10px;
            border-radius: 10px;
            color: #363635;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.5);
            margin: 0px auto;
            width: 50%;
            align-items: left;
            border-radius: 10px;
            font-weight: 500;
            font-size: 18px;
            font-family: 'Poppins', sans-serif;
            color: #ffffffff;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
            cursor: pointer;
            transition: 0.3s ease;
        }

         .glassbox:hover {
            background: #ffffff;
            background: linear-gradient(360deg,rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
            border: 2px solid #363635;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
            color: #363635;



        }


       

        /* Payment and Receipt Styles */

        .glassbox-2 {
            background: rgba(255, 255, 255, 0.3);
            padding: 20px;
            border: 2px solid white;
            width: 50%;
            margin: 5px auto;
            border-radius: 10px;
            color: #363635;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.5);
        }

      .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 15px;
          
        }
    

        h2 {
            color: #ffffffff;
            text-align: center;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 1px solid #ffffffff;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .payment-option:hover {
          background: #afbda4;
          background: linear-gradient(360deg, rgba(175, 189, 164, 1) 0%, rgba(150, 176, 163, 1) 100%);
        }

        .payment-option.selected {
        background: #afbda4;
        background: linear-gradient(360deg, rgba(175, 189, 164, 1) 0%, rgba(150, 176, 163, 1) 100%);
        }

        .payment-option input[type="radio"] {
            margin: 0;
            cursor: pointer;
        }

        .payment-logo {
            width: 60px;
            height: 40px;
            object-fit: contain;
            background-color: white;
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
            cursor: pointer;
        }

        .payment-label {
            cursor: pointer;
            flex: 1;
            color: #ffffff;
            text-shadow: 0 2px 3px rgba(0, 0, 0, 0.6);
            font-family: 'Segoe UI', Arial, sans-serif;
            font-weight: 500;
            font-size: 18px;
        }

        .payment-details {
            margin-top: 20px;
            display: none;
        }

        .payment-details.active {
            display: block;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #ffffffff;
            text-shadow: 0 2px 3px rgba(0, 0, 0, 0.6);

        }

        p {
            color: #ffffffff;
            text-shadow: 0 2px 3px rgba(0, 0, 0, 0.6);
            text-align: center;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #4b4b4b;
            border-radius: 5px;
            background-color: #e8f1ec;
        }

        .form-group input.valid {
            border-color: #4caf50;
            background-color: #a3c2b1;
            
        }

        .form-group input.invalid {
            border-color: #ff4444;
            background-color: #ffe6e6;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .btn {
            background-color: #4b4b4b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            margin-top: 10px;
            margin-right: 10px;
        }

        .btn:hover {
           background: #ffffff;
           background: linear-gradient(90deg,rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
           color: #363635;
           border: 1px solid #363635;
           text-shadow: 0 2px 3px rgba(0, 0, 0, 0.6);
           font-family: 'Poppins', sans-serif;
           font-size: 18px;
           font-weight: 500;
        }

        .btn-primary {
            background-color: #4b4b4b;
            background: linear-gradient(90deg,rgba(75, 75, 75, 1) 0%, rgba(43, 43, 43, 1) 100%);
            border: 1px solid #CCCCCC;
            width: 100%;
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            font-weight: 500;
        }

        .btn-primary:hover {
            
        }

        .btn-primary:disabled {
            background-color: #CCCCCC;
            cursor: not-allowed;
        }

        .paypal-redirect {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f1ec;
            border-radius: 5px;
            border: 1px solid #4b4b4b;
        }

        .paypal-redirect p {
            margin-bottom: 15px;
        }

        .error-message {
            color: #ff4444;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
        
        .hidden {
            display: none;
        }
        
        .validation-status {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            display: none;
        }
        
        .validation-status.valid {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .validation-status.invalid {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
     <header>
         <div class="logo">
         <img src="peakscinematransparent.png" alt="PeaksCinemas Logo" onclick="window.location.href='home.php'">
         </div>
    
        <div class="header-actions">
        <button class="profile-btn" onclick="window.location.href='<?= $profile_link ?>'" title="Profile">👤</button>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M5.121 17.804A8 8 0 1118.88 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
   </svg>
   </button>
    </div>
   </header>

    <main>
        <div id="topLinkSection">
            <nav class="topLink">
                <a href="home.php">Home</a><p>&nbsp/&nbsp</p>
                <a href="movie.php?movie_id=<?= htmlspecialchars($Movie_ID) ?>">Malls with "<?= htmlspecialchars($movieDetails['MovieName']) ?>"</a><p>&nbsp/&nbsp</p>
                <a href="mall.php?movie_id=<?= htmlspecialchars($Movie_ID) ?>&mall_id=<?= htmlspecialchars($Mall_ID) ?>&date=<?= htmlspecialchars($Date) ?>">Available theaters in "<?= htmlspecialchars($mallDetails['MallName']) ?>"</a><p>&nbsp/&nbsp</p>
                <a href="seat_selection.php?movie_id=<?= htmlspecialchars($Movie_ID) ?>&mall_id=<?= htmlspecialchars($Mall_ID) ?>&date=<?= htmlspecialchars($Date) ?>&timeslot_id=<?= htmlspecialchars($TimeSlot_ID) ?>">Seats Selection in <?= htmlspecialchars($timeslotDetails['TheaterName']) ?></a><p>&nbsp/&nbsp</p>
                <a id="active">Payment</a> 
            </nav>
        </div>
        
        <section id="seatsSelectionSection">
            <div class="glassbox">
            <div id="seatsSelectionText">Selected Seats: 
                <?php 
                if (!empty($selectedSeats)) { 
                    $seatRowCol_stmt = $conn -> prepare("SELECT SeatRow, SeatColumn FROM seats WHERE Seat_ID = ?"); echo "Seat's Selected: "; 
                    foreach ($selectedSeats as $seatID) { $seatRowCol_stmt -> bind_param("i", $seatID); 
                        $seatRowCol_stmt -> execute(); $seatDetails = ($seatRowCol_stmt -> get_result()) -> fetch_assoc(); 
                        echo $seatDetails['SeatRow'] . $seatDetails['SeatColumn'] . " ";
                    }
                } else { 
                    echo "No seats selected"; 
                }
                ?>
                | Total Price: ₱<?= number_format($totalPrice, 2) ?>
            </div>
            </div>
        </section>

    
        <form id="paymentForm" action="receipt.php" method="POST" novalidate>
           
            <input type="hidden" name="movie_id" value="<?= htmlspecialchars($Movie_ID) ?>">
            <input type="hidden" name="mall_id" value="<?= htmlspecialchars($Mall_ID) ?>">
            <input type="hidden" name="date" value="<?= htmlspecialchars($Date) ?>">
            <input type="hidden" name="timeslot_id" value="<?= htmlspecialchars($TimeSlot_ID) ?>">
            <input type="hidden" name="totalPrice" value="<?= htmlspecialchars($totalPrice) ?>">
            <?php foreach ($selectedSeats as $seat): ?>
                <input type="hidden" name="selectedSeats[]" value="<?= htmlspecialchars($seat) ?>">
            <?php endforeach; ?>
            
            <div class="glassbox-2">
            <div id="paymentSection">
                
                <h2>Payment Method</h2>
                <div class="payment-methods">
                    <div class="payment-option" onclick="selectPaymentMethod('credit')">
                        <input type="radio" id="credit" name="paymentMethod" value="credit">
                        <img src="visa.png" alt="Visa" class="payment-logo" onclick="selectPaymentMethod('credit')">
                        <label for="credit" class="payment-label" onclick="selectPaymentMethod('credit')">Credit/Debit Card</label>
                    </div>
                    
                    <div class="payment-option" onclick="selectPaymentMethod('paypal')">
                        <input type="radio" id="paypal" name="paymentMethod" value="paypal">
                        <img src="paypal.png" alt="PayPal" class="payment-logo" onclick="selectPaymentMethod('paypal')">
                        <label for="paypal" class="payment-label" onclick="selectPaymentMethod('paypal')">PayPal</label>
                    </div>
                    
                    <div class="payment-option" onclick="selectPaymentMethod('gcash')">
                        <input type="radio" id="gcash" name="paymentMethod" value="gcash">
                        <img src="gcash.png" alt="GCash" class="payment-logo" onclick="selectPaymentMethod('gcash')">
                        <label for="gcash" class="payment-label" onclick="selectPaymentMethod('gcash')">GCash</label>
                    </div>
                    
                    <div class="payment-option" onclick="selectPaymentMethod('paymaya')">
                        <input type="radio" id="paymaya" name="paymentMethod" value="paymaya">
                        <img src="paymaya.png" alt="PayMaya" class="payment-logo" onclick="selectPaymentMethod('paymaya')">
                        <label for="paymaya" class="payment-label" onclick="selectPaymentMethod('paymaya')">PayMaya</label>
                    </div>
                </div>

                <!-- Credit Card Form -->
                <div id="creditDetails" class="payment-details">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cardFirstName">First Name</label>
                            <input type="text" id="cardFirstName" name="cardFirstName" placeholder="John" data-pattern="[A-Za-z\s]+" data-required="true">
                            <div class="error-message" id="cardFirstNameError">Please enter a valid first name (letters only)</div>
                        </div>
                        <div class="form-group">
                            <label for="cardLastName">Last Name</label>
                            <input type="text" id="cardLastName" name="cardLastName" placeholder="Doe" data-pattern="[A-Za-z\s]+" data-required="true">
                            <div class="error-message" id="cardLastNameError">Please enter a valid last name (letters only)</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cardNumber">Card Number</label>
                        <input type="text" id="cardNumber" name="cardNumber" placeholder="1234 5678 9012 3456" data-pattern="[0-9\s]{13,19}" data-required="true" maxlength="19">
                        <div class="error-message" id="cardNumberError">Please enter a valid card number (13-16 digits)</div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiryDate">Expiry Date</label>
                            <input type="text" id="expiryDate" name="expiryDate" placeholder="MM/YY" data-pattern="(0[1-9]|1[0-2])\/[0-9]{2}" data-required="true" maxlength="5">
                            <div class="error-message" id="expiryDateError">Please enter a valid expiry date (MM/YY)</div>
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" name="cvv" placeholder="123" data-pattern="[0-9]{3,4}" data-required="true" maxlength="4">
                            <div class="error-message" id="cvvError">Please enter a valid CVV (3-4 digits)</div>
                        </div>
                    </div>
                </div>
                
                <!-- PayPal Form -->
                <div id="paypalDetails" class="payment-details">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="paypalFirstName">First Name</label>
                            <input type="text" id="paypalFirstName" name="paypalFirstName" placeholder="John" data-pattern="[A-Za-z\s]+" data-required="true">
                            <div class="error-message" id="paypalFirstNameError">Please enter a valid first name (letters only)</div>
                        </div>
                        <div class="form-group">
                            <label for="paypalLastName">Last Name</label>
                            <input type="text" id="paypalLastName" name="paypalLastName" placeholder="Doe" data-pattern="[A-Za-z\s]+" data-required="true">
                            <div class="error-message" id="paypalLastNameError">Please enter a valid last name (letters only)</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="paypalPhone">Phone Number</label>
                        <input type="text" id="paypalPhone" name="paypalPhone" placeholder="09XX XXX XXXX" data-pattern="09[0-9]{9}" data-required="true" maxlength="11">
                        <div class="error-message" id="paypalPhoneError">Please enter a valid Philippine mobile number (09XXXXXXXXX)</div>
                    </div>
                    <div class="form-group">
                        <label for="paypalEmail">Email Address</label>
                        <input type="email" id="paypalEmail" name="paypalEmail" placeholder="your.email@example.com" data-required="true">
                        <div class="error-message" id="paypalEmailError">Please enter a valid email address</div>
                    </div>
                    <p>You will be redirected to PayPal to complete your payment.</p>
                </div>
                
                <!-- GCash Form -->
                <div id="gcashDetails" class="payment-details">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="gcashFirstName">First Name</label>
                            <input type="text" id="gcashFirstName" name="gcashFirstName" placeholder="John" data-pattern="[A-Za-z\s]+" data-required="true">
                            <div class="error-message" id="gcashFirstNameError">Please enter a valid first name (letters only)</div>
                        </div>
                        <div class="form-group">
                            <label for="gcashLastName">Last Name</label>
                            <input type="text" id="gcashLastName" name="gcashLastName" placeholder="Doe" data-pattern="[A-Za-z\s]+" data-required="true">
                            <div class="error-message" id="gcashLastNameError">Please enter a valid last name (letters only)</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="gcashNumber">GCash Mobile Number</label>
                        <input type="text" id="gcashNumber" name="gcashNumber" placeholder="09XX XXX XXXX" data-pattern="09[0-9]{9}" data-required="true" maxlength="11">
                        <div class="error-message" id="gcashNumberError">Please enter a valid Philippine mobile number (09XXXXXXXXX)</div>
                    </div>
                    <p>You will receive a payment request on your GCash app.</p>
                </div>
                
                <!-- PayMaya Form -->
                <div id="paymayaDetails" class="payment-details">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="paymayaFirstName">First Name</label>
                            <input type="text" id="paymayaFirstName" name="paymayaFirstName" placeholder="John" data-pattern="[A-Za-z\s]+" data-required="true">
                            <div class="error-message" id="paymayaFirstNameError">Please enter a valid first name (letters only)</div>
                        </div>
                        <div class="form-group">
                            <label for="paymayaLastName">Last Name</label>
                            <input type="text" id="paymayaLastName" name="paymayaLastName" placeholder="Doe" data-pattern="[A-Za-z\s]+" data-required="true">
                            <div class="error-message" id="paymayaLastNameError">Please enter a valid last name (letters only)</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="paymayaNumber">PayMaya Mobile Number</label>
                        <input type="text" id="paymayaNumber" name="paymayaNumber" placeholder="09XX XXX XXXX" data-pattern="09[0-9]{9}" data-required="true" maxlength="11">
                        <div class="error-message" id="paymayaNumberError">Please enter a valid Philippine mobile number (09XXXXXXXXX)</div>
                    </div>
                    <p>You will receive a payment request on your PayMaya app.</p>
                </div>
                </div>

                <!-- Validation Status -->
                <div id="validationStatus" class="validation-status"></div>

                <!-- Submit Button -->
                <button type="submit" id="submitButton" class="btn btn-primary" disabled>Complete Payment</button>
            </div>
        </form>
    </main>

    <script>
        
        let currentPaymentMethod = '';

        function selectPaymentMethod(method) {
            
            document.getElementById(method).checked = true;
            currentPaymentMethod = method;
            
          
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            
            const currentOption = document.querySelector(`.payment-option input[value="${method}"]`).closest('.payment-option');
            currentOption.classList.add('selected');
            
            
            document.querySelectorAll('.payment-details').forEach(details => {
                details.classList.remove('active');
            });
            
            if (method === 'credit') {
                document.getElementById('creditDetails').classList.add('active');
            } else if (method === 'paypal') {
                document.getElementById('paypalDetails').classList.add('active');
            } else if (method === 'gcash') {
                document.getElementById('gcashDetails').classList.add('active');
            } else if (method === 'paymaya') {
                document.getElementById('paymayaDetails').classList.add('active');
            }
            
            
            validateCurrentForm();
        }

        
        function formatCardNumber(input) {
            // Remove all non-digits
            let value = input.value.replace(/\D/g, '');
            
            // Add spaces every 4 digits
            value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            
            // Update input value
            input.value = value;
            
            return value.replace(/\s/g, '');
        }

        // Format expiry date
        function formatExpiryDate(input) {
            let value = input.value.replace(/\D/g, '');
            
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            
            input.value = value;
            return value;
        }

        // Validate a single field
        function validateField(field) {
            const value = field.value.trim();
            const pattern = field.getAttribute('data-pattern');
            const isRequired = field.getAttribute('data-required') === 'true';
            const errorElement = document.getElementById(field.id + 'Error');
            
            // Remove existing validation classes
            field.classList.remove('valid', 'invalid');
            
            // Skip validation if field is empty and not required
            if (!isRequired && value === '') {
                if (errorElement) errorElement.style.display = 'none';
                return true;
            }
            
            // Check if field is required but empty
            if (isRequired && value === '') {
                field.classList.add('invalid');
                if (errorElement) errorElement.style.display = 'block';
                return false;
            }
            
            // Special validation for specific field types
            let isValid = true;
            
            if (field.id === 'cardNumber') {
                const cardNumber = formatCardNumber(field);
                isValid = cardNumber.length >= 13 && cardNumber.length <= 16;
            } else if (field.id === 'expiryDate') {
                const expiryValue = formatExpiryDate(field);
                const regex = /^(0[1-9]|1[0-2])\/[0-9]{2}$/;
                isValid = regex.test(expiryValue);
            } else if (field.type === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                isValid = emailRegex.test(value);
            } else if (pattern) {
                const regex = new RegExp('^' + pattern + '$');
                isValid = regex.test(value);
            }
            
            // Update UI
            if (isValid) {
                field.classList.add('valid');
                if (errorElement) errorElement.style.display = 'none';
            } else {
                field.classList.add('invalid');
                if (errorElement) errorElement.style.display = 'block';
            }
            
            return isValid;
        }

        // Validate the current payment method form
        function validateCurrentForm() {
            const statusElement = document.getElementById('validationStatus');
            const submitButton = document.getElementById('submitButton');
            
            if (!currentPaymentMethod) {
                statusElement.style.display = 'none';
                submitButton.disabled = true;
                return false;
            }
            
            const activeDetails = document.querySelector('.payment-details.active');
            if (!activeDetails) {
                statusElement.style.display = 'none';
                submitButton.disabled = true;
                return false;
            }
            
            const fields = activeDetails.querySelectorAll('input[data-required="true"]');
            let allValid = true;
            let emptyFields = 0;
            let filledFields = 0;
            
            for (let field of fields) {
                const isValid = validateField(field);
                if (!isValid) allValid = false;
                
                if (field.value.trim() === '') {
                    emptyFields++;
                } else {
                    filledFields++;
                }
            }
            
            // Update validation status
            if (filledFields === 0 && emptyFields === fields.length) {
                // No fields filled yet
                statusElement.style.display = 'none';
                statusElement.textContent = '';
                statusElement.className = 'validation-status';
            } else if (allValid) {
                // All fields are valid
                statusElement.style.display = 'block';
                statusElement.textContent = '✓ All fields are valid. You can proceed with payment.';
                statusElement.className = 'validation-status valid';
            } else {
                // Some fields are invalid
                statusElement.style.display = 'block';
                statusElement.textContent = 'Please fill in all required fields correctly.';
                statusElement.className = 'validation-status invalid';
            }
            
            // Enable/disable submit button
            submitButton.disabled = !allValid;
            
            return allValid;
        }

        // Real-time input validation
        document.addEventListener('DOMContentLoaded', function() {
            // Add input event listeners for real-time validation
            const inputs = document.querySelectorAll('input[data-required="true"]');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Only validate if this field belongs to the current payment method
                    const activeDetails = document.querySelector('.payment-details.active');
                    if (activeDetails && activeDetails.contains(this)) {
                        validateField(this);
                        validateCurrentForm();
                    }
                });
                
                input.addEventListener('blur', function() {
                    const activeDetails = document.querySelector('.payment-details.active');
                    if (activeDetails && activeDetails.contains(this)) {
                        validateField(this);
                        validateCurrentForm();
                    }
                });
            });

            // Form submission handler
            document.getElementById('paymentForm').addEventListener('submit', function(e) {
                const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked');
                
                if (!selectedMethod) {
                    e.preventDefault();
                    alert('Please select a payment method');
                    return;
                }
                
                // Validate only the current payment method's fields
                if (!validateCurrentForm()) {
                    e.preventDefault();
                    alert('Please fill in all required fields for the selected payment method correctly.');
                    return;
                }
                
                // If validation passes, the form will submit to receipt.php
            });
            
            // Initialize with no payment method selected
            validateCurrentForm();
        });
    </script>
</body>
</html>