<?php
    include("peakscinemas_database.php");
    session_start();
    $profile_link = "personal_info_form.php";

    // Validate inputs
    $Movie_ID    = filter_input(INPUT_GET, 'movie_id', FILTER_VALIDATE_INT);
    $Mall_ID     = filter_input(INPUT_GET, 'mall_id', FILTER_VALIDATE_INT);
    $Date        = filter_input(INPUT_GET, 'date');
    $TimeSlot_ID = filter_input(INPUT_GET, 'timeslot_id', FILTER_VALIDATE_INT);

    if ($Movie_ID === null || $Movie_ID === false ||
        $Mall_ID === null || $Mall_ID === false ||
        $Date === null || $Date === false ||
        $TimeSlot_ID === null || $TimeSlot_ID === false) {
        header("Location: home.php");
        exit;
    }


    // Fetch movie details
    $movie_stmt = $conn->prepare("SELECT * FROM movie WHERE Movie_ID = ?");
    $movie_stmt->bind_param("i", $Movie_ID);
    $movie_stmt->execute();
    $movieDetails = $movie_stmt->get_result()->fetch_assoc();

    // Fetch mall details
    $mall_stmt = $conn->prepare("SELECT * FROM mall WHERE Mall_ID = ?");
    $mall_stmt->bind_param("i", $Mall_ID);
    $mall_stmt->execute();
    $mallDetails = $mall_stmt->get_result()->fetch_assoc();

    // Fetch timeslot + theater details
    $timeslot_stmt = $conn->prepare("
        SELECT t.*, th.*
        FROM timeslot t
        INNER JOIN theater th ON t.Theater_ID = th.Theater_ID
        WHERE t.TimeSlot_ID = ?
    ");
    $timeslot_stmt->bind_param("i", $TimeSlot_ID);
    $timeslot_stmt->execute();
    $timeslotDetails = $timeslot_stmt->get_result()->fetch_assoc();

    if (!$timeslotDetails || !$movieDetails || !$mallDetails) {
        header("Location: home.php");
        exit;
    }

    // Fetch seats with availability and price for this timeslot
    $seats_stmt = $conn->prepare("
        SELECT s.Seat_ID, s.SeatRow, s.SeatColumn, st.SeatPrice, st.SeatAvailability
        FROM seats s
        INNER JOIN seat_timeslot st ON s.Seat_ID = st.Seat_ID
        WHERE st.TimeSlot_ID = ?
        GROUP BY s.Seat_ID, s.SeatRow, s.SeatColumn, st.SeatPrice, st.SeatAvailability
        ORDER BY s.SeatRow, CAST(s.SeatColumn AS UNSIGNED)
    ");
    $seats_stmt->bind_param("i", $TimeSlot_ID);
    $seats_stmt->execute();
    $seatLayout = $seats_stmt->get_result();

    $layoutProper = [];
    if ($seatLayout) {
        while ($seat = $seatLayout->fetch_assoc()) {
            // Normalize row key (avoid duplicates like "A", "a", " A ")
            $rowKey = strtoupper(trim($seat['SeatRow']));
            $layoutProper[$rowKey][] = [
                'Seat_ID'         => $seat['Seat_ID'],
                'SeatPrice'       => $seat['SeatPrice'],
                'SeatAvailability'=> (int)$seat['SeatAvailability'],
                'SeatColumn'      => (int)$seat['SeatColumn']
            ];
        }
    }

    // Sort seats within each row by column number
    foreach ($layoutProper as $row => &$seats) {
        usort($seats, function($a, $b) {
            return $a['SeatColumn'] <=> $b['SeatColumn'];
        });
    }
    unset($seats); // break reference



    // Utility function
    function input_cleanup($data) {
        $data = trim($data);
        $data = stripslashes($data);
        return $data;
    }

    // Handle seat selection
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selectedSeats'])) {
        $_SESSION['selectedSeats'] = array_map('input_cleanup', $_POST['selectedSeats']);
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
                
                transition: 0.3s;
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

            /* movie stuff*/

            #movieDetailsSection {
                background-color: #a3c2b1;
                padding: 20px;
                border: 5px solid black;
                width: 50%;
                margin: 5px auto;
                display: flex;
                align-items: flex-start;
                gap: 20px;
                border-radius: 10px;
                color: #363635;
            }

            .posterCard img {
                width: 100%;
                height: 280px;
                object-fit: cover;
                border-radius: 6px;
                background-color: #fff;
            }

            .movieInfo {
                flex: 1;
                display: flex;
                flex-direction: column;
                min-height: 280px;
            }

            .movieInfo h1 {
                margin-top: 0;
                font-size: 24px;
            }

            .movieInfo .bottomDetails {
                margin-top: auto;
            }

            .movieInfo .bottomDetails p {
                margin: 6px 0;
            }

            /* For The Mall Cards */

            * {
                box-sizing: border-box;
            }

            main {
                display: flex;
                flex-direction: column;
            }

            /* Available seats stuff */

            .glassbox {
                background: rgba(255, 255, 255, 0.3); /* semi-transparent white */
                border: 3px solid white;
                padding: 20px;
                border: 2px solid white;
                width: 50%;
                margin: 5px auto;
                display: flex;
                align-items: flex-start;
                gap: 20px;
                border-radius: 10px;
                color: #363635;
                flex-direction: column;
                justify-content: center;
                box-shadow: 0 0 8px rgba(0, 0, 0, 0.5);
                width: fit-content; /* or a fixed width like 800px */
            }

           #seatsSelectionText {
                margin: 0px auto;
                display: inline-block;
                width: 100%;
                align-items: left;
                border-radius: 10px;
                font-weight: bold;
                font-size: 20px;
                text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
                font-family: 'Segoe UI', Arial, sans-serif;
                font-size: 20px;
                color: white;
            }

            #seatsContainer {
                display: flex;
                width: 100%;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 5px;
                margin: 0 auto;
                padding: 25px;
                font-weight: bold;
                color: black;
            }
            
            #theaterScreen {
                color: #363635;
                border: 2px solid #000000ff;
                background: #ffffff;
                background: linear-gradient(360deg,rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
                width: 75%;
                text-align: center;
                font-family: 'Poppins', sans-serif;
                font-weight: 700;
                box-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
                margin-bottom: 10px;
                border-radius: 50px 50px 20px 20px;
                margin-bottom: 50px;
            }

            #theaterScreen:hover {
                background-color: #ffffffff;
                background: linear-gradient(360deg,rgba(245, 245, 245, 1) 0%, rgba(255, 255, 255, 1) 100%);
                color: #2b2b2b;
                border: 1px solid #4b4b4b;
                box-shadow: 0 0 8px rgba(255,255,255,0.3);
                transition: 0.3s ease;
            }
                
            .emptySeat {                
                width: 25px;
                height: 25px;
                background-color: transparent;
                border: 3px solid transparent;
            }

            .availableSeatCheckbox {
                display: flex;
                position: relative;
                cursor: pointer;
                align-items: center;
                justify-content: center;
            }

            .availableSeatCheckbox input{
                opacity: 0;
                cursor: pointer;
                height: 0;
                width: 0;
            }

            .availableTheaterSeat {
                display: flex;
                border: 1px solid black;
                background: #0a9900;
                background: linear-gradient(360deg, rgba(10, 153, 0, 1) 0%, rgba(1, 125, 16, 1) 100%);
                color: #fff;
                width: 40px;
                height: 40px;
                margin: 4px;
                text-align: center;
                justify-content: center;
                align-items: center;
                border-radius: 20px 20px 0 0;
            }

             .availableTheaterSeat:hover {
                background: #00e842;
                background: linear-gradient(360deg, rgba(0, 232, 66, 1) 0%, rgba(0, 173, 0, 1) 100%);
                color: #2b2b2b;
                border: 1px solid #CCCCCC;
                box-shadow: 0 0 8px rgba(255,255,255,0.3);
                transition: 0.3s;
            }

            .availableSeatCheckbox input:checked ~ .availableTheaterSeat {
                background: #a84c00;
                background: linear-gradient(360deg, rgba(168, 76, 0, 1) 0%, rgba(102, 44, 0, 1) 100%);
            }

            .availableSeatCheckbox input:checked ~ .availableTheaterSeat:hover {
                background: #A84C00;
                background: linear-gradient(360deg, rgba(168, 76, 0, 1) 0%, rgba(219, 66, 0, 1) 100%);
                border: 1px solid #CCCCCC;
            }

            .unavailableTheaterSeat {
                display: flex;
                border: 1px solid black;
                background: #960008;
                background: linear-gradient(360deg, rgba(150, 0, 8, 1) 0%, rgba(255, 0, 0, 1) 100%);
                width: 40px;
                height: 40px;
                text-align: center;
                justify-content: center;
                align-items: center;
                border-radius: 20px 20px 0 0;
                margin: 4px;
            }

            tr {
                text-align: center;
                justify-content: center;
                vertical-align: middle;
            }

            td {
                margin: 3px;
                vertical-align: middle;
            }

            .seatRows {
                color: white;
                font-family: 'Poppins', sans-serif;
                padding: 13px;
                text-shadow: 0 1px 1px rgba(0, 0, 0, 0.6);
            }

          #seatsCalculatorContainer {
                margin-top: 10px;
                padding: 10px;
                border: 2px solid #2b2b2b;
                border-radius: 10px;
                background: #ffffff;
                background: linear-gradient(360deg,rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
                width: 100%;
                max-width: 400px;
                text-align: center;
                margin-left: auto;
                margin-right: auto;
                font-weight: 700;
                font-family: 'Poppins', sans-serif;
                box-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
            }


            #submission {
                display: flex;
                justify-content: flex-end;
                width: 100%;
                padding-right: 25px;
            }

            #submission button {
                box-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
                background: #ffffff;
                background: linear-gradient(360deg,rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
                padding: 10px;
                border: 2px solid #2b2b2b;
                border-radius: 10px;
                font-weight: bold;
                font-size: 16px;
                cursor: pointer;
            }

             #submission button:hover {
                background-color: #ffffffff;
                background: linear-gradient(360deg,rgba(245, 245, 245, 1) 0%, rgba(255, 255, 255, 1) 100%);
                color: #2b2b2b;
                border: 1px solid #4b4b4b;
                box-shadow: 0 0 8px rgba(255,255,255,0.3);
                transition: 0.3s ease;
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
                    <a href="movie.php?movie_id=<?= htmlspecialchars($movieDetails['Movie_ID']) ?> ">Malls with "<?= htmlspecialchars($movieDetails['MovieName']) ?>"</a><p>&nbsp/&nbsp</p>
                    <a href="mall.php?movie_id=<?= htmlspecialchars($movieDetails['Movie_ID']) ?>&mall_id=<?= htmlspecialchars($mallDetails['Mall_ID']) ?>&date=<?= htmlspecialchars($Date) ?>">Available theaters in "<?= htmlspecialchars($mallDetails['MallName']) ?>"</a><p>&nbsp/&nbsp</p>
                    <a id="active">Seats Selection in <?= htmlspecialchars($timeslotDetails['TheaterName']) ?></a> 
                </nav>
            </div>
            
            <!-- Updated form to point to payment.php with hidden fields -->
             
            <form id="seatsSelectionSection" action="payment.php" method="POST">
                <div class="glassbox">
                <!-- Hidden fields to pass data to payment.php -->
                <input type="hidden" name="movie_id" value="<?= htmlspecialchars($Movie_ID) ?>">
                <input type="hidden" name="mall_id" value="<?= htmlspecialchars($Mall_ID) ?>">
                <input type="hidden" name="date" value="<?= htmlspecialchars($Date) ?>">
                <input type="hidden" name="timeslot_id" value="<?= htmlspecialchars($TimeSlot_ID) ?>">
                
                <div id="seatsSelectionText">Select seats: </div>
                <div id="seatsContainer">
                    <div id="theaterScreen">SCREEN</div>
                    <table class="seatsLayoutProper">
                        <?php foreach ($layoutProper as $row => $columns): ?> 
                            <tr>
                                <!-- Row label at the start -->
                                <th class="seatRows"><?= htmlspecialchars($row) ?></th>

                                <?php foreach ($columns as $seat): ?>
                                    <?php if ($seat['SeatColumn'] == 0) continue; ?> <!-- Skip 0s -->

                                    <?php if ($seat['SeatAvailability'] == 0): ?>
                                        <td class="unavailableTheaterSeat"><?= htmlspecialchars($seat['SeatColumn']) ?></td>
                                    <?php else: ?>
                                        <td>
                                            <label class="availableSeatCheckbox">
                                                <input type="checkbox" 
                                                    data-price="<?= htmlspecialchars($seat['SeatPrice']) ?>" 
                                                    name="selectedSeats[]" 
                                                    value="<?= htmlspecialchars($seat['Seat_ID']) ?>">
                                                <div class="availableTheaterSeat"><?= htmlspecialchars($seat['SeatColumn']) ?></div>
                                            </label>
                                        </td>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <!-- Row label at the end -->
                                <th class="seatRows"><?= htmlspecialchars($row) ?></th>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                </div>
                <div id="seatsCalculatorContainer">
                    <div>
                        <p>Total Seats Selected: <span id="seatTotal"></span></p>
                        <p>Total Price: ₱ <span id="seatPriceTotal"></span></p>
                    </div>
                </div>
                <input type="hidden" id="priceTotal" name="priceTotal" value="0">
                <div id="submission">
                    <button type="submit" name="seatSelectionSubmission" value="seatSelectionSubmission">NEXT</button>
                </div>
                </div>
            </form>
             </main>

        <script>
            const seatTotal = document.getElementById("seatTotal");
            seatTotal.innerText = 0;
            const seatPriceTotal = document.getElementById("seatPriceTotal");
            seatPriceTotal.innerText = 0;
            const priceTotalInput = document.getElementById("priceTotal");

            document.querySelectorAll('.availableSeatCheckbox').forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    const selectedCount = document.querySelectorAll('input[type="checkbox"]:checked').length;
                    seatTotal.innerText = selectedCount;

                    let priceTotal = 0;
                    document.querySelectorAll('input[type="checkbox"]:checked').forEach(selectedSeats => {
                        priceTotal += parseFloat(selectedSeats.getAttribute('data-price'));
                    });
                    
                    seatPriceTotal.innerText = priceTotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    
                    // Update the hidden input field with the actual price total
                    priceTotalInput.value = priceTotal;
                });
            });

            // Add form validation to prevent submission if no seats are selected
            document.getElementById('seatsSelectionSection').addEventListener('submit', function(event) {
                const selectedSeats = document.querySelectorAll('input[name="selectedSeats[]"]:checked');
                if (selectedSeats.length === 0) {
                    alert('Please select at least one seat before proceeding.');
                    event.preventDefault();
                }
            });
        </script>
    </body>
</html>