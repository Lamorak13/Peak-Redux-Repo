<!DOCTYPE html>
<html>
    <head>
        <script src="admin_gate.js"></script>
        <script>admin_gate.gatekeep(1); </script>
        <link rel="stylesheet" type="text/css" href="admin_stylesheet.css">
    </head>
    <body>
        <?php include('admin_header.php'); ?>
        <main>
            <section id="adminGallerySection" class="gallerySection">
                <button type="button" class="generalAdminButton" id="addAdminButton" onclick="staffMenuOpenClose()">Add New Admin</button>
                <div id="adminGallery" class="gallery"></div>
            </section>
            <section></section>
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
                        <button type="submit" id="movieSubmitButton" class="generalAdminButton">Add</button>
                    </div>
                </form>
            </div>
        </main>
        <script>
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
        </script>
    </body>
</html>