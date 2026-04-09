<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
    </head>
    <body>
        <?php include("admin_header.php"); ?>
        <main>
            <div>
                <label for="inputYear">Year: </label>
                <input type="number" id="inputYear">
                <canvas id="monthlySales"></canvas>
            </div>
        </main>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>

            document.addEventListener("DOMContentLoaded", function() {
                currentYear = new Date().getFullYear();

                getMonthlySales(currentYear);
                
                document.getElementById('inputYear').setAttribute('min', +currentYear);
                document.getElementById('inputYear').value = +currentYear;
            })

            document.getElementById('inputYear').addEventListener("change", function() {
                getMonthlySales(this.value);
            })
            
            monthlySales = null;
            function getMonthlySales(year) {
                fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=monthly_sales/${year}`, {
                    method: 'GET'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const monthly_sales = data.data;

                    if (monthlySales) {
                        monthlySales.destroy();
                    }

                    monthlySales = new Chart(
                        document.getElementById('monthlySales'),
                        {
                            type: 'line',
                            data: {
                                labels: monthly_sales.map(month => month.month),
                                datasets: [
                                    {
                                        label: 'Sales per Month',
                                        data: monthly_sales.map(month => month.revenue),
                                        fill: false,
                                        tension: 0.1
                                    }
                                ]
                            },
                            options: {
                                scales: {
                                    y: {
                                        min: 0,
                                        suggestedMax: 2500
                                    }
                                }
                            }
                        }
                    )
                })
                .catch(error => {
                    console.error(error);
                });
            }
            
        </script>
    </body>
</html>