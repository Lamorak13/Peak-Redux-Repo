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
                <div id="movieDetailsContainer"></div>
                <div id="theaterDaterangeContainer">
                    <div id="daterangeTheaterSelectionContainer">
                        <div id="daterangeTheaterLabel">Date ranges for theater:</div>
                        <select id="theaterSelection"></select>
                    </div>
                    <div id="daterangeLoader" class="loader"><div>Loading...</div></div>
                    <div id="daterangeContent" style="display:none">
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
                    let movie = data.data[0];
                    
                    document.title = "Admin - " + movie.MovieName;

                    const movieDetailsContainer = document.getElementById('movieDetailsContainer');
                    
                    const movieTopDetails = document.createElement('div');
                    movieTopDetails.classList.add('movieTopDetails');

                    // For the Movie Poster + Trailer

                    const moviePosterTrailer = document.createElement('div');
                    moviePosterTrailer.classList.add('moviePosterTrailer');
                    
                    const moviePoster = document.createElement('img')
                    moviePoster.src = movie.MoviePoster;
                    moviePoster.alt = movie.MovieName;
                    moviePoster.classList.add('moviePoster');
                    moviePosterTrailer.append(moviePoster);
                    
                    const movieTrailerURL = document.createElement('button');
                    movieTrailerURL.textContent = "Youtube Trailer";
                    movieTrailerURL.addEventListener("click", function() {
                        window.location.href = movie.TrailerURL;
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
                })
                .catch(error => {
                    console.error(error);
                });

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
                    let theaters = data.data;

                    theaters.forEach(theater => {
                        const option = document.createElement('option');
                        option.textContent = theater.TheaterName;
                        option.value = theater.Theater_ID;

                        theaterSelection.append(option);
                    })
                    
                    getDateranges(theaterSelection.value);
                })
                .catch(error => {
                    console.error(error);
                });

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

            theaterSelection.addEventListener("change", function() {
                getDateranges(theaterSelection.value);
                console.log(theaterSelection.value);
            })

            function getDateranges(Theater_ID) {
                const daterangeLoader = document.getElementById('daterangeLoader');
                const daterangeContent = document.getElementById('daterangeContent');

                daterangeLoader.style.display = 'flex';
                daterangeContent.style.display = 'none';

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
                    setTimeout(() => {
                        daterangeLoader.style.display = 'none';
                        daterangeContent.style.display = 'flex';
                    }, 250);
                })                
                .catch(error => {
                    console.error(error);
                });                
            }

            // const addDateButton = document.getElementById('addDateButton');
            // const addDateMenu = document.getElementById('addDateMenu');

            // let isOpen = false;
            // addDateButton.addEventListener("click", function() {
            //     if (isOpen) {
            //         addDateMenu.style.visibility = "hidden";
            //     } else {
            //         addDateMenu.style.visibility = "visible";
            //     }                
            //     isOpen = !isOpen
            // })
        </script>
    </body>
</html>