<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
    </head>
    <body>
        <main>
            <section id="loginFormContainer">
                <form id="loginForm">
                    <img src="peakscinematransparent.png" style="width: 50%; height: 50%; background-color: #f6e8e085; border-radius: 15px; padding: 0 5px 0 5px">
                    <div class="field">
                        <label for="emailField">Email: </label>
                        <input type="email" id="emailField" name="emailField" placeholder="Email" autocomplete="off">
                    </div>

                    <div class="field">
                        <label for="passwordField">Password: </label>
                        <input type="password" id="passwordField" name="passwordField" placeholder="Password" autocomplete="off">
                    </div>

                    <button type="submit" class="generalAdminButton">Log In</button>
                </form>
            </section>
        </main>
        <script>
            const loginForm = document.getElementById('loginForm');

            loginForm.addEventListener("submit", function(e) {
                e.preventDefault();

                const formData = new FormData(loginForm);

                fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=admin_login', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        "Email": formData.get('emailField'),
                        "Password": formData.get('passwordField')
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        console.log(response.error);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.token) {
                        const jwt_token = data.token;
                        localStorage.setItem('jwt_token', jwt_token);
                        
                        let payload;
                        try {
                            payload = JSON.parse(atob(jwt_token.split('.')[1]));
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
                    } else {
                        console.log("login failed.");
                    }
                })
                .catch(error => {
                    console.error(error);
                })
            })
        </script>
    </body>
</html>