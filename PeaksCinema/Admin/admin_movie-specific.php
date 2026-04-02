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
        </main>
        <script>
            const theaterSelection = document.getElementById("theaterSelection");

            const url = new URL(window.location.href);
            const Movie_ID = url.searchParams.get('movie_id');

            document.addEventListener("DOMContentLoaded", function() {
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
                    moviePoster.src = "/" + movie.MoviePoster;
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

                    const editMovieButton = document.createElement('button');
                    editMovieButton.classList.add('generalAdminButton');
                    editMovieButton.textContent = "Edit Movie Details";
                    movieDetailsContainer.append(editMovieButton);

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
            })

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
                startDateInput.classList.add('dateInput');
                startDateInput.id = "startDateInput";
                daterangeInputSpan.append(startDateInput);

                daterangeInputSpan.innerHTML += " - ";

                const endDateInputLabel = document.createElement('label');
                endDateInputLabel.textContent = "End Date: ";
                endDateInputLabel.htmlFor = "endDateInput";
                daterangeInputSpan.append(endDateInputLabel);
                const endDateInput = document.createElement('input');
                endDateInput.type = 'date';
                endDateInput.classList.add('dateInput');
                endDateInput.id = "endDateInput";
                daterangeInputSpan.append(endDateInput);
                
                addDaterangeMenu.append(daterangeInputSpan);

                // daterangeTimeslotInputs (e.g. (9:00) (10:30) (11:45) (+) )

                const daterangeTimeslotInputsPlus = document.createElement('span');
                daterangeTimeslotInputsPlus.classList.add('daterangeTimeslotInputsPlus');
                
                const daterangeTimeslotInputs = document.createElement('span');
                daterangeTimeslotInputs.classList.add('daterangeTimeslotInputs');
                daterangeTimeslotInputsPlus.append(daterangeTimeslotInputs);
                
                function createTimeslot() {
                    const timeslotInputSpan = document.createElement('span');
                    timeslotInputSpan.classList.add('timeslotInputSpan');

                    const timeslotInput = document.createElement('input');
                    timeslotInput.type = 'time';
                    timeslotInput.classList.add('timeslotInput');
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

                const addDaterangeMenuSubmit = document.createElement('button');
                addDaterangeMenuSubmit.type = 'submit';
                addDaterangeMenuSubmit.textContent = "Add Date Range";
                addDaterangeMenuSubmit.classList.add('addDateRangeMenuSubmit');
                addDaterangeMenuSubmit.classList.add('generalAdminButton');
                addDaterangeMenu.append(addDaterangeMenuSubmit);

                //

                everythingDateranges.append(addDaterangeMenu)
                addDateContainer.append(everythingDateranges);

                let addDaterangeMenuIsOpen = false;
                addDaterangeButton.addEventListener("click", function() {
                    if (addDaterangeMenuIsOpen) {
                        addDaterangeMenuIsOpen = !addDaterangeMenuIsOpen;
                        addDaterangeMenu.style.display = 'none';
                        addDaterangeButton.classList.remove('active');
                    } else {
                        addDaterangeMenuIsOpen = !addDaterangeMenuIsOpen;
                        addDaterangeMenu.style.display = 'flex';
                        addDaterangeButton.classList.add('active');
                    }
                })
            }
            
        </script>
        <script src="admin.js"></script>
    </body>
</html>