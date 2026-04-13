<header>
    <nav id="navAdmin">
    </nav>
    <button id="logoutButton" class="generalAdminButton">Log Out</button>
</header>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const logoutButton = document.getElementById('logoutButton');
        logoutButton.addEventListener('click', function() {
            localStorage.removeItem('jwt_token');
            window.location.href = 'admin_login.php';
        })
    })    
</script>