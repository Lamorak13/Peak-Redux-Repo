<?php
    include("peakscinemas_database.php");
    session_start();
    $profile_link = "personal_info_form.php";

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
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['screeningDate'])) {
        $mallData = [];

        $screeningDate = $_POST['screeningDate'];
        $availableMalls_stmt = $conn -> prepare("SELECT * FROM mall
                                                 INNER JOIN theater ON mall.Mall_ID=theater.Mall_ID
                                                 INNER JOIN timeslot ON theater.Theater_ID=timeslot.Theater_ID
                                                 WHERE timeslot.Movie_ID = ? AND timeslot.Date = ? ");
        $availableMalls_stmt -> bind_param("is", $Movie_ID, $screeningDate);
        $availableMalls_stmt -> execute();
        $mallsResult = $availableMalls_stmt -> get_result();

        while ($mall = $mallsResult -> fetch_assoc()) {
            $Mall_ID = $mall['Mall_ID'];

            $theaterTypes_stmt = $conn -> prepare("SELECT DISTINCT TheaterType FROM theater
                                                        WHERE Mall_ID = ?");
            $theaterTypes_stmt -> bind_param("i", $Mall_ID);
            $theaterTypes_stmt -> execute();
            $theaterTypes_result = $theaterTypes_stmt -> get_result();

            $theaterTypes = [];
            while ($type = $theaterTypes_result -> fetch_assoc()) {
                $theaterTypes[] = $type['TheaterType'];
            }

            $screeningTypes_stmt = $conn -> prepare("SELECT DISTINCT ScreeningType FROM timeslot
                                                     INNER JOIN theater ON timeslot.Theater_ID=theater.Theater_ID
                                                     WHERE theater.Mall_ID = ? AND timeslot.Movie_ID = ? AND timeslot.Date = ?");
            $screeningTypes_stmt -> bind_param("iis", $Mall_ID, $Movie_ID, $screeningDate);
            $screeningTypes_stmt -> execute();
            $screeningTypes_result = $screeningTypes_stmt -> get_result();

            $screeningTypes = [];
            while($type = $screeningTypes_result -> fetch_assoc()) {
                $screeningTypes[] = $type['ScreeningType'];
            }

            $theaterNames_stmt = $conn -> prepare("SELECT DISTINCT TheaterName FROM theater
                                                   INNER JOIN timeslot ON theater.Theater_ID=timeslot.Theater_ID
                                                   WHERE Mall_ID = ? AND timeslot.Movie_ID = ? AND timeslot.Date = ?");
            $theaterNames_stmt -> bind_param("iis", $Mall_ID, $Movie_ID, $screeningDate);
            $theaterNames_stmt -> execute();
            $theaterNames_result = $theaterNames_stmt -> get_result();

            $theaterNames = [];
            while($names = $theaterNames_result -> fetch_assoc()) {
                $theaterNames[] = $names['TheaterName'];
            }
            
            $mall['TheaterNames'] = $theaterNames;
            $mall['TheaterTypes'] = $theaterTypes;
            $mall['ScreeningTypes'] = $screeningTypes;
            $mallData[] = $mall;
        }

        echo json_encode($mallData);

        exit;
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

           header {
                background-color: #6A7F3F;
                background: linear-gradient(90deg,rgba(106, 127, 63, 1) 0%, rgba(74, 106, 90, 1) 100%);
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 30px;
                border-bottom: 1px solid #ffffffff;
            }

           body {
                font-family: 'Segoe UI', Arial, sans-serif;
                color: white;
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                background: #5C4033;
                background: linear-gradient(360deg, rgba(92, 64, 51, 1) 0%, rgba(51, 17, 0, 1) 100%);
      
                }

            body::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(to bottom, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 11%, transparent 100%);
                pointer-events: none;
                }

            .logo img {
               height: 50px;
               width: auto;
               cursor: pointer;
               transition: transform 0.2s ease;
            }

            .logo img:hover {
               transform: scale(1.05);
            }

            nav {
                display: flex;
                gap: 10px;
            }

           .profile-btn {
                background-color: #4b4b4b;
                background: linear-gradient(90deg,rgba(75, 75, 75, 1) 0%, rgba(43, 43, 43, 1) 100%);
                border: 1px solid #CCCCCC;
                border-radius: 50%;
                width: 45px;
                height: 45px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                margin-left: auto;
                box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            }

            .profile-btn svg {
                width: 24px;
                height: 24px;
                transition: transform 0.3s ease;
            }

            .profile-btn:hover {
                background: #ffffff;
                background: linear-gradient(90deg,rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
                transform: scale(1.1);
                border: 1px solid #4b4b4b;
                box-shadow: 0 0 8px rgba(255,255,255,0.3);
            }

            .profile-btn:hover svg {
                transform: scale(1.05);
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
                color: #FFFFFF;
                background-color: #4b4b4b;
                background: linear-gradient(90deg,rgba(75, 75, 75, 1) 0%, rgba(43, 43, 43, 1) 100%);
                text-decoration: none;
                padding: 8px 15px;
                border-radius: 10px;
                border: 1px solid #CCCCCC;
                transition: 0.3s;
                margin-top: 10px;
                font-size: 18px;
                font-weight: 900;
                font-family: 'Poppins', sans-serif;
                font-weight: 600;
                text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
            }

            .topLink a#active {
                background: #ffffff;
                background: linear-gradient(360deg,rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
                color: white;
                text-decoration: none;
                padding: 8px 15px;
                border-radius: 10px;
                border: 1px solid #4b4b4b;
                font-weight: bold;
                color: #363635;
                font-family: 'Poppins', sans-serif;
                font-weight: 900;
                transition: 0.3s;
            }
        

            .topLink a:hover,
            .topLink a.active {
                background-color: #ffffffff;
                background: linear-gradient(90deg,rgba(245, 245, 245, 1) 0%, rgba(255, 255, 255, 1) 100%);
                color: #2b2b2b;
                border: 1px solid #4b4b4b;
                box-shadow: 0 0 8px rgba(255,255,255,0.3);
                transition: 0.3s;
            }

            /* movie stuff*/

            .glassbox {
                background: rgba(255, 255, 255, 0.3); /* semi-transparent white */
                border: 3px solid white;
                width: 50%;
                margin: 5px auto;
                display: flex;
                align-items: flex-start;
                gap: 20px;
                border-radius: 10px;
                color: #363635;
                padding-top: 15px;
                padding-left: 15px;
                padding-bottom: 8px;
                margin-top: 25px;
            }

           

            .posterCard img {
                width: 100%;
                height: 280px;
                object-fit: cover;
                border-radius: 6px;
                border: 1px solid #4b4b4b;
                box-shadow: 0 0 8px rgba(255,255,255,0.3);
            }

            .movieInfo {
                flex: 1;
                display: flex;
                flex-direction: column;
                min-height: 280px;
            }

            .movieInfo h1 {
                margin-top: 0;
                font-size: 25px;
                font-family: 'Poppins', sans-serif;
                font-weight: 800;
                color: #FFFFFF;
                text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
                
            }

            .movieInfo p {
                margin-top: 5px;
                margin-right: 0px;
                font-size: 16px;
                font-weight: 600;
                font-family: 'Poppins', sans-serif;
                text-align: left;
                color: #ffffffff;
                text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
                
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

            .glassbox-2 {
                background: rgba(255, 255, 255, 0.3); /* semi-transparent white */
                padding: 20px;
                border: 3px solid white;
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

            #availableMallsText {
                margin: 0px auto;
                display: inline-block;
                width: 100%;
                align-items: left;
                border-radius: 10px;
                font-weight: bold;
                font-size: 18px;
                color: #ffffffff;
                text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
                margin-bottom: 15px;

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
                font-family: 'Poppins', sans-serif;
                font-size: 17px;
                text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
                color: #ffffffff;
                margin-bottom: 3px;
                text-align: center;

            }

            input[type="date"] {
                background: #ffffff;
                background: linear-gradient(90deg,rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
                font-size: 16px;
                font-family: 'Segoe UI', Arial, sans-serif;
                border: 1px solid #000000ff;
                border-radius: 10px;
                padding: 7px;
                box-shadow: 0 0 8px rgba(255,255,255,0.3);
                cursor: pointer;
            }

            input[type="date"]:hover {
                background-color: #4b4b4b;
                background: linear-gradient(90deg,rgba(75, 75, 75, 1) 0%, rgba(43, 43, 43, 1) 100%);
                border: 1px solid #CCCCCC;
                transition: 0.3s;
                color: #CCCCCC;
               
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
                width: 100%;
                border: 1px solid #000000ff;
                border-radius: 10px;
                min-width: 35%;
                height: auto;
                overflow: hidden;
                background-color: rgba(255, 255, 255, 0.2); /* semi-transparent white */
            }

            .mallTypesContainer {
                display: flex;
                width: 100%;
                min-width: 35%;
                height: auto;
                overflow: hidden;
                background-color: #6A7F3F;
                background: linear-gradient(90deg,rgba(106, 127, 63, 1) 0%, rgba(74, 106, 90, 1) 100%);
                border-bottom: 2px solid #000000ff;
            }

            .mallLocation {
                display: flex;
                width: 100%;
                min-width: 35%;
                height: auto;
                overflow: hidden;
                background: #5C4033;
                background: linear-gradient(90deg, rgba(92, 64, 51, 1) 0%, rgba(51, 17, 0, 1) 100%);
                font-weight: bold;
                font-family: 'Segoe UI', Arial, sans-serif;
                text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
                color: #FFFFFF;
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
                color: #4b4b4b;
                background: #ffffff;
                background: linear-gradient(90deg, rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
                font-family: 'Segoe UI', Arial, sans-serif;
                font-weight: 900;
                border-right: 1px solid #000000ff;
                text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
                cursor: pointer;
            }

            .mallName:hover {
                background-color: #4b4b4b;
                background: linear-gradient(90deg,rgba(75, 75, 75, 1) 0%, rgba(43, 43, 43, 1) 100%);
                border: 1px solid #CCCCCC;
                transition: 0.3s;
                color: #CCCCCC;
            }

            .detailContainer {
                display: flex;
                flex: 1 1 auto;
                flex-direction: column;
                box-sizing: border-box;
                height: auto;
                padding: 3px;
                width: 100%;
                max-width: 100%;
            }

            .screeningTypesContainer {
                display: flex;
                border-bottom: 5px solid #000000ff;
                min-height: 50%;
                width: 100%;
                border-bottom: 1px solid #000;
                box-sizing: border-box;
                flex-wrap: wrap;
                align-items: flex-start;
                height: auto;
                padding: 3px;
                gap: 5px;
            }

            .screeningType {
                display: inline-flex;
                border: 1px solid #000000ff;
                border-radius: 10px;
                padding: 5px;
                color: #000;
                margin-bottom: 5px;
                background: #ffffff;
                background: linear-gradient(90deg, rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
                font-family: 'Segoe UI', Arial, sans-serif;
                text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
                cursor: pointer;
            }

            .screeningType:hover {
                background-color: #4b4b4b;
                background: linear-gradient(90deg,rgba(75, 75, 75, 1) 0%, rgba(43, 43, 43, 1) 100%);
                border: 1px solid #CCCCCC;
                transition: 0.3s;
                color: #CCCCCC;
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
                border: 1px solid #000000ff;
                border-radius: 10px;
                padding: 5px;
                margin-top: 5px;
                color: #000000ff;
                background: #ffffff;
                background: linear-gradient(90deg, rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
                font-family: 'Segoe UI', Arial, sans-serif;
                font-weight: 400;
                text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
                cursor: pointer;
            }

            .theaterType:hover {
                background-color: #4b4b4b;
                background: linear-gradient(90deg,rgba(75, 75, 75, 1) 0%, rgba(43, 43, 43, 1) 100%);
                border: 1px solid #CCCCCC;
                transition: 0.3s;
                color: #CCCCCC;
            }

            #screeningsMsg {
                color: #b41b06ff;
                font-weight: bold;
                text-align: center;
            }
        </style>
    </head>
    <body>
       <header>
    <div class="logo">
      <img src="peakscinematransparent.png" alt="PeaksCinemas Logo" onclick="window.location.href='home.php'">
    </div>
    
    <div class="header-actions">
      <button class="profile-btn" onclick="window.location.href='<?= $profile_link ?>'" title="Profile">👤</button>
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M5.121 17.804A8 8 0 1118.88 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
  </svg>
</button>
    </div>
  </header>

        <main>
            <div id="topLinkSection">
                <nav class="topLink">
                    <a href="home.php">Home</a><p>&nbsp/&nbsp</p>
                    <a id="active">Malls with "<?= htmlspecialchars($movieDetails['MovieName']) ?>"</a>
                </nav>
            </div>
            
            <section id = "movieDetailsSection">
            <div class="glassbox">
            
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
                            <p> Runtime: <?= htmlspecialchars($movieDetails['Runtime']) ?> minutes</p>
                            </div>
                    <?php endif; ?>
                </div>
            </div>
            </section>
            
            <div class="glassbox-2">
            <section id="availableMalls">
                <div class="dateContainer">
                    <label for="screeningDate">Date of Screening: </label>
                    <input type="date" id="screeningDate" name="screeningDate"><br>
                </div>
                <p id="screeningsMsg"></p>                
                <div id="availableMallsText">Malls with this movie: </div>
                <div id="mallsContainer"></div>
            </section>
            </div>
        </main>

        <script>
            const Movie_ID = new URLSearchParams(window.location.search).get("id");

            const mallsContainer = document.getElementById("mallsContainer");
            const screeningsMsg = document.getElementById("screeningsMsg");
            const screeningDate = document.getElementById("screeningDate");
            const availableMallsText = document.getElementById("availableMallsText");
            const date = new Date();

            let selectedMall = "";

            function setDate(actualDate) {
                fetch("", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "screeningDate=" + encodeURIComponent(actualDate) + "&id=" + encodeURIComponent(Movie_ID)
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    mallsContainer.innerHTML = "";
                    screeningsMsg.innerHTML = "";
                    availableMallsText.innerHTML = "Malls with this movie: ";

                    if (data.length === 0) {
                        screeningsMsg.innerHTML = "Sorry, there are no screenings for this date. Please select another date and try again.";
                        availableMallsText.innerHTML = "";
                    }

                    const seenMalls = new Set();                    
                    data.forEach(mall => {
                        
                        if (!seenMalls.has(mall.MallName)) {
                            seenMalls.add(mall.MallName);

                            const mallCard = document.createElement("div");
                            mallCard.classList.add("mallCard");
                            mallCard.addEventListener("click", function(e) {
                                
                                window.location.href = `mall.php?movie_id=<?= htmlspecialchars($movieDetails['Movie_ID'])?>&mall_id=${mall.Mall_ID}&date=${actualDate}`;
                            })

                            mallsContainer.appendChild(mallCard);

                            const mallTypesContainer = document.createElement("div");
                            mallTypesContainer.classList.add("mallTypesContainer");
                            mallCard.appendChild(mallTypesContainer);
                            
                            const mallName = document.createElement("div");
                            mallName.classList.add("mallName");
                            mallName.textContent = `${mall.MallName}`;
                            mallTypesContainer.appendChild(mallName);

                            const detailContainer = document.createElement("div");
                            detailContainer.classList.add("detailContainer");
                            mallTypesContainer.appendChild(detailContainer);

                            const screeningTypesContainer = document.createElement("div");
                            screeningTypesContainer.classList.add("screeningTypesContainer");
                            detailContainer.appendChild(screeningTypesContainer);

                            const theaterTypesContainer = document.createElement("div");
                            theaterTypesContainer.classList.add("theaterTypesContainer");
                            detailContainer.appendChild(theaterTypesContainer);
                            
                            const seenScreeningTypes = new Set();
                            mall.ScreeningTypes.forEach(type => {

                                if (!seenScreeningTypes.has(type)) {
                                    seenScreeningTypes.add(type);
                                    
                                    const screeningType = document.createElement("div");
                                    screeningType.classList.add("screeningType");
                                    screeningType.textContent = type;
                                    screeningTypesContainer.appendChild(screeningType);
                                }
                            })
                            
                            const seenTheaterTypes = new Set();
                            mall.TheaterTypes.forEach(type => {

                                if (!seenTheaterTypes.has(type)) {
                                    seenTheaterTypes.add(type);

                                    const theaterType = document.createElement("div");
                                    theaterType.classList.add("theaterType");
                                    theaterType.textContent = type;
                                    theaterTypesContainer.appendChild(theaterType);
                                }                            
                            })

                            const mallLocation = document.createElement("div");
                            mallLocation.classList.add("mallLocation");
                            mallLocation.textContent = "Location: " + mall.Location;
                            mallCard.appendChild(mallLocation);
                        }
                    })
                })
            }

            window.addEventListener("DOMContentLoaded", function() {                
                const today = date.toISOString().split('T')[0];
                screeningDate.setAttribute("min", today);
                screeningDate.setAttribute("value", today);

                setDate(today);
            })

            screeningDate.addEventListener("change", function() {
                setDate(screeningDate.value);
            })

        </script>
    </body>
</html>