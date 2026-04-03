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

                    const theaterDetailsSpan = document.createElement('span');
                    theaterDetailsSpan.classList.add('theaterDetailsSpan');

                    const theaterName = document.createElement('div');
                    theaterName.classList.add('theaterContainer');
                    theaterName.textContent = theater.TheaterName;
                    theaterDetailsSpan.append(theaterName);

                    theaterDetailsSpan.append(" - ");

                    const theaterType = document.createElement('div');
                    theaterType.classList.add('theaterType');
                    theaterType.textContent = theater.TheaterType;
                    theaterDetailsSpan.append(theaterType);

                    theaterDetailsContainer.append(theaterDetailsSpan);

                    const deleteTheaterButton = document.createElement('button')
                    deleteTheaterButton.classList.add('deleteDaterange');
                    deleteTheaterButton.textContent = "Delete Theater From System";
                    deleteTheaterButton.addEventListener("click", () => areYouSure("theater", theater.Theater_ID, theater.TheaterName));
                    theaterDetailsContainer.append(deleteTheaterButton);

                    // Theater Layout

                    const theaterSeatPlan = document.createElement('div');
                    theaterSeatPlan.classList.add('theaterSeatPlan');

                    const screen = document.createElement('div');
                    screen.classList.add('screen');
                    screen.textContent = "SCREEN";
                    theaterSeatPlan.append(screen);

                    const seatPlanContainer = document.createElement('div');

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
                        seatPlanContainer.appendChild(rowDiv);
                    }

                    theaterSeatPlan.append(seatPlanContainer);
                    theaterDetailsContainer.append(theaterSeatPlan);
                })
                .catch(error => {
                    console.error(error);
                });
            })
        </script>
        <script src="admin.js"></script>
    </body>
</html>