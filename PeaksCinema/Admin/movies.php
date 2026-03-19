

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Movies</title>
    </head>
    <body onload="getMovies()">
        <?php include("header_admin.php") ?>
        <main>
            <section id="moviesGallery" class="gallery">
                <div id="availableMoviesText" class="availableText">Available Movies</div>
                <span><button type="button" id="addMovieButton" class="generalAdminButton" onclick="openMovieMenu(true)">Add New Movie</button></span>
                <div id="availableMovies"></div>                
            </section>            
            <div id="addMovieMenuContainer" class="movieMenuContainer">
                <div id="addMovieMenu" class="movieMenu">
                    <div class="scrollContent">
                        <button type="button" id="closeMovieMenu" class="generalAdminButton" onclick="openMovieMenu(false)">Back</button>
                        <form id = "addMovieForm" class="movieForm" autocomplete="off">
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
                                    <input type="text" id="TrailerURL" name="TrailerURL" placeholder="Trailer Link" required><br><br>
                                    <div id="trailerPreviewText">Trailer Preview: </div>
                                    <div id="trailerPreview"></div> <!-- Preview container -->
                                </div>
                                <br>

                                <div>
                                    <button type="submit" id="movieUpload" class="generalAdminButton" name="movieDetails" value="movieDetails">Add</button>
                                </div>
                            </div>
                            <div id="rightSection">
                                <div>
                                    <label for="moviePosterUp">Movie Poster: </label><br>
                                    <input type="file" id="moviePosterUp" name="moviePosterUp" accept="image/png, image/jpeg, image/jpg" required><br><br>
                                </div>
                                <div>Poster Preview:</div>
                                <img id="posterPreview" src="">
                            </div>
                            </div>
                        </form>
                    </div>
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
    </body>
</html>
