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
            <section id="refundsGallerySection" class="gallerySection">
                <button type="button" class="generalAdminButton" id="addRefundButton" onclick="">Make New Refund</button>
                <div id="refundsGallery" class="gallery"></div>
            </section>
            <section></section>            
        </main>
        <script>
            
        </script>
    </body>
</html>