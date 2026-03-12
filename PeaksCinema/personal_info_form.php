<?php
session_start();
include("peakscinemas_database.php");

if (isset($_GET['logged_out'])) {
    $message = "You have successfully logged out.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["customer_info"])) {
    function input_cleanup($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $firstName = $lastName = $email = $password = $confirmPassword = $countryCode = $phoneNumber = "";

    if (!empty($_POST["lastName"])) {
        $lastName = input_cleanup($_POST['lastName']);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $lastName)) {
            echo "<script>alert('Invalid last name.');</script>";
            exit();
        }
    }

    if (!empty($_POST["firstName"])) {
        $firstName = input_cleanup($_POST['firstName']);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $firstName)) {
            echo "<script>alert('Invalid first name.');</script>";
            exit();
        }
    }

    if (!empty($_POST["email"])) {
        $email = input_cleanup($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Invalid email format.');</script>";
            exit();
        }
    }

    if (!empty($_POST["password"])) {
        $passwordPlain = input_cleanup($_POST['password']);
        $confirmPassword = input_cleanup($_POST['confirmPassword']);

        if ($passwordPlain !== $confirmPassword) {
            echo "<script>alert('Passwords do not match.');</script>";
            exit();
        } else {
            $password = password_hash($passwordPlain, PASSWORD_DEFAULT);
        }
    }

    $countryCode = input_cleanup($_POST['countryCode']);
    $phoneNumber = input_cleanup($_POST['phoneNumber']);

    if ($firstName && $lastName && $email && $password) {
        $check = mysqli_query($conn, "SELECT * FROM customer WHERE Email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            echo "<script>alert('Email already exists. Please log in.');</script>";
        } else {
            $sql = "INSERT INTO customer (Name, Email, Password, CountryCode, PhoneNumber)
                VALUES ('$firstName $lastName', '$email', '$password', '$countryCode', '$phoneNumber')";
            
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Sign Up Successful! Please log in now.');</script>";
                echo "<script>
                    document.addEventListener('DOMContentLoaded', () => {
                        document.getElementById('signupForm').style.display = 'none';
                        document.getElementById('loginForm').style.display = 'block';
                    });
                </script>";
            } else {
                echo "<script>alert('Database error: " . mysqli_error($conn) . "');</script>";
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login_user"])) {
    $email = trim($_POST["loginEmail"]);
    $password = trim($_POST["loginPassword"]);

    $stmt = $conn->prepare("SELECT Customer_ID, Name, Password FROM customer WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['Password'])) {
            $_SESSION['user_id'] = $user['Customer_ID'];
            $_SESSION['user_name'] = $user['Name'];
            header("Location: home.php");
            exit();
        } else {
            echo "<script>alert('Invalid password.');</script>";
        }
    } else {
        echo "<script>alert('Email not found.');</script>";
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PeaksCinemas, Sign Up and Login</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', 'Segoe UI', sans-serif;
    }

      body {
            font-family: 'Segoe UI', Arial, sans-serif;
                color: white;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: flex-start;
                min-height: 100vh;
                padding-top: 100px;
                background: #5C4033;
                background: linear-gradient(360deg, rgba(92, 64, 51, 1) 0%, rgba(51, 17, 0, 1) 100%);
      
                }

            body::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(to bottom, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 11%, transparent 100%);
                pointer-events: none;
            }

    

    header {
        background-color: #6A7F3F;
        background: linear-gradient(90deg,rgba(106, 127, 63, 1) 0%, rgba(74, 106, 90, 1) 100%);
        backdrop-filter: blur(6px);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 30px;
        border-bottom: 1px solid #ffffffff;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
    }

    .logo img {
        height: 50px;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .logo img:hover {
        transform: scale(1.08);
    }

    .container-2 {
        width: 100%;
        max-width: 500px;
        margin-top: 30px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        animation: fadeIn 0.8s ease-in-out;
    }

    form {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 30px;
        border-radius: 15px;
        width: 100%;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        transition: all 0.3s ease-in-out;
    }

    form p {
        text-align: center;
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 20px;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.7);
    }

    label {
        font-weight: 600;
        margin-top: 10px;
        display: block;
        color: #e0e0e0;
    }

    input {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: none;
        outline: none;
        background: rgba(255, 255, 255, 0.9);
        color: #222;
        margin-top: 5px;
        margin-bottom: 15px;
        font-size: 16px;
        transition: box-shadow 0.3s ease;
    }

    input:focus {
        box-shadow: 0 0 5px 2px #a3c2b1;
    }

    ::placeholder {
        color: #777;
    }

    button {
        background-color: #4b4b4b;
        background: linear-gradient(90deg,rgba(75, 75, 75, 1) 0%, rgba(43, 43, 43, 1) 100%);
        border: 1px solid #cccccc;
        color: #ffffffff;
        font-weight: bold;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 18px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    button:hover {
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
        color: #363635;
        border: 1px solid #363635;
        background: #ffffff;
        background: linear-gradient(90deg,rgba(255, 255, 255, 1) 0%, rgba(204, 204, 204, 1) 100%);
        transform: scale(1.05);
    }

    .switch {
        text-align: center;
        margin-top: 20px;
    }

    .link-button {
        background: none;
        border: none;
        color: #a3c2ff;
        text-decoration: underline;
        cursor: pointer;
        font-size: 17px;
        transition: color 0.3s ease;
    }

    .link-button:hover {
        color: #363635;
    }

    #loginForm {
        display: none;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 600px) {
        body { padding-top: 80px; }
        form { padding: 20px; }
    }
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="peakscinematransparent.png" alt="PeaksCinemas Logo" onclick="window.location.href='home.php'">
    </div>
</header>

<main>
    <div class="container-2">
        <form id="signupForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
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

            <label for="phoneNumber">Phone Number (optional)</label>
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

        <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <p>Log In</p>

            <label for="loginEmail">Email</label>
            <input type="email" id="loginEmail" name="loginEmail" placeholder="Enter your email" required>

            <label for="loginPassword">Password</label>
            <input type="password" id="loginPassword" name="loginPassword" placeholder="Enter your password" required>

            <div style="text-align: center; margin-top: 20px;">
                <button type="submit" name="login_user">Login</button>
            </div>

            <div class="switch">
                <button type="button" class="link-button" id="showSignup">Don't have an account?</button>
            </div>
        </form>
    </div>
</main>

<script>
    const showLogin = document.getElementById('showLogin');
    const showSignup = document.getElementById('showSignup');
    const signupForm = document.getElementById('signupForm');
    const loginForm = document.getElementById('loginForm');

    showLogin.addEventListener('click', () => {
        signupForm.style.display = 'none';
        loginForm.style.display = 'block';
        loginForm.style.animation = 'fadeIn 0.5s ease';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    showSignup.addEventListener('click', () => {
        loginForm.style.display = 'none';
        signupForm.style.display = 'block';
        signupForm.style.animation = 'fadeIn 0.5s ease';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>
</body>
</html>