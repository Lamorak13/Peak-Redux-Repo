<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PeaksCinemas, Sign Up and Login</title>
<style>
    *{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Segoe UI',sans-serif;
}

body{
min-height:100vh;
display:flex;
flex-direction:column;
align-items:center;
padding-top:120px;
overflow-x:hidden;
position:relative;
background:url("backgroundimage.png") no-repeat center center/cover;
}

body::before{
content:"";
position:absolute;
inset:0;
background:linear-gradient(
    to bottom,
    rgba(7,16,24,0.85),
    rgba(7,16,24,0.95)
)
z-index:0;
}

header{
position:fixed;
width:100%;
top:0;
padding:20px 60px;
display:flex;
justify-content:space-between;
align-items:center;
z-index:1000;
background:linear-gradient(to bottom,rgba(7,16,24,0.95),transparent);
transition:0.3s;
}

.logo img{
height:45px;
cursor:pointer;
}

.container-2{
width:100%;
max-width:480px;
padding:20px;
display:flex;
flex-direction:column;
align-items:center;
animation:fadeIn 0.8s ease-in-out;
}

form, #status{
background:rgba(255,255,255,0.05);
backdrop-filter:blur(2.5px);
padding:35px;
border-radius:15px;
width:100%;
border:1px solid rgba(255,255,255,0.1);
box-shadow:0 8px 40px rgba(0,0,0,0.6);
transition:0.3s;
}

form p{
text-align:center;
font-size:26px;
font-weight:700;
margin-bottom:25px;
letter-spacing:1px;
}

#loginFormContainer {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

#signupForm {    
    display: none;
}

#loginForm {
}

label{
font-weight:600;
margin-top:10px;
display:block;
color:#ccc;
}

input{
width:100%;
padding:12px;
border-radius:8px;
border:none;
outline:none;
background:rgba(255,255,255,0.9);
color:#222;
margin-top:6px;
margin-bottom:15px;
font-size:15px;
}

input:focus{
box-shadow:0 0 6px #2dd4bf;
}

button{
background:linear-gradient(135deg,#2dd4bf,#14b8a6);
color:#071018;
border:none;
padding:12px 20px;
border-radius:8px;
font-weight:bold;
font-size:16px;
cursor:pointer;
transition:0.3s;
width:100%;
}

button:hover{
transform:scale(1.05);
}

.switch{
text-align:center;
margin-top:20px;
}

.link-button{
background:none;
border:none;
color:#2dd4bf;
cursor:pointer;
font-size:15px;
text-decoration:underline;
}

.link-button:hover{
color:white;
}

@keyframes fadeIn{
from{opacity:0;transform:translateY(15px);}
to{opacity:1;transform:translateY(0);}
}

@media (max-width:600px){
header{padding:20px;}
}

#loginErrorMessage {
    font-weight: bold;
    color: #f14d38ec;
}
</style>
</head>
    <body>
        <header>
            <div class="logo">
                <img src="peakscinemastransparent.png" 
                    onclick="window.location.href='home.php'">
            </div>
        </header>

        <main>
            <div class="container-2">
                <form id="signupForm">
                    <p>Sign Up</p>
                    
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" placeholder="Enter your last name">

                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" placeholder="Enter your first name">

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email">

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password">

                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter your password">

                    <label for="phoneNumber">Phone Number</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="countryCode" name="countryCode" size="5" maxlength="5" placeholder="+00" style="width: 30%;">
                        <input type="tel" id="phoneNumber" name="phoneNumber" maxlength="10" placeholder="9*********" style="width: 70%;">
                    </div>

                    <div style="text-align: center; margin-top: 20px;">
                        <button type="submit" name="customer_info" value="Next">Next</button>
                    </div>

                    <div class="switch">
                        <button type="button" class="link-button" id="showLogin">Already have an account?</button>
                    </div>
                </form>

                <div id="loginFormContainer">
                    <div id="status" style="display:none"></div>
                    <form id="loginForm">
                        <p>Log In</p>

                        <label for="loginEmail">Email</label>
                        <input type="email" id="loginEmail" name="loginEmail" placeholder="Enter your email" required>

                        <label for="loginPassword">Password</label>
                        <input type="password" id="loginPassword" name="loginPassword" placeholder="Enter your password" required>
                        <div id="loginErrorMessage"></div>

                        <div style="text-align: center; margin-top: 20px;">
                            <button type="submit" name="login_user">Login</button>
                        </div>

                        <div class="switch">
                            <button type="button" class="link-button" id="showSignup">Don't have an account?</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>

        <script>
            const showLogin = document.getElementById('showLogin');
            const showSignup = document.getElementById('showSignup');
            const signupForm = document.getElementById('signupForm');
            const loginForm = document.getElementById('loginForm');

            function showLoginFunc() {
                signupForm.style.display = 'none';
                loginForm.style.display = 'block';
                loginForm.style.animation = 'fadeIn 0.5s ease';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            showLogin.addEventListener('click', showLoginFunc);

            showSignup.addEventListener('click', () => {
                loginForm.style.display = 'none';
                document.getElementById('status').style.display = 'none';
                signupForm.style.display = 'block';
                signupForm.style.animation = 'fadeIn 0.5s ease';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            signupForm.addEventListener("submit", function(e) {
                e.preventDefault();

                formData = new FormData(signupForm);

                fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=customer_signup', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        console.log(response.error);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        console.log(data.error);
                    }
                    else if (data.status) {
                        document.getElementById('status').style.display = "block";
                        document.getElementById('status').innerText = "Successfully signed up! Please log in now.";
                        showLoginFunc();
                        signupForm.reset();
                    } 
                })
            })

            loginForm.addEventListener("submit", function(e) {
                e.preventDefault();

                const formData = new FormData(loginForm);

                fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=customer_login', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        "loginEmail": formData.get('loginEmail'),
                        "loginPassword": formData.get('loginPassword')
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
                            window.location.href = 'personal_info_form.php';
                            return;
                        }

                        const currentTime = Math.floor(Date.now() / 1000);

                        if (!payload || payload.exp < currentTime) {
                            localStorage.removeItem('jwt_token');
                            window.location.href = 'personal_info_form.php';
                            return;
                        }
                        
                        window.location.href = 'home.php';
                    } else {
                        document.getElementById('loginErrorMessage').textContent = "Wrong Email or Password.";
                        document.getElementById('loginPassword').value = "";
                    }
                })
                .catch(error => {
                    console.error(error);
                })
            })
        </script>
    </body>
</html>