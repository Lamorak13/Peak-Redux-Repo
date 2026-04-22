<?php
// session_start();
// include("peakscinemas_database.php");

// $Customer_ID = $_SESSION['user_id'];
// $profile_link = "profile.php";

// $Movie_ID = $_POST['movie_id'] ?? '';
// $Mall_ID = $_POST['mall_id'] ?? '';
// $Date = $_POST['date'] ?? '';
// $TimeSlot_ID = $_POST['timeslot_id'] ?? '';
// $selectedSeats = $_POST['selectedSeats'] ?? [];

// // ✅ Stop if no movie or seats selected
// if (empty($Movie_ID) || empty($selectedSeats)) {
//     header("Location: seat_selection.php");
//     exit;
// }

// // Convert seats array to string
// $Seats = implode(",", $selectedSeats);

// // Compute total price
// $pricePerSeat = 250;
// $totalPrice = count($selectedSeats) * $pricePerSeat;
// $status = "Paid";


// // 🔵 1. GET MOVIE NAME
// $movie_stmt = $conn->prepare("SELECT MovieName FROM movie WHERE Movie_ID = ?");
// $movie_stmt->bind_param("i", $Movie_ID);
// $movie_stmt->execute();
// $movieDetails = $movie_stmt->get_result()->fetch_assoc();
// $MovieName = $movieDetails['MovieName'];


// // 🔵 2. GET MALL NAME
// $mall_stmt = $conn->prepare("SELECT MallName FROM mall WHERE Mall_ID = ?");
// $mall_stmt->bind_param("i", $Mall_ID);
// $mall_stmt->execute();
// $mallDetails = $mall_stmt->get_result()->fetch_assoc();
// $MallName = $mallDetails['MallName'];


// // 🔵 3. GET THEATER NAME FROM TIMESLOT
// $timeslot_stmt = $conn->prepare("SELECT Theater_ID FROM timeslot WHERE TimeSlot_ID = ?");
// $timeslot_stmt->bind_param("i", $TimeSlot_ID);
// $timeslot_stmt->execute();
// $timeslotDetails = $timeslot_stmt->get_result()->fetch_assoc();

// $theater_stmt = $conn->prepare("SELECT TheaterName FROM theater WHERE Theater_ID = ?");
// $theater_stmt->bind_param("i", $timeslotDetails['Theater_ID']);
// $theater_stmt->execute();
// $theaterDetails = $theater_stmt->get_result()->fetch_assoc();
// $TheaterName = $theaterDetails['TheaterName'];
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
overflow-x:hidden;
min-height:100vh;
}

/* ===== HEADER (EXACT movie.php) ===== */
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
cursor:pointer;
}

.profile-btn{
background:linear-gradient(135deg,#2dd4bf,#14b8a6);
border:none;
padding:8px 20px;
border-radius:30px;
font-weight:bold;
cursor:pointer;
color:#071018;
transition:0.3s;
}

.profile-btn:hover{
transform:scale(1.05);
}

/* ===== MAIN ===== */
main{
margin-top:130px;
padding:0 60px;
}

/* ===== BREADCRUMB (movie.php style) ===== */
.topLink{
display:flex;
gap:10px;
margin-bottom:30px;
flex-wrap:wrap;
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
.topLink a.active{
background:#2dd4bf;
color:#071018;
}

.topLink a#active{
background:#2dd4bf;
color:#071018;
font-weight: bold;
}

/* ===== MAIN CONTAINERS (converted from glassbox) ===== */
.main-container,
.glassbox,
.glassbox-2{
background:rgba(255,255,255,0.06);
backdrop-filter:blur(10px);
border-radius:15px;
padding:25px;
margin-bottom:30px;
box-shadow:0 10px 30px rgba(0,0,0,0.4);
}

/* ===== PAYMENT TITLE ===== */
h2{
font-size:2rem;
margin-bottom:20px;
text-align:center;
}

/* ===== PAYMENT OPTIONS ===== */
.payment-methods{
display:flex;
flex-direction:column;
gap:15px;
margin-top:15px;
}

.payment-option{
display:flex;
align-items:center;
gap:15px;
padding:15px;
border-radius:12px;
background:rgba(255,255,255,0.08);
border:1px solid transparent;
cursor:pointer;
transition:0.3s;
}

.payment-option:hover{
background:#2dd4bf;
color:#071018;
transform:scale(1.02);
}

.payment-option.selected{
background:#2dd4bf;
color:#071018;
border:1px solid #14b8a6;
}

/* radio */
.payment-option input[type="radio"]{
transform:scale(1.2);
cursor:pointer;
}

/* logo */
.payment-logo{
width:60px;
height:40px;
object-fit:contain;
background:white;
padding:5px;
border-radius:5px;
}

/* text */
.payment-label{
font-size:18px;
font-weight:600;
}

/* ===== FORM ===== */
.form-group{
margin-bottom:15px;
}

.form-group label{
display:block;
margin-bottom:5px;
font-weight:500;
opacity:0.9;
}

.form-group input{
width:100%;
padding:10px;
border-radius:8px;
border:none;
background:#0d1b2a;
color:white;
border:1px solid rgba(255,255,255,0.2);
}

.form-group input.valid{
border-color:#2dd4bf;
}

.form-group input.invalid{
border-color:#ff4444;
}

/* ===== BUTTONS ===== */
.btn,
.btn-primary{
background:#2dd4bf;
color:#071018;
border:none;
padding:12px 20px;
border-radius:30px;
cursor:pointer;
font-weight:bold;
transition:0.3s;
width:100%;
}

.btn:hover,
.btn-primary:hover{
background:#14b8a6;
transform:scale(1.03);
}

/* disabled */
.btn-primary:disabled{
background:#555;
color:#aaa;
cursor:not-allowed;
transform:none;
}

/* ===== TABS (kept minimal) ===== */
.tabs{
display:flex;
justify-content:center;
margin-bottom:15px;
}

.tab{
background:rgba(255,255,255,0.08);
color:white;
padding:10px 20px;
border-radius:20px;
margin:0 5px;
cursor:pointer;
transition:0.3s;
}

.tab.active{
background:#2dd4bf;
color:#071018;
}

p{
opacity:0.9;
text-align:center;
}

.hidden{
display:none;
}

.payment-details{
    display: none;
}

.payment-details.active{
    display: block;
}

.progress-container{
    margin-top:20px;
    margin-bottom:20px;
}

.progress-label{
    font-size:14px;
    margin-bottom:8px;
    opacity:0.9;
    text-align:center;
}

.progress-bar{
    width:100%;
    height:10px;
    background:rgba(255,255,255,0.1);
    border-radius:20px;
    overflow:hidden;
}

#progressFill{
    height:100%;
    width:0%;
    background:#ff4444;
    transition:0.3s ease;
    border-radius:20px;
}
    </style>
</head>
<body>
     <header>
         <div class="logo">
         <img src="peakscinemastransparent.png" alt="PeaksCinemas Logo" onclick="window.location.href='home.php'">
         </div>
    
        <div class="header-actions">
        <button class="profile-btn" onclick="window.location.href='profile_edit.php'" title="Profile">👤</button>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M5.121 17.804A8 8 0 1118.88 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
   </svg>
   </button>
    </div>
   </header>

    <main>
        <div id="topLinkSection">
            <nav class="topLink">            
                <a href="home.php">Home</a>
                <a class="theatersWith">1. Select Theater For</a>
                <a class="selectionFor">2. Select Seats For </a> 
                <a id="active">3. Payment Information</a> 
            </nav>
        </div>
        
        <section id="seatsSelectionSection">
            <div class="glassbox">
                <div id="movieToWatch" style="font-weight: bold"></div><br>
                <div id="seatsSelectionText" style="font-weight: bold">Selected Seats: <div>
            </div>
        </section>

    
        <form id="paymentForm">
           
            <input type="hidden" name="movie_id" value="">
            <input type="hidden" name="mall_id" value="">
            <input type="hidden" name="date" value="">
            <input type="hidden" name="timeslot_id" value="">
            <input type="hidden" name="totalPrice" value="">
                <input type="hidden" name="selectedSeats[]" value="">
            
            <div class="glassbox-2">
            <div id="paymentSection">
                
                <h2>Payment Method</h2>
                <div class="payment-methods">
                    <div class="payment-option" onclick="selectPaymentMethod('credit'); updateProgress();">
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
                
                <div class="progress-container">
    <div class="progress-label">
        Payment Form Completion: <span id="progressText">0%</span>
    </div>

    <div class="progress-bar">
        <div id="progressFill"></div>
    </div>
</div>

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

            function updateProgress() {
    const activeDetails = document.querySelector('.payment-details.active');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');

    if (!activeDetails) {
        progressFill.style.width = "0%";
        progressText.textContent = "0%";
        return;
    }

    const fields = activeDetails.querySelectorAll('input[data-required="true"]');

    let total = fields.length;
    let filled = 0;

    fields.forEach(field => {
        if (field.value.trim() !== "") {
            filled++;
        }
    });

    let percent = total === 0 ? 0 : Math.round((filled / total) * 100);

    progressFill.style.width = percent + "%";
    progressText.textContent = percent + "%";

    // color logic
    if (percent < 40) {
        progressFill.style.background = "#ff4444"; // red
    } else if (percent < 70) {
        progressFill.style.background = "#facc15"; // yellow
    } else {
        progressFill.style.background = "#2dd4bf"; // green
    }
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
            const booking = JSON.parse(sessionStorage.getItem('tempBooking'));

            if (booking && booking.selectedSeats) {
                const seatList = booking.selectedSeats.map(seat => seat.SeatRowColumn).join(', ');
                document.getElementById('seatsSelectionText').append(seatList + " | Total Price: P " + booking.totalPrice);
                document.querySelector('.selectionFor').append(booking.date);
                document.querySelector('.selectionFor').href = 'seat_selection.php?timeslot_id=' + booking.TimeSlot_ID;
            } else {
                window.location.href = 'home.php';
            }

            const movieData = JSON.parse(sessionStorage.getItem('currentMovie'));

            if (movieData) {
                document.getElementById('movieToWatch').append('Movie: "' + movieData.MovieName + '"');
                document.querySelector('.theatersWith').append(' "' + movieData.MovieName + '"');
                document.querySelector('.theatersWith').href = 'movie.php?movie_id=' + movieData.Movie_ID;
            } else {
                window.location.href = 'home.php';
            }

            // Add input event listeners for real-time validation
            const inputs = document.querySelectorAll('input[data-required="true"]');
            inputs.forEach(input => {
                input.addEventListener('input', function() { updateProgress();
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
                e.preventDefault();

                const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked');
                
                if (!selectedMethod) {
                    alert('Please select a payment method');
                    return;
                }
                
                if (!validateCurrentForm()) {
                    alert('Please fill in all required fields for the selected payment method correctly.');
                    return;
                }

                const token = localStorage.getItem('jwt_token');
                const payload = JSON.parse(atob(token.split('.')[1]));

                const booking = JSON.parse(sessionStorage.getItem('tempBooking'));

                const payloadForReceipt = {
                    "Customer_ID": payload.id,
                    "PaymentMethod": selectedMethod.value,
                    "totalPrice": booking.totalPrice,
                    "selectedSeats": booking.selectedSeats
                };

                fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=receipt', {
                    method: 'POST',
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(payloadForReceipt)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        console.log("Something went horribly wrong.");
                    }
                    if (data.status) {
                        const Receipt_ID = data.Receipt_ID;
                        window.location.href = 'receipt.php?receipt_id=' + Receipt_ID;
                    }
                })
                .catch(error => {
                    console.error(error);
                })
            });
            
            validateCurrentForm();
        });
    </script>
</body>
</html>