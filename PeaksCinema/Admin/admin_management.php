<!DOCTYPE html>
<html>
    <head>
        <script src="admin_gate.js"></script>
        <script>admin_gate.gatekeep(2); </script>
        <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
    </head>
    <body>
        <?php include('admin_header.php'); ?>
        <main>
            <section id="adminGallerySection" class="gallerySection">
                <button type="button" class="generalAdminButton" id="addAdminButton" onclick="staffMenuOpenClose()">Add New Admin</button>
                <div id="adminGallery" class="gallery"></div>
            </section>
            <div id="staffMenuContainer" style="display: none">
                <form id="staffMenu">
                    <div id="scrollable">
                        <button type="button" id="theBackButton" class="generalAdminButton" onclick="staffMenuOpenClose()">Back</button>
                        <div id="staff">
                            <label for="staffName">Staff Name:</label>
                            <input type="text" id="staffName" name="staffName" autocomplete="off" required>

                            <label for="staffEmail">Staff Email:</label>
                            <input type="email" id="staffEmail" name="staffEmail" autocomplete="off" required>

                            <label for="staffLevel">Staff Type:</label>
                            <select id="staffLevel" name="staffLevel" required>
                                <option value="">Please select a staff level.</option>
                                <option value="0">Cashier</option>
                                <option value="1">Admin</option>
                                <option value="2">Super Admin</option>
                            </select>
                        </div>
                        <button type="submit" id="staffSubmitButton" class="generalAdminButton">Add</button>
                    </div>
                </form>
            </div>
        </main>
        <script>
            document.addEventListener("DOMContentLoaded", getAdmins);

            function getAdmins() {
                document.getElementById('adminGallery').innerHTML = "";
                fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=staff', {
                    method: "GET"
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    let admins = data.data;

                    if (Array.isArray(admins) && admins.length > 0) {
                        admins.forEach(admin => {
                            const adminContainer = document.createElement('div');
                            adminContainer.classList.add('adminContainer');

                            const adminInfo = document.createElement('div');
                            adminInfo.classList.add('adminInfo');

                            if (admin.Admin_ID !== 1) {
                                const adminName = document.createElement('div');
                                adminName.classList.add('adminName');
                                adminName.textContent = admin.FirstName + " " + admin.LastName;
                                adminInfo.append(adminName);
                            }

                            const adminEmail = document.createElement('div');
                            adminEmail.classList.add('adminEmail');
                            adminEmail.textContent = admin.Email;
                            adminInfo.append(adminEmail);

                            adminContainer.append(adminInfo);

                            const adminControls = document.createElement('div');
                            adminControls.classList.add('adminControls');

                            const levelSelect = document.createElement('select');
                            levelSelect.classList.add('levelSelect');
                            const levels = ['Cashier', 'Admin', 'Super Admin'];
                            levels.forEach((level, index) => {
                                const option = document.createElement('option');
                                option.value = index;
                                option.textContent = level;
                                if (index === parseInt(admin.AccessLevel)) {
                                    option.selected = true;
                                }
                                levelSelect.append(option);
                            });

                            levelSelect.addEventListener('change', function() {
                                const newLevel = this.value;
                                const oldLevel = admin.AccessLevel;

                                if (confirm(`Are you sure you want to change this staff member's level from "${levels[oldLevel]}" to "${levels[newLevel]}"?`)) {
                                    updateStaffLevel(admin.Admin_ID, newLevel);
                                } else {
                                    // Revert the selection
                                    levelSelect.value = oldLevel;
                                }
                            });

                            adminControls.append(levelSelect);

                            const deleteBtn = document.createElement('button');
                            deleteBtn.classList.add('deleteAdminBtn');
                            deleteBtn.textContent = 'Delete';
                            deleteBtn.addEventListener('click', function(e) {
                                e.stopPropagation();
                                if (confirm(`Are you sure you want to delete ${admin.FirstName} ${admin.LastName}?`)) {
                                    deleteStaffMember(admin.Admin_ID);
                                }
                            });

                            adminControls.append(deleteBtn);

                            const resetPasswordBtn = document.createElement('button');
                            resetPasswordBtn.classList.add('resetPasswordBtn');
                            resetPasswordBtn.textContent = 'Reset Password';
                            resetPasswordBtn.addEventListener('click', function(e) {
                                e.stopPropagation();
                                if (confirm(`Reset password to 'admin1234' for this staff member?`)) {
                                    resetPassword(admin.Admin_ID);
                                }
                            });

                            adminControls.append(resetPasswordBtn);
                            adminContainer.append(adminControls);

                            document.getElementById('adminGallery').append(adminContainer);
                        });
                    } else {
                        document.getElementById('adminGallery').innerText = "No staff members found.";
                    }
                })
                .catch(error => {
                    console.error(error);
                    document.getElementById('adminGallery').innerText = "Error loading staff members.";
                });
            }

            function updateStaffLevel(staffId, newLevel) {
                const token = localStorage.getItem('admin_jwt_token');
                fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=staff/${staffId}`, {
                    method: "PUT",
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ accessLevel: parseInt(newLevel) })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Staff level updated successfully");
                    getAdmins();
                })
                .catch(error => {
                    console.error(error);
                    alert("Failed to update staff level");
                    getAdmins();
                });
            }

            function deleteStaffMember(staffId) {
                const token = localStorage.getItem('admin_jwt_token');
                fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=staff/${staffId}`, {
                    method: "DELETE",
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Staff member deleted successfully");
                    getAdmins();
                })
                .catch(error => {
                    console.error(error);
                    alert("Failed to delete staff member");
                });
            }

            function resetPassword(staffId) {
                const token = localStorage.getItem('admin_jwt_token');
                fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=staff/${staffId}/reset-password`, {
                    method: "PUT",
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    alert("Password has been reset to 'admin1234'");
                    console.log("Password reset successfully");
                })
                .catch(error => {
                    console.error(error);
                    alert("Failed to reset password");
                });
            }

            const staffMenuContainer = document.getElementById('staffMenuContainer');
            let isStaffMenuOpen = false;

            function staffMenuOpenClose() {
                if (isStaffMenuOpen) {
                    staffMenuContainer.style.display = "none";
                } else {
                    staffMenuContainer.style.display = "flex";
                }
                isStaffMenuOpen = !isStaffMenuOpen;
            }

            const staffMenuForm = document.getElementById('staffMenu');
            staffMenuForm.addEventListener("submit", function(e) {
                e.preventDefault();

                const formData = new FormData(staffMenuForm);
                const token = localStorage.getItem('admin_jwt_token');

                fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=staff', {
                    method: "POST",
                    headers: {
                        'Authorization': `Bearer ${token}`
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.error || `HTTP error! ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data.status);
                    if (data.tempPassword) {
                        alert(`Staff member created! Temporary password: ${data.tempPassword}\nMake sure to share this with them.`);
                    }
                    getAdmins();
                    staffMenuOpenClose();
                    staffMenuForm.reset();
                })
                .catch(error => {
                    console.error(error);
                    alert("Error: " + error.message);
                })
            })

            staffMenuForm.addEventListener("keydown", function(e) {
                if (e.key === "Enter" && e.target.tagName === "INPUT") {
                    e.preventDefault();
                }
            })
        </script>
    </body>
</html>