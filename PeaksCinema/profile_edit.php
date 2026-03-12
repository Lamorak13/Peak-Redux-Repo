<?php
session_start();
include("peakscinemas_database.php");

if (isset($_GET['logout'])) {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: personal_info_form.php?logged_out=1");
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: personal_info_form.php");
    exit;
}

$profile_link = isset($_SESSION['user_id']) ? "profile_edit.php" : "personal_info_form.php";

$stmt = $conn->prepare("SELECT Name, Email, PhoneNumber, Password FROM customer WHERE Customer_ID = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "User not found.";
    exit;
}
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $user['Password'];

    $updateStmt = $conn->prepare("UPDATE customer SET Name = ?, Email = ?, PhoneNumber = ?, Password = ? WHERE Customer_ID = ?");
    $updateStmt->bind_param("ssssi", $name, $email, $phone, $hashedPassword, $_SESSION['user_id']);

    if ($updateStmt->execute()) {
        $message = "Your profile has been updated! (●'◡'●)";
        $user['Name'] = $name;
        $user['Email'] = $email;
        $user['PhoneNumber'] = $phone;
        $user['Password'] = $hashedPassword;
    } else {
        $message = "❌ Error updating profile. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile - PeaksCinemas</title>
<style>
:root {
    --bg-dark: #1f1f1f;
    --bg-light: #2e2e2e;
    --accent: #a3c2b1;
    --text-light: #ffffff;
    --border-color: #444;
    --error: #ff4b4b;
}
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body {
    font-family: 'Poppins', sans-serif;
    background-color: var(--bg-dark);
    color: var(--text-light);
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh;
    padding-top: 100px;
    overflow-x: hidden;
}

header {
    background-color: var(--accent);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 40px;
    border-bottom: 2px solid var(--border-color);
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 10;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
.logo img {
    height: 50px;
    cursor: pointer;
    transition: transform 0.2s ease;
}
.logo img:hover {
    transform: scale(1.05);
}
.header-actions {
    display: flex;
    align-items: center;
}
.profile-btn {
    background-color: var(--bg-dark);
    color: var(--text-light);
    border: 1px solid #ffffff50;
    border-radius: 50%;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}
    .profile-btn svg {
      width: 24px;
      height: 24px;
    }
.profile-btn:hover {
    background-color: #ffffff;
    color: var(--bg-dark);
    transform: scale(1.1);
    box-shadow: 0 0 8px rgba(255,255,255,0.3);
}
.logout-btn {
    background-color: var(--error);
    color: white;
    border: none;
    border-radius: 6px;
    padding: 8px 14px;
    margin-left: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}
.logout-btn:hover {
    background-color: white;
    color: var(--error);
}

.main-container {
    background-color: var(--bg-light);
    padding: 35px;
    border-radius: 16px;
    width: 90%;
    max-width: 550px;
    border: 1px solid var(--border-color);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    animation: fadeIn 0.7s ease forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

h2 {
    text-align: center;
    margin-bottom: 25px;
    font-weight: 600;
    color: var(--accent);
}

label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
}
input[type="text"], input[type="email"], input[type="tel"], input[type="password"] {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 15px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background-color: #3b3b3b;
    color: white;
    transition: 0.3s;
}
input:focus {
    border-color: var(--accent);
    outline: none;
    box-shadow: 0 0 6px var(--accent);
}
.password-container {
    position: relative;
}
#togglePassword {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #aaa;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
}
#togglePassword:hover {
    color: var(--accent);
}

input[type="submit"] {
    background-color: var(--accent);
    color: #1f1f1f;
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-weight: 600;
    width: 100%;
    cursor: pointer;
    transition: all 0.3s ease;
}
input[type="submit"]:hover {
    background-color: #bcd8c7;
    transform: scale(1.03);
}

.message {
    margin-bottom: 20px;
    text-align: center;
    font-weight: bold;
    color: var(--accent);
}
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="peakscinematransparent.png" alt="PeaksCinemas Logo" onclick="window.location.href='home.php'">
    </div>
    <div class="header-actions">
        <button class="profile-btn" onclick="window.location.href='<?= $profile_link ?>'" title="Profile">👤</button>
        <a href="?logout=1"><button class="logout-btn">Logout</button></a>
    </div>
</header>

<main class="main-container">
    <h2>Edit Your Profile</h2>
    <?php if (!empty($message)) echo "<div class='message'>{$message}</div>"; ?>
    <form method="post" action="">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['Name']) ?>" required>

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required>

        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['PhoneNumber']) ?>" required pattern="[0-9]{10}" title="10-digit phone number">

        <label for="password">New Password</label>
        <div class="password-container">
            <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
            <button type="button" id="togglePassword">Show</button>
        </div>

        <input type="submit" value="Save Changes">
    </form>
</main>

<script>
const passwordInput = document.getElementById('password');
const toggleBtn = document.getElementById('togglePassword');

toggleBtn.addEventListener('click', () => {
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleBtn.textContent = 'Hide';
    } else {
        passwordInput.type = 'password';
        toggleBtn.textContent = 'Show';
    }
});
</script>

</body>
</html>