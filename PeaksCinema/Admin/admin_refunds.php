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
            const refundsGallery = document.getElementById('refundsGallery');
            window.onload = function() {
                fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=refund', {
                    method: 'GET'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const refunds = data.data;

                    refunds.forEach(refund => {
                        const refundContainer = document.createElement('div');
                        refundContainer.classList.add('refundContainer');
                        refundContainer.addEventListener("click", function() {
                            console.log("test");
                        })
                        
                        const bookingRef = document.createElement('div');
                        bookingRef.classList.add('bookingRef');
                        bookingRef.textContent = "PC" + refund.Receipt_ID + new Date(refund.PaymentDate).getFullYear();
                        refundContainer.append(bookingRef);

                        const refundReason = document.createElement('div');
                        refundReason.classList.add('refundReason');
                        refundReason.textContent = refund.RefundReason;
                        refundContainer.append(refundReason);

                        refundsGallery.append(refundContainer);
                    })
                })
            }
        </script>
    </body>
</html>