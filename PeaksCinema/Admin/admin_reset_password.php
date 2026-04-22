<!DOCTYPE HTML>
<html>
    <head>
        <script src="admin_gate.js"></script>
        <script>admin_gate.gatekeep(0);</script>
        <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
    </head>
    <body>
        <main>
            <section id="loginFormContainer">
                <form id="resetForm">
                    <img src="peakscinematransparent.png" style="width: 50%; height: 50%; background-color: #f6e8e085; border-radius: 15px; padding: 0 5px 0 5px">
                    <p class="formTitle">Reset your password</p>
                    <p class="formSubtitle">You’re signed in with a temporary password. Set a new password to continue.</p>
                    <div class="field">
                        <label for="newPassword">New Password: </label>
                        <input type="password" id="newPassword" name="newPassword" placeholder="New password" autocomplete="off" required>
                    </div>
                    <div class="field">
                        <label for="confirmPassword">Confirm Password: </label>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm password" autocomplete="off" required>
                    </div>

                    <div class="passwordRequirements" aria-label="Password requirements">
                        <div class="passwordRequirementsTitle">Password must include:</div>
                        <ul id="requirementsList">
                            <li id="reqLen" class="reqBad">At least 8 characters</li>
                            <li id="reqUpper" class="reqBad">1 uppercase letter</li>
                            <li id="reqLower" class="reqBad">1 lowercase letter</li>
                            <li id="reqNum" class="reqBad">1 number</li>
                            <li id="reqSym" class="reqBad">1 symbol (example: ! @ # $)</li>
                            <li id="reqSpace" class="reqBad">No spaces</li>
                        </ul>
                    </div>

                    <div id="matchStatus" class="inlineStatus" aria-live="polite"></div>

                    <button type="submit" id="submitBtn" class="generalAdminButton" disabled>Set Password</button>
                </form>
            </section>
        </main>
        <script>
            const resetForm = document.getElementById('resetForm');
            const newPasswordEl = document.getElementById('newPassword');
            const confirmPasswordEl = document.getElementById('confirmPassword');
            const submitBtn = document.getElementById('submitBtn');
            const matchStatus = document.getElementById('matchStatus');

            function setReq(id, ok) {
                const el = document.getElementById(id);
                if (!el) return;
                el.classList.toggle('reqOk', ok);
                el.classList.toggle('reqBad', !ok);
            }

            function validatePassword(pw) {
                const hasLen = pw.length >= 8;
                const hasUpper = /[A-Z]/.test(pw);
                const hasLower = /[a-z]/.test(pw);
                const hasNum = /[0-9]/.test(pw);
                const hasSym = /[^A-Za-z0-9]/.test(pw);
                const noSpaces = !/\s/.test(pw);

                setReq('reqLen', hasLen);
                setReq('reqUpper', hasUpper);
                setReq('reqLower', hasLower);
                setReq('reqNum', hasNum);
                setReq('reqSym', hasSym);
                setReq('reqSpace', noSpaces);

                const notTemp = pw !== 'admin1234';

                return hasLen && hasUpper && hasLower && hasNum && hasSym && noSpaces && notTemp;
            }

            function updateUI() {
                const pw = newPasswordEl.value || '';
                const conf = confirmPasswordEl.value || '';

                const pwOk = validatePassword(pw);
                const match = conf.length > 0 ? pw === conf : false;

                if (conf.length === 0) {
                    matchStatus.textContent = '';
                    matchStatus.className = 'inlineStatus';
                } else if (match) {
                    matchStatus.textContent = 'Passwords match.';
                    matchStatus.className = 'inlineStatus reqOk';
                } else {
                    matchStatus.textContent = 'Passwords do not match.';
                    matchStatus.className = 'inlineStatus reqBad';
                }

                submitBtn.disabled = !(pwOk && match);
            }

            newPasswordEl.addEventListener('input', updateUI);
            confirmPasswordEl.addEventListener('input', updateUI);
            document.addEventListener('DOMContentLoaded', updateUI);

            resetForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const newPassword = newPasswordEl.value;
                const confirmPassword = confirmPasswordEl.value;

                if (newPassword !== confirmPassword) {
                    alert("Passwords do not match.");
                    return;
                }

                if (!validatePassword(newPassword) || newPassword === 'admin1234') {
                    alert("Please meet all password requirements (and do not reuse the temporary password).");
                    return;
                }

                const token = localStorage.getItem('admin_jwt_token');
                if (!token) {
                    window.location.href = 'admin_login.php';
                    return;
                }

                fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=admin_password', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ newPassword })
                })
                .then(async (response) => {
                    const body = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(body.error || `HTTP error! ${response.status}`);
                    }
                    return body;
                })
                .then(() => {
                    alert("Password updated. Please log in again.");
                    localStorage.removeItem('admin_jwt_token');
                    window.location.href = 'admin_login.php';
                })
                .catch((error) => {
                    console.error(error);
                    alert("Error: " + error.message);
                });
            });
        </script>
    </body>
</html>
