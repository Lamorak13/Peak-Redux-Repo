

<html>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-size: cover;
        }
        body.active {
            background-color: rgba(43, 2, 2, 0.47);
        }

        main {
            display: flex;
            color: white;
            background-color: #122729;
            height: 100vh;
        }

        #moviesGallery {
            padding: 25px;
        }

        #moviesContainer {
            display: flex;
        }
        
        .movieCard .moviePoster {            
            width: 100%;
            height: 260px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
        }
        .movieCard .movieName {
            margin: 10px 0 8px;
            font-weight: 600;
            font-size: 0.92rem;
            min-height: 2.2em;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1.3;
        }

        #addMovieMenuContainer {
            position: absolute;
            display: none;
            width: 100%;
            height: 100%;
            align-items: center;
            justify-content: center;
        }
        #addMovieMenu {
            display: none;
            position: absolute;
            padding: 20px;
            border-radius: 15px;
            background-color: rgba(0, 0, 0, 0.5);
            color: #F9F9F9;
            font-weight: bold;
        }
        #closeMovieMenu {
            border: 3px solid white;
            border-radius: 15px;
            padding: 7px;
            margin: 0 0 5px 0;
            font-weight: bold;
        }
        #addMovieForm {
            padding: 8px;
        }
        #addMovieForm input:not([type="file"]), #movieDesc, #movieRating {
            border: 2px solid #ff4d4d;
            border-radius: 7px;
            padding: 3px;
        }
    </style>
    <body onload="getMovies()">
        <?php include("header_admin.php") ?>
        <main>
            <section id="moviesGallery">
                <span><button type="button" id="addMovieButton" onclick="openMovieMenu(true)">Add New Movie</button></span>
                <div id="moviesContainer"></div>
                
            </section>            
            <div id="addMovieMenuContainer" class="">
                <div id="addMovieMenu">
                    <button type="button" id="closeMovieMenu" onclick="openMovieMenu(false)">Back</button>
                    <form id = "addMovieForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
                        <div>
                            <label for="movieName">Movie Name: </label>
                            <input type="text" id="movieName" name="movieName" placeholder="Movie Name" required>
                        </div>
                        <br>

                        <div>
                            <label for="movieDesc">Movie Description: </label><br>
                            <textarea id="movieDesc" name="movieDesc" rows="10" cols="75" placeholder="Movie Description" required></textarea>
                        </div>
                        <br>

                        <div>
                            <label for="movieGenre">Movie Genre(s): </label><br>
                            <input type="text" id="movieGenre" name="movieGenre" placeholder="Movie Genre" required>
                        </div>
                        <br>

                        <div>
                            <label for="movieRating">Movie Rating: </label><br>
                            <select name="movieRating" id="movieRating">
                                <option value="">Select a rating:</option>
                                <option value="G">Rated G</option>
                                <option value="PG">Rated PG</option>
                                <option value="R-13">Rated R-13</option>
                                <option value="R-16">Rated R-16</option>
                                <option value="R-18">Rated R-18</option> 
                            </select>
                        </div>
                        <br>

                        <div>
                            <label for="movieRuntime">Movie Runtime (in minutes): </label>
                            <input type="number" id="movieRuntime" name="movieRuntime" placeholder="Runtime (in minutes)" min="0" required>
                        </div>
                        <br>

                        <div>
                            <label for="moviePosterUp">Movie Poster: </label><br>
                            <input type="file" id="moviePosterUp" name="moviePosterUp" accept="image/png, image/jpeg, image/jpg" required>
                        </div>
                        <br>

                        <div>
                            <label for="TrailerUrl">Trailer URL: </label><br>
                            <input type="text" id="TrailerURL" name="TrailerURL" placeholder="Trailer Link" required>
                        </div>
                        <br>

                        <div>
                            <button type="submit" name="movieDetails" value="movieDetails">Upload</button>
                        </div>
                    </form>
            </div>
            </div>
            
        </main>
        <script>
            body = document.body;

            moviesContainer = document.getElementById('moviesContainer');
            function getMovies() {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        moviesContainer.innerHTML = this.responseText;

                        const movieCards = document.querySelectorAll('.movieCard');
                        movieCards.forEach(e => {
                            e.addEventListener("click", function() {
                                window.location.href = 'movie_timeslot.php?id=' + e.id;
                            })
                        });
                    }                    
                };                
                xmlhttp.open("GET", "queries_admin.php?q=movies", true);
                xmlhttp.send();
            }

            

            addMovieMenu = document.getElementById('addMovieMenu');
            addMovieButton = document.getElementById('addMovieButton');
            function openMovieMenu(isOpen) {
                if (isOpen) {
                    addMovieButton.disabled = true;
                    body.classList.add("active");
                    addMovieMenu.style.display = 'block';
                } else {
                    addMovieButton.disabled = false;
                    body.classList.remove("active");
                    addMovieMenu.style.display = 'none'; 
                }
            }
        </script>
    </body>
</html>
