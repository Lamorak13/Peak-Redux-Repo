// cool name right. could have more edge though

(function() {
    const token = localStorage.getItem('jwt_token');
    
    if (!token) {
        window.location.href = 'admin_login.php';
        return;
    }

    const payload = JSON.parse(atob(token.split('.')[1]));
    const currentTime = Math.floor(Date.now() / 1000);

    if (payload.exp < currentTime) {
        localStorage.removeItem('jwt_token');
        window.location.href = 'admin_login.php';
        return;
    }

    const level = payload.access_level;

    console.log(level);

    function createA(id, href, buttonText) {
        const element = document.createElement('a');
        element.id = id;
        element.href = href;
        element.innerText = buttonText
        document.getElementById('navAdmin').append(element);
    }

    document.addEventListener("DOMContentLoaded", function() {
        switch (level) {
            case 0:
                createA('staff_cashier', 'staff_cashier.php', "Cashier");
                break;
            case 1:
                createA('dashboard', 'admin_dashboard.php', "Dashboard");
                createA('admin_movie-gallery', 'admin_movie-gallery.php', "Movies");
                createA('admin_theater-gallery', 'admin_theater-gallery.php', "Theaters");
                break;
            case 2:            
                createA('dashboard', 'admin_dashboard.php', "Dashboard");
                createA('admin_movie-gallery', 'admin_movie-gallery.php', "Movies");
                createA('admin_theater-gallery', 'admin_theater-gallery.php', "Theaters");
                createA('admin_management', 'admin_management.php', 'Admin Management');
                break;
        }
    })

    
}) ();