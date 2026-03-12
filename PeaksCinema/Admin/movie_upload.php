<?php 
    include("peakscinemas_database.php");
    // Creates a directory for the movie posters in case it doesn't exist yet
    $posterFolder = $_SERVER['DOCUMENT_ROOT'] . '/PeaksCinema/MoviePosters';
    if (!is_dir($posterFolder)) {
        mkdir($posterFolder, 0755, true);
    }

    // empty variables for later use
    $MovieName = $MovieDescription = $Genre = $Rating = $Runtime = $MoviePoster = $MovieAvailability = "";


    // kung nagsubmit nung admin nung form tapos nandun rin nung poster
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["moviePosterUp"])) {
        // input cleanup func for later use   
        function input_cleanup($data) {
        $data = trim($data);
        $data = stripslashes($data);
        return $data;
        }

        // prepared statement for later use
        $stmt = $conn -> prepare("INSERT INTO movie(MovieName, MovieDescription, Genre, Rating, Runtime, MoviePoster, MovieAvailability)
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt -> bind_param("ssssiss", $MovieName, $MovieDescription, $Genre, $Rating, $Runtime, $MoviePoster, $MovieAvailability);
        
        // more input cleanup yayyyyy
        $MovieName = input_cleanup($_POST['movieName']);
        $MovieDescription = input_cleanup($_POST['movieDesc']);
        $Genre = input_cleanup($_POST['movieGenre']);
        $Rating = input_cleanup($_POST['movieRating']);
        $Runtime = input_cleanup($_POST['movieRuntime']);        

        // this makes a "path" to the uploaded file
        $temp = $_FILES['moviePosterUp']['tmp_name'];
        // this cuts off the file type from the image name. like usually file names are like "poster.png". this line of code gets just the png
        $fileType = pathinfo($_FILES['moviePosterUp']['name'], PATHINFO_EXTENSION);
        // this basically renames the file to match the movie name. if the movie name is Superman, this line of code would make it Superman.png as the file name
        $fileName = $MovieName . "." . $fileType;
        // This then creates the path where the file will be created in
        $endPath = $posterFolder . "/" . $fileName;

        // This moves the temporary file to an actual folder, which is the end path
        if (move_uploaded_file($temp, $endPath)) {
            $MoviePoster = 'PeaksCinema/MoviePosters/' . $fileName; // If it successfully uploads the file, it creates a path to the file, and then sends that path to the database. if all goes well, it should show up on the actual website
        } else {
            echo "There was an error with uploading the poster. Please try again. ";
        }

        $MovieAvailability = input_cleanup($_POST['movieAvailability']); 

        // actual execution of the prepared statement with the new information
        $stmt -> execute();
    }

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
    <head>
        <style>
            body {
                display: flex;
                flex-direction: column;
                align-items: center;
                min-height: 100vh;
                margin: 0;      
                background: linear-gradient(90deg,rgba(106, 127, 63, 1) 0%, rgba(74, 106, 90, 1) 100%);
                padding-top: 150px;
            }

            header {
                border: 4px solid black;
                border-bottom: none;
                border-top-left-radius: 25px;
                border-top-right-radius: 25px;
                background: rgba(255, 255, 255, 0.8);
                overflow: hidden;
                padding: 0px;
            }

            nav {
                display: flex;
            }

            a {
                padding: 5px 10px;
                text-decoration: none;
                border-radius: 10px 10px 0 0;
                border-bottom: none;
                color: black;
            }

            a:hover {
                background: rgba(70, 58, 58, 0.8);
                color: white;
            }

            main {
                display: flex;
                border: 4px solid black;
                border-radius: 50px;
                overflow: hidden;
                background: rgba(255, 255, 255, 0.8);
                padding: 20px;
            }

            body.movieUpload main{
                display: flex;
            }

            body.movieUpload section {
                width:50%;
            }

            body.movieUpload #movieFormSection {
                width: auto;
                padding: 20px;
                border-right: 4px solid black;
            }

            body.movieUpload #posterPreviewSection {
                width: auto;
                padding: 20px;
            }

            body.movieUpload #posterPreview {
                width: 100%;
                height: 280px;
                object-fit: cover;
                border-radius: 6px;
                background-color: #fff;
                border: 2px solid black; 
                display: none;
            }

            input, textarea, select, button {
                border-radius: 15px;
                padding: 5px;
            }

        </style>
    </head>
    <body class = "movieUpload">
        <header>
            <nav>
                <a href="dashboard.php" target="_self">Dashboard</a>
                <a href="malls_selection_admin.php" target="_self">Malls</a>
                <a href="movie_upload.php" target="_self">Movie Upload</a>
                <a href="theater_upload.php" target="_self">Theater Upload</a>
                <a href="mall_upload.php" target="_self">Mall Upload</a>
            </nav>
        </header>
        <main>
            <section id="movieFormSection">
                <form id = "movieDetails" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
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
                        <label for="movieGenre">Movie Genre: </label><br>
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
                        <label for="movieAvailability">Movie Availability: </label>
                        <select id = "movieAvailability" name = "movieAvailability" required>
                            <option value = "Now Showing">Now Showing</option>
                            <option value = "Coming Soon">Coming Soon</option>
                        </select>
                    </div>
                    <br>

                    <div>
                        <button type="submit" name="movieDetails" value="movieDetails">Upload</button>
                    </div>
                </form>
            </section>
            <section id="posterPreviewSection">
                <h2>Poster Preview:</h2>
                <img id="posterPreview" src="" alt="Poster Preview">
            </section>                     
        </main>
        
        <!-- javascript-->
        <script> 
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
        </script>
    </body>
</html>