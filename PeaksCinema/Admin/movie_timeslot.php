<!DOCTYPE html>
<html>
    <style>
    
    main {
        display: flex;
        background-color: white;
        height: 100vh;
    }

    #movieDetails {
        display: flex;
        height: 100%;
        width: 30%;
    }

    .leftSection {
        gap:30px;
        height:100%;
        border: 2px solid black;
        padding:25px;
    }

    .posterCard img {
        width:220px; 
        border-radius:8px; 
        box-shadow:0 5px 20px rgba(0,0,0,0.5);
    }

    #dateSection {    
        display: flex;
        flex-direction: column;
        width: 70%;
        border: 2px solid black;
    }

    #theaterSelection {
        padding: 25px;
    }

    #everythingAboutDates {
        height: 100%;
        border-top: 2px solid black;
        padding: 25px;
    }

    button#addDateButton {
        border: 3px solid black;
        border-radius: 25px;
        padding: 5px;
        font-size: 80%;
        font-weight: bold;
        color: black;
        transition: border 0.5s, padding 0.5s, color 0.5s;
    }

    button#addDateButton:hover {
        border: 3px solid rgb(18, 141, 172);
        padding: 7px;
        color: black;        
    }

    button#addDateButton:active {
        background-color: rgb(18, 141, 172);
    }

    #addDateMenu {
        visibility: hidden;
    }

    #allTimeslotsContainer, #dayTimeslotsContainer {
        display: none;
    }

    .currentDates {
        display: inline-block;
        border: 3px solid black;
        border-radius: 15px;
        padding: 5px;
        margin-bottom: 5px;
        transition: border 0.3s, color 0.3s, padding 0.3s, font-weight 0.1s;
    }

    .currentDates:hover {
        border: 3px solid red;
        color: red;
        padding: 7px;
        font-weight: bold;
    }

    </style>
    <body onload="getMovieInfo()">
        <?php include("header_admin.php"); ?>
        <main>
            <div id="failed"></div>
            <section id="movieDetails"></section>
            <section id="dateSection">
                <div id="theaterSelection"></div>
                <div id="everythingAboutDates">
                    <div id="allDatesContainer"></div>
                    <div>
                        <span><button type="button" id="addDateButton">Add New Date +</button></span>
                        <div id="addDateMenu">
                            <input type="radio" class="dateTypeSelection" name="dateTypeSelection" value="0" onclick="dateTypeSelection()">Add timeslots for all days</input>
                            <input type="radio" class="dateTypeSelection" name="dateTypeSelection" value="1" onclick="dateTypeSelection()">Add timeslots for specific days</input><br>
                            <div id="allTimeslotsContainer" class="timeslotContainer">
                                <form id="timeslotAllForm">
                                    <p>Start Date: <input type="date" id="startDate"> - End Date: <input type="date" id="endDate"><span style="color: grey;">(optional)</span></p>
                                    <div>Timeslots: </div>
                                    <span id="allTimeslots"><input type="time" class="timeslots" name="timeslotALL"onchange="addTimeslot()"></span><br>
                                    <input type="submit" id="saveDateButton" value="Save"></input>
                                </form>
                                <div id="maxNumberForAll"></div>
                            </div>
                            
                            <div id="dayTimeslotsContainer" class="timeslotContainer">hello</div>

                        </div>
                    </div>
                </div>
            </section>
            
            
        </main>
        <footer></footer>
        <script>
            const failed = document.getElementById('failed');

            const movieDetails = document.getElementById('movieDetails');
            const urlParams = new URLSearchParams(window.location.search);
            function getMovieInfo() {
                var Movie_ID = urlParams.get('id');
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        if (this.responseText.includes('id=failed')) {
                            failed.innerHTML = this.responseText;
                            window.location.href = 'movies.php';
                        } else {
                            movieDetails.innerHTML = this.responseText;
                            getTheaterNames();
                        }                        
                    }                    
                };                
                xmlhttp.open("GET", "queries_admin.php?q=moviedetails&movie_id=" + Movie_ID, true);
                xmlhttp.send();
            }

            function getTheaterNames() {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        theaterSelection.innerHTML = this.responseText;                       
                    }                    
                };                
                xmlhttp.open("GET", "queries_admin.php?q=theaternames", true);
                xmlhttp.send();
            }
            const allDatesContainer = document.getElementById('allDatesContainer');

            const timeButtons = document.querySelectorAll('.timeButton');
            timeButtons.forEach(e => {
                e.addEventListener("click", function() {
                    this.remove();
                })
            })

            const addDateButton = document.getElementById('addDateButton');
            const addDateMenu = document.getElementById('addDateMenu');
            var isDateMenuOpen = false;
            addDateButton.addEventListener("click", function() {
                if (!isDateMenuOpen) {
                    isDateMenuOpen = true;
                    addDateButton.innerText = "Add New Date -";
                    addDateMenu.style.visibility = 'visible';
                } else {
                    isDateMenuOpen = false;
                    addDateButton.innerText = "Add New Date +";
                    addDateMenu.style.visibility = 'hidden';
                }                
            })

            saveDateButton.addEventListener("click", function() {
                var startDate = document.getElementById("startDate");
                if (!startDate.value) {
                    alert("please type a start date");
                } else {
                    addDateMenu.style.visibility = 'hidden';
                    isDateMenuOpen = false;
                    addDateButton.innerText = "Add New Date +";
                }                
            })

            const allTimeslotsContainer = document.getElementById("allTimeslotsContainer");
            const dayTimeslotsContainer = document.getElementById("dayTimeslotsContainer");
            function dateTypeSelection() {
                let dateTypeSelected = document.querySelector('input[name="dateTypeSelection"]:checked');
                if (dateTypeSelected != null) {
                    if (dateTypeSelected.value == 0) {
                        dayTimeslotsContainer.style.display = 'none';
                        allTimeslotsContainer.style.display = 'block';                        
                    } else {
                        allTimeslotsContainer.style.display = 'none';
                        dayTimeslotsContainer.style.display = 'block';
                    }
                }
            }
            const allTimeslots = document.getElementById('allTimeslots');
            const maxNumberForAll = document.getElementById('maxNumberForAll');
            let addedTimes = 0 ;
            function addTimeslot() {
                if (addedTimes < 4) {
                    const timeslot = document.createElement('input');
                    timeslot.type = 'time';
                    timeslot.name = 'timeslotALL';
                    timeslot.addEventListener("change", addTimeslot);
                    allTimeslots.appendChild(timeslot);
                    addedTimes += 1;
                } else {
                    maxNumberForAll.innerHTML = "Maximum amount of timeslots reached.";
                }                
            }

            function getTheaterInfo(theaterId) {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        allDatesContainer.innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "queries_admin.php?q=theaterdatetimes&id=" + theaterId, true);
                xmlhttp.send();
            }


            const form = document.getElementById('timeslotAllForm');
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');
            form.addEventListener("submit", function(e) {
                e.preventDefault();

                var Movie_ID = urlParams.get('id');
                var selectedTheater = document.querySelector('input[name="theaterSelection"]:checked').value;

                const formData = new FormData(form);
                let timeslots = formData.getAll('timeslotALL').filter(t => t !== "");

                let allTimeslots = [];
                let start = new Date(startDate.value);
                if (!endDate.value) {
                    let dateStr = start.toLocaleDateString('en-CA');
                    timeslots.forEach(time => {
                        allTimeslots.push({ date: dateStr, timeslot: time});
                    });
                } else {
                    let end = new Date(endDate.value);
                    for (var d = new Date(startDate.value); d <= end; d.setDate(d.getDate() + 1)) {
                        let dateStr = new Date(d).toLocaleDateString('en-CA');
                        timeslots.forEach(time => {
                            allTimeslots.push({ date: dateStr, timeslot: time});
                        });
                    }
                }
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log(this.responseText);
                    }
                };
                xmlhttp.open("POST", "queries_admin.php?q=datetimesent", true);
                xmlhttp.setRequestHeader("Content-Type", "application/json");
                xmlhttp.send(JSON.stringify({
                    Theater_ID: selectedTheater,
                    Movie_ID: Movie_ID,
                    StartDate: startDate.value,
                    EndDate: endDate.value,
                    timeslots: allTimeslots
                }));
                console.log(allTimeslots);
            })

            function deleteDateTime(id) {                       
                console.log("test");
                DateRange_ID = id;
                xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        document.getElementById(DateRange_ID).remove();
                    }
                };
                xmlhttp.open("POST", "queries_admin.php?q=datedeletion", true);
                xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlhttp.send("id=" + encodeURIComponent(DateRange_ID));
            }
            
        </script>
    </body>
</html>

