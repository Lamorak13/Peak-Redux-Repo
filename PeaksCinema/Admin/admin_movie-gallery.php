<!DOCTYPE HTML>
<html>
    <body>
        <head>
            <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
            <title>Admin - Movie Gallery</title>
        </head>
        <main>
            <section id="movieGallerySection">
                <button type="button" class="generalAdminButton">Add New Movie</button>
                <div id="movieGallery"></div>
            </section>            
        </main>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
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
                            moviePoster.src = movie.MoviePoster;
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
            })
        </script>
    </body>
</html>