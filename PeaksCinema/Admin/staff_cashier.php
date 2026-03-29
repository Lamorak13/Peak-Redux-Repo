<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
    </head>
    <body>
        <main>
            <div id="cashierFundamentalsContainer">
                <form id="cashierFundamentals">
                    <div id="currentDateTime"></div>
                    <div id="cashierFlow">
                        <select id="selectMovies" required></select>
                        <select id="selectTheaters" required><option value="">Please select a theater.</option></select>
                        <select id="selectTimeslots" required><option value="">Please select a timeslot.</option></select>
                    </div>
                    <div id="seatPlanContainer"></div>
                    <div id="seatsCalculator"></div>
                    <button type="submit" id="submitButton" class="generalAdminButton">Submit</button>
                </form>
            </div>
            
        </main>
        <script>
            const cashierFlow = document.getElementById('cashierFlow');
            const selectMovies = document.getElementById('selectMovies');
            const selectTheaters = document.getElementById('selectTheaters');
            const selectTimeslots = document.getElementById('selectTimeslots');
            const currentDateTime = document.getElementById('currentDateTime');

            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); 
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;

            var todayFormatted = new Date();
            var formattedDate = todayFormatted.toLocaleDateString(undefined, {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'});
            currentDateTime.textContent = "Good day! Today is currently " + formattedDate;

            // 1. Get Movies!
            document.addEventListener("DOMContentLoaded", function() {
                fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie', {
                    method: "GET"
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    movies = data.data;
                    selectMovies.innerHTML = "";

                    if (Array.isArray(movies)) {
                        const option = document.createElement("option");
                        option.textContent = "Please select a movie.";
                        option.value = "";
                        selectMovies.appendChild(option);
                            movies.forEach(movie => {
                            const option = document.createElement("option");
                            option.value = movie.Movie_ID;
                            option.textContent = movie.MovieName;
                            selectMovies.appendChild(option);
                        })
                    } else {
                        console.warn(data.data);
                        selectTheaters.innerHTML = "";
                        const option = document.createElement("option");
                        option.value = "";
                        option.textContent = data.data;
                        selectTheaters.appendChild(option);
                        selectTheaters.selectedIndex = 0;
                    }
                })
                .catch(error => {
                    console.error(error);
                });
            })

            // 2. Get Theaters!!

            selectMovies.addEventListener("change", function() {
                const Movie_ID = this.value;

                fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie/${Movie_ID}/theaters&date=${today}`, {
                    method: "GET"
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    theaters = data.data;
                    selectTheaters.innerHTML = "";

                    if (Array.isArray(theaters)) {
                        const option = document.createElement("option");
                        option.textContent = "Please select a theater.";
                        option.value = "";
                        selectTheaters.appendChild(option);
                        theaters.forEach(theater => {
                            const option = document.createElement("option");
                            option.value = theater.Theater_ID;
                            option.textContent = theater.TheaterName;
                            selectTheaters.appendChild(option);
                        })
                    } else {
                        console.warn(data.data);
                        selectTheaters.innerHTML = "";
                        const option = document.createElement("option");
                        option.value = "";
                        option.textContent = data.data;
                        selectTheaters.appendChild(option);
                        selectTheaters.selectedIndex = 0;
                    }
                    
                })
                .catch(error => {
                    console.error(error);
                });
            })

            // 3. Get Timeslots!!!

            selectTheaters.addEventListener("change", function() {
                const Movie_ID = selectMovies.value;
                const Theater_ID = this.value;

                fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie/${Movie_ID}/theater/${Theater_ID}&date=${today}`, {
                    method: "GET"
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    timeslots = data.data;
                    selectTimeslots.innerHTML = "";

                    if (Array.isArray(timeslots)) {
                        const option = document.createElement("option");
                        option.textContent = "Please select a timeslot.";
                        option.value = "";
                        selectTimeslots.appendChild(option);
                        timeslots.forEach(timeslot => {
                            const option = document.createElement("option");
                            option.value = timeslot.TimeSlot_ID;
                            option.dataset.id = timeslot.TimeSlot_ID;
                            option.textContent = timeslot.StartTime;
                            selectTimeslots.appendChild(option);
                        })
                    } else {
                        console.warn(data.data);
                        selectTimeslots.innerHTML = "";
                        const option = document.createElement("option");
                        option.value = "";
                        option.textContent = data.data;
                        selectTimeslots.appendChild(option);
                        selectTimeslots.selectedIndex = 0;
                    }                    
                })
                .catch(error => {
                    console.error(error);
                });
            })

            // 4. Get Seats!!!!

            selectTimeslots.addEventListener("change", function() {
                const Movie_ID = selectMovies.value;
                const Theater_ID = selectTheaters.value;
                const TimeSlot_ID = this.value;

                fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie/${Movie_ID}/theater/${Theater_ID}/timeslot/${TimeSlot_ID}/seats`, {
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

                    for (const row in seats) {
                        const rowDiv = document.createElement('div');
                        rowDiv.classList.add('seatRow');
                        
                        const rowLabelStart = document.createElement('div');
                        rowLabelStart.classList.add('rowLabel');
                        rowLabelStart.textContent = row;
                        rowDiv.append(rowLabelStart);

                        for (const col of seats[row]) {
                            const label = document.createElement('label');
                            label.classList.add('seatLabel');

                            if (col.SeatColumn == 0) {
                                const seatBox = document.createElement('div');
                                seatBox.textContent = col.SeatColumn;
                                seatBox.classList.add('aisle');
                                rowDiv.appendChild(seatBox);
                            } else if (col.SeatAvailability == 0){
                                const seatBox = document.createElement('div');
                                seatBox.textContent = col.SeatColumn;
                                seatBox.classList.add('unavailableSeat');
                                rowDiv.appendChild(seatBox);
                            } else {
                                const seatBox = document.createElement('input');
                                seatBox.type = 'checkbox';
                                seatBox.value = col.SeatTimeSlot_ID;
                                seatBox.classList.add('properSeat');
                                seatBox.dataset.price = col.SeatPrice;
                                seatBox.addEventListener("change", seatCalculator);
                                label.appendChild(seatBox);
                                label.appendChild(document.createTextNode(col.SeatColumn));
                                rowDiv.appendChild(label);
                            }
                        }

                        const rowLabelEnd = document.createElement('div');
                        rowLabelEnd.classList.add('rowLabel');
                        rowLabelEnd.textContent = row;
                        rowDiv.append(rowLabelEnd);

                        document.getElementById('seatPlanContainer').appendChild(rowDiv);
                    }                    
                    
                    seatPlanContainer.style.opacity = 1;
                    document.getElementById('seatsCalculator').style.opacity = 1;
                    document.getElementById('submitButton').style.opacity = 1;

                    seatCalculator();
                })
                .catch(error => {
                    console.error(error);
                });
            })

            // 5. "Submit?", "Um, because you're of type "Submit" and you submit??" "Oh, right." 

            const cashierSubmitForm = document.getElementById("cashierFundamentals")
            cashierSubmitForm.addEventListener("submit", function(e) {
                const seats = document.querySelectorAll(".properSeat");
                const isChecked = Array.from(seats).some(seat => seat.checked);

                if (!isChecked) {
                    e.preventDefault();
                    alert("Please select at least 1 seat.");
                } else {
                    const SeatTimeSlot_ID = document.getElementById('selectTimeslots').value;
                    const selectedSeats = Array.from(document.querySelectorAll(".properSeat:checked"))
                                          .map(seat => seat.value);
                    
                    const payload = { // yall ever play TF2. fun game. haha. really fun. haha. i love payload. haha.
                        SeatTimeSlot_ID,
                        seats: selectedSeats
                    }

                    fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=seat_timeslot', {
                        method: "PUT",
                        headers: {"Content-Type": "application/json"},
                        body: JSON.stringify(payload)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        cashierSubmitForm.reset();
                    })
                    .catch(error => {
                        console.error(error);
                        alert("There's been an error submitting the booking. Please try again.");
                    })
                }
            })

            // 2.1 the world has broken, and there are only calculators left roaming the world. goodness gracious whose fault is this??

            function seatCalculator() {
                document.getElementById('seatsCalculator').style.display = 'flex';
                document.getElementById('submitButton').style.display = 'flex';
                const selectedSeats = document.querySelectorAll('input[type="checkbox"]:checked')
                const selectedCount = selectedSeats.length;

                let priceTotal = 0;
                document.querySelectorAll('input[type="checkbox"]:checked').forEach(selectedSeats => {
                    priceTotal += parseFloat(selectedSeats.dataset.price);
                });
                
                
                document.getElementById("seatsCalculator").textContent = 'Selected Seats: ' + selectedCount + ' | Total Price: P ' + priceTotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

        </script>
    </body>
</html>