<?php 
    include("peakscinemas_database.php");
    
    $stmt = $conn -> prepare("SELECT * FROM mall");
    $stmt -> execute();
    $existingMalls = $stmt -> get_result();
    
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
    <head>
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

            main {
                display: flex;
                flex-direction: column;
                border: 4px solid black;
                border-radius: 50px;
                overflow: hidden;
                background: rgba(255, 255, 255, 0.8);
                width: 35%;
                text-align: center;
                align-items: center;
                justify-content: center;
            }

            main a {
                border: 2px solid black;
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
            <p>Please select a mall:</p>
            <?php
                while($row = $existingMalls -> fetch_assoc()) {
                    echo "<a href ='mall_admin.php?mall_id=" . urlencode($row['Mall_ID']) . "'>" . htmlspecialchars($row['MallName']) . "</a><br>";
                }
            ?>
        </main>
    </body>
</html>