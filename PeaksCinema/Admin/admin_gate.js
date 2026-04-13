const admin_gate = {
    gatekeep: function(requiredLevel = 1) {
        const token = localStorage.getItem('jwt_token');
        if (!token) {
            window.location.href = 'admin_login.php';
            return;
        }

        let payload;
        try {
            payload = JSON.parse(atob(token.split('.')[1]));
        } catch (e) {
            localStorage.removeItem('jwt_token');
            window.location.href = 'admin_login.php';
            return;
        }

        const currentTime = Math.floor(Date.now() / 1000);

        if (!payload || payload.exp < currentTime) {
            localStorage.removeItem('jwt_token');
            window.location.href = 'admin_login.php';
            return;
        }

        const userLevel = payload.access_level;

        if (payload.access_level < requiredLevel) {
            switch (userLevel) {
                case 0:
                    window.location.href = 'staff_cashier.php';
                    break;
                case 1:
                case 2:
                    window.location.href = 'admin_dashboard.php';
                    break;
                default: 
                    window.location.href = 'admin_login.php';
                    break;
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            function createA(id, href, buttonText) {
            const element = document.createElement('a');
            element.id = id;
            element.href = href;
            element.innerText = buttonText
            document.getElementById('navAdmin').append(element);
    }
            switch (userLevel) {
                case 0:
                    createA('staff_cashier', 'staff_cashier.php', "Cashier");
                    break;
                case 1:
                    createA('staff_cashier', 'staff_cashier.php', "Cashier");
                    createA('dashboard', 'admin_dashboard.php', "Dashboard");
                    createA('admin_movie-gallery', 'admin_movie-gallery.php', "Movies");
                    createA('admin_theater-gallery', 'admin_theater-gallery.php', "Theaters");
                    break;
                case 2:    
                    createA('staff_cashier', 'staff_cashier.php', "Cashier");        
                    createA('dashboard', 'admin_dashboard.php', "Dashboard");
                    createA('admin_movie-gallery', 'admin_movie-gallery.php', "Movies");
                    createA('admin_theater-gallery', 'admin_theater-gallery.php', "Theaters");
                    createA('admin_management', 'admin_management.php', 'Admin Management');
                    break;
            }
        })
    }
}