<!DOCTYPE HTML>
<html>
    <head>
        <script src="admin_gate.js"></script>
        <script>admin_gate.gatekeep(1); </script>
        <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
        <title>Admin - Movie Gallery</title>
    </head> 
    <body>               
        <?php include("admin_header.php"); ?>
        <main>
            <section id="movieGallerySection" class="gallerySection">
                <button type="button" class="generalAdminButton" id="addMovieButton" onclick="movieMenuOpenClose()">Add New Movie</button>
                <div id="movieGallery" class="gallery"></div>
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
                                    <label for="MoviePoster" id="posterInput">
                                        <span id="posterShow">Upload Poster</span>
                                        <img id="posterPreview" src="" style="display: none">
                                    </label>
                                    <input type="file" id="MoviePoster" name="MoviePoster" accept="image/png, image/jpeg, image/jpg" required>                                 
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
            document.addEventListener("DOMContentLoaded", getMovies())

            function getMovies() {
                document.getElementById('movieGallery').innerHTML = "";
                fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie', {
                    method: "GET"                    
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    let movies = data.data;

                    if (Array.isArray(movies)) {
                        movies.forEach(movie => {
                            const movieContainer = document.createElement('div');
                            movieContainer.classList.add('movieContainer');
                            movieContainer.addEventListener("click", function() {
                                window.location.href = 'admin_movie-specific.php?movie_id=' + movie.Movie_ID;
                            })

                            const moviePoster = document.createElement('img')
                            moviePoster.src = "/" + movie.MoviePoster;
                            moviePoster.alt = movie.MovieName;
                            moviePoster.classList.add('moviePoster');
                            movieContainer.append(moviePoster);

                            const movieName = document.createElement('div');
                            movieName.textContent = movie.MovieName;
                            movieContainer.append(movieName);

                            document.getElementById('movieGallery').append(movieContainer);
                        })
                    } else {
                        console.warn(data.data);
                        movieGallery.innerText = data.data;
                    }
                })
                .catch(error => {
                    console.error(error);
                });
            }

            const addMovieButton = document.getElementById('addMovieButton');
            const movieMenuContainer = document.getElementById('movieMenuContainer');
            let isMovieMenuOpen = false;

            function movieMenuOpenClose() {
                if (isMovieMenuOpen) {
                    movieMenuContainer.style.display = "none";
                } else {
                    movieMenuContainer.style.display = "flex";
                }
                isMovieMenuOpen = !isMovieMenuOpen;
            }

            let currentText = "";
            const posterInput = document.getElementById('posterInput');
            const posterInputProper = document.getElementById('MoviePoster');
            const posterPreview = document.getElementById('posterPreview');
            posterInput.addEventListener("mouseenter", function() {
                currentText = posterPreview.alt;
                
                posterPreview.alt = "Insert Poster";
            })
            posterInput.addEventListener("mouseleave", function() {                
                posterPreview.alt = currentText;
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

            const movieMenuForm = document.getElementById('movieMenu');
            movieMenuForm.addEventListener("submit", function(e) {
                e.preventDefault();

                const formData = new FormData(movieMenuForm);

                fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie/', {
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
                    console.log(data.status);
                    posterPreview.src = "";
                    posterInput.classList.remove('uploaded');
                    posterShow.classList.remove('uploaded');
                    getMovies();
                    movieMenuOpenClose();
                    movieMenuForm.reset();

                })
                .catch(error => {
                    console.error(error);
                })
            })

            movieMenuForm.addEventListener("keydown", function(e) {
                if (e.key === "Enter" && e.target.tagName === "INPUT") {
                    e.preventDefault();
                }
            })
        </script>
    </body>
</html>