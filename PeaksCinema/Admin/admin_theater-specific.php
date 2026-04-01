<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
    </head>
    <body>
        <?php include("admin_header.php"); ?>
        <main>
            <section id="content">
                <div id="theaterDetailsContainer"></div>
                <div id="seatPlanContainer">
            </section>
        </main>
        <script>
            const url = new URL(window.location.href);
            const Theater_ID = url.searchParams.get('theater_id');

            const theaterDetailsContainer = document.getElementById('theaterDetailsContainer');

            document.addEventListener("DOMContentLoaded", function() {
                fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=theater/${Theater_ID}/seats`, {
                    method: 'GET'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        window.location.href = "admin_theater-gallery.php";
                        return;
                    }
                    const theater = data.data.theater;
                    const seats = data.data.seats;

                    const theaterName = document.createElement('div');
                    theaterName.classList.add('theaterContainer');
                    theaterName.textContent = theater.TheaterName;
                    theaterDetailsContainer.append(theaterName);

                    const theaterType = document.createElement('div');
                    theaterType.classList.add('theaterType');
                    theaterType.textContent = theater.TheaterType;
                    theaterDetailsContainer.append(theaterType);

                    const deleteTheaterButton = document.createElement('button')
                    deleteTheaterButton.classList.add('deleteDaterange');
                    deleteTheaterButton.textContent = "Delete Theater From System";
                    deleteTheaterButton.addEventListener("click", () => areYouSure("theater", theater.Theater_ID, theater.TheaterName));
                    theaterDetailsContainer.append(deleteTheaterButton);
                })
                .catch(error => {
                    console.error(error);
                });
            })
        </script>
        <script src="admin.js"></script>
    </body>
</html>