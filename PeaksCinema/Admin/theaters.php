<html>
    <head>
        <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
    </head>
    <body onload="getTheaters()">
        
        <?php include("header_admin.php") ?>
        <main>
            <section id="theatersGallery" class="gallery">
                <div id="availableTheatersText" class="availableText">Available Theaters</div>
                <span><button type="button" id="addTheaterButton" class="generalAdminButton" onclick="openTheaterMenu(true)">Add New Theater</button></span>
                <div id="availableTheaters"></div>                
            </section>
            <div id="addTheaterMenuContainer">
                <div id = "addTheaterMenu">
                    <div class="scrollContent">
                        <button type="button" id="" class="generalAdminButton" onclick="openTheaterMenu(false)">Back</button>
                        <form id = "theaterForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
                            <div id="leftSection">
                                <div>
                                    <label for="theaterName">Theater Name: </label>
                                    <input type="text" id="theaterName" name="theaterName" placeholder="Theater Name" required>
                                </div>
                                <br>

                                <div>
                                    <label for="theaterType">Theater Type: </label>
                                    <input type="text" id="theaterType" name="theaterType" placeholder="Theater Type" required>
                                </div>
                                <br>

                                <div>
                                    <label for="theaterLayoutUp">Theater Layout: </label><br>
                                    <input type="file" id="theaterLayoutUp" name="theaterLayoutUp" accept=".json" required>
                                </div>
                                <br>

                                <div>
                                    <label for="totalSeats">Total Seats: <input type="hidden" id="totalSeats" name="totalSeats">
                                    <span id = "totalSeatsNum"></span></label>
                                </div>
                                <br>

                                <div>
                                    <button type="submit" name="movieDetails" class="generalAdminButton" value="movieDetails">Add</button>
                                </div>
                            </div>
                            <div id="rightSection">
                                <div id = "theaterLayoutSection">
                                    <span>Layout Preview:</span><br><br>
                                    <div id = "theaterScreen"></div>
                                    <div id = "theaterLayoutPreview"></div>
                                    <table id = 'tablelayout'></table>
                                </div>
                            </div>
                        </form>                        
                    </div>
                </div>
            </div>            
        </main>

        <script>
            addTheaterMenu = document.getElementById('addTheaterMenu');
            addTheaterMenuContainer = document.getElementById('addTheaterMenuContainer');
            addTheaterButton = document.getElementById('addTheaterButton');
            function openTheaterMenu(isOpen) {
                if (isOpen) {
                    addTheaterButton.disabled = true;
                    addTheaterMenuContainer.style.display = 'flex';
                } else {
                    addTheaterButton.disabled = false;
                    addTheaterMenuContainer.style.display = 'none'; 
                }
            }

            const theaterScreen = document.getElementById("theaterScreen");

            const theaterLayoutUp = document.getElementById("theaterLayoutUp");
            const theaterLayoutPreview = document.getElementById("theaterLayoutPreview");

            const totalSeatsNum = document.getElementById("totalSeatsNum");
            const totalSeats = document.getElementById("totalSeats");

            var theaterLayoutJSON = "";

            theaterLayoutUp.addEventListener('change', function() {                        
                const tableLayout = document.getElementById('tablelayout');
                tableLayout.innerHTML = '';

                const reader = new FileReader();
                reader.addEventListener('load', () => {
                    try {
                        theaterLayoutJSON = JSON.parse(reader.result);
                        const seatLayout = theaterLayoutJSON.seats;
                        let seatCount = 0;
                        
                        for (const seatRow in seatLayout) {
                            const seatCols = seatLayout[seatRow]
                                .map(col => {
                                    const seatCol = col.SeatColumn;
                                    const seatType = col.SeatType.toLowerCase();

                                    if (Number(seatCol) == 0 || seatType === "Empty") {
                                        return `<td class = "theaterSeat emptySeat"></td>`;
                                    }                                    
                                    seatCount += 1;
                                    return `<td class = "theaterSeat">${seatCol}</td>`;
                                })
                                .join('');

                            tableLayout.innerHTML += `<tr><th>${seatRow}</th><td>${seatCols}</td><th>${seatRow}</th></tr>`;                  
                        }

                        theaterScreen.classList.add("screenCSS");
                        theaterScreen.innerHTML = "SCREEN";

                        totalSeatsNum.innerHTML = `${seatCount}`;
                        totalSeats.value = seatCount;
                        

                    } catch (error) {
                        theaterLayoutPreview.innerText = "There was an error with the JSON file. Please try another or fix the errors within this one.";
                        console.error(error);
                    }
                })

                reader.readAsText(this.files[0]);
            })

            availableTheaters = document.getElementById('availableTheaters');
            function getTheaters() {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        availableTheaters.innerHTML = this.responseText;

                        const theaterContainers = document.querySelectorAll('.theaterContainer');
                        theaterContainers.forEach(e => {
                            e.addEventListener("click", function() {
                                window.location.href = 'theater_individual.php?id=' + e.dataset.id;
                            })
                        });
                    }                    
                };                
                xmlhttp.open("GET", "queries_admin.php?q=theaters", true);
                xmlhttp.send();
            }   
        </script>
    </body>
</html>