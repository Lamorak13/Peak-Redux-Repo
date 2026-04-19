<!DOCTYPE html>
<html>
<head>
    <link rel="manifest" href="manifest.json">
    <script src="customer_gate.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
        body{background:linear-gradient(to bottom,#071018,#0d1b2a);color:white;min-height:100vh;}
        
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
        .logo img{height:45px;cursor:pointer;}
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

        main{margin-top:130px;padding:0 60px;}
        .topLink{display:flex;gap:10px;margin-bottom:30px;flex-wrap:wrap;}
        .topLink a{background:rgba(255,255,255,0.08);padding:8px 15px;border-radius:20px;color:white;text-decoration:none;}
        .topLink a.active,.topLink a:hover{background:#2dd4bf;color:#071018;font-weight: bold}

        .glassbox,.glassbox-2{background:rgba(255,255,255,0.06);backdrop-filter:blur(10px);border-radius:15px;padding:25px;margin-bottom:30px;box-shadow:0 10px 30px rgba(0,0,0,0.4);}

        .posterCard{position:relative;width:100%;max-width:700px;height:320px;overflow:hidden;border-radius:15px;cursor:pointer;}
        .posterCard img{width:100%;height:100%;object-fit:cover;border-radius:15px;}
        .play-overlay{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:80px;height:80px;background:rgba(0,0,0,0.75);border:5px solid white;border-radius:50%;display:flex;align-items:center;justify-content:center;}
        .play-icon{width:0;height:0;border-top:15px solid transparent;border-bottom:15px solid transparent;border-left:26px solid white;margin-left:8px;}

        .theaterCard{
        background:rgba(255,255,255,0.08);
        border-radius:12px;
        padding:15px;
        margin-bottom:15px;
        }

        .theaterName{
        font-weight:bold;
        margin-bottom:10px;
        }

        .timeslotContainer{
        display:flex;
        flex-wrap:wrap;
        gap:10px;
        }

        .timeslots{
        padding:8px 15px;
        border-radius:20px;
        background:#2dd4bf;
        color:#071018;
        font-weight:bold;
        cursor:pointer;
        transition:0.3s;
        }

        .timeslots:hover{
        transform:scale(1.05);
        background:#14b8a6;
        }

        #screeningDate {
            border-radius: 15px;
            border: 2px solid black;
            font-weight: bold;
            padding: 3px;
        }

        .daterangepicker td.available {
            color: black;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s;
        }

        .daterangepicker td.available:hover {
            background-color: black;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">
            <img src="peakscinemastransparent.png" alt="PeaksCinemas Logo" onclick="window.location.href='home.php'">
        </div>
        <button class="profile-btn" onclick="window.location.href='profile_edit.php'">👤</button>
    </header>

    <main>
        <div class="topLink">
            <a href="home.php">Home</a>
            <a id="theatersWithMovie" class="active">1. Select Theater For </a>
        </div>

        <section class="glassbox">
            <div id="trailerPlayer" class="posterCard">
                <img id="posterTrailer" src="">
                <div class="play-overlay"><div class="play-icon"></div></div>
            </div>

            <div class="movieInfo">
                <h1 id="MovieName"></h1>
                <p id="MovieDescription"></p>
                <p id="Genre">Genre: </p>
                <p id="Rating">Rating: </p>
                <p id="Runtime">Runtime: </p>
            </div>
        </section>

        <div class="glassbox-2">
            <section id="availableTheatersSection">
                <label for="screeningDate"><strong>Date of Screening:</strong></label>
                <input type="text" id="screeningDate" name="screeningDate"><br><br>
                <p id="screeningsMsg" style="color:#ff6b6b;"></p>
                <div id="availableMallsText" style="font-weight:bold;margin:15px 0 10px;">Theaters with this movie:</div>
                <div id="theatersContainer"></div>
            </section>
        </div>
    </main>

    <!-- Trailer Modal -->
    <div id="trailerModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.95);z-index:3000;align-items:center;justify-content:center;">
        <div style="width:90%;max-width:900px;position:relative;">
            <span onclick="closeTrailer()" style="position:absolute;top:-15px;right:-15px;font-size:40px;color:white;cursor:pointer;background:#000;width:45px;height:45px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:3px solid white;">×</span>
            <iframe id="trailerFrame" width="100%" height="500" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>

    <script>
        window.addEventListener("scroll",()=>{
            document.querySelector("header").classList.toggle("scrolled",window.scrollY>50);
        });

        function playTrailer(url) {
            const modal = document.getElementById('trailerModal');
            const iframe = document.getElementById('trailerFrame');

            if (!url) { alert("No trailer available."); return; }

            if (url.includes("youtube.com/watch?v=")) {
                url = "https://www.youtube.com/embed/" + url.split("v=")[1].split("&")[0] + "?autoplay=1";
            } else if (url.includes("youtu.be/")) {
                url = "https://www.youtube.com/embed/" + url.split("youtu.be/")[1].split("?")[0] + "?autoplay=1";
            }

            iframe.src = url;
            modal.style.display = "flex";
        }

        function closeTrailer() {
            document.getElementById('trailerModal').style.display = "none";
            document.getElementById('trailerFrame').src = "";
        }

        // Load Theaters
        const urlParams = new URLSearchParams(window.location.search);
        const Movie_ID = urlParams.get('movie_id');

        function loadTheaters(date) {
            const theatersContainer = document.getElementById("theatersContainer");
            theatersContainer.innerHTML = "";

            const msg = document.getElementById("screeningsMsg");
            msg.textContent = "Loading...";

            fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie/31/theaters&date=${date}`, {
                method: "GET"
            })
            .then(response => {
                if (!response.ok) {
                    console.log(response.error);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    msg.textContent = data.error;
                }

                msg.textContent = "";
                const theaters = data.data;

                if (theaters.length == 0) {
                    msg.textContent = "Sorry, there are no more screenings left today.";
                    availableMallsText.innerHTML = "";
                    return;
                }

                availableMallsText.innerHTML = "Theaters with this movie:";
                
                theaters.forEach(theater => {
                    const theaterCard = document.createElement('div');
                    theaterCard.classList.add('theaterCard');

                    const theaterName = document.createElement('div');
                    theaterName.classList.add('theaterName');
                    theaterName.textContent = theater.TheaterName;
                    theaterCard.append(theaterName);

                    const timeslotContainer = document.createElement('div');
                    timeslotContainer.classList.add('timeslotContainer');
                    theaterCard.append(timeslotContainer);

                    theater.Timeslots.forEach(timeslot => {
                        const timeslots = document.createElement('div');
                        timeslots.classList.add('timeslots');
                        timeslots.textContent = timeslot.ScreeningType + " - " + timeslot.StartTime;

                        timeslots.addEventListener("click", function() {
                            window.location.href = 'seat_selection.php?timeslot_id=' + timeslot.TimeSlot_ID;
                        })
                        timeslotContainer.append(timeslots);
                    })

                    document.getElementById('theatersContainer').append(theaterCard);
                })
            })
        }

        // Initialize
        window.onload = function() {
            // Gets Movie Details
            fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie/${Movie_ID}`, {
                method: 'GET'
            })
            .then(response => {
                if (!response.ok) {
                    console.log(response.error);
                }
                return response.json();
            })
            .then(data => {
                const movieData = data.data[0];
                console.log(movieData);

                document.getElementById('theatersWithMovie').append('"' + movieData.MovieName + '"');
                sessionStorage.setItem('currentMovie', JSON.stringify(movieData));

                document.getElementById('posterTrailer').src = "/" + movieData.MoviePoster;
                document.getElementById('MovieName').innerText = movieData.MovieName;
                document.getElementById('MovieDescription').innerText = movieData.MovieDescription;
                document.getElementById('Genre').append(movieData.Genre);
                document.getElementById('Rating').append(movieData.Rating);
                document.getElementById('Runtime').append(movieData.Runtime + " Minutes");

                document.getElementById('trailerPlayer').addEventListener("click", () => playTrailer(movieData.TrailerURL));
            })

            //Gets Available Dates and makes them the only ones available for the date input . . .
            fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie/${Movie_ID}/timeslots`, {
                method: 'GET'
            })
            .then(response => {
                if (!response.ok) {
                    console.log(response.error);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    window.location.href = 'home.php';
                    return;
                }
                const timeslots = data.data;

                const curYear = new Date().getFullYear();
                const availableDates = timeslots.map(slot => slot.Date);
                console.log(availableDates);

                const sortedDates = availableDates.slice().sort();
                const firstDate = sortedDates[0];
                const lastDate = sortedDates[sortedDates.length - 1];

                const dateInput = $('input[name="screeningDate"]');
                dateInput.val(moment(firstDate).format('MM/DD/YYYY'));
                
                dateInput.daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    minYear: curYear,
                    minDate: firstDate,
                    maxDate: lastDate,
                    autoApply: true,
                    isInvalidDate: function(date) {
                        const dateString = date.format('YYYY-MM-DD');
                        return !availableDates.includes(dateString);
                    }
                }, function(selected) {
                    const selectedDate = selected.format('YYYY-MM-DD');
                    loadTheaters(selectedDate);
                })
                loadTheaters(firstDate);
            })
        };
    </script>
</body>
</html>