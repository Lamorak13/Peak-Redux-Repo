<?php 
    include '../peakscinemas_database.php';

    $Movie_ID = "";
    $movieData = "";

    if (isset($_GET['id'])) {
        $Movie_ID = $_GET['id'];
        $Movie_ID = intval($Movie_ID);
    } else {
        header("Location: movies.php");
        exit;
    }
?>

<!DOCTYPE html>
<html>
    <style>
        body {
            margin: 0;
        }
        
        main {
            display: flex;
            color: white;
            background-color: #122729;
            margin: 0;
        }

        #movieDetails {
            display: flex;
            width: 40%;
            color: #f6e8e0;
            background-color: #122729;
            gap:30px;
            padding: 25px;
        }

        .topDetails {
            display: flex;
        }

        .topDetails img {
            width: 220px;
            height: 260px;
            border-radius: 15px;
            border: 2px solid #f6e8e0;
        }

        .topDetails #rightOfPosterDetails {
            padding: 0 15px 0 25px;
        }

        .topDetails #rightOfPosterDetails #movieTitle {
            font-weight: bold;
            font-size: 28px;
        }

        .topDetails #rightOfPosterDetails #trailerLink {
            background-color: #ac2847;
            border: 2px solid #ec273f;
            border-radius: 15px;
            margin-top: 10px;
            padding: 5px;
            display: inline-flex;
            font-weight: bold;
            margin-bottom: 10px;
            transition: transform 0.3s;
        }
        .topDetails #rightOfPosterDetails #trailerLink:hover {
            transform: scale(1.05);
            cursor: pointer;
        }

        .bottomDetails {
            display: flex;
            margin-top: 15px;
            gap: 5px;
        }

        #dateSection {    
            display: flex;
            flex-direction: column;
            width: 60%;
            border-left: 3px solid #f6e8e0;;
            background-color: #122729;
        }

        #theaterSelection {
            padding: 25px;
            border-bottom: 3px solid #f6e8e0;
        }

        #theaterSelection #theaterSelectionTitle{
            margin-bottom: 15px;
        }

        #everythingAboutDates {
            height: 100%;
            padding: 25px;
            display: none;
        }

        button#addDateButton {
            border: 3px solid #f6e8e0;;
            border-radius: 25px;
            margin: 0 5px 0 5px;
            padding: 5px;
            font-size: 80%;
            font-weight: bold;
            background-color: black;
            color: #f6e8e0;
            transition: border 0.5s, color 0.5s;
        }

        button#addDateButton:hover {
            border: 3px solid #f6e8e0;
            background-color: #f6e8e0;
            color: black;
            cursor: pointer;
        }

        button#addDateButton.active {
            border: 3px solid #f6e8e0;
            background-color: #f6e8e0;
            color: black;
            border-radius: 15px 15px 0 0;
        }

        #addDateMenu {
            visibility: hidden;
            position: relative;
            border: 3px solid #f6e8e0;
            border-radius: 0 15px 15px 15px;
            padding: 5px;
            margin: 0 5px 0 5px;
        }

        #addDateMenu input:not([type="submit"]) {
            border: 2px solid black;
            color: black;
            background-color: #f6e8e0;
            border-radius: 15px;
            padding: 5px;
            margin-bottom: 5px;
        }

        #warningMessageContainer {
            display: flex;
            height: 70%;
            width: 100%;
            justify-content: center;
            align-items: center;
        }

        #warningMessage {
            font-weight: bold;
            border: 3px solid #f6e8e0;
            border-radius: 10px;
            padding: 25px;
        }

        .currentDatesContainer {
            border: 3px solid #f6e8e0;
            border-radius: 10px;
            background-color: #122729;
            margin: 5px;
            padding: 5px;
        }

        .daterange {
            display: inline-flex;
            align-items: flex-end;
            border-bottom: 2px solid #f6e8e0;
            margin-bottom: 10px;
            max-width: 280px;
            min-width: 280px;
        }

        .daterangeContainer {
            display: inline-flex;
            justify-content: flex-end;
        }

        .daterangeOptions {
            display: inline-flex;
            margin-bottom: 10px;
            gap: 8px;
        }

        .daterangeOptions div {
            border: 2px solid black;
            border-radius: 15px;
            padding: 5px;
            font-weight: bold;
        }

        .daterangeOptions .daterangeEdit {
            background-color: white;
            border-color: black;
            color: black;
            transition: transform 0.3s;
        }
        .daterangeOptions .daterangeEdit:hover {
            transform: scale(1.1);
            cursor: pointer;
        }

        .daterangeOptions .daterangeDelete {
            border-color: black;
            background-color: #ad1b07;
            color: white;
            transition: transform 0.3s;
        }
        .daterangeOptions .daterangeDelete:hover {
            transform: scale(1.1);
            cursor: pointer;
        }

        .timeslots {
            display: inline-block;
            border: 2px solid black;
            color: black;
            background-color: #f6e8e0;
            border-radius: 15px;
            padding: 5px;
            margin-bottom: 5px;
        }

        #movieEdit {
            display: inline-flex;
            width: 100%;
            justify-content: center;
            padding: 5px;
            background-color: black;
            color: white;
            border: 2px solid white;
            border-radius: 15px;
            margin-top: 15px;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s, border 0.3s;
        }
        #movieEdit:hover {
            background-color: white;
            color: black;
            border: 2px solid black;
            cursor: pointer;
        }

        #trailerModal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.85);
            justify-content: center;
            align-items: center;
        }

        #allDatesContainer {
            font-weight: bold;
        }

        #dayTimeslotsContainer {
            display: none;
        }

        #allTimeslotsAdd {
            display: flex;
        }

        #addTimeslotButton, #saveDateButton {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            padding: 5px;
            background-color: black;
            color: white;
            border: 2px solid white;
            border-radius: 15px;
            font-weight: bold;
            margin-bottom: 5px;
            transition: background-color 0.3s, color 0.3s, border 0.3s;
        }
        #addTimeslotButton:hover, #saveDateButton:hover {
            background-color: white;
            color: black;
            border: 2px solid black;
            cursor: pointer;
        }        

        #maxNumberForAll {
            color: #ec273f;
            margin: 0 0 15px 0;
            visibility: hidden;
        }

        #editMovieMenuContainer {
            position: absolute;
            display: none;
            width: 100%;
            height: 100%;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.5);
        }

        #editMovieMenu {
            width: auto;
            padding: 20px;
            border-radius: 15px;
            background-color: #122729;
            color: #F9F9F9;
            font-weight: bold;
            align-items: center;
            justify-content: center;
        }
        #closeMovieMenu, #movieUpload {
            display: inline-flex;
            justify-content: center;
            padding: 7px;
            background-color: black;
            color: white;
            border: 2px solid white;
            border-radius: 15px;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s, border 0.3s;
        }
        #closeMovieMenu:hover, #movieUpload:hover {
            background-color: white;
            color: black;
            border: 2px solid black;
            cursor: pointer;
        }
        #editMovieForm {
            padding: 8px;
            display: flex;
        }
        #editMovieForm #leftSection {
            border-right: 3px solid #f6e8e0;
            padding-right: 15px;
        }
        #editMovieForm #rightSection {
            padding-left: 15px;
        }

        #editMovieForm input:not([type="file"]), #movieDesc, #movieRating {
            border: 2px solid #ff4d4d;
            border-radius: 7px;
            padding: 3px;
        }

        #posterPreview {
            width: 220px;
            height: 260px;
            border: 3px solid #f6e8e0;
            border-radius: 15px;
            overflow: hidden;
        }

        #trailerPreview {
            border: 3px solid #f6e8e0;
            border-radius: 15px;
            width: 320px;
            height: 180px;
            overflow: hidden;
        }
    </style>
    <body>
        <?php include("header_admin.php"); ?>
        <main>
            <section id="movieDetails">
                <?php
                    $stmt = $conn->prepare("SELECT * FROM movie
                                            WHERE Movie_ID = ?");
                    $stmt->bind_param("i", $Movie_ID);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows === 0) {
                        header("Location: movies.php");
                        exit;
                    } else {
                        while ($row = $result->fetch_assoc()) {
                            $movieData = $row;
                            echo '<div>';
                            echo '<div class="topDetails">';
                            echo '<img src=../../' . htmlspecialchars($row['MoviePoster']) . ' class="moviePoster">';
                            echo '<div id="rightOfPosterDetails">';
                            echo '<div id="movieTitle">"', htmlspecialchars($row['MovieName']), '"</div>';
                            echo '<div id="trailerLink" onclick="openTrailer(\'' . htmlspecialchars($row['TrailerURL']) . '\')">Youtube Trailer</div>';
                            echo '<div><strong>Genre:</strong> ' . htmlspecialchars($row['Genre']), '</div>';
                            echo '<div><strong>Rating:</strong> ' . htmlspecialchars($row['Rating']), '</div>';
                            echo '<div><strong>Runtime:</strong> ' . htmlspecialchars($row['Runtime']), ' minutes </div>';
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="bottomDetails">';
                            echo '<div><strong>Description:</strong></div>';
                            echo '<div class="desc">'. htmlspecialchars($row['MovieDescription']), '</div>';
                            echo '</div>'; // For the movie details.
                        }
                    }
                    $stmt->close();
                ?>
                <div id="movieEdit" onclick="openMovieEdit(true)">Edit Movie Details</div></div>
            </section>
            <section id="dateSection">
                <div id="theaterSelection">
                    <div id="theaterSelectionTitle"><strong>Select a theater: </strong></div>
                    <?php
                        $stmt = $conn->prepare("SELECT Theater_ID, TheaterName FROM theater");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        while($row = $result->fetch_assoc()) {
                            echo '<label>';
                            echo '<input type="radio" class="theaterSelection" name="theaterSelection" 
                                    value="' . $row['Theater_ID'] . '" 
                                    onclick="getTheaterInfo(' . $row['Theater_ID'] . ')">';
                            echo htmlspecialchars($row['TheaterName']);
                            echo '</label><br>';
                        }
                        $stmt->close();
                    ?>
                </div>
                <div id="warningMessageContainer">
                    <div id="warningMessage">Please select a theater first.</div>
                </div>
                <div id="everythingAboutDates">
                    <div id="allDatesContainer"></div>
                    <div>
                        <span><button type="button" id="addDateButton">Add New Date Range +</button></span>
                        <div id="addDateMenu">
                            <div id="allTimeslotsContainer" class="timeslotContainer">
                                <form id="timeslotAllForm">
                                    <p>Start Date: <input type="date" id="startDate" required> - End Date: <input type="date" id="endDate"><span style="color: grey;">(optional)</span></p>
                                    <div>Timeslots: </div>
                                    <div id="allTimeslotsAdd">
                                        <div id="allTimeslots"><input type="time" class="timeslotsInput" name="timeslotALL" required></div>
                                        <button type="button" id="addTimeslotButton" onclick="addTimeslot()">+</button>
                                    </div>
                                    <div id="maxNumberForAll">Maximum amount of timeslots reached.*</div>
                                    <input type="submit" id="saveDateButton" value="Save">
                                </form>
                            </div>
                        </div>  
                    </div>
                </div>    
            </section>
            <div id="editMovieMenuContainer" class="">
                <div id="editMovieMenu">
                    <button type="button" id="closeMovieMenu" onclick="openMovieEdit(false)">Back</button>
                    <form id = "editMovieForm" autocomplete="off">
                        <div id="leftSection"> 
                            <div>
                                <label for="movieName">Movie Name: </label>
                                <input type="text" id="movieName" name="movieName" placeholder="Movie Name" value="<?= htmlspecialchars($movieData['MovieName']) ?>" required>
                            </div>
                            <br>

                            <div>
                                <label for="movieDesc">Movie Description: </label><br>
                                <textarea id="movieDesc" name="movieDesc" rows="10" cols="75" placeholder="Movie Description" required><?= htmlspecialchars($movieData['MovieDescription']) ?></textarea>
                            </div>
                            <br>

                            <div>
                                <label for="movieGenre">Movie Genre(s): </label><br>
                                <input type="text" id="movieGenre" name="movieGenre" placeholder="Movie Genre" value="<?= htmlspecialchars($movieData['Genre']) ?>" required>
                            </div>
                            <br>

                            <div>
                                <label for="movieRating">Movie Rating: </label><br>
                                <select name="movieRating" id="movieRating">
                                    <option value="">Select a rating:</option>
                                    <option value="G" <?= $movieData['Rating']=="G"?"selected":"" ?>>Rated G</option>
                                    <option value="PG" <?= $movieData['Rating']=="PG"?"selected":"" ?>>Rated PG</option>
                                    <option value="R-13" <?= $movieData['Rating']=="R-13"?"selected":"" ?>>Rated R-13</option>
                                    <option value="R-16" <?= $movieData['Rating']=="R-16"?"selected":"" ?>>Rated R-16</option>
                                    <option value="R-18" <?= $movieData['Rating']=="R-18"?"selected":"" ?>>Rated R-18</option> 
                                </select>
                            </div>
                            <br>

                            <div>
                                <label for="movieRuntime">Movie Runtime (in minutes): </label>
                                <input type="number" id="movieRuntime" name="movieRuntime" placeholder="Runtime (in minutes)" min="0" value="<?= htmlspecialchars($movieData['Runtime']) ?>" required>
                            </div>
                            <br>
                            
                            <div>
                                <label for="TrailerUrl">Trailer URL: </label><br>
                                <input type="text" id="TrailerURL" name="TrailerURL" placeholder="Trailer Link" value="<?= htmlspecialchars($movieData['TrailerURL']) ?>" required><br><br>
                                <div id="trailerPreviewText">Trailer Preview: </div>
                                <div id="trailerPreview"></div> <!-- Preview container -->
                            </div>
                            <br>

                            <div>
                                <button type="submit" id="movieUpload" name="movieDetails" value="movieDetails">Edit</button>
                            </div>
                        </div>
                        <div>                           

                        <div id="rightSection">
                            <div>
                                <label for="moviePosterUp">Movie Poster: </label><br>
                                <input type="file" id="moviePosterUp" name="moviePosterUp" accept="image/png, image/jpeg, image/jpg" required><br><br>
                            </div>
                            <div>Poster Preview:</div>
                            <img id="posterPreview" src="" alt="Poster Preview">
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <script>
            theaterSelection = document.getElementById('theaterSelection');
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
                    addDateButton.innerText = "Add New Date Range -";
                    addDateMenu.style.visibility = 'visible';
                    addDateButton.classList.add('active');
                } else {
                    isDateMenuOpen = false;
                    addDateButton.innerText = "Add New Date Range +";
                    addDateMenu.style.visibility = 'hidden';
                    addDateButton.classList.remove('active');
                }                
            })

            const allTimeslots = document.getElementById('allTimeslots');
            const maxNumberForAll = document.getElementById('maxNumberForAll');
            const addTimeslotButton = document.getElementById('addTimeslotButton')
            let addedTimes = 0 ;
            function addTimeslot() {
                if (addedTimes < 4) {
                    const timeslot = document.createElement('input');
                    timeslot.type = 'time';
                    timeslot.name = 'timeslotALL';
                    timeslot.classList.add('timeslots');
                    allTimeslots.appendChild(timeslot);
                    addedTimes += 1;
                    if (addedTimes == 4) {
                        addTimeslotButton.style.display = 'none';
                        maxNumberForAll.style.visibility = 'visible';
                    }     
                }            
            }
            
            const everythingAboutDates = document.getElementById('everythingAboutDates');
            const warningMessageContainer = document.getElementById('warningMessageContainer');
            function getTheaterInfo(Theater_ID) {
                const urlParams = new URLSearchParams(window.location.search);
                var Movie_ID = urlParams.get('id');

                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        everythingAboutDates.style.display = 'block';
                        warningMessageContainer.style.display = 'none';
                        allDatesContainer.innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "queries_admin.php?q=theaterdatetimes&id=" + Theater_ID + "&movie_id=" + Movie_ID, true);
                xmlhttp.send();
            }

            timeslotAllForm = document.getElementById('timeslotAllForm');
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');
            timeslotAllForm.addEventListener("submit", function(e) {
                e.preventDefault();

                const urlParams = new URLSearchParams(window.location.search);
                var Movie_ID = urlParams.get('id');
                var Theater_ID = document.querySelector('input[name="theaterSelection"]:checked').value;

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
                        getTheaterInfo(Theater_ID);
                        
                        startDate.value = "";
                        endDate.value = "";

                        const timeslotContainer = document.getElementById("allTimeslots");
                        timeslotContainer.innerHTML = ""; // clear all inputs

                        // add back a single empty timeslot input
                        const newInput = document.createElement("input");
                        newInput.type = "time";
                        newInput.className = "timeslots";
                        newInput.name = "timeslotALL";
                        newInput.onchange = addTimeslot;
                        timeslotContainer.appendChild(newInput);
                    }
                };
                xmlhttp.open("POST", "queries_admin.php?q=datetimesent", true);
                xmlhttp.setRequestHeader("Content-Type", "application/json");
                xmlhttp.send(JSON.stringify({
                    Theater_ID: Theater_ID,
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

            function openTrailer(url) {
                let videoId = "";

                if(url.includes("watch?v=")){
                    videoId = url.split("watch?v=")[1];
                } 
                else if(url.includes("youtu.be/")){
                    videoId = url.split("youtu.be/")[1];
                }

                const embedURL = "https://www.youtube.com/embed/" + videoId + "?autoplay=1";

                document.getElementById("trailerFrame").src = embedURL;
                document.getElementById("trailerModal").style.display = "flex";
            }

            function closeTrailer() {
                document.getElementById("trailerModal").style.display = "none";
                document.getElementById("trailerFrame").src = "";
            }

            const movieEdit = document.getElementById('movieEdit');
            const editMovieMenuContainer = document.getElementById('editMovieMenuContainer');
            function openMovieEdit(isOpen) {
                if (isOpen) {
                    getYoutubeID(trailerInput.value.trim());
                    movieEdit.disabled = true;
                    editMovieMenuContainer.style.display = 'flex';
                } else {
                    movieEdit.disabled = false;
                    editMovieMenuContainer.style.display = 'none'; 
                }
            }

            editMovieForm = document.getElementById('editMovieForm');
            editMovieForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const urlParams = new URLSearchParams(window.location.search);
                var Movie_ID = urlParams.get('id');
                
                const formData = new FormData(editMovieForm);

                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log(this.responseText);
                        // window.location.href = "movie_timeslot.php?id=" + Movie_ID;
                    }
                };
                xmlhttp.open("POST", "queries_admin.php?q=movieedit&id=" + Movie_ID, true);
                xmlhttp.send(formData);
            })

            moviePosterUp.addEventListener('change', function() {
                const reader = new FileReader();
                reader.addEventListener('load', () => {
                    uploadedPoster = reader.result;
                    const posterPreview = document.getElementById('posterPreview');
                    posterPreview.src = uploadedPoster;
                    posterPreview.style.display = "block";
                })
                reader.readAsDataURL(this.files[0]);
            })

            function getMovieInfo() {                
                const urlParams = new URLSearchParams(window.location.search);
                var Movie_ID = urlParams.get('id');
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        if (this.responseText.includes('id=failed')) {
                            failed.innerHTML = this.responseText;
                        } else {
                            movieDetails.innerHTML = this.responseText;
                            getTheaterNames();
                            // window.location.href = 'movies.php';
                        }                        
                    }                    
                };                
                xmlhttp.open("GET", "queries_admin.php?q=moviedetails&movie_id=" + Movie_ID, true);
                xmlhttp.send();
            }

        const trailerInput = document.getElementById('TrailerURL');
        const trailerPreview = document.getElementById('trailerPreview');

        function getYoutubeID(url) {
            let id = url.match(/youtu\.be\/([^\?]+)/);
            if(id) return id[1];
            id = url.match(/v=([^&]+)/);
            if(id) return id[1];
            return url; // fallback if they just paste the ID
        }

        trailerInput.addEventListener('input', () => {
            const id = getYoutubeID(trailerInput.value.trim());
            if(id) {
                trailerPreview.innerHTML = `
                    <iframe width="320" height="180" 
                            src="https://www.youtube.com/embed/${id}" 
                            frameborder="0" allowfullscreen></iframe>
                `;
            } else {
                trailerPreview.innerHTML = ''; // clear if input empty
            }
        });
            
        </script>

        <div id="trailerModal">
            <div style="position: relative; width: 80%; max-width: 900px;">
                <span onclick="closeTrailer()" style="position: absolute; top: -40px; right: 0; font-size: 30px; cursor:pointer; color: white;">X</span>
                <iframe id="trailerFrame" width="100%" height="500" src="" framborder="0" allow="autoplay; encrypted-media" allowfullscreen>
            </div>
        </div>
    </body>
</html>

