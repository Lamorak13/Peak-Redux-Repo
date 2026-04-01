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
                <form id="theaterMenu" class="form">
                    <div id="scrollable">
                        <button type="button" id="theBackButton" class="generalAdminButton" onclick="theaterMenuOpenClose()">Back</button>
                        
                        <div id="theaterMenuTop">
                            <div id="theaterMenuLeft">
                                <label for="TheaterName">Theater Name: </label>
                                <input type="text" id="TheaterName" name="theaterName" placeholder="Theater Name" required>

                                <label for="TheaterType">Theater Type: </label>
                                <input type="text" id="TheaterType" name="theaterType" placeholder="Theater Type" required>
                            </div>
                            <label for="seatPlanInput" id="seatPlanInputLabel"><span>Please insert a theater layout JSON</span></label>
                            <input type="file" id="seatPlanInput" name="theaterLayoutUp" accept=".json" required>
                        </div>

                        <div id="seatPlanContainer">

                        </div>

                        <button type="submit" id="movieSubmitButton" class="generalAdminButton">Add</button>
                    </div>
                </form>
            </div>            
        </main>
        <script>
            const theaterGallery = document.getElementById('theaterGallery');
            function getTheaters() {
                theaterGallery.innerHTML = "";
                
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
            }

            document.addEventListener("DOMContentLoaded", function() {
                getTheaters();
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

            const seatPlanInput = document.getElementById('seatPlanInput');
            seatPlanInput.addEventListener("change", function() {
                const reader = new FileReader();
                reader.addEventListener('load', () => {
                    try {
                        let theaterLayoutJSON = JSON.parse(reader.result);
                        const seats = theaterLayoutJSON.seats;
                        let seatCount = 0;

                        seatPlanContainer.innerHTML = "";
                        
                        for (const row in seats) {
                            const rowDiv = document.createElement('div');
                            rowDiv.classList.add('seatRow');
                            
                            const rowLabelStart = document.createElement('div');
                            rowLabelStart.classList.add('rowLabel');
                            rowLabelStart.textContent = row;
                            rowDiv.append(rowLabelStart);

                            for (const col of seats[row]) {
                                const label = document.createElement('label');
                                label.classList.add('seatLabel');

                                if (col.SeatColumn == 0) {
                                    const seatBox = document.createElement('div');
                                    seatBox.textContent = col.SeatColumn;
                                    seatBox.classList.add('aisle');
                                    rowDiv.appendChild(seatBox);
                                } else if (col.SeatAvailability == 0){
                                    const seatBox = document.createElement('div');
                                    seatBox.textContent = col.SeatColumn;
                                    seatBox.classList.add('unavailableSeat');
                                    rowDiv.appendChild(seatBox);
                                } else {
                                    label.appendChild(document.createTextNode(col.SeatColumn));
                                    rowDiv.appendChild(label);
                                }
                            }
                            const rowLabelEnd = document.createElement('div');
                            rowLabelEnd.classList.add('rowLabel');
                            rowLabelEnd.textContent = row;
                            rowDiv.append(rowLabelEnd);

                            document.getElementById('seatPlanContainer').appendChild(rowDiv);
                        }
                    } catch (error) {
                        console.error(error);
                    }
                })

                reader.readAsText(seatPlanInput.files[0]);
            })

            const theaterMenuForm = document.getElementById('theaterMenu');
            theaterMenuForm.addEventListener("submit", function(e) {
                e.preventDefault();

                const formData = new FormData(theaterMenuForm);

                fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=theater/', {
                    method: "POST",
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data.status);
                    seatPlanContainer.innerHTML = "";
                    getTheaters();
                    theaterMenuOpenClose();
                    theaterMenuForm.reset();

                })
                .catch(error => {
                    console.error(error);
                })
            })
        </script>
    </body>
</html>