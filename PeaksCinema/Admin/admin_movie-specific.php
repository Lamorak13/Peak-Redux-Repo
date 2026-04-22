<!DOCTYPE HTML>
<html>
    <head>
        <script src="admin_gate.js"></script>
        <script>admin_gate.gatekeep(1); </script>
        <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
        <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    </head>
    <body>
        <?php include("admin_header.php"); ?>
        <main>
            <div id="loader" class="loader">
                <div>loading..</div>
            </div>
            <section id="content" style="display: none">
                <div id="movieDetailsContainer"></div>
                <div id="theaterDaterangeContainer">
                    <div id="daterangeTheaterSelectionContainer">
                        <div id="daterangeTheaterLabel">Date ranges for theater:</div>
                        <select id="theaterSelection"></select>
                    </div>
                    <div id="daterangeContent">
                        <div id="daterangesGallery"></div>
                        <div id="addDateContainer"></div>
                    </div>
                </div>
            </section>
            <div id="movieMenuContainer" style="display: none">
                <form id="movieMenu">
                    <div id="scrollable">
                    <button type="button" id="theBackButton" class="generalAdminButton" onclick="movieMenuOpenClose()">Back</button>
                        <div id="movieEverything">
                            <div id="movieMenuTop">
                                <label for="MovieName">Movie Name: </label>
                                <input type="text" id="MovieName" name="MovieName" placeholder="Movie Name" required>

                                <label for="MovieDescription">Movie Description: </label>
                                <textarea id="MovieDescription" name="MovieDescription" placeholder="Movie Description" required></textarea>

                                <label for="Genre">Movie Genre: </label>
                                <input type="text" id="Genre" name="Genre" placeholder="Movie Genre" required>

                                <label for="Rating">Movie Rating: </label>
                                <select name="Rating" id="Rating" required>
                                    <option value="">Select a rating:</option>
                                    <option value="G">Rated G</option>
                                    <option value="PG">Rated PG</option>
                                    <option value="R-13">Rated R-13</option>
                                    <option value="R-16">Rated R-16</option>
                                    <option value="R-18">Rated R-18</option> 
                                </select>

                                <label for="Runtime">Movie Runtime (in minutes): </label>
                                <input type="number" id="Runtime" name="Runtime" placeholder="Runtime (in minutes)" min="0" required>
                            </div>
                            <div id="movieMenuBottom">
                                <div id="posterUploadContainer">
                                    <div id="posterPreviewText">Movie Poster:</div>
                                    <label for="MoviePoster" id="posterInput" class="uploaded">
                                        <span id="posterShow">Upload Poster</span>
                                        <img id="posterPreview" src="" style="display: block">
                                    </label>
                                    <input type="file" id="MoviePoster" name="MoviePoster" accept="image/png, image/jpeg, image/jpg">                                 
                                </div>
                                <div id="trailerUploadContainer">
                                    <label for="TrailerURL">Youtube Trailer Link:</label>
                                    <input type="text" id="TrailerURL" name="TrailerURL" placeholder="Youtube Trailer Link" required>
                                    <div id="trailerPreviewText">Trailer Preview: </div>
                                    <div id="trailerPreview"></div>
                                </div>                        
                            </div>
                        </div>
                        <button type="submit" id="movieSubmitButton" class="generalAdminButton">Edit</button>
                    </div>
                </form>
            </div>
        </main>
        <script>
            const theaterSelection = document.getElementById("theaterSelection");

            const url = new URL(window.location.href);
            const Movie_ID = url.searchParams.get('movie_id');
            
            var lastDate = new Date().toDateString();
            var dateRangePickerInstance = null;
            var usedDatesForPicker = new Set();
            var minimumAllowedStartTime = null;

            async function getUsedDatesForTheater(Theater_ID) {
                if (!Theater_ID) {
                    return new Set();
                }

                try {
                    const response = await fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=daterange/all/theater/${Theater_ID}&exclude_movie=${Movie_ID}`, {
                        method: "GET"
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }

                    const data = await response.json();
                    const blockedDates = new Set();
                    const ranges = data.data || [];

                    ranges.forEach(range => {
                        const rangeStart = moment(range.StartDate, "YYYY-MM-DD");
                        const rangeEnd = range.EndDate
                            ? moment(range.EndDate, "YYYY-MM-DD")
                            : moment(range.StartDate, "YYYY-MM-DD");

                        for (let day = rangeStart.clone(); day.isSameOrBefore(rangeEnd); day.add(1, "day")) {
                            blockedDates.add(day.format("YYYY-MM-DD"));
                        }
                    });

                    return blockedDates;
                } catch (error) {
                    console.error(error);
                    return new Set();
                }
            }

            function timeToMinutes(timeValue) {
                if (!timeValue) return 0;
                const timeParts = timeValue.split(":");
                const hours = Number(timeParts[0]) || 0;
                const minutes = Number(timeParts[1]) || 0;
                return (hours * 60) + minutes;
            }

            function minutesToTimeInput(totalMinutes) {
                const normalized = ((totalMinutes % 1440) + 1440) % 1440;
                const hours = Math.floor(normalized / 60).toString().padStart(2, "0");
                const minutes = (normalized % 60).toString().padStart(2, "0");
                return `${hours}:${minutes}`;
            }

            function roundUpToFiveMinutes(totalMinutes) {
                return Math.ceil(totalMinutes / 5) * 5;
            }

            function formatMinutesToAmPm(totalMinutes) {
                const normalized = ((totalMinutes % 1440) + 1440) % 1440;
                const hours24 = Math.floor(normalized / 60);
                const minutes = normalized % 60;
                const suffix = hours24 >= 12 ? "PM" : "AM";
                const hours12 = (hours24 % 12) === 0 ? 12 : (hours24 % 12);
                return `${hours12}:${minutes.toString().padStart(2, "0")} ${suffix}`;
            }

            function getCurrentRuntimeMinutes() {
                return Number(document.getElementById("Runtime").value) || 0;
            }

            async function getOccupiedTimeslotsForRange(Theater_ID, startDate, endDate) {
                if (!Theater_ID || !startDate || !endDate) {
                    return [];
                }

                try {
                    const response = await fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=timeslot/all/theater/${Theater_ID}&start_date=${startDate}&end_date=${endDate}`, {
                        method: "GET"
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }

                    const data = await response.json();
                    return data.data || [];
                } catch (error) {
                    console.error(error);
                    return [];
                }
            }

            async function getMovieInfo() {
                console.log(Movie_ID);

                movieDetailsContainer.innerHTML = "";

                const moviePromise = fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie/${Movie_ID}/`, {
                    method: "GET"
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        window.location.href = "admin_movie-gallery.php";
                        return;
                    }
                    let movie = data.data[0];
                    
                    document.title = "Admin - " + movie.MovieName;

                    const movieDetailsContainer = document.getElementById('movieDetailsContainer');
                    
                    const movieTopDetails = document.createElement('div');
                    movieTopDetails.classList.add('movieTopDetails');

                    // For the Movie Poster + Trailer

                    const moviePosterTrailer = document.createElement('div');
                    moviePosterTrailer.classList.add('moviePosterTrailer');
                    
                    const moviePoster = document.createElement('img')
                    moviePoster.src = "/" + movie.MoviePoster + "?t=" + new Date().getTime();
                    moviePoster.alt = movie.MovieName;
                    moviePoster.classList.add('moviePoster');
                    moviePosterTrailer.append(moviePoster);
                    
                    const movieTrailerURL = document.createElement('button');
                    movieTrailerURL.textContent = "Youtube Trailer";
                    movieTrailerURL.addEventListener("click", function() {
                        window.open(movie.TrailerURL);
                    })
                    movieTrailerURL.classList.add('movieTrailerURL');
                    moviePosterTrailer.append(movieTrailerURL);

                    movieTopDetails.append(moviePosterTrailer);

                    // For the Movie Name + other info (This is to the right of the movie poster + trailer)

                    const movieNameOthers = document.createElement('div');
                    movieNameOthers.classList.add('movieNameOthers');

                    const movieName = document.createElement('div');
                    movieName.textContent = movie.MovieName;
                    movieName.id = 'movieName';
                    movieNameOthers.append(movieName);

                    const movieGenre = document.createElement('div');
                    movieGenre.textContent = "Genre(s): " + movie.Genre;
                    movieGenre.id = 'movieGenre';
                    movieNameOthers.append(movieGenre);

                    const movieRating = document.createElement('div');
                    movieRating.textContent = "Rating: " + movie.Rating;
                    movieRating.id = 'movieRating';
                    movieNameOthers.append(movieRating);

                    const movieRuntime = document.createElement('div');
                    movieRuntime.textContent = "Runtime: " + movie.Runtime + " minutes";
                    movieRuntime.id = 'movieRuntime';
                    movieNameOthers.append(movieRuntime);

                    movieTopDetails.append(movieNameOthers);

                    movieDetailsContainer.append(movieTopDetails);

                    // movie description, self explanatory

                    const movieDescription = document.createElement('div');
                    movieDescription.textContent = movie.MovieDescription;
                    movieDetailsContainer.append(movieDescription);

                    // Edit movie code blocks

                    const editMovieButton = document.createElement('button');
                    editMovieButton.classList.add('generalAdminButton');
                    editMovieButton.textContent = "Edit Movie Details";
                    editMovieButton.addEventListener("click", movieMenuOpenClose);
                    movieDetailsContainer.append(editMovieButton);

                    document.getElementById('MovieName').value = movie.MovieName;
                    document.getElementById('MovieDescription').value = movie.MovieDescription;
                    document.getElementById('Genre').value = movie.Genre;
                    document.getElementById('Rating').value = movie.Rating;
                    document.getElementById('Runtime').value = movie.Runtime;
                    document.getElementById('posterPreview').src = '/' + movie.MoviePoster + "?t=" + new Date().getTime();
                    document.getElementById('TrailerURL').value = movie.TrailerURL;
                    getTrailer();

                    //

                    const deleteMovieButton = document.createElement('button');
                    deleteMovieButton.classList.add('deleteDaterange');
                    deleteMovieButton.textContent = "Delete Movie From System";
                    deleteMovieButton.addEventListener("click", () => areYouSure("movie", movie.Movie_ID, movie.MovieName));
                    movieDetailsContainer.append(deleteMovieButton);
                })

                const theaterPromise = fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=theater', {
                    method: 'GET'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        const errorMessage = document.createElement('div');
                        errorMessage.classList.add('errorMessage');
                        errorMessage.textContent = data.error;
                        daterangesGallery.append(errorMessage);

                        const option = document.createElement('option');
                        option.textContent = "---";
                        option.value = "";
                        theaterSelection.append(option);

                        theaterSelection.disabled = true;
                    } else {
                        let theaters = data.data;

                        theaters.forEach(theater => {
                            const option = document.createElement('option');
                            option.textContent = theater.TheaterName;
                            option.value = theater.Theater_ID;

                            theaterSelection.append(option);
                            
                        })                        
                        getDateranges(theaterSelection.value);
                        createDaterangeMenu(lastDate);
                    }
                })

                Promise.all([moviePromise, theaterPromise])
                .catch(error => {
                    console.error(error);
                })
                .finally(() => {
                    setTimeout(() => {
                        loader.style.display = 'none';
                        content.style.display = 'flex';
                    }, 500);
                })
            }

            document.addEventListener("DOMContentLoaded", getMovieInfo)

            const addDateContainer = document.getElementById('addDateContainer');
            theaterSelection.addEventListener("change", function() {
                getDateranges(theaterSelection.value);
            })

            function getDateranges(Theater_ID) {
                const daterangesGallery = document.getElementById('daterangesGallery');
                daterangesGallery.innerHTML = "";

                const daterangePromise = fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=daterange/all/movie/${Movie_ID}/theater/${Theater_ID}`, {
                    method: "GET"
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        const errorMessage = document.createElement('div');
                        errorMessage.classList.add('errorMessage');
                        errorMessage.textContent = data.error;
                        daterangesGallery.append(errorMessage);

                        lastDate = new Date();
                    } else {
                        let dateranges = data.data;
                        

                        dateranges.forEach(daterange => {
                            const daterangeContainer = document.createElement('div');
                            daterangeContainer.classList.add('daterangeContainer');

                            const daterangeTop = document.createElement('div');
                            daterangeTop.classList.add('daterangeTop');                        

                            const daterangeProper = document.createElement('div');
                            daterangeProper.classList.add('daterangeProper');

                            var startDateFormatted = new Date(daterange.StartDate);
                            var startDateFormatted = startDateFormatted.toLocaleDateString(undefined, {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'});

                            const startDate = document.createElement('div');
                            startDate.classList.add('startDate');
                            startDate.textContent = startDateFormatted;
                            daterangeProper.append(startDate);

                            if (daterange.EndDate) {
                                var endDateFormatted = new Date(daterange.EndDate);
                                var endDateFormatted = endDateFormatted.toLocaleDateString(undefined, {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'});

                                const endDate = document.createElement('div');
                                endDate.classList.add('endDate');
                                endDate.textContent = " - " + endDateFormatted;
                                daterangeProper.append(endDate);
                                
                                lastDate = daterange.EndDate;
                            } else {
                                lastDate = daterange.StartDate;
                            }                           


                            daterangeTop.append(daterangeProper);

                            const deleteDaterange = document.createElement('button');
                            deleteDaterange.classList.add('deleteDaterange');
                            deleteDaterange.textContent = "Delete";
                            deleteDaterange.addEventListener("click", () => areYouSure("daterange", daterange.DateRange_ID, null, daterangeProper, theaterSelection.value));

                            daterangeTop.append(deleteDaterange);

                            daterangeContainer.append(daterangeTop);

                            const timeslotsContainer = document.createElement('div');
                            timeslotsContainer.classList.add('timeslotsContainer');
                            daterange.Timeslots.forEach(timeslot => {
                                const timeslotDiv = document.createElement('div');
                                timeslotDiv.classList.add('timeslotDiv');
                                const timeslotAsDate = new Date(`1970-01-01T${timeslot.StartTime}`);
                                timeslotDiv.textContent = timeslotAsDate.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true });
                                timeslotsContainer.append(timeslotDiv);
                            })
                            daterangeContainer.append(timeslotsContainer);

                            daterangesGallery.append(daterangeContainer);
                        })
                    }
                    createDaterangeMenu(lastDate);
                })                
                .catch(error => {
                    console.error(error);
                });                
            }
            
            function createDaterangeMenu(minValueWhole) {
                let maxTimeslots = 5;
                let currentTimeslots = 1;
                dateRangePickerInstance = null;
                usedDatesForPicker = new Set();
                minimumAllowedStartTime = null;

                addDateContainer.innerHTML = "";
                const everythingDateranges = document.createElement('div');
                everythingDateranges.classList.add('everythingDateranges');

                const addDaterangeButton = document.createElement('button');
                addDaterangeButton.classList.add('addDaterangeButton');
                addDaterangeButton.classList.add('generalAdminButton');
                addDaterangeButton.textContent = "Add New Date Range";
                addDaterangeButton.type = 'button';
                everythingDateranges.append(addDaterangeButton);

                const addDaterangeMenu = document.createElement('form');
                addDaterangeMenu.id = 'addDaterangeMenu';
                addDaterangeMenu.style.display = 'none';

                // Date Range Picker
                const daterangeInputSpan = document.createElement('span');
                daterangeInputSpan.classList.add('daterangeInputSpan');

                const dateRangePickerLabel = document.createElement('label');
                dateRangePickerLabel.textContent = "Select Date Range: ";
                dateRangePickerLabel.htmlFor = "dateRangePicker";
                daterangeInputSpan.append(dateRangePickerLabel);

                const dateRangePickerInput = document.createElement('input');
                dateRangePickerInput.type = 'text';
                dateRangePickerInput.id = 'dateRangePicker';
                dateRangePickerInput.name = 'dateRangePicker';
                dateRangePickerInput.classList.add('dateRangePickerInput');
                dateRangePickerInput.required = true;
                daterangeInputSpan.append(dateRangePickerInput);

                addDaterangeMenu.append(daterangeInputSpan);

                // Timeslots
                const daterangeTimeslotInputsPlus = document.createElement('span');
                daterangeTimeslotInputsPlus.classList.add('daterangeTimeslotInputsPlus');
                
                const daterangeTimeslotInputs = document.createElement('span');
                daterangeTimeslotInputs.classList.add('daterangeTimeslotInputs');
                daterangeTimeslotInputsPlus.append(daterangeTimeslotInputs);

                function rebuildTimeslotOptions() {
                    const allTimeslotSelects = Array.from(document.querySelectorAll('#addDaterangeMenu .timeslotInput'));
                    const runtimeMinutes = getCurrentRuntimeMinutes();
                    const theaterOpeningMinutes = 10 * 60; // 10:00 AM
                    const baseMinimumMinutes = Math.max(
                        theaterOpeningMinutes,
                        minimumAllowedStartTime ? timeToMinutes(minimumAllowedStartTime) : 0
                    );

                    let rollingMinimum = baseMinimumMinutes;

                    allTimeslotSelects.forEach(select => {
                        const previousValue = select.value;
                        select.innerHTML = "";

                        for (let totalMinutes = rollingMinimum; totalMinutes < 1440; totalMinutes += 5) {
                            const option = document.createElement('option');
                            option.value = minutesToTimeInput(totalMinutes);
                            option.textContent = formatMinutesToAmPm(totalMinutes);
                            select.append(option);
                        }

                        if (select.options.length === 0) {
                            const noTimesOption = document.createElement('option');
                            noTimesOption.value = "";
                            noTimesOption.textContent = "No Times Left For This Day";
                            noTimesOption.selected = true;
                            noTimesOption.disabled = true;
                            select.append(noTimesOption);
                            rollingMinimum = 1440;
                            return;
                        }

                        if (previousValue && timeToMinutes(previousValue) >= rollingMinimum) {
                            select.value = previousValue;
                        }

                        const selectedMinutes = timeToMinutes(select.value);
                        if (runtimeMinutes > 0) {
                            rollingMinimum = roundUpToFiveMinutes(selectedMinutes + runtimeMinutes);
                        } else {
                            rollingMinimum = selectedMinutes;
                        }
                    });
                }

                function createTimeslot() {
                    const timeslotInputSpan = document.createElement('span');
                    timeslotInputSpan.classList.add('timeslotInputSpan');

                    const timeslotInput = document.createElement('select');
                    timeslotInput.name = "timeslot";
                    timeslotInput.classList.add('timeslotInput');
                    timeslotInput.addEventListener("change", rebuildTimeslotOptions);
                    timeslotInputSpan.append(timeslotInput);

                    if (currentTimeslots != 1) {
                        const timeslotDelete = document.createElement('button');
                        timeslotDelete.type = 'button';
                        timeslotDelete.classList.add('timeslotDelete');
                        timeslotDelete.textContent = "X";
                        timeslotDelete.addEventListener("click", function(e) {
                            e.preventDefault();
                            this.parentNode.remove();
                            currentTimeslots -= 1;
                            timeslotAddButton.style.display = 'block';
                        })
                        timeslotInputSpan.append(timeslotDelete);
                    } else {
                        timeslotInput.required = true;
                    }
                    return timeslotInputSpan;
                }

                daterangeTimeslotInputs.append(createTimeslot());

                const timeslotAddButton = document.createElement('button');
                timeslotAddButton.type = 'button';
                timeslotAddButton.classList.add('timeslotAddButton');
                timeslotAddButton.classList.add('generalAdminButton');
                timeslotAddButton.textContent = "+";
                timeslotAddButton.addEventListener("click", function(e) {
                    e.preventDefault();
                    if (currentTimeslots <= (maxTimeslots - 1)) {
                        currentTimeslots += 1;
                        daterangeTimeslotInputs.append(createTimeslot());
                        if (currentTimeslots == maxTimeslots) {
                            timeslotAddButton.style.display = 'none';
                        }
                    } else {
                        timeslotAddButton.style.display = 'none';
                        console.error("Max timeslots reached");
                    }
                })
                daterangeTimeslotInputsPlus.append(timeslotAddButton);

                addDaterangeMenu.append(daterangeTimeslotInputsPlus);

                // Screening Type and Price
                const screeningTypePrice = document.createElement('span');

                const screeningTypeLabel = document.createElement('label');
                screeningTypeLabel.htmlFor = 'screeningType';
                screeningTypeLabel.textContent = "Screening Type: ";
                screeningTypePrice.append(screeningTypeLabel);
                
                const screeningTypeInput = document.createElement('select');
                screeningTypeInput.id = 'screeningType';
                screeningTypeInput.classList.add('screeningTypeInput');
                screeningTypeInput.name = "ScreeningType";
                screeningTypeInput.required = true;

                const nullOption = document.createElement('option');
                nullOption.textContent = "Please select a screening type";
                nullOption.value = "";
                screeningTypeInput.append(nullOption);
                
                const option2D = document.createElement('option');
                option2D.textContent = "2D";
                option2D.value = "2D";
                screeningTypeInput.append(option2D);
                const option3D = document.createElement('option');
                option3D.textContent = "3D";
                option3D.value = "3D";
                screeningTypeInput.append(option3D);

                screeningTypePrice.append(screeningTypeInput);

                const seatPriceInputLabel = document.createElement('label');
                seatPriceInputLabel.htmlFor = 'seatPrice';
                seatPriceInputLabel.textContent = "Seat Price (In Pesos): ";
                screeningTypePrice.append(seatPriceInputLabel);

                const seatPriceInput = document.createElement('input');
                seatPriceInput.type = 'number';
                seatPriceInput.name = "SeatPrice";
                seatPriceInput.required = true;
                screeningTypePrice.append(seatPriceInput);

                addDaterangeMenu.append(screeningTypePrice);

                // Submit button
                const addDaterangeMenuSubmit = document.createElement('button');
                addDaterangeMenuSubmit.type = 'submit';
                addDaterangeMenuSubmit.textContent = "Add Date Range";
                addDaterangeMenuSubmit.classList.add('addDateRangeMenuSubmit');
                addDaterangeMenuSubmit.classList.add('generalAdminButton');
                addDaterangeMenu.append(addDaterangeMenuSubmit);

                // Form submission
                addDaterangeMenu.addEventListener("submit", function(e) {
                    e.preventDefault();

                    if (!dateRangePickerInstance) {
                        console.error("Date range picker not initialized");
                        return;
                    }

                    const formData = new FormData(addDaterangeMenu);
                    const Theater_ID = theaterSelection.value;

                    // Get selected date range from daterangepicker
                    const startDate = dateRangePickerInstance.startDate;
                    const endDate = dateRangePickerInstance.endDate;

                    if (!startDate || !endDate) {
                        console.error("Invalid date range selected");
                        return;
                    }

                    let timeslots = Array.from(document.querySelectorAll('#addDaterangeMenu .timeslotInput'))
                        .map(input => input.value)
                        .filter(t => t !== "");

                    if (timeslots.length === 0) {
                        console.error("No timeslots provided");
                        return;
                    }

                    let allTimeslots = [];
                    const runtimeMinutes = getCurrentRuntimeMinutes();
                    const minAllowedMinutes = minimumAllowedStartTime ? timeToMinutes(minimumAllowedStartTime) : null;
                    const timeslotMinutesSorted = timeslots.map(timeToMinutes).sort((a, b) => a - b);

                    if (minAllowedMinutes !== null) {
                        for (const minutes of timeslotMinutesSorted) {
                            if (minutes < minAllowedMinutes) {
                                alert(`Timeslot must be ${minimumAllowedStartTime} or later for the selected dates.`);
                                return;
                            }
                        }
                    }

                    if (runtimeMinutes > 0 && timeslotMinutesSorted.length > 1) {
                        for (let i = 1; i < timeslotMinutesSorted.length; i++) {
                            const previousMinimum = roundUpToFiveMinutes(timeslotMinutesSorted[i - 1] + runtimeMinutes);
                            if (timeslotMinutesSorted[i] < previousMinimum) {
                                alert(`Timeslots must be at least ${runtimeMinutes} minutes apart (rounded to 5-minute steps).`);
                                return;
                            }
                        }
                    }

                    for (let d = startDate.clone(); d.isSameOrBefore(endDate); d.add(1, 'day')) {
                        if (usedDatesForPicker.has(d.format('YYYY-MM-DD'))) {
                            alert("One or more selected dates are already used.");
                            return;
                        }
                    }

                    // Generate all timeslots for each day in the range
                    for (let d = startDate.clone(); d.isSameOrBefore(endDate); d.add(1, 'day')) {
                        let dateStr = d.format('YYYY-MM-DD');
                        timeslots.forEach(time => {
                            allTimeslots.push({ date: dateStr, timeslot: time });
                        });
                    }

                    const payload = {
                        StartDate: startDate.format('YYYY-MM-DD'),
                        EndDate: endDate.format('YYYY-MM-DD'),
                        timeslots: allTimeslots,
                        ScreeningType: formData.get("ScreeningType"),
                        SeatPrice: Number(formData.get("SeatPrice"))
                    }

                    console.log("Sending payload:", payload);

                    fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=daterange/all/theater/${Theater_ID}/movie/${Movie_ID}`, {
                        method: 'POST',
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log(data.status);
                        daterangeMenuOpenClose();
                        getDateranges(Theater_ID);
                    })
                    .catch(error => {
                        console.error(error);
                    })
                })

                everythingDateranges.append(addDaterangeMenu)
                addDateContainer.append(everythingDateranges);

                let addDaterangeMenuIsOpen = false;
                addDaterangeButton.addEventListener("click", async function(e) {
                    e.preventDefault();
                    daterangeMenuOpenClose();
                    
                    if (addDaterangeMenuIsOpen) {
                        // Initialize daterangepicker when menu opens
                        if (!dateRangePickerInstance) {
                            const theaterId = theaterSelection.value;
                            usedDatesForPicker = await getUsedDatesForTheater(theaterId);
                            let minDate = moment().startOf('day');
                            let firstAvailableDate = minDate.clone();

                            while (usedDatesForPicker.has(firstAvailableDate.format('YYYY-MM-DD'))) {
                                firstAvailableDate.add(1, 'day');
                            }
                            
                            const pickerElement = $(dateRangePickerInput).daterangepicker({
                                locale: {
                                    format: 'YYYY-MM-DD'
                                },
                                minDate: minDate,
                                startDate: firstAvailableDate,
                                endDate: firstAvailableDate,
                                opens: 'center',
                                isInvalidDate: function(date) {
                                    return usedDatesForPicker.has(date.format('YYYY-MM-DD'));
                                }
                            });
                            dateRangePickerInstance = pickerElement.data('daterangepicker');

                            async function refreshMinimumAllowedStartTime() {
                                const selectedStartDate = dateRangePickerInstance.startDate.format('YYYY-MM-DD');
                                const selectedEndDate = dateRangePickerInstance.endDate.format('YYYY-MM-DD');
                                const occupiedTimeslots = await getOccupiedTimeslotsForRange(theaterId, selectedStartDate, selectedEndDate);

                                let minimumStartMinutes = null;
                                occupiedTimeslots.forEach(slot => {
                                    const slotStartMinutes = timeToMinutes(slot.StartTime);
                                    const slotRuntime = Number(slot.Runtime) || 0;
                                    const slotEndRounded = roundUpToFiveMinutes(slotStartMinutes + slotRuntime);
                                    if (minimumStartMinutes === null || slotEndRounded > minimumStartMinutes) {
                                        minimumStartMinutes = slotEndRounded;
                                    }
                                });

                                minimumAllowedStartTime = minimumStartMinutes !== null ? minutesToTimeInput(minimumStartMinutes) : null;
                                rebuildTimeslotOptions();
                            }

                            await refreshMinimumAllowedStartTime();
                            pickerElement.on('apply.daterangepicker', async function() {
                                await refreshMinimumAllowedStartTime();
                            });
                        }
                    }
                });

                function daterangeMenuOpenClose() {
                    if (addDaterangeMenuIsOpen) {
                        addDaterangeMenuIsOpen = !addDaterangeMenuIsOpen;
                        addDaterangeMenu.style.display = 'none';
                        addDaterangeButton.classList.remove('active');
                    } else {
                        addDaterangeMenuIsOpen = !addDaterangeMenuIsOpen;
                        addDaterangeMenu.style.display = 'flex';
                        addDaterangeButton.classList.add('active');
                    }
                }
            }

            let isMovieMenuOpen = false;
            function movieMenuOpenClose() {
                if (isMovieMenuOpen) {
                    movieMenuContainer.style.display = "none";
                } else {
                    movieMenuContainer.style.display = "flex";
                }
                isMovieMenuOpen = !isMovieMenuOpen;
            }

            const trailerInput = document.getElementById('TrailerURL');
            const trailerPreview = document.getElementById('trailerPreview');

            function getYoutubeID(url) {
                let id = url.match(/youtu\.be\/([^\?]+)/);
                if(id) {
                    return id[1];
                } else {
                    console.log("not an id");
                }
                id = url.match(/v=([^&]+)/);
                if(id) return id[1];
                return url; // fallback if they just paste the ID
            }

            trailerInput.addEventListener('input', getTrailer);

            function getTrailer() {
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
            }

            const movieMenuForm = document.getElementById('movieMenu');
            movieMenuForm.addEventListener("submit", async function(e) {
                e.preventDefault();

                const formData = new FormData(movieMenuForm);

                // Anything ASIDE from the Movie Poster !!!

                await fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie/${Movie_ID}`, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        MovieName: formData.get("MovieName"),
                        MovieDescription: formData.get("MovieDescription"),
                        Genre: formData.get("Genre"),
                        Rating: formData.get("Rating"),
                        Runtime: formData.get("Runtime"),
                        TrailerURL: formData.get("TrailerURL")
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error(error);
                })

                // THE MOVIE POSTER

                await fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie/${Movie_ID}/poster`, {
                    method: "POST",
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error(error);
                })                
        
                await getMovieInfo();
                movieMenuOpenClose();
            })

            movieMenuForm.addEventListener("keydown", function(e) {
                if (e.key === "Enter" && e.target.tagName === "INPUT") {
                    e.preventDefault();
                }
            })

            MoviePoster.addEventListener("change", function() {
                const file = this.files[0];

                if (file) {
                    const reader = new FileReader();

                    reader.addEventListener("load", function() {
                        posterPreview.setAttribute("src", this.result);
                        posterPreview.style.display = "block";
                        posterInput.classList.add('uploaded');
                        posterShow.classList.add('uploaded');
                    })
                    reader.readAsDataURL(file);
                }
            })
            
        </script>
        <script src="admin.js"></script>
    </body>
</html>