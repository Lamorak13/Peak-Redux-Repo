// cool name right. could have more edge though

(function() {
    const token = localStorage.getItem('jwt_token');
    
    if (!token) {
        window.location.href = 'personal_info_form.php';
        return;
    }

    let payload;
    try {
        payload = JSON.parse(atob(token.split('.')[1]));
    } catch (e) {
        localStorage.removeItem('jwt_token');
        window.location.href = 'personal_info_form.php';
        return;
    }

    const currentTime = Math.floor(Date.now() / 1000);

    if (payload.exp < currentTime) {
        localStorage.removeItem('jwt_token');
        window.location.href = 'personal_info_form.php';
        return;
    }

    // Reject non-customer tokens (e.g. admin accounts)
    if (payload.role !== 'customer') {
        window.location.href = 'personal_info_form.php';
        return;
    }
}) ();