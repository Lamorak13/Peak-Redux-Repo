<style>
    body {
        font-family: 'Outfit', sans-serif;
        background: url("movie-background-collage.jpg") no-repeat center center fixed;
        background-size: cover;
        margin: 0;
    }
    body.active {
        background-color: rgba(43, 2, 2, 0.47);
    }

    header {        
        background-color: #1C1C1C;
        color: #F9F9F9;
        display: flex;
        align-items: center;
        padding: 10px 30px;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        gap: 10px;
        height: 5%;
    }

    header a {
        border: 2px solid black;
        padding: 5px;
        border-radius: 8px;
        background-color: white;
        color: black;
        font-weight: bold;
        text-decoration: none;
        transition: border 0.3s, padding 0.3s, transform 0.3s;
    }
    header a:hover {
        border: 2px solid #ff4d4d;
        padding: 7px;
        transform: scale(1.05);
    }

    #logo{
        height: 50px;
        cursor: pointer;
        transition: transform 0.2s ease;
        filter: invert(1);
        margin: 0 15px 0 0;
    }
    #logo:hover {
        transform: scale(1.05);
    }
</style>
<header>
    <img src= "peakscinematransparent.png" id="logo">
    <a href="dashboard.php">Dashboard</a>
    <a href="movies.php">Movies</a>
</header>