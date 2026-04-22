<!DOCTYPE HTML>
<html>
    <head>
        <script src="admin_gate.js?v=2"></script>
        <script>admin_gate.gatekeep(1);</script>
        <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
        <style>
            main {
                display: grid;
                gap: 20px;
                padding: 20px;
                align-items: start;
                box-sizing: border-box;
            }

            /* ── LEFT COLUMN ── */
            .chartsSection {
                display: flex;
                flex-direction: column;
                gap: 0;
                min-width: 0;
            }

            .dashboardTabs {
                display: flex;
                gap: 5px;
                padding: 0;
                background-color: transparent;
            }

            .dashboardTab {
                padding: 10px 18px;
                background-color: #14272a;
                color: #8a9bad;
                border: 2px solid #f6e8e0;
                border-bottom: none;
                cursor: pointer;
                font-weight: bold;
                font-size: 0.9rem;
                transition: all 0.2s;
                border-radius: 12px 12px 0 0;
                position: relative;
                top: 2px;
            }

            .dashboardTab:hover {
                background-color: #1a3a3f;
                color: #f6e8e0;
            }

            .dashboardTab.active {
                background-color: #f6e8e0;
                color: #0c181a;
                top: 0;
            }

            .dashboardContent {
                padding: 20px;
                background-color: #0c181a;
                border-radius: 0 12px 12px 12px;
                border: 2px solid #f6e8e0;
            }

            .tabContent {
                display: none;
            }

            .tabContent.active {
                display: block;
            }

            .chartContainer {
                position: relative;
                height: 380px;
                margin-top: 16px;
            }

            .yearControl {
                display: flex;
                gap: 10px;
                align-items: center;
            }

            .yearControl label {
                font-weight: bold;
                color: #f6e8e0;
            }

            .yearControl input {
                border: 2px solid #f6e8e0;
                border-radius: 8px;
                padding: 6px 10px;
                background-color: #0c181a;
                color: white;
                width: 90px;
            }

            /* ── RIGHT COLUMN ── */
            .tablesSection {
                display: flex;
                flex-direction: column;
                gap: 0;
                position: sticky;
                top: 70px; /* clears the fixed header */
            }

            .tablesSectionHeader {
                padding: 10px 18px;
                background-color: #14272a;
                color: #f6e8e0;
                border: 2px solid #f6e8e0;
                border-bottom: none;
                font-weight: bold;
                font-size: 0.9rem;
                border-radius: 12px 12px 0 0;
                position: relative;
                top: 2px;
                width: fit-content;
            }

            .moviesTableContainer {
                padding: 20px;
                background-color: #0c181a;
                border-radius: 0 12px 12px 12px;
                border: 2px solid #f6e8e0;
            }

            .moviesTable {
                width: 100%;
                border-collapse: collapse;
            }

            .moviesTable thead {
                background-color: #14272a;
                border-bottom: 2px solid #f6e8e0;
            }

            .moviesTable th {
                padding: 10px 12px;
                text-align: left;
                font-weight: bold;
                color: #f6e8e0;
                border: 1px solid rgba(246,232,224,0.3);
                font-size: 0.85rem;
            }

            .moviesTable td {
                padding: 10px 12px;
                color: #f6e8e0;
                border: 1px solid rgba(246,232,224,0.15);
                font-size: 0.9rem;
            }

            .moviesTable tbody tr:hover {
                background-color: #14272a;
            }

            .moviesTable tbody tr:nth-child(even) {
                background-color: #0a1214;
            }

            .rankBadge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 28px;
                height: 28px;
                border-radius: 50%;
                font-weight: bold;
                font-size: 0.85rem;
            }

            .rankBadge.gold   { background-color: #f59e0b; color: #1a1a1a; }
            .rankBadge.silver { background-color: #9ca3af; color: #1a1a1a; }
            .rankBadge.bronze { background-color: #b45309; color: white; }
            .rankBadge.other  { background-color: #14272a; color: #f6e8e0; border: 1px solid #f6e8e0; }

            .rankColumn  { width: 15%; text-align: center; }
            .movieNameColumn { width: 55%; }
            .revenueColumn { width: 30%; text-align: right; font-weight: bold; color: #2dd4bf; }
        </style>
    </head>
    <body>
        <?php include("admin_header.php"); ?>
        <main>

            <!-- ══ LEFT: Charts ══ -->
            <div class="chartsSection">
                <div class="dashboardTabs">
                    <button class="dashboardTab active" onclick="switchTab('sales', this)">Monthly Sales</button>
                    <button class="dashboardTab"        onclick="switchTab('theater', this)">Theater Analytics</button>
                </div>

                <!-- Sales Tab -->
                <div id="sales" class="tabContent active">
                    <div class="dashboardContent">
                        <div class="yearControl">
                            <label for="inputYear">Year:</label>
                            <input type="number" id="inputYear">
                        </div>
                        <div class="chartContainer">
                            <canvas id="monthlySales"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Theater Analytics Tab -->
                <div id="theater" class="tabContent">
                    <div class="dashboardContent">
                        <div class="yearControl">
                            <label for="theaterYear">Year:</label>
                            <input type="number" id="theaterYear">
                        </div>
                        <div class="chartContainer">
                            <canvas id="theaterChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tablesSection">
                <div class="tablesSectionHeader">Top Grossing Movies</div>
                <div class="moviesTableContainer">
                    <table class="moviesTable">
                        <thead>
                            <tr>
                                <th class="rankColumn">Rank</th>
                                <th class="movieNameColumn">Movie</th>
                                <th class="revenueColumn">Revenue</th>
                            </tr>
                        </thead>
                        <tbody id="moviesTableBody">
                            <tr>
                                <td colspan="3" style="text-align:center; color:#8a9bad;">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            let monthlySalesChart = null;
            let theaterChart      = null;

            const colors = [
                '#2dd4bf','#14b8a6','#0d9488','#047857',
                '#059669','#10b981','#34d399','#6ee7b7',
                '#a7f3d0','#d1fae5','#f0fdfa','#ecfdf5'
            ];

            document.addEventListener("DOMContentLoaded", function () {
                const currentYear = new Date().getFullYear();

                const inputYear   = document.getElementById('inputYear');
                const theaterYear = document.getElementById('theaterYear');

                inputYear.min   = currentYear - 5;
                inputYear.value = currentYear;
                theaterYear.min   = currentYear - 5;
                theaterYear.value = currentYear;

                getMonthlySales(currentYear);
                getTheaterAnalytics(currentYear);
                getTopMovies();

                inputYear.addEventListener("change",   () => getMonthlySales(inputYear.value));
                theaterYear.addEventListener("change", () => getTheaterAnalytics(theaterYear.value));
            });

            function switchTab(tabName, btn) {
                document.querySelectorAll('.tabContent').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.dashboardTab').forEach(b => b.classList.remove('active'));
                document.getElementById(tabName).classList.add('active');
                btn.classList.add('active');
            }

            /* ── Chart defaults ── */
            const chartScaleOpts = {
                y: {
                    min: 0,
                    ticks: { color: '#f6e8e0' },
                    grid:  { color: 'rgba(246,232,224,0.1)' }
                },
                x: {
                    ticks: { color: '#f6e8e0' },
                    grid:  { color: 'rgba(246,232,224,0.1)' }
                }
            };

            const chartPluginOpts = {
                legend: { labels: { color: '#f6e8e0' } }
            };

            function getMonthlySales(year) {
                fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=monthly_sales/${year}`)
                .then(r => r.json())
                .then(data => {
                    if (monthlySalesChart) monthlySalesChart.destroy();

                    monthlySalesChart = new Chart(document.getElementById('monthlySales'), {
                        type: 'line',
                        data: {
                            labels: data.data.map(m => m.month),
                            datasets: [{
                                label: 'Sales per Month',
                                data: data.data.map(m => m.revenue),
                                borderColor: '#2dd4bf',
                                backgroundColor: 'rgba(45,212,191,0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointBackgroundColor: '#2dd4bf'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: chartPluginOpts,
                            scales: chartScaleOpts
                        }
                    });
                })
                .catch(console.error);
            }

            function getTheaterAnalytics(year) {
                fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=theater_sales/${year}`)
                .then(r => r.json())
                .then(data => {
                    if (theaterChart) theaterChart.destroy();

                    const months   = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    const theaters = data.data;

                    const otherColors = ['#ff0000','#0000ff','#00cc00','#ffff00','#9900ff','#ff6600'];

                    const datasets = theaters.map((theater, i) => {
                        const isOther = theater.type.toLowerCase().includes('other');
                        const color = isOther ? otherColors[i % otherColors.length] : colors[i % colors.length];

                        return {
                            label: theater.type,
                            data: theater.year.map(m => m.revenue),
                            borderColor: color,
                            backgroundColor: color + '20',
                            fill: false,
                            tension: 0.4,
                            borderWidth: 2,
                            pointBackgroundColor: color
                        };
                    });

                    theaterChart = new Chart(document.getElementById('theaterChart'), {
                        type: 'line',
                        data: { labels: months, datasets },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: chartPluginOpts,
                            scales: chartScaleOpts
                        }
                    });
                })
                .catch(console.error);
            }

            function getTopMovies() {
                fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=top_movies`)
                .then(r => r.json())
                .then(data => {
                    const tbody = document.getElementById('moviesTableBody');
                    tbody.innerHTML = '';

                    if (!data.data || data.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;color:#8a9bad;">No data available</td></tr>';
                        return;
                    }

                    data.data.forEach((movie, i) => {
                        const rank = i + 1;
                        const revenue = parseInt(movie.TotalRevenue);
                        const revenueDisplay = isNaN(revenue) ? '' : `₱${revenue.toLocaleString()}`;

                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="rankColumn">${rank}</td>
                            <td class="movieNameColumn">${movie.MovieName}</td>
                            <td class="revenueColumn">${revenueDisplay}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('moviesTableBody').innerHTML =
                        '<tr><td colspan="3" style="text-align:center;color:#ff4d4d;">Error loading data</td></tr>';
                });
            }
        </script>
    </body>
</html>