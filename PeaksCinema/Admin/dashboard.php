<?php
    include("peakscinemas_database.php");
    $stmt = $conn -> prepare("SELECT SUM(Price) FROM ticket");
    $stmt -> execute();
    $stmt -> bind_result($TotalSales);
    $stmt -> fetch();
    $stmt -> close();


    function retrieveSeatData($conn, $theaterType) {
        try {
            $retrieveSeatType_stmt = $conn -> prepare("SELECT SUM(Price) FROM ticket 
                                                       INNER JOIN seats ON ticket.Seat_ID=seats.Seat_ID
                                                       INNER JOIN theater ON theater.Theater_ID = seats.Theater_ID
                                                       WHERE TheaterType = ?");
            $retrieveSeatType_stmt -> bind_param("s", $theaterType);
            $retrieveSeatType_stmt -> execute();
            $retrieveSeatType_stmt -> bind_result($seatTypeRevenue);
            $retrieveSeatType_stmt -> fetch();

            $retrieveSeatType_stmt -> close();

            return($seatTypeRevenue);
        } catch (Exception $e) {
            return null;
        }
    }

    $retrieveCustomerInfo_stmt = $conn -> prepare("SELECT customer.Customer_ID, customer.Name, SUM(ticket.Price) AS TotalPaid FROM customer
                                                   INNER JOIN ticket ON customer.Customer_ID=ticket.Customer_ID
                                                   GROUP BY customer.Customer_ID
                                                   ORDER BY TotalPaid DESC");
    $retrieveCustomerInfo_stmt -> execute();
    $customerResults = $retrieveCustomerInfo_stmt -> get_result();

    $yearSelected = 2025;

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['salesYear'])) {
        $yearSelected = $_POST['salesYear'];

        $retrieveSalesFromMonth_stmt = $conn -> prepare("SELECT MONTH(DateTime) AS Month, SUM(Price) AS TotalSales FROM ticket
                                                         WHERE YEAR(DateTime) = ?
                                                         GROUP BY MONTH(DateTime)
                                                         ORDER BY Month");
        $retrieveSalesFromMonth_stmt -> bind_param("i", $yearSelected);
        $retrieveSalesFromMonth_stmt -> execute();
        $retrievedResults = $retrieveSalesFromMonth_stmt -> get_result();

        $months = [
            "January" => 0, "February" => 0, "March" => 0,
            "April" => 0, "May" => 0, "June" => 0,
            "July" => 0, "August" => 0, "September" => 0,
            "October" => 0, "November" => 0, "December" => 0
        ];
        $sales = [];

        while ($row = $retrievedResults -> fetch_assoc()) {
            $monthName = date("F", mktime(0, 0, 0, $row['Month'], 10));
            $months[$monthName] = $row['TotalSales'];
        }

        $labels = array_keys($months);
        $sales = array_values($months);
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
        <style>
            body {
                display: flex;
                flex-direction: column;
                align-items: center;
                min-height: 100vh;
                margin: 0;      
                background: linear-gradient(90deg,rgba(106, 127, 63, 1) 0%, rgba(74, 106, 90, 1) 100%);
                padding-top: 150px;
            }

            header {
                border: 4px solid black;
                border-bottom: none;
                border-top-left-radius: 25px;
                border-top-right-radius: 25px;
                background: rgba(255, 255, 255, 0.8);
                overflow: hidden;
                padding: 0px;
            }

            nav {
                display: flex;
            }

            a {
                padding: 5px 10px;
                text-decoration: none;
                border-radius: 10px 10px 0 0;
                border-bottom: none;
                color: black;
            }

            a:hover {
                background: rgba(70, 58, 58, 0.8);
                color: white;
            }

            #detailsSection {
                display: flex;
                flex-direction: column;
                border: 4px solid black;
                border-radius: 50px;
                overflow: hidden;
                background: rgba(255, 255, 255, 0.8)
            }

            .row {
                height: auto;
                width: auto;
                display: flex;
                gap: 20px;
                padding: 25px;
            }

            #one {
                display: flex;
                flex-direction: column;
                border-bottom: 2px solid black;
                width: 100%;
                justify-content: center;
                text-align: center;
                padding: 5px;
            }

            #selectYear {
                border-right: 2px solid black;
            }

            table, th, td {
                border: 2px solid black;
                border-collapse: collapse;
                margin: auto;
                padding: 5px;
            }

            tr {
                border: 1px solid black;
            }

            .chart {
                width: 600px;
                height: 300px;
            }

            input, textarea, select, button {
                border-radius: 15px;
                padding: 5px;
            }
        </style>
    </head>
    <body>
        <header>
            <nav>
                <a href="dashboard.php" target="_self">Dashboard</a>
                <a href="malls_selection_admin.php" target="_self">Malls</a>
                <a href="movie_upload.php" target="_self">Movie Upload</a>
                <a href="theater_upload.php" target="_self">Theater Upload</a>
                <a href="mall_upload.php" target="_self">Mall Upload</a>
            </nav>
        </header>
        <main>
            <section id="detailsSection">
                <div class="row" id="one">
                    <div>
                        <p><strong>Total Sales (Lifetime): ₱<?= number_format($TotalSales, 2) ?></strong></p>
                    </div>
                    <div>
                        <h><strong>High Paying Customers:</strong></h>
                        <table>
                            <tr>
                                <th>Customer_ID</th>
                                <th>Customer Name</th>
                                <th>Total Paid</th>
                            </tr>                    
                            <?php while($row = $customerResults -> fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['Customer_ID']); ?></td>
                                    <td><?= htmlspecialchars($row['Name']); ?></td>
                                    <td><?= htmlspecialchars($row['TotalPaid']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    </div>                    
                </div>
                <div class="row">
                    <div id="selectYear">
                        <form name="selectYearForm" id="selectYearForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <label for="salesYear">Type a year:</label>
                            <input type="number" id="salesYear" name="salesYear" min=2025 value=2025>
                        </form>
                        <div class="chart">
                            <canvas id="monthlyTrendsChart" style="width:100%;max-width:700px"></canvas>
                        </div>
                    </div>
                    <div class="chart">
                        <canvas id="seatTypeChart" style="width:100%;max-width:700px"></canvas>
                    </div>
                </div>
            </section>            
        </main>
        <script>
            const yearSelect = document.getElementById("salesYear");
            const selectYearForm = document.getElementById("selectYearForm");

            yearSelect.addEventListener("change", function() {
                let selectedDate = yearSelect.value;
                selectYearForm.submit();
            })

            const maxValue = Math.max(...<?= json_encode($sales) ?>);
            new Chart("monthlyTrendsChart", {
                type: "line",
                data: {
                    labels: <?= json_encode($labels) ?>,
                    datasets: [{
                        label: "Monthly Ticket Sales",
                        backgroundColor: "rgba(0, 0, 255, 0.2)",
                        fill: false,
                        borderColor: "rgba(0, 0, 255, 0.5)",
                        data: <?= json_encode($sales) ?>
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {beginAtZero: true, min: 0}
                        }],
                    }
                }
            })

            new Chart("seatTypeChart", {
                type: "bar",
                data: {
                    labels: ["Top-Selling Seat Types"],
                    datasets: [
                        {
                            label: "Regular",
                            backgroundColor: "blue",
                            data: [<?= retrieveSeatData($conn, "Regular") ?>]
                        },
                        {
                            label: "Director's Club",
                            backgroundColor: "red",
                            data: [<?= retrieveSeatData($conn, "Director's Club") ?>]
                        }
                    ]
                },
                options: {
                    scales: {
                        xAxes: [{
                            barPercentage: 0.4,
                            categoryPercentage: 0.4
                        }],
                        yAxes: [{
                            ticks: {
                                callback: function(value) {
                                    return Math.round(value / 100) * 100;
                                }
                            }
                        }]
                    }
                }
            })
        </script>
    </body>
</html>