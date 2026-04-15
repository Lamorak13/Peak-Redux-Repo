<?php
    include("peakscinemas_database.php");
    session_start();
    $profile_link = "personal_info_form.php";
      
    $Movie_ID = filter_input(INPUT_GET, 'movie_id', FILTER_VALIDATE_INT);
    $Mall_ID = filter_input(INPUT_GET, 'mall_id', FILTER_VALIDATE_INT);
    $Date = filter_input(INPUT_GET, 'date');

    if (!$Movie_ID || !$Mall_ID || !$Date) {
        header("Location: home.php");
        exit;
    }

    $movie_stmt = $conn->prepare("SELECT * FROM movie WHERE Movie_ID = ?");
    $movie_stmt->bind_param("i", $Movie_ID);
    $movie_stmt->execute();
    $movieDetails = ($movie_stmt->get_result())->fetch_assoc();

    $mall_stmt = $conn->prepare("SELECT * FROM mall WHERE Mall_ID = ?");
    $mall_stmt->bind_param("i", $Mall_ID);
    $mall_stmt->execute();
    $mallDetails = ($mall_stmt->get_result())->fetch_assoc();

    $theater_stmt = $conn->prepare("SELECT DISTINCT theater.Theater_ID, theater.TheaterName FROM theater
                                      INNER JOIN timeslot ON theater.Theater_ID=timeslot.Theater_ID
                                      WHERE timeslot.Date = ? AND timeslot.Movie_ID = ? AND theater.Mall_ID = ?");
    $theater_stmt->bind_param("sii", $Date, $Movie_ID, $Mall_ID);
    $theater_stmt->execute();
    $theaterResult = $theater_stmt->get_result();

    $theaterData = [];

    while ($theater = $theaterResult->fetch_assoc()) {
        $Theater_ID = $theater['Theater_ID'];

        $timeslot_stmt = $conn->prepare("SELECT TimeSlot_ID, ScreeningType, StartTime FROM timeslot
                                           WHERE Theater_ID = ? AND Date = ? AND Movie_ID = ?");
        $timeslot_stmt->bind_param("isi", $Theater_ID, $Date, $Movie_ID);
        $timeslot_stmt->execute();
        $timeslot_result = $timeslot_stmt->get_result();

        $TimeslotDetails = [];
        while($time = $timeslot_result->fetch_assoc()) {
            $TimeslotDetails[$time['TimeSlot_ID']] = $time['ScreeningType'] . ' - ' . date("g:i A", strtotime($time['StartTime']));
        }
        
        $theater['Timeslots'] = $TimeslotDetails;
        $theaterData[] = $theater;
    }
    
    if (!$movieDetails || !$mallDetails) {
        header("Location: home.php");
        exit;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <style>
*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Segoe UI',sans-serif;
}

body{
background:linear-gradient(to bottom,#071018,#0d1b2a);
color:white;
overflow-x:hidden;
min-height:100vh;
}

/* HEADER */
header{
position:fixed;
width:100%;
top:0;
padding:20px 60px;
display:flex;
justify-content:space-between;
align-items:center;
z-index:1000;
background:linear-gradient(to bottom,rgba(7,16,24,0.95),transparent);
transition:0.3s;
}

header.scrolled{
background:#071018;
box-shadow:0 4px 25px rgba(0,0,0,0.6);
}

.logo img{
height:45px;
cursor:pointer;
}

/* PROFILE BUTTON */
.profile-btn{
background:linear-gradient(135deg,#2dd4bf,#14b8a6);
border:none;
padding:8px 20px;
border-radius:30px;
font-weight:bold;
cursor:pointer;
color:#071018;
transition:0.3s;
}

.profile-btn:hover{
transform:scale(1.05);
}

/* MAIN */
main{
margin-top:130px;
padding:0 60px;
}

/* TOP LINKS */
.topLink{
display:flex;
gap:10px;
margin-bottom:30px;
flex-wrap:wrap;
}

.topLink a{
background:rgba(255,255,255,0.08);
padding:8px 15px;
border-radius:20px;
text-decoration:none;
color:white;
transition:0.3s;
}

.topLink a#active,
.topLink a:hover{
background:#2dd4bf;
color:#071018;
}

/* GLASS BOXES */
.glassbox,
.glassbox-2{
background:rgba(255,255,255,0.06);
backdrop-filter:blur(10px);
border-radius:15px;
padding:25px;
margin-bottom:30px;
box-shadow:0 10px 30px rgba(0,0,0,0.4);
}

/* POSTER */
.posterCard{
width:100%;
max-width:700px;
height:320px;
overflow:hidden;
border-radius:15px;
}

.posterCard img{
width:100%;
height:100%;
object-fit:cover;
border-radius:15px;
transition:0.4s ease;
}

/* MOVIE INFO */
.glassbox{
display:flex;
flex-direction:column;
gap:25px;
}

.movieInfo{
display:flex;
flex-direction:column;
}

.movieInfo h1{
font-size:2rem;
margin-bottom:15px;
}

.movieInfo p{
opacity:0.9;
margin-bottom:8px;
}

/* MALL CARD */
#mallCard{
display:flex;
justify-content:space-between;
align-items:center;
padding:20px;
border-radius:15px;
background:rgba(255,255,255,0.06);
margin-bottom:30px;
}

.mallName{
font-weight:bold;
font-size:1.2rem;
}

.mallLocation{
opacity:0.8;
}

/* THEATER */
.theaterCard{
background:rgba(255,255,255,0.08);
border-radius:12px;
padding:15px;
margin-bottom:15px;
}

.theaterName{
font-weight:bold;
margin-bottom:10px;
}

.timeslotContainer{
display:flex;
flex-wrap:wrap;
gap:10px;
}

.timeslots{
padding:8px 15px;
border-radius:20px;
background:#2dd4bf;
color:#071018;
font-weight:bold;
cursor:pointer;
transition:0.3s;
}

.timeslots:hover{
transform:scale(1.05);
background:#14b8a6;
}
</style>
</head>
<body>
<header>
    <div class="logo">
        <img src="peakscinemastransparent.png" alt="PeaksCinemas Logo" onclick="window.location.href='home.php'">
    </div>
    <div class="header-actions">
        <button class="profile-btn" onclick="window.location.href='<?= $profile_link ?>'" title="Profile">👤</button>
    </div>
</header>

<main>
    <div id="topLinkSection">
        <nav class="topLink">
            <a href="home.php">Home</a><p>&nbsp;/&nbsp;</p>
            <a href="movie.php?movie_id=<?= htmlspecialchars($movieDetails['Movie_ID']) ?>">Malls with "<?= htmlspecialchars($movieDetails['MovieName']) ?>"</a><p>&nbsp;/&nbsp;</p>
            <a id="active">Theatre Selection - <?= htmlspecialchars($mallDetails['MallName']) ?></a>             
        </nav>
    </div>

    <section id="movieDetailsSection">
        <div class="glassbox">
            <div class="posterCard">
                <?php if ($movieDetails): ?>
                    <img src="/<?= htmlspecialchars($movieDetails['MoviePoster']) ?>" alt="<?= htmlspecialchars($movieDetails['MovieName']) ?>">
                <?php endif; ?>
            </div>
            <div class="movieInfo">
                <?php if ($movieDetails): ?>
                    <h1><?= htmlspecialchars($movieDetails['MovieName']) ?></h1>
                    <p><?= htmlspecialchars($movieDetails['MovieDescription']) ?></p>
                    <div class='bottomDetails'>
                        <p>Genre: <?= htmlspecialchars($movieDetails['Genre']) ?></p>
                        <p>Rating: <?= htmlspecialchars($movieDetails['Rating']) ?></p>
                        <p>Runtime: <?= htmlspecialchars($movieDetails['Runtime']) ?> minutes</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="mallCard">
        <div class="mallName"><?= htmlspecialchars($mallDetails['MallName']) ?></div>
        <div class="mallLocation"><?= htmlspecialchars($mallDetails['Location']) ?></div>
    </section>

    <div class="glassbox-2">
        <section id="availableTheatersSection">
            <div id="availableTheatersText">Available theaters with this movie, please select to continue:</div>
            <div id="theaterContainer">
                <?php foreach ($theaterData as $theater): ?>
                    <div class="theaterCard">
                        <div class="theaterName"><?= htmlspecialchars($theater['TheaterName']) ?></div>
                        <div class="timeslotContainer">
                            <?php foreach ($theater['Timeslots'] as $timeslot_id => $details): ?>
                                <div class="timeslots" data-id='<?= htmlspecialchars($timeslot_id) ?>'><?= htmlspecialchars($details) ?></div>
                            <?php endforeach; ?>
                        </div>     
                    </div>
                <?php endforeach; ?>
            </div>                
        </section>
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".timeslots");

    buttons.forEach(btn => {
        btn.addEventListener("click", () => {
            buttons.forEach(b => b.classList.remove("selected"));
            btn.classList.add("selected");

            const timeslot_id = btn.getAttribute('data-id');
            setTimeout(() => {
                window.location.href = `seat_selection.php?movie_id=<?= $Movie_ID ?>&mall_id=<?= $Mall_ID ?>&date=<?= $Date ?>&timeslot_id=${timeslot_id}`;
            }, 200);
        });
    });
});


</script>
</body>
</html>