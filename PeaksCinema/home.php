<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="manifest" href="manifest.json">
  <link rel="stylesheet" href="site.css">
  <script src="customer_gate.js"></script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PeaksCinemas</title>

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
}

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

.hero-slider{
position:relative;
height:100vh;
overflow:hidden;
opacity: 0;
transition: opacity 0.5s ease-out;
}

.hero-slider.shown {
  opacity: 1;
}

.slide{
position:absolute;
width:100%;
height:100%;
opacity:0;
transition:opacity 1s ease-in-out;
}

.slide.active{
opacity:1;
}

.slide img{
width:100%;
height:100%;
object-fit:cover;
filter:brightness(0.65);
}

.hero-slider::after{
content:"";
position:absolute;
left:0;
right:0;
bottom:0;
height:45%;
background:linear-gradient(to top,#071018 15%,transparent);
}

.slide-content{
position:absolute;
top:40%;
left:60px;
max-width:600px;
z-index:2;
}

.slide-content h1{
font-size:2.5rem;
font-weight:800;
margin-bottom:20px;
}

.slide-content button{
background:linear-gradient(135deg,#2dd4bf,#14b8a6);
color:#071018;
border:none;
padding:12px 30px;
border-radius:8px;
font-weight:bold;
cursor:pointer;
transition:0.3s;
}

.slide-content button:hover{
transform:scale(1.05);
}

.dots{
position:absolute;
bottom:30px;
left:50%;
transform:translateX(-50%);
display:flex;
gap:10px;
}

.dot{
width:10px;
height:10px;
background:rgba(255,255,255,0.3);
border-radius:50%;
cursor:pointer;
}

.dot.active{
background:#2dd4bf;
}

.movie-section{
padding:0 60px;
position:relative;
z-index:3;
margin-top:80px;
}

#nowShowing{
margin-top:-120px;
}

.movie-section h2{
font-size:1.9rem;
margin-bottom:25px;
font-weight:700;
letter-spacing:1px;
}

.movies-row{
display:flex;
gap:25px;
overflow-x:auto;
padding-bottom:20px;
scrollbar-width:none;
}

.movies-row::-webkit-scrollbar{
display:none;
}

.movie-card{
min-width:340px;
height:200px;
border-radius:16px;
overflow:hidden;
cursor:pointer;
position:relative;
box-shadow:0 4px 15px rgba(0,0,0,0.4);
opacity: 0;
transition: opacity 0.5s ease-out, transform 0.4s ease, box-shadow 0.4s ease;
}

.movie-card.shown {
  opacity: 1;
}

.movie-card:not(.noMoviesMessage):hover{
transform:scale(1.15);
box-shadow:0 25px 50px rgba(0,0,0,0.7);
z-index:10;
}

.movie-card img{
width:100%;
height:100%;
object-fit:cover;
transition:opacity 0.4s ease;
}

.movie-card:hover img{
opacity:0.12;
}

.noMoviesMessage {
  display: flex;
  align-content: center;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  padding: 15px;
  width: 340px;
  text-align: center;
  cursor: default;
  font-weight: bold;
}

.trailer-preview{
position:absolute;
top:0;
left:0;
width:100%;
height:100%;
opacity:0;
transition:opacity 0.4s ease;
overflow:hidden;
border-radius:16px;
}

.movie-card:hover .trailer-preview{
opacity:1;
}

.trailer-preview video{
width:100%;
height:100%;
object-fit:cover;
}

.volume-btn{
position:absolute;
top:14px;
right:14px;
width:38px;
height:38px;
background:rgba(0,0,0,0.75);
color:white;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
font-size:22px;
cursor:pointer;
opacity:0;
transition:all 0.3s ease;
z-index:15;
box-shadow:0 4px 12px rgba(0,0,0,0.5);
}

.movie-card:hover .volume-btn{
opacity:1;
}

.volume-btn:hover{
background:rgba(45,212,191,0.9);
transform:scale(1.15);
}

.movie-title{
position:absolute;
bottom:14px;
left:18px;
color:white;
font-size:1.15rem;
font-weight:bold;
text-shadow:0 2px 10px rgba(0,0,0,0.9);
z-index:2;
pointer-events:none;
transition:opacity 0.3s;
}

.movie-card:hover .movie-title{
opacity:0;
}

footer{
margin-top:120px;
padding:50px;
background:#050c14;
text-align:center;
color:#aaa;
}

@keyframes shimmer {
    0% { background-position: -468px 0; }
    100% { background-position: 468px 0; }
}

.movie-card-loader {        
    min-width:340px;
    height:200px;
    border-radius:16px;
    overflow:hidden;
    position:relative;
    box-shadow:0 4px 15px rgba(0,0,0,0.4);
    background-image: linear-gradient(
        to right, 
        #1a2631 0%, 
        #253341 20%, 
        #1a2631 40%, 
        #1a2631 100%
    );
    background-repeat: no-repeat;
    background-size: 800px 200px; 
    display: inline-block;
    animation: shimmer 1.5s linear infinite forwards;
    opacity: 1;
    transition: opacity 0.5s ease-out;
}

.hero-loader {
  width:100%;
  height:100vh;
  background-color: #1a2631;
  background-image: linear-gradient(
      to right, 
      #1a2631 0%, 
      #253341 20%, 
      #1a2631 40%, 
      #1a2631 100%
  );
  background-repeat: no-repeat;
  background-size: 200% 100%; 
  animation: shimmer 2s linear infinite forwards;
  opacity: 1;
  transition: opacity 0.5s ease-out;
}

.hero-loader.fade-out, .movie-card-loader.fade-out {
  opacity: 0;
}
</style>
</head>

  <body>
    <header>
      <div class="logo">
        <img src="peakscinemastransparent.png" onclick="window.location.href='home.php'">
      </div>

      <button class="profile-btn" onclick="window.location.href='profile_edit.php'">👤</button>
    </header>

    <div class="hero-slider">
      <div class="hero-loader"></div>
      <div class="dots"></div>
    </div>

    <div id="nowShowing" class="movie-section">
      <h2>Now Showing</h2>
      <div id="nowShowingRow" class="movies-row">
        <div class="movie-card-loader"></div>
        <div class="movie-card-loader"></div>
        <div class="movie-card-loader"></div>
        <div class="movie-card-loader"></div>
        <div class="movie-card-loader"></div>
      </div>
    </div>

    <div id="comingSoon" class="movie-section">
      <h2>Coming Soon</h2>
      <div id="comingSoonRow" class="movies-row">
        <div class="movie-card-loader"></div>
        <div class="movie-card-loader"></div>
        <div class="movie-card-loader"></div>
        <div class="movie-card-loader"></div>
        <div class="movie-card-loader"></div>
      </div>
    </div>

    <footer>
      <h2>About Us</h2>
      <p>Welcome to <strong>PeaksCinemas</strong>, where Peak Movies meet Peak Experiences.</p>
    </footer>

    <script>

      const nowShowingRow = document.getElementById('nowShowingRow');
      const comingSoonRow = document.getElementById('comingSoonRow');

      let nowShowingTotal = 0;
      let comingSoonTotal = 0;

      document.addEventListener("DOMContentLoaded", function() {
        fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=movie', {
          method: 'GET'
        })
        .then(response => {
          if (!response.ok) {
              console.log(response.error);
          }
          return response.json();
        })
        .then(data => {
          const minDelay = new Promise(resolve => setTimeout(resolve, 800));
          return Promise.all([data, minDelay]);
        })
        .then(([data]) => {
          const movies = data.data;
      

          const movieCardLoader = document.querySelectorAll('.movie-card-loader');
          
          const heroLoader = document.querySelector('.hero-loader')
          if (heroLoader) {
            movieCardLoader.forEach(indiv => indiv.classList.add('fade-out'));
            heroLoader.classList.add('fade-out');
            
            setTimeout(() => {
              nowShowingRow.innerHTML = "";
              comingSoonRow.innerHTML = "";
              heroLoader.remove();
              movieCardLoader.forEach(indiv => indiv.remove());          

              let index = 0;
              movies.forEach(movie => {
                // Hero Slider
                const slide = document.createElement('div');
                slide.classList.add('slide');

                if (index == 0) {
                  slide.classList.add('active');
                }

                const slidePoster = document.createElement('img');
                slidePoster.src = "/" + movie.MoviePoster;
                slide.append(slidePoster);

                const slideContent = document.createElement('div');
                slideContent.classList.add('slide-content');

                const movieName = document.createElement('h1');
                movieName.textContent = movie.MovieName;
                slideContent.append(movieName);

                const bookNowButton = document.createElement('button');
                bookNowButton.textContent = "Book Now";
                bookNowButton.addEventListener("click", function() {
                  window.location.href = 'movie.php?movie_id=' + movie.Movie_ID;
                })
                slideContent.append(bookNowButton);

                slide.append(slideContent);
                document.querySelector('.hero-slider').append(slide);

                index++;
                // Now Showing / Coming Soon
                const movieCard = document.createElement('div');
                movieCard.classList.add('movie-card');

                const moviePoster = document.createElement('img');
                moviePoster.src = "/" + movie.MoviePoster;
                movieCard.append(moviePoster);

                const movieTitle = document.createElement('div');
                movieTitle.textContent = movie.MovieName;
                movieTitle.classList.add('movie-title');
                movieCard.append(movieTitle);

                movieCard.addEventListener("click", function() {
                window.location.href = 'movie.php?movie_id=' + movie.Movie_ID; 
                })

                if (movie.MovieAvailability === 'Now Showing') {
                  nowShowingRow.append(movieCard);
                  nowShowingTotal++;
                } else if (movie.MovieAvailability === 'Coming Soon') {
                  comingSoonRow.append(movieCard);
                  comingSoonTotal++;
                }            
              })

              const noMoviesMessage = document.createElement('div');
              noMoviesMessage.classList.add('movie-card');
              noMoviesMessage.classList.remove('movie-card:hover');
              noMoviesMessage.classList.add('noMoviesMessage');
              noMoviesMessage.textContent = "Seems no movies are currently showing. Please come back soon!";

              const noComingSoon = document.createElement('div');
              noComingSoon.classList.add('movie-card');
              noComingSoon.classList.remove('movie-card:hover');
              noComingSoon.classList.add('noMoviesMessage');
              noComingSoon.textContent = "Seems no movies are coming soon..";
              if (nowShowingTotal == 0) {
                nowShowingRow.append(noMoviesMessage);
              }
              if (comingSoonTotal == 0) {
                comingSoonRow.append(noComingSoon);
              }
              
              const slides = document.querySelectorAll(".slide");
              const dotsContainer = document.querySelector(".dots");
              let current = 0;

              slides.forEach((_,i)=>{
                const dot=document.createElement("div");
                dot.classList.add("dot");
                if(i===0) dot.classList.add("active");
                dot.addEventListener("click",()=>showSlide(i));
                dotsContainer.appendChild(dot);
              });

              const dots=document.querySelectorAll(".dot");

              function showSlide(index){
                slides[current].classList.remove("active");
                dots[current].classList.remove("active");
                current=index;
                slides[current].classList.add("active");
                dots[current].classList.add("active");
              }

              setInterval(()=>{
                let next=(current+1)%slides.length;
                showSlide(next);
              },5000);

              document.querySelector('.hero-slider').classList.add('shown');
              document.querySelectorAll('.movie-card').forEach((indiv, i) => {
                setTimeout(() => {
                  indiv.classList.add('shown');
                }, i * 100);
              });
            }, 500)  
            
          }       
        })        
      })

      window.addEventListener("scroll",()=>{
        document.querySelector("header")
        .classList.toggle("scrolled",window.scrollY>50);
      });

      document.querySelectorAll('.movie-card').forEach(card => {
        console.log("test");
          const video = card.querySelector('video');
          const volumeBtn = card.querySelector('.volume-btn');
          if (!video || !volumeBtn) return;

          let isMuted = true;

          card.addEventListener('mouseenter', () => {
              console.log("test");
              video.play().catch(() => {});
              volumeBtn.style.opacity = '1';
          });

          card.addEventListener('mouseleave', () => {
              video.pause();
              video.currentTime = 0;
              video.muted = true;
              isMuted = true;
              volumeBtn.textContent = '🔇';
              volumeBtn.style.opacity = '0';
          });

          volumeBtn.addEventListener('click', (e) => {
              e.stopImmediatePropagation();
              isMuted = !isMuted;
              video.muted = isMuted;
              volumeBtn.textContent = isMuted ? '🔇' : '🔊';
          });
      });

      if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
          navigator.serviceWorker.register('./sw.js')
            .then(reg => console.log('Service Worker Registered!'))
            .catch(err => console.log('Registration failed:', err));
        });
      }
    </script>
  </body>
</html>

<!-- <?php while ($row = $now_showing_results->fetch_assoc()): ?>
      <div class="movie-card"
           onclick="window.location.href='movie.php?movie_id=<?= $row['Movie_ID']?>'">
        
        <img src="/<?= htmlspecialchars($row['MoviePoster']) ?>">
        
        <?php if(!empty($row['MovieTrailer'])): ?>
        <div class="trailer-preview">
          <video src="trailers/<?= htmlspecialchars($row['TrailerURL']) ?>" 
                 muted loop playsinline preload="none"></video>
          
          <div class="volume-btn" title="Toggle sound">🔇</div>
        </div>
        <?php endif; ?>
        
        <div class="movie-title"><?= htmlspecialchars($row['MovieName']) ?></div>
      </div>
    <?php endwhile; ?> -->

    <!-- <?php while ($row = $coming_soon_results->fetch_assoc()): ?>
          <div class="movie-card"
              onclick="window.location.href='cs-movie.php?movie_id=<?= $row['Movie_ID']?>'">
            
            <img src="/<?= htmlspecialchars($row['MoviePoster']) ?>">
            
            <?php if(!empty($row['MovieTrailer'])): ?>
            <div class="trailer-preview">
              <video src="trailers/<?= htmlspecialchars($row['MovieTrailer']) ?>" 
                    muted loop playsinline preload="none"></video>
              
              <div class="volume-btn" title="Toggle sound">🔇</div>
            </div>
            <?php endif; ?>
            
            <div class="movie-title"><?= htmlspecialchars($row['MovieName']) ?></div>
          </div>
        <?php endwhile; ?> -->