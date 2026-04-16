<!DOCTYPE html>
<html>
    <head>
        <link rel="manifest" href="manifest.json">
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

.topLink a#active {
    font-weight: bold;
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
overflow-x: auto;
}

.seatsLayoutProper{
border-spacing:6px;
margin: 0 auto;
}

.availableTheaterSeat, .unavailableTheaterSeat, .seat3D {
    width: 7vw;
    height: 7vw;
    max-width: 45px;
    max-height: 45px;
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
                    <a href="home.php">Home</a>
                    <a class="theatersWith">1. Select Theater For </a>
                    <a class="selectionFor" id="active">2. Select Seats For </a> 
                </nav>
            </div>
            
            <div style="display:flex; gap:15px; justify-content:center; margin-bottom:20px;">
  <div><span style="background:#2dd4bf; padding:5px 10px; border-radius:5px;"></span> Available</div>
  <div><span style="background:#f59e0b; padding:5px 10px; border-radius:5px;"></span> Selected</div>
  <div><span style="background:#374151; padding:5px 10px; border-radius:5px;"></span> Unavailable</div>
</div>
             
            <form id="seatsSelectionSection">
                <div class="glassbox">
                <input type="hidden" name="movie_id" value="">
                <input type="hidden" name="mall_id" value="">
                <input type="hidden" name="date" value="">
                <input type="hidden" name="timeslot_id" value="">
                
                <div id="seatsSelectionText">Seat Selection, Please select: </div>
                <div id="seatsContainer">
                    <table class="seatsLayoutProper"></table>
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

            function seatCalc() {
                const selectedCount = document.querySelectorAll('input[type="checkbox"]:checked').length;
                seatTotal.innerText = selectedCount;

                let priceTotal = 0;
                document.querySelectorAll('input[type="checkbox"]:checked').forEach(selectedSeats => {
                    priceTotal += parseFloat(selectedSeats.getAttribute('data-price'));
                });
                
                seatPriceTotal.innerText = priceTotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                
                priceTotalInput.value = priceTotal;
            }

            const urlParams = new URLSearchParams(window.location.search);
            const TimeSlot_ID = urlParams.get('timeslot_id');                

            document.getElementById('seatsSelectionSection').addEventListener('submit', function(event) {
                event.preventDefault();

                const selectedSeats = document.querySelectorAll('input[name="selectedSeats[]"]:checked');
                if (selectedSeats.length === 0) {
                    alert('Please select at least one seat before proceeding.');
                    return;
                }

                const bookingInfo = {
                    seatCount: selectedSeats.length,
                    totalPrice: document.getElementById('priceTotal').value,
                    selectedSeats: Array.from(selectedSeats).map(s => {
                        return {
                            SeatRowColumn: s.getAttribute('data-seatRowCol'), 
                            SeatTimeSlot_ID: s.getAttribute('data-id'),
                            SeatPrice: s.getAttribute('data-price')
                        };                        
                    }),
                    date: seatDate,
                    TimeSlot_ID: TimeSlot_ID
                };

                sessionStorage.setItem('tempBooking', JSON.stringify(bookingInfo));
                window.location.href = 'payment.php';
            });            

            let seatDate = "";

            window.onload = function() {
                const movieData = JSON.parse(sessionStorage.getItem('currentMovie'));

                document.querySelector('.theatersWith').append('"' + movieData.MovieName + '"');
                document.querySelector('.theatersWith').href = 'movie.php?movie_id=' + movieData.Movie_ID;
                fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=timeslot/${TimeSlot_ID}`, {
                    method: "GET"
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    seats = data.data;
                    console.log(seats);

                    seatDate = data.Date;
                    document.querySelector('.selectionFor').append(data.Date);

                    for (const row in seats) {
                        const tr = document.createElement('tr');
                        
                        const seatRows = document.createElement('th');
                        seatRows.classList.add('seatRows');
                        seatRows.textContent = row;
                        tr.append(seatRows);

                        for (const col of seats[row]) {
                            const td = document.createElement('td');

                            if (col.SeatColumn == 0) {
                                const emptySeat = document.createElement('div');
                                emptySeat.classList.add('emptySeat');
                                td.append(emptySeat);
                            } else if (col.SeatAvailability == 0){
                                const unavailableTheaterSeat = document.createElement('div');
                                unavailableTheaterSeat.classList.add('unavailableTheaterSeat');
                                td.append(unavailableTheaterSeat);
                            } else {                                
                                const availableSeatCheckbox = document.createElement('label');
                                availableSeatCheckbox.classList.add('availableSeatCheckbox');

                                const inputforseat = document.createElement('input');
                                inputforseat.type = 'checkbox';
                                inputforseat.setAttribute('data-id', col.SeatTimeSlot_ID);
                                inputforseat.setAttribute('data-price', col.SeatPrice);
                                inputforseat.setAttribute('data-seatRowCol', `${row}${col.SeatColumn}`);
                                inputforseat.addEventListener("change", seatCalc);
                                inputforseat.name = "selectedSeats[]";
                                availableSeatCheckbox.append(inputforseat);

                                const seat3D = document.createElement('div');
                                seat3D.classList.add('seat3D');

                                const seat = document.createElement('div');
                                seat.classList.add('seat');
                                seat3D.append(seat);

                                const seatNumber = document.createElement('div');
                                seatNumber.classList.add('seatNumber');
                                seatNumber.textContent = col.SeatColumn;
                                seat3D.append(seatNumber);

                                const person = document.createElement('div');
                                person.classList.add('person');
                                person.textContent = "🧍";
                                seat3D.append(person);

                                availableSeatCheckbox.append(seat3D);
                                td.append(availableSeatCheckbox);
                            }
                            tr.append(td);
                        }

                        const rowLabelEnd = document.createElement('th');
                        rowLabelEnd.classList.add('seatRows');
                        rowLabelEnd.textContent = row;
                        tr.append(rowLabelEnd);

                        document.querySelector('.seatsLayoutProper').append(tr);
                    }
                })
                .catch(error => {
                    console.error(error);
                });
            } 
            
        </script>
    </body>
</html>