<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
    </head>
    <body>
        <?php include("admin_header.php"); ?>
        <main>
            <div id="loader" class="loader">
                <div>loading..</div>
            </div>
            <section id="content" style="display: none">
                <!-- <div id="areYouSureScreenContainer" style="display: none">
                    <div id="areYouSureScreen"></div>
                </div> -->
                <div id="movieDetailsContainer"></div>
                <div id="theaterDaterangeContainer">
                    <div id="daterangeTheaterSelectionContainer">
                        <div id="daterangeTheaterLabel">Date ranges for theater:</div>
                        <select id="theaterSelection"></select>
                    </div>
                    <!-- <div id="daterangeLoader" class="loader"><div>Loading...</div></div> -->
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
                        <button type="submit" id="movieSubmitButton" class="generalAdminButton">Add</button>
                    </div>
                </form>
            </div>
        </main>
        <script>
            const theaterSelection = document.getElementById("theaterSelection");

            const url = new URL(window.location.href);
            const Movie_ID = url.searchParams.get('movie_id');

            function getMovieInfo() {
                console.log(Movie_ID);

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
                        createDaterangeMenu();
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
                createDaterangeMenu();
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
                    } else {
                        let dateranges = data.data;

                        dateranges.forEach(daterange => {
                            const daterangeContainer = document.createElement('div');
                            daterangeContainer.classList.add('daterangeContainer');

                            const daterangeTop = document.createElement('div');
                            daterangeTop.classList.add('daterangeTop');                        

                            const daterangeProper = document.createElement('div');
                            daterangeProper.classList.add('daterangeProper');

                            const startDate = document.createElement('div');
                            startDate.classList.add('startDate');
                            startDate.textContent = daterange.StartDate + " - ";
                            daterangeProper.append(startDate);

                            const endDate = document.createElement('div');
                            endDate.classList.add('endDate');
                            endDate.textContent = daterange.EndDate;
                            daterangeProper.append(endDate);

                            daterangeTop.append(daterangeProper);

                            const deleteDaterange = document.createElement('button');
                            deleteDaterange.classList.add('deleteDaterange');
                            deleteDaterange.textContent = "Delete";
                            deleteDaterange.addEventListener("click", () => areYouSure("daterange", daterange.DateRange_ID, null, daterangeProper, theaterSelection.value));
                            // {
                            //     const areYouSureScreenContainer = document.createElement('div');
                            //     areYouSureScreenContainer.id = 'areYouSureScreenContainer';
                            //     areYouSureScreenContainer.style.display = "flex";
                            //     const areYouSureScreen = document.createElement('div');
                            //     areYouSureScreen.id = 'areYouSureScreen';
                            //     areYouSureScreen.textContent = "Do you really want to delete this date range?";

                            //     const cloneDaterangeProper = daterangeProper.cloneNode(true);
                            //     cloneDaterangeProper.classList.add('daterangeProper');

                            //     areYouSureScreen.append(cloneDaterangeProper);

                            //     const areYouSureScreenButtons = document.createElement('div');
                            //     areYouSureScreenButtons.classList.add('areYouSureScreenButtons');

                            //     const theBackButton = document.createElement('button');
                            //     theBackButton.classList.add('generalAdminButton');
                            //     theBackButton.id = 'theBackButton';
                            //     theBackButton.textContent = "Back";

                            //     theBackButton.addEventListener("click", function() {
                            //         areYouSureScreenContainer.remove();
                            //     })
                            //     areYouSureScreenButtons.append(theBackButton);

                            //     const deleteFinalButton = document.createElement('button');
                            //     deleteFinalButton.classList.add('deleteDaterange');
                            //     deleteFinalButton.textContent = "Yes, Delete";

                            //     deleteFinalButton.addEventListener("click", function() {
                            //         fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=daterange/${daterange.DateRange_ID}`, {
                            //             method: "DELETE"
                            //         })
                            //         .then(response => {
                            //             if (!response.ok) {
                            //                 throw new Error(`HTTP error! ${response.status}`);
                            //             }
                            //             return response.json();
                            //         })
                            //         .then(data => {
                            //             getDateranges();
                            //             areYouSureScreenContainer.delete();
                            //         })
                            //         .catch(error => {
                            //             console.error(error);
                            //         })
                            //     })
                            //     areYouSureScreenButtons.append(deleteFinalButton);
                            //     areYouSureScreen.append(areYouSureScreenButtons);
                            //     areYouSureScreenContainer.append(areYouSureScreen);
                            //     document.getElementById('content').append(areYouSureScreenContainer);
                            // })

                            daterangeTop.append(deleteDaterange);

                            daterangeContainer.append(daterangeTop);

                            const timeslotsContainer = document.createElement('div');
                            timeslotsContainer.classList.add('timeslotsContainer');
                            daterange.Timeslots.forEach(timeslot => {
                                const timeslotDiv = document.createElement('div');
                                timeslotDiv.classList.add('timeslotDiv');
                                timeslotDiv.textContent = timeslot.StartTime;
                                timeslotsContainer.append(timeslotDiv);
                            })
                            daterangeContainer.append(timeslotsContainer);

                            daterangesGallery.append(daterangeContainer);
                        })
                    }
                })                
                .catch(error => {
                    console.error(error);
                });                
            }
            
            function createDaterangeMenu() {
                let maxTimeslots = 5;
                let currentTimeslots = 1;

                addDateContainer.innerHTML = "";
                const everythingDateranges = document.createElement('div');
                everythingDateranges.classList.add('everythingDateranges');

                const addDaterangeButton = document.createElement('button');
                addDaterangeButton.classList.add('addDaterangeButton');
                addDaterangeButton.classList.add('generalAdminButton');
                addDaterangeButton.textContent = "Add New Date Range";
                everythingDateranges.append(addDaterangeButton);

                const addDaterangeMenu = document.createElement('form');
                addDaterangeMenu.id = 'addDaterangeMenu';
                addDaterangeMenu.style.display = 'none';

                // daterangeInputSpan (e.g. Start Date: [ ] - End Date: [ ] )
                const daterangeInputSpan = document.createElement('span');
                daterangeInputSpan.classList.add('daterangeInputSpan');

                const startDateInputLabel = document.createElement('label');
                startDateInputLabel.textContent = "Start Date: ";
                startDateInputLabel.htmlFor = "startDateInput";
                daterangeInputSpan.append(startDateInputLabel);
                const startDateInput = document.createElement('input');
                startDateInput.type = 'date';
                startDateInput.name = "StartDate";
                startDateInput.required = true;
                startDateInput.classList.add('dateInput');
                startDateInput.id = "startDateInput";
                daterangeInputSpan.append(startDateInput);

                daterangeInputSpan.append(" - ");

                const endDateInputLabel = document.createElement('label');
                endDateInputLabel.textContent = "End Date: ";
                endDateInputLabel.htmlFor = "endDateInput";
                daterangeInputSpan.append(endDateInputLabel);
                const endDateInput = document.createElement('input');
                endDateInput.type = 'date';
                endDateInput.name = "EndDate";
                endDateInput.classList.add('dateInput');
                endDateInput.id = "endDateInput";
                daterangeInputSpan.append(endDateInput);

                startDateInput.addEventListener("change", function() {
                    if (startDateInput.value) {
                        const minValue = new Date(startDateInput.value);
                        minValue.setDate(minValue.getDate() + 1);
                        endDateInput.setAttribute('min', minValue.toISOString().split('T')[0]);
                        if (minValue > new Date(endDateInput.value)) {
                            endDateInput.value = "";
                        }
                    } else {
                        endDateInput.removeAttribute('min');
                    }                   
                })

                daterangeInputSpan.append("optional");
                
                addDaterangeMenu.append(daterangeInputSpan);

                // daterangeTimeslotInputs (e.g. (9:00) (10:30) (11:45) (+) )

                const daterangeTimeslotInputsPlus = document.createElement('span');
                daterangeTimeslotInputsPlus.classList.add('daterangeTimeslotInputsPlus');
                
                const daterangeTimeslotInputs = document.createElement('span');
                daterangeTimeslotInputs.classList.add('daterangeTimeslotInputs');
                daterangeTimeslotInputsPlus.append(daterangeTimeslotInputs);
                
                let minTime = "";
                function createTimeslot() {
                    const timeslotInputSpan = document.createElement('span');
                    timeslotInputSpan.classList.add('timeslotInputSpan');

                    const timeslotInput = document.createElement('input');
                    timeslotInput.type = 'time';
                    timeslotInput.name = "timeslot";
                    timeslotInput.classList.add('timeslotInput');
                    timeslotInput.addEventListener("change", function() {
                        console.log("not rn");
                    })
                    timeslotInputSpan.append(timeslotInput);

                    if (currentTimeslots != 1) {
                        const timeslotDelete = document.createElement('button');
                        timeslotDelete.type = 'button';
                        timeslotDelete.classList.add('timeslotDelete');
                        timeslotDelete.textContent = "X";
                        timeslotDelete.addEventListener("click", function() {
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
                timeslotAddButton.addEventListener("click", function() {
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

                //

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

                //

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

                //

                const addDaterangeMenuSubmit = document.createElement('button');
                addDaterangeMenuSubmit.type = 'submit';
                addDaterangeMenuSubmit.textContent = "Add Date Range";
                addDaterangeMenuSubmit.classList.add('addDateRangeMenuSubmit');
                addDaterangeMenuSubmit.classList.add('generalAdminButton');
                addDaterangeMenu.append(addDaterangeMenuSubmit);

                //
                
                addDaterangeMenu.addEventListener("submit", function(e) {
                    e.preventDefault();

                    const formData = new FormData(addDaterangeMenu);
                    const Theater_ID = theaterSelection.value;

                    let timeslots = formData.getAll('timeslot').filter(t => t !== "");
                    let allTimeslots = [];
                    let start = new Date(formData.get("StartDate"));
                    let end = formData.get("EndDate") ? new Date(formData.get("EndDate")) : null;
                    if (!end) {
                        let dateStr = start.toLocaleDateString('en-CA');
                        timeslots.forEach(time => {
                            allTimeslots.push({ date: dateStr, timeslot: time});
                        });
                    } else {
                        for (var d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
                            let dateStr = new Date(d).toLocaleDateString('en-CA');
                            timeslots.forEach(time => {
                                allTimeslots.push({ date: dateStr, timeslot: time});
                            });
                        }
                    }

                    const payload = {
                        StartDate: start.toISOString().split('T')[0],
                        EndDate: end ? end.toISOString().split('T')[0] : null,
                        timeslots: allTimeslots,
                        ScreeningType: formData.get("ScreeningType"),
                        SeatPrice: Number(formData.get("SeatPrice"))
                    }

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

                //

                everythingDateranges.append(addDaterangeMenu)
                addDateContainer.append(everythingDateranges);

                let addDaterangeMenuIsOpen = false;
                addDaterangeButton.addEventListener("click", daterangeMenuOpenClose);

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
            movieMenuForm.addEventListener("submit", function(e) {
                e.preventDefault();

                const formData = new FormData(movieMenuForm);

                // Anything ASIDE from the Movie Poster !!!

                fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie/${Movie_ID}`, {
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
                
                if (formData.get("MoviePoster").size === 0) {                    
                    movieDetailsContainer.innerHTML = "";
                    getMovieInfo();
                    movieMenuOpenClose();
                } else {
                    fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie/${Movie_ID}/poster`, {
                        method: "POST",
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        movieDetailsContainer.innerHTML = "";
                        getMovieInfo();
                        movieMenuOpenClose();
                    })
                    .catch(error => {
                        console.error(error);
                    })
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