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
                <button type="button" class="generalAdminButton" id="addAdminButton" onclick="adminMenuOpenClose()">Add New Admin</button>
                <div id="adminGallery" class="gallery"></div>
            </section>
        </main>
    </body>
</html>