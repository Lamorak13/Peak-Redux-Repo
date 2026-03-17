

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
            display: flex;
            flex-direction: column;
            width: 100%;
            padding: 25px;
        }

        #availableMoviesText {
            font-weight: bold;
            font-size: 28;
            border-bottom: 3px solid #f6e8e0;
            padding: 0 15px 0 0;
            margin-bottom: 10px;
        }

        #availableMovies {
            display: flex;
            padding: 25px;
            gap: 25px;
        }

        .movieContainer {
            border: 3px solid #f6e8e0;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        .movieContainer:hover {
            transform: scale(1.05);
            cursor: pointer;
        }
        
        .movieContainer .moviePoster {
            width: 220px;
            height: 260px;
            object-fit: cover;
        }

        .movieContainer .movieName {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            font-weight: bold;
        }
        #addMovieButton {            
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
        #addMovieButton:hover {            
            background-color: white;
            color: black;
            border: 2px solid black;
            cursor: pointer;
        }
        #addMovieMenuContainer {
            position: absolute;
            display: none;
            width: 100%;
            height: 100%;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.5);
        }

        #addMovieMenu {
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
        #addMovieForm {
            padding: 8px;
            display: flex;
        }
        #addMovieForm #leftSection {
            border-right: 3px solid #f6e8e0;
            padding-right: 15px;
        }
        #addMovieForm #rightSection {
            padding-left: 15px;
        }

        #addMovieForm input:not([type="file"]), #movieDesc, #movieRating {
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
        
    </style>
    <body onload="getMovies()">
        <?php include("header_admin.php") ?>
        <main>
            <section id="moviesGallery">
                <div id="availableMoviesText">Available Movies</div>
                <span><button type="button" id="addMovieButton" onclick="openMovieMenu(true)">Add New Movie</button></span>
                <div id="availableMovies"></div>                
            </section>            
            <div id="addMovieMenuContainer" class="">
                <div id="addMovieMenu">
                    <button type="button" id="closeMovieMenu" onclick="openMovieMenu(false)">Back</button>
                    <form id = "addMovieForm" autocomplete="off">
                        <div id="leftSection"> 
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
                                <label for="TrailerUrl">Trailer URL: </label><br>
                                <input type="text" id="TrailerURL" name="TrailerURL" placeholder="Trailer Link" required>
                            </div>
                            <br>

                            <div>
                                <button type="submit" id="movieUpload" name="movieDetails" value="movieDetails">Upload</button>
                            </div>
                        </div>
                        <div>                            

                        <div id="rightSection">
                            <div>
                                <label for="moviePosterUp">Movie Poster: </label><br>
                                <input type="file" id="moviePosterUp" name="moviePosterUp" accept="image/png, image/jpeg, image/jpg" required>
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
            body = document.body;

            availableMovies = document.getElementById('availableMovies');
            function getMovies() {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        availableMovies.innerHTML = this.responseText;

                        const movieContainers = document.querySelectorAll('.movieContainer');
                        movieContainers.forEach(e => {
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
            addMovieMenuContainer = document.getElementById('addMovieMenuContainer');
            addMovieButton = document.getElementById('addMovieButton');
            function openMovieMenu(isOpen) {
                if (isOpen) {
                    addMovieButton.disabled = true;
                    addMovieMenuContainer.style.display = 'flex';
                } else {
                    addMovieButton.disabled = false;
                    addMovieMenuContainer.style.display = 'none'; 
                }
            }

            const moviePosterUp = document.getElementById("moviePosterUp");
            const posterPreview = document.getElementById("posterPreview");
            var uploadedPoster = "";

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

            addMovieForm = document.getElementById('addMovieForm');
            addMovieForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(addMovieForm);

                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log(this.responseText);
                        getMovies();
                        openMovieMenu(false);
                        addMovieForm.reset();
                        posterPreview.src = "";
                    }
                };
                xmlhttp.open("POST", "queries_admin.php?q=movieupload", true);
                xmlhttp.send(formData);
            })
        </script>
    </body>
</html>
