const admin_gate = {
    gatekeep: function(requiredLevel = 1) {
        const token = localStorage.getItem('admin_jwt_token');
        if (!token) {
            window.location.href = 'admin_login.php';
            return;
        }

        let payload;
        try {
            payload = JSON.parse(atob(token.split('.')[1]));
        } catch (e) {
            localStorage.removeItem('admin_jwt_token');
            window.location.href = 'admin_login.php';
            return;
        }

        const currentTime = Math.floor(Date.now() / 1000);

        if (!payload || payload.exp < currentTime) {
            localStorage.removeItem('admin_jwt_token');
            window.location.href = 'admin_login.php';
            return;
        }

        if (payload.role !== 'admin') {
            window.location.href = 'admin_login.php';
            return;
        }

        // If this admin is still using the temporary password, force them onto the reset page.
        const currentPage = (window.location.pathname.split('/').pop() || '').toLowerCase();
        if (payload.must_reset === true && currentPage !== 'admin_reset_password.php') {
            window.location.href = 'admin_reset_password.php';
            return;
        }

        const userLevel = parseInt(payload.access_level);

        if (userLevel < requiredLevel) {
            // Redirect to the highest page they ARE allowed on
            if (userLevel === 0) {
                window.location.href = 'staff_cashier.php';
            } else if (userLevel >= 1) {
                window.location.href = 'admin_dashboard.php';
            } else {
                window.location.href = 'admin_login.php';
            }
            return; // CRITICAL — stop execution after redirect
        }

        document.addEventListener("DOMContentLoaded", function() {
            function createA(id, href, buttonText) {
                const element = document.createElement('a');
                element.id = id;
                element.href = href;
                element.innerText = buttonText;
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
                    createA('admin_refunds', 'admin_refunds.php', "Refunds");
                    break;
                case 2:
                    createA('staff_cashier', 'staff_cashier.php', "Cashier");
                    createA('dashboard', 'admin_dashboard.php', "Dashboard");
                    createA('admin_movie-gallery', 'admin_movie-gallery.php', "Movies");
                    createA('admin_theater-gallery', 'admin_theater-gallery.php', "Theaters");
                    createA('admin_refunds', 'admin_refunds.php', "Refunds");
                    createA('admin_management', 'admin_management.php', 'Admin Management');
                    break;
            }
        });
    }
}