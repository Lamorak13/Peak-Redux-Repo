<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
        <title>Admin - Theater Gallery</title>
    </head>
    <body>
        <?php include("admin_header.php"); ?>  
        <main>
            <section id="theaterGallerySection" class="gallerySection">
                <button type="button" class="generalAdminButton" onclick="theaterMenuOpenClose()">Add New Theater</button>
                <div id="theaterGallery" class="gallery"></div>
            </section>
            <div id="theaterMenuContainer" style="display: none">
                <form id="theaterMenu">
                        <div id="scrollable">
                        <button type="button" id="theBackButton" class="generalAdminButton" onclick="theaterMenuOpenClose()">Back</button>
                            
                        <label for="TheaterName">Theater Name: </label>
                        <input type="text" id="TheaterName" name="TheaterName">

                        <label for="TheaterType">Theater Type: </label>
                        <input type="text" id="TheaterType" name="TheaterType">

                        <button type="submit" id="movieSubmitButton" class="generalAdminButton">Add</button>
                    </div>
                </form>
            </div>            
        </main>
        <script>
                document.addEventListener("DOMContentLoaded", function() {
                    fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=theater', {
                        method: "GET"                    
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            return;
                        }

                        const theaterGallery = document.getElementById('theaterGallery');
                        let theaters = data.data;
                        theaters.forEach(theater => {
                            const theaterContainer = document.createElement('div');
                            theaterContainer.classList.add('theaterContainer');
                            theaterContainer.textContent = theater.TheaterName;
                            theaterContainer.addEventListener("click", function() {
                                window.location.href = 'admin_theater-specific.php?theater_id=' + theater.Theater_ID;
                            })
                            theaterGallery.append(theaterContainer);
                        })
                        
                    })
                    .catch(error => {
                        console.error(error);
                    });
                })
                
                const theaterMenuContainer = document.getElementById('theaterMenuContainer');
                let isTheaterMenuOpen = false
                function theaterMenuOpenClose() {
                    if (isTheaterMenuOpen) {
                        theaterMenuContainer.style.display = "none";
                    } else {
                        theaterMenuContainer.style.display = "flex";
                    }
                    isTheaterMenuOpen = !isTheaterMenuOpen;
                }
            </script>
    </body>
</html>