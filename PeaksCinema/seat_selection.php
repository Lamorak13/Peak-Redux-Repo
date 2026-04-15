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
}

.logo img{
height:45px;
cursor:pointer;
}

.profile-btn{
background:linear-gradient(135deg,#2dd4bf,#14b8a6);
border:none;
padding:8px 18px;
border-radius:30px;
font-weight:bold;
cursor:pointer;
color:#071018;
transition:0.3s;
}

.profile-btn:hover{
transform:scale(1.05);
}

main{
margin-top:130px;
padding:0 60px;
}

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

.topLink a#active,
.topLink a:hover{
background:#2dd4bf;
color:#071018;
}

.glassbox{
background:rgba(255,255,255,0.06);
backdrop-filter:blur(10px);
border-radius:15px;
padding:30px;
box-shadow:0 10px 30px rgba(0,0,0,0.4);
}

#seatsSelectionText{
font-size:1.4rem;
font-weight:bold;
margin-bottom:15px;
}

#seatsContainer{
display:flex;
flex-direction:column;
align-items:center;
}

.seatsLayoutProper{
border-spacing:6px;
}

.seatRows{
opacity:0.7;
font-weight:bold;
}

.emptySeat{
width:35px;
height:35px;
}

.availableSeatCheckbox{
cursor:pointer;
}

.availableSeatCheckbox input{
display:none;
}

.availableTheaterSeat{
width:40px;
height:40px;
display:flex;
align-items:center;
justify-content:center;
border-radius:10px 10px 4px 4px;
background:#2dd4bf;
color:#071018;
font-weight:bold;
transition:0.25s;
}

.availableTheaterSeat:hover{
transform:scale(1.1);
background:#14b8a6;
}

.availableSeatCheckbox input:checked + .availableTheaterSeat{
background:#f59e0b;
color:white;
box-shadow:0 0 10px #f59e0b;
}

.unavailableTheaterSeat{
width:40px;
height:40px;
display:flex;
align-items:center;
justify-content:center;
border-radius:10px;
background:#374151;
opacity:0.5;
}

#seatsCalculatorContainer{
margin-top:25px;
padding:20px;
border-radius:12px;
background:rgba(255,255,255,0.08);
text-align:center;
max-width:300px;
margin-left:auto;
margin-right:auto;
}

#seatsCalculatorContainer p{
margin:6px 0;
font-weight:bold;
}

#submission{
margin-top:20px;
display:flex;
justify-content:flex-end;
}

#submission button{
background:linear-gradient(135deg,#2dd4bf,#14b8a6);
border:none;
padding:12px 25px;
border-radius:30px;
font-weight:bold;
cursor:pointer;
color:#071018;
transition:0.3s;
}

#submission button:hover{
transform:scale(1.05);
}

.glassbox{
animation:fadeIn 0.5s ease;
}

@keyframes fadeIn{
from{opacity:0;transform:translateY(10px);}
to{opacity:1;transform:translateY(0);}
}
.seat3D{
position:relative;
width:45px;
height:45px;
perspective:200px;
}

.seat3D .seat{
width:100%;
height:100%;
background:#2dd4bf;
border-radius:10px 10px 6px 6px;
transform:rotateX(15deg);
box-shadow:
0 6px 0 #0f766e,
0 10px 15px rgba(0,0,0,0.5);
transition:0.3s;
}


.seat3D:hover .seat{
transform:rotateX(15deg) scale(1.1);
background:#14b8a6;
}

.availableSeatCheckbox input:checked + .seat3D .seat{
background:#f59e0b;
box-shadow:
0 6px 0 #b45309,
0 0 15px #f59e0b;
}

.person{
position:absolute;
bottom:5px;
left:50%;
transform:translateX(-50%) scale(0);
font-size:20px;
transition:0.3s;
}

.availableSeatCheckbox input:checked + .seat3D .person{
transform:translateX(-50%) scale(1);
animation:sitDown 0.3s ease;
}

.person.walking{
animation:walkAway 0.4s forwards;
}

.seatNumber{
position:absolute;
top:50%;
left:50%;
transform:translate(-50%,-50%);
font-size:12px;
font-weight:bold;
color:#071018;
text-shadow:0 1px 2px rgba(255,255,255,0.4);
transition:0.2s;
pointer-events:none;
}

.person{
position:absolute;
bottom:5px;
left:50%;
transform:translateX(-50%) scale(0);
font-size:18px;
transition:0.3s;
}

.availableSeatCheckbox input:checked + .seat3D .seatNumber{
opacity:0;
transform:translate(-50%,-50%) scale(0.5);
}

.availableSeatCheckbox input:checked + .seat3D .person{
transform:translateX(-50%) scale(1);
animation:sitDown 0.3s ease;
}

.seatNumber,
.person{
transition:0.25s ease;
}

@keyframes sitDown{
0%{transform:translateX(-50%) translateY(-20px) scale(0.5);}
100%{transform:translateX(-50%) translateY(0) scale(1);}
}

@keyframes walkAway{
0%{transform:translateX(-50%) scale(1);}
100%{transform:translateX(60px) scale(0);opacity:0;}
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
            
            <div style="display:flex; gap:15px; justify-content:center; margin-bottom:20px;">
  <div><span style="background:#2dd4bf; padding:5px 10px; border-radius:5px;"></span> Available</div>
  <div><span style="background:#f59e0b; padding:5px 10px; border-radius:5px;"></span> Selected</div>
  <div><span style="background:#374151; padding:5px 10px; border-radius:5px;"></span> Unavailable</div>
</div>
             
            <form id="seatsSelectionSection" action="payment.php" method="POST">
                <div class="glassbox">
                <input type="hidden" name="movie_id" value="<?= htmlspecialchars($Movie_ID) ?>">
                <input type="hidden" name="mall_id" value="<?= htmlspecialchars($Mall_ID) ?>">
                <input type="hidden" name="date" value="<?= htmlspecialchars($Date) ?>">
                <input type="hidden" name="timeslot_id" value="<?= htmlspecialchars($TimeSlot_ID) ?>">
                
                <div id="seatsSelectionText">Seat Selection, Please select: </div>
                <div id="seatsContainer">
                    <table class="seatsLayoutProper">
                        <?php foreach ($layoutProper as $row => $columns): ?> 
                            <tr>
                                <th class="seatRows"><?= htmlspecialchars($row) ?></th>
                                <?php foreach ($columns as $seat): ?>
                                    <td>
                                    <?php if ($seat['SeatType'] === 'Empty' || $seat['SeatColumn'] == 0): ?>
                                        <div class = "emptySeat"></div>
                                    <?php elseif ($seat['SeatAvailability'] == 0 ): ?>
                                        <div class="unavailableTheaterSeat"></div></td>
                                    <?php else: ?>
                                        <div>
                                            <label class="availableSeatCheckbox">
                                                <input type="checkbox" data-type ="<?= htmlspecialchars($seat['SeatType']) ?>" data-price="<?= htmlspecialchars($seat['SeatPrice']) ?>" name= "selectedSeats[]" value="<?= htmlspecialchars($seat['Seat_ID']) ?>">
                                                <div class="seat3D">
                                                <div class="seat"></div>

                                                <div class="seatNumber">
                                                    <?= htmlspecialchars($seat['SeatColumn']) ?>
                                                </div>

                                                <div class="person">🧍</div>
                                            </div>
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
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
                <input type="hidden" id="priceTotal" name="totalPrice" value="0">
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
                    
                    priceTotalInput.value = priceTotal;
                });
            });

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