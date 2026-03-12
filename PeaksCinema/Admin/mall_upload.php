<?php 
    include("peakscinemas_database.php");

    // empty variables for later use
    $MallName = $Location = "";
    $uploadMessage = "";

    // kung naupload na nung form ito mangyayari
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        // to clean up the inputted information
        function input_cleanup($data) {
        $data = trim($data);
        $data = stripslashes($data);
        return $data;
        }

        // prepared statement for later use
        $stmt = $conn -> prepare("INSERT INTO mall(MallName, Location)
                                  VALUES (?, ?)");
        $stmt -> bind_param("ss", $MallName, $Location);
        
        // admin input cleanup
        $MallName = input_cleanup($_POST['mallName']);
        $Location = input_cleanup($_POST['location']);
        // tapos actual execution ng prepared statement
        try {
            $stmt -> execute();

            $uploadMessage = "Successfully uploaded " . htmlspecialchars($MallName);
        } catch (Exception $e) {
            $uploadMessage = "There was a problem with uploading the mall. Please try again and make sure you have completed all fields.";
        }
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

            body main{
                display: flex;
            }

            body #mallDetailsSection {
                display: flex;
                flex-direction: column;
                border: 4px solid black;
                border-radius: 50px;
                overflow: hidden;
                background: rgba(255, 255, 255, 0.8);
                padding: 20px;
            }

            input, textarea, select, button {
                border-radius: 15px;
                padding: 5px;
            }
        </style>
    </head>
    <body>
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
            <section id = mallDetailsSection>
                <form id = "mallDetails" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <div>
                        <label for="mallName">Mall Name: </label>
                        <input type="text" id="mallName" name="mallName" placeholder="Mall Name" required>
                    </div>
                    <br>

                    <div>
                        <label for="location">Mall Location: </label><br>
                        <textarea id="location" name="location" rows="10" cols="75" placeholder="Mall Location" required></textarea>
                    </div>
                    <br>

                    <div>
                        <button type="submit" name="movieDetails" value="movieDetails">Upload</button>
                    </div>
                </form>
                <br>
                
                <div>
                    <?php echo $uploadMessage; ?>
                </div>
            </section>
        </main>
    </body>
</html>