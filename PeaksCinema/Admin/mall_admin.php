<?php 
    include("peakscinemas_database.php");
    
    $Mall_ID = filter_input(INPUT_GET, 'mall_id', FILTER_VALIDATE_INT);

    if ($Mall_ID) {
        $stmt = $conn -> prepare("SELECT * FROM mall WHERE Mall_ID = ?");
        $stmt -> bind_param("i", $Mall_ID);
        $stmt -> execute();
        $mallDetails = ($stmt -> get_result()) -> fetch_assoc();

        $theater_stmt = $conn -> prepare("SELECT * FROM theater WHERE Mall_ID = ?");
        $theater_stmt -> bind_param("i", $Mall_ID);
        $theater_stmt -> execute();
        $theatersInMall = $theater_stmt -> get_result();
    }
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
                padding: 20px;
            }

            body #mallDetailsSection {
                width:35%;
                padding: 20px;
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
            <?php if($mallDetails): ?>
                    <div><strong>Location: </strong><?= htmlspecialchars($mallDetails['Location']) ?> </div><br>
                    <div><strong>Theaters in <?= htmlspecialchars($mallDetails['MallName']) ?>:</strong></div><br>

                    <?php while ($row = $theatersInMall -> fetch_assoc()): ?>
                        <a href = "theater_admin.php?mall_id=<?= urlencode($mallDetails['Mall_ID']) ?>&theater_id=<?= urlencode($row['Theater_ID']) ?>" >
                            <?= htmlspecialchars($row['TheaterName']) ?>
                        </a><br>
                    <?php endwhile; ?>
            <?php endif; ?>
            
        </main>
    </body>
</html>