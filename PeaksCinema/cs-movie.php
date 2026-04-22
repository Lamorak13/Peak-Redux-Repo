<?php
    include("peakscinemas_database.php");

    $Movie_ID = filter_input(INPUT_GET, 'movie_id', FILTER_VALIDATE_INT);

    if (!$Movie_ID) {
        header("Location: home.php");
        exit;
    }

    $stmt = $conn -> prepare("SELECT * FROM movie WHERE Movie_ID = ?");
    $stmt -> bind_param("i", $Movie_ID);
    $stmt -> execute();
    $movieDetails = ($stmt -> get_result()) -> fetch_assoc();

    if (!$movieDetails) {
        header("Location: home.php");
        exit;
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="site.css">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            header {
                background-color: #a3c2b1;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 30px;
                border-bottom: 3px solid #4b4b4b;
            }

            body {
                font-family: 'Segoe UI', Arial, sans-serif;
                background-color: #2b2b2b;
                color: white;
            }

            .logo img {
                height: 45px;
                width: auto;
            }

            nav {
                display: flex;
                gap: 10px;
            }

            nav a {
                background-color: #4b4b4b;
                color: white;
                text-decoration: none;
                padding: 8px 15px;
                border-radius: 10px;
                border: 1px solid #a3c2b1;
                transition: 0.3s;
            }

            nav a:hover,
            nav a.active {
                background-color: #a3c2b1;
                color: #2b2b2b;
            }

            .main-container {
                background-color: #a3c2b1;
                margin: 40px auto;
                width: 85%;
                padding: 30px;
                border-radius: 10px;
                border: 3px solid #4b4b4b;
            }

            .tabs {
                display: flex;
                justify-content: center;
                margin-bottom: 15px;
            }

            .tab {
                background-color: #4b4b4b;
                color: white;
                padding: 10px 25px;
                border-radius: 10px 10px 0 0;
                margin: 0 3px;
                cursor: pointer;
                font-weight: bold;
                border: 1px solid #4b4b4b;
            }

            .tab.active {
                background-color: #a3c2b1;
                color: #2b2b2b;
                border-bottom: none;
            }

            /* Top Link Stuff */

            #topLinkSection {
                width: 50%;
                margin: 10px auto;
                display: flex;
                align-items: center;
                gap: 20px;
                border-radius: 10px;
            }

            .topLink {
                border-radius: 10px;
                width: auto;
                align-items: center;
                text-align: center;
            }

            .topLink a {
                background-color: #4b4b4b;
                color: white;
                text-decoration: none;
                padding: 8px 15px;
                border-radius: 10px;
                border: 1px solid #a3c2b1;
                transition: 0.3s;
            }

            .topLink a#active {
                background-color: #a3c2b1;
                color: white;
                text-decoration: none;
                padding: 8px 15px;
                border-radius: 10px;
                border: 1px solid #a3c2b1;
                font-weight: bold;
                color: #2b2b2b;
            }

            .topLink a:hover,
            .topLink a.active {
                background-color: #a3c2b1;
                color: #2b2b2b;
            }

            /* movie stuff*/

            #movieDetailsSection {
                background-color: #a3c2b1;
                padding: 20px;
                border: 5px solid black;
                width: 50%;
                margin: 5px auto;
                display: flex;
                align-items: flex-start;
                gap: 20px;
                border-radius: 10px;
                color: #363635;
            }

            .posterCard img {
                width: 100%;
                height: 280px;
                object-fit: cover;
                border-radius: 6px;
                background-color: #fff;
            }

            .movieInfo {
                flex: 1;
                display: flex;
                flex-direction: column;
                min-height: 280px;
            }

            .movieInfo h1 {
                margin-top: 0;
                font-size: 24px;
            }

            .movieInfo .bottomDetails {
                margin-top: auto;
            }

            .movieInfo .bottomDetails p {
                margin: 6px 0;
            }

            /* For The Mall Cards */

            * {
                box-sizing: border-box;
            }

            main {
                display: flex;
                flex-direction: column;
            }

            #availableMallsText {
                margin: 0px auto;
                display: inline-block;
                width: 100%;
                align-items: left;
                border-radius: 10px;
                font-weight: bold;
                font-size: 18px;
                color: #363635;
            }

            #availableMalls {
                background-color: #a3c2b1;
                padding: 20px;
                border: 5px solid black;
                width: 50%;
                margin: 10px auto;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 10px;
                border-radius: 10px;
                color: #595A4A;
            }

            .dateContainer {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 3px;
            }

            label[for="screeningDate"] {
                font-weight: bold;
                color: #363635;
            }

            input[type="date"] {
                background-color: #F0D2D1;
                font-size: 15px;
                border: 3px solid #252525ff;
                border-radius: 5px;
                padding: 7px;
            }
            
            #mallsContainer {
                display: flex;
                width: 100%;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 5px;
            }

            .mallCard {
                display: flex;
                flex-direction: column;
                width: 80%;
                border: 5px solid #252525ff;
                border-radius: 10px;
                min-width: 35%;
                height: auto;
                overflow: hidden;
                background-color: #595A4A;
            }

            .mallTypesContainer {
                display: flex;
                width: 100%;
                min-width: 35%;
                height: auto;
                overflow: hidden;
                background-color: #595A4A;
                border-bottom: 5px solid #252525ff;
            }

            .mallLocation {
                display: flex;
                width: 100%;
                min-width: 35%;
                height: auto;
                overflow: hidden;
                background-color: #595A4A;
                color: #FFFFFF;
                font-weight: bold;
                padding: 5px;
                align-items: center;
            }

            .mallName {
                padding: 15px;
                width: 25%;
                display: flex;
                justify-content: center;
                text-align: center;
                align-items: center;
                box-sizing: border-box;
                background-color: #FFFFFF;
                color: #595A4A;
                font-weight: bold;
                border-right: 5px solid #252525ff;
            }

            .detailContainer {
                display: flex;
                flex: 1 1 auto;
                flex-direction: column;
                box-sizing: border-box;
                height: auto;
                padding: 3px;
            }

            .screeningTypesContainer {
                display: flex;
                border-bottom: 4px solid #252525ff;
                min-height: 50%;
                flex-wrap: wrap;
                align-items: flex-start;
                height: auto;
                padding: 3px;
                gap: 5px;
            }

            .screeningType {
                display: inline-flex;
                border: 3px solid #252525ff;
                border-radius: 10px;
                padding: 5px;
                margin-bottom: 5px;
                background-color: #FFFFFF;
                color: #595A4A;
                font-weight: bold;
            }

            .theaterTypesContainer {
                display: flex;
                min-height: 50%;
                flex-wrap: wrap;
                align-items: flex-start;
                height: auto;
                padding: 3px;
                gap: 5px;
            }

            .theaterType {
                display: inline-flex;
                border: 3px solid #252525ff;
                border-radius: 10px;
                padding: 5px;
                margin-top: 5px;
                background-color: #FFFFFF;
                color: #595A4A;
                font-weight: bold;
            }

            #screeningsMsg {
                color: #595A4A;
                font-weight: bold;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <header>
            <div class="logo">
                <img src="peakscinema transparent.png" alt="PeaksCinemas Logo">
            </div>
            <nav>
                <a href="home.php" class="Active">Home</a>
                <a href="about.php">About Us</a>
            </nav>
        </header>

        <main>
            <div id="topLinkSection">
                <nav class="topLink">
                    <a href="home.php">Home</a><p>&nbsp/&nbsp</p>
                    <a id="active">"<?= htmlspecialchars($movieDetails['MovieName']) ?>"</a>
                </nav>
            </div>
            <section id = "movieDetailsSection">
                <div class = "posterCard">

                    <?php if ($movieDetails): ?>
                        <img src = "/<?= htmlspecialchars($movieDetails['MoviePoster']) ?>"
                            alt = "<?= htmlspecialchars($movieDetails['MovieName']) ?>">
                    <?php endif; ?>

                    </div>
                <div class = "movieInfo">                    
                    <?php if ($movieDetails): ?>
                            <h1><?= htmlspecialchars($movieDetails['MovieName']) ?></h1>
                            <p><?= htmlspecialchars($movieDetails['MovieDescription']) ?></p>

                            <div class = 'bottomDetails'>
                            <p> Genre: <?= htmlspecialchars($movieDetails['Genre']) ?></p>
                            <p> Rating: <?= htmlspecialchars($movieDetails['Rating']) ?></p>
                            </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </body>
</html>