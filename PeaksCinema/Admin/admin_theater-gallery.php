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
                <button type="button" class="generalAdminButton">Add New Theater</button>
                <div id="theaterGallery" class="gallery"></div>
            </section>
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
            </script>
        </main>
    </body>
</html>