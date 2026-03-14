<?php
include("peakscinemas_database.php");
session_start();

$profile_link = "personal_info_form.php";

if(isset($_SESSION['user_id'])) {
    $profile_link = "profile_edit.php";

    $stmt = $conn->prepare("SELECT Name, PhoneNumber, Email FROM customer WHERE Customer_ID = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user_result = $stmt->get_result();
    if($user_result->num_rows > 0){
        $user = $user_result->fetch_assoc();
    }
}

if(isset($_GET['ajax_search']) && !empty($_GET['ajax_search'])){
    $term = "%{$_GET['ajax_search']}%";
    $stmt = $conn->prepare("SELECT Movie_ID, MovieName, MoviePoster FROM movie WHERE MovieName LIKE ?");
    $stmt->bind_param("s", $term);
    $stmt->execute();
    $result = $stmt->get_result();

    $movies = [];
    while($row = $result->fetch_assoc()){
        $movies[] = $row;
    }

    echo json_encode($movies);
    exit;
}

function getAvailableMovies($conn, $availability) {
    $stmt = $conn->prepare("SELECT * FROM movie WHERE MovieAvailability = ?");
    $stmt->bind_param("s", $availability);
    $stmt->execute();
    return $stmt->get_result();
}

$now_showing_results = getAvailableMovies($conn, 'Now Showing');
$coming_soon_results = getAvailableMovies($conn, 'Coming Soon');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PeaksCinemas</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      scroll-behavior: smooth;
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

     header {
        background-color: #6A7F3F;
        background: linear-gradient(90deg,rgba(106, 127, 63, 1) 0%, rgba(74, 106, 90, 1) 100%);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 30px;
        border-bottom: 1px solid #ffffffff;
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

    .search-container {
      display: block;
      margin-left: 15px;
      margin-right: 15px;
      
    }

    .search-container input {
      width: 300px;
      padding: 8px 40px 8px 15px;
      border-radius: 25px;
      border: 1px solid #4b4b4b;
      background: #ffffff;
      background: linear-gradient(90deg, rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
      font-family: 'Segoe UI', Arial, sans-serif;
      font-size: 0.95rem;
      padding-top: 10px;
      padding-bottom: 10px;
      padding-left: 20px;
      padding-right: 20px;
      outline: none;
      transition: all 0.3s ease;
    }

    .search-container input:focus {
      border-color: #2b2b2b;
      box-shadow: 0 0 6px rgba(0, 0, 0, 0.2);
    }

    .search-container button {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: #2b2b2b;
      font-size: 18px;
      transition: transform 0.2s ease;
    }

    .search-container button:hover {
      transform: translateY(-50%) scale(1.1);
    }
    
    .glassbox {
      background: rgba(255, 255, 255, 0.2); /* semi-transparent white */
      margin: 40px auto;
      width: 85%;
      padding: 30px;
      backdrop-filter: blur(12px); /* frosted blur */
      -webkit-backdrop-filter: blur(12px); /* Safari support */
      border: 2px solid #ffffffff; /* subtle border */
      border-radius: 10px;
      color: #ffffffff; /* light text for contrast */
    }
    
    .tabs {
      display: flex;
      justify-content: center;
      position: relative;
      margin-bottom: 0;
      gap: 15px;
    }

    .tab {
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff;
      padding: 8px 20px;
      border-radius: 10px 10px 0 0;
      margin: 0 3px;
      cursor: pointer;
      font-weight: 900;
      font-family: 'Poppins', sans-serif;
      text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      border: 1px solid #ffffffff;
      border-bottom: none;
      position: relative;
      z-index: 2;
      text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
      transition: transform 0.2s ease;
    }

    .tab.active {
      background-color: #4b4b4b;
      background: linear-gradient(90deg,rgba(75, 75, 75, 1) 0%, rgba(43, 43, 43, 1) 100%);
      border-top: 1px solid  #ffffffff;
      border-left: 1px solid  #ffffffff;
      border-right: 1px solid  #ffffffff;
      text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
      font-family: 'Poppins', sans-serif;
    }

    .tab:hover {
      background: #ffffff;
      background: linear-gradient(90deg,rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
      border-top: 1px solid #4b4b4b;
      border-left: 1px solid #4b4b4b;
      border-right: 1px solid #4b4b4b;
      color: #4b4b4b;
      font-family: 'Poppins', sans-serif;
      transition: 0.3s;
      box-shadow: 0 0 8px rgba(255,255,255,0.3);

    }

    .movies-container {
      border: 1px solid #ffffffff;
      border-radius: 10px;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 20px;
      padding: 20px;
      backdrop-filter: blur(12px);
      
    }

    .movie-card {
      background: rgba(255, 255, 255, 0.2);
      border-radius: 10px;
      border: 1px solid #ffffff;
      padding: 15px;
      text-align: center;
      color: white;
      backdrop-filter: blur(12px);
      box-shadow: 0 3px 6px rgba(0,0,0,0.3);
      
    }

      .movie-card img {
      width: 100%;
      height: 280px;
      object-fit: cover;
      border-radius: 6px;
      background-color: #fff;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
    }

    .movie-card img:hover {
      transform: scale(1.08);
      box-shadow: 0 10px 20px rgba(0,0,0,0.6);
    }

    .movie-title {
      margin: 15px 0 10px;
      font-weight: 500;
      font-size: 22px;
      font-family: 'Segoe UI', Arial, sans-serif;
      text-shadow: 0 2px 5px rgba(0, 0, 0, 0.9);
    }

    .buy-btn {
      color: #ffffff;
      background-color: #4b4b4b;
      background: linear-gradient(90deg,rgba(75, 75, 75, 1) 0%, rgba(43, 43, 43, 1) 100%);
      border: 1px solid #ffffff;
      padding: 6px 16px;
      border-radius: 10px;
      cursor: pointer;
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      transition: 0.3s;
    }

    .buy-btn:hover {
      background: #ffffff;
      background: linear-gradient(90deg,rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
      transition: 0.3s ease;
      border: 1px solid #4b4b4b;
      color: #4b4b4b;
      box-shadow: 0 0 8px rgba(255,255,255,0.3);

    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
    }

    footer {
      background-color: #6A7F3F;
      background: linear-gradient(90deg,rgba(106, 127, 63, 1) 0%, rgba(74, 106, 90, 1) 100%);
      width: 100%;
      padding: 20px 0;
      border-top: 3px solid #ffffffff;
      text-align: center;
      margin-top: auto;
      
    }

    footer h2 {
      font-family: 'Segoe UI', Arial, sans-serif;
      font-size: 1.5rem;
      margin-bottom: 10px;
      text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);

      
    }

    footer p {
      width: 75%;
      margin: 0 auto;
      text-align: center;
      line-height: 1.5;
      font-size: 0.95rem;
      text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }
    
    .header-actions {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      flex: 1;
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

.poster-container {
  position: relative;
  cursor: pointer;
}

.poster-container img {
  width: 100%;
  height: 280px;
  object-fit: cover;
  border-radius: 6px;
  transition: transform 0.3s ease;
}

.poster-container:hover img {
  transform: scale(1.05);
}

.poster-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.65);
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  font-size: 18px;
  opacity: 0;
  border-radius: 6px;
  transition: opacity 0.3s ease;
}

.poster-container:hover .poster-overlay {
  opacity: 1;
} 
  </style>
</head>

<body>

  <header>
    <div class="logo">
      <img src="peakscinematransparent.png" alt="PeaksCinemas Logo" onclick="window.location.href='home.php'">
    </div>

    <div class="search-container">
  <form id="searchForm" action="javascript:void(0);" method="get">
    <input type="text" id="searchInput" name="search" placeholder="Movie Search" autocomplete="off">
  </form>
  <div id="searchResults" style="position:absolute; top:40px; width:320px; background:#fff; color:#000; border-radius:5px; max-height:200px; overflow-y:auto; display:none; z-index:1000;"></div>
</div>

    <div class="header-actions">
      <button class="profile-btn" onclick="window.location.href='<?= $profile_link ?>'" title="Profile">👤</button>
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M5.121 17.804A8 8 0 1118.88 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
  </svg>
</button>
    </div>
  </header>
  
  <div class="glassbox">
  <main id="home" class="main-container">
    <div class="tabs">
      <div class="tab active" onclick="showTab('now-showing')">Now Showing</div>
      <div class="tab" onclick="showTab('coming-soon')">Coming Soon</div>
    </div>

    <div id="now-showing" class="tab-content active">
        <div class="movies-container">

          <?php while ($row = $now_showing_results -> fetch_assoc()): ?>
            <div class = 'movie-card'>
            <div class="poster-container" onclick="window.open('<?= htmlspecialchars($row['TrailerURL']) ?>','_blank')">
                <img src='/<?= htmlspecialchars($row['MoviePoster']) ?>' alt="<?= htmlspecialchars($row['MovieName']) ?>">
                <div class="poster-overlay">Watch Trailer ▶</div>
            </div>
            <div class = 'movie-title'><?= htmlspecialchars($row['MovieName']) ?></div>
            <button class = 'buy-btn' data-id='<?= htmlspecialchars($row['Movie_ID'])?>'>Buy Tickets</button>
            </div>
          <?php endwhile; ?>

        </div>
      </div>
      <div id="coming-soon" class="tab-content">
        <div class="movies-container">

          <?php while ($row = $coming_soon_results -> fetch_assoc()): ?>
            <div class = 'movie-card'>
            <img src='/<?= htmlspecialchars($row['MoviePoster']) ?>' alt="<?= htmlspecialchars($row['MovieName']) ?>">
            <div class = 'movie-title'><?= htmlspecialchars($row['MovieName']) ?></div>
            <button class = 'buy-btn' data-id='<?= htmlspecialchars($row['Movie_ID'])?>'>Buy Tickets</button>
            </div>
          <?php endwhile; ?>
      </div>
    </div>
    </div>
  </main>

  <footer>
    <h2>About Us</h2>
    <p>
      Welcome to <strong>PeaksCinemas</strong>, where Peak Movies meet Peak Experiences.
    </p>
  </footer>

  <script>
function showTab(tabId) {
    const tabs = document.querySelectorAll('.tab');
    const contents = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));
    contents.forEach(content => content.classList.remove('active'));
    document.querySelector(`[onclick="showTab('${tabId}')"]`).classList.add('active');
    document.getElementById(tabId).classList.add('active');
}

function attachBuyButtons() {
    document.querySelectorAll('.buy-btn').forEach(button => {
        button.onclick = () => {
            const movieId = button.getAttribute('data-id');
            window.location.href = `movie.php?movie_id=${movieId}`;
        };
    });
}

attachBuyButtons();

const searchInput = document.getElementById("searchInput");
const searchResults = document.getElementById("searchResults");

searchInput.addEventListener("input", function() {
    const query = searchInput.value.trim();
    if(query.length === 0){
        searchResults.style.display = "none";
        searchResults.innerHTML = "";
        return;
    }

    fetch(`home.php?ajax_search=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            if(data.length === 0){
                searchResults.innerHTML = "<div style='padding:10px;'>No movies found</div>";
            } else {
                searchResults.innerHTML = data.map(movie => 
                    `<div class='result-item' style='display:flex; align-items:center; padding:5px; cursor:pointer; border-bottom:1px solid #ddd;' 
                         onclick="window.location.href='movie.php?movie_id=${movie.Movie_ID}'">
                         <img src='/${movie.MoviePoster}' alt='${movie.MovieName}' style='width:50px; height:70px; object-fit:cover; margin-right:10px; border-radius:4px;'>
                         <span>${movie.MovieName}</span>
                    </div>`
                ).join("");
            }
            searchResults.style.display = "block";
        });
});

document.addEventListener("click", function(e){
    if(!searchResults.contains(e.target) && e.target !== searchInput){
        searchResults.style.display = "none";
    }
});
</script>
</body>
</html>