<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
    </head>
    <body>
        <main>
            <div id="cashierFundamentalsContainer">
                <section id="cashierFundamentals">
                    <div id="currentDateTime"></div>
                    <div id="cashierFlow">
                        <select id="selectMovies"></select>
                        <select id="selectTheaters"><option value=null>Please select a theater.</option></select>
                        <select id="selectTimeslots"><option value=null>Please select a timeslot.</option></select>
                    </div>
                </section>
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
            currentDateTime.textContent = "Good day! Today is currently " + today;

            // 1. Get Movies!
            document.addEventListener("DOMContentLoaded", function() {
                fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie')
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



                fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie/${Movie_ID}/theaters&date=${today}`)
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

                fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie/${Movie_ID}/theater/${Theater_ID}&date=${today}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("test");
                    timeslots = data.data;
                    selectTimeslots.innerHTML = "";

                    if (Array.isArray(timeslots)) {
                        const option = document.createElement("option");
                        option.textContent = "Please select a timeslot.";
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
        </script>
    </body>
</html>