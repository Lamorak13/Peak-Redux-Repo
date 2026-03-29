<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
        <title>Movie</title>
    </head>
    <body>
        <main>
            <div id="movieDetailsContainer"></div>
        </main>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const url = new URL(window.location.href);
                const Movie_ID = url.searchParams.get('movie_id');
                console.log(Movie_ID);

                fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie/${Movie_ID}/`, {
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

                    const movieDetailsContainer = document.getElementById('movieDetailsContainer');
                    const moviePoster = document.createElement('img')
                    moviePoster.src = movie.MoviePoster;
                    moviePoster.alt = movie.MovieName;
                    moviePoster.classList.add('moviePoster');
                    movieDetailsContainer.append(moviePoster);

                    const movieName = document.createElement('div');
                    movieName.textContent = movie.MovieName;
                    movieDetailsContainer.append(movieName);

                    const movieDescription = document.createElement('div');
                    movieDescription.textContent = movie.MovieDescription;
                    movieDetailsContainer.append(movieDescription);

                    const movieGenre = document.createElement('div');
                    movieGenre.textContent = movie.Genre;
                    movieDetailsContainer.append(movieGenre);

                    const movieRating = document.createElement('div');
                    movieRating.textContent = movie.Rating;
                    movieDetailsContainer.append(movieRating);

                    const movieRuntime = document.createElement('div');
                    movieRuntime.textContent = movie.Runtime + " minutes";
                    movieDetailsContainer.append(movieRuntime);

                    const movieTrailerURL = document.createElement('div');
                    movieTrailerURL.textContent = movie.TrailerURL + " minutes";
                    movieDetailsContainer.append(movieTrailerURL);
                })
                .catch(error => {
                    console.error(error);
                });
            })
        </script>
    </body>
</html>