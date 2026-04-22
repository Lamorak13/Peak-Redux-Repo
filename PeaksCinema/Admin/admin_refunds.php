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
            <section id="refundsWorkspace">
                <div id="refundRequestsPanel">
                    <div class="panelTitle">Receipts</div>
                    <div id="refundsGallery"></div>
                </div>
                <div id="refundDetailsPanel">
                    <div class="panelTitle">Refund Review</div>
                    <div id="refundDetailsEmpty">Select a refund request to review.</div>
                    <div id="refundDetailsContent" style="display:none;"></div>
                </div>
            </section>
        </main>
        <script>
            const refundsGallery = document.getElementById('refundsGallery');
            const refundDetailsEmpty = document.getElementById('refundDetailsEmpty');
            const refundDetailsContent = document.getElementById('refundDetailsContent');
            let refundsCache = [];
            let selectedRefundId = null;

            function bookingReferenceFromReceipt(receiptId, paymentDate) {
                return "PC" + receiptId + new Date(paymentDate).getFullYear();
            }

            function renderRefundList() {
                refundsGallery.innerHTML = "";

                if (refundsCache.length === 0) {
                    const emptyState = document.createElement('div');
                    emptyState.classList.add('errorMessage');
                    emptyState.textContent = "No pending refund requests.";
                    refundsGallery.append(emptyState);
                    refundDetailsEmpty.style.display = 'block';
                    refundDetailsContent.style.display = 'none';
                    return;
                }

                refundsCache.forEach((refund, index) => {
                    const refundContainer = document.createElement('button');
                    refundContainer.type = "button";
                    refundContainer.classList.add('refundContainer');
                    if (selectedRefundId === refund.Refund_ID) {
                        refundContainer.classList.add('active');
                    }
                    refundContainer.addEventListener("click", function() {
                        selectedRefundId = refund.Refund_ID;
                        renderRefundList();
                        renderRefundDetails(refund.Refund_ID);
                    });

                    const receiptIndex = document.createElement('div');
                    receiptIndex.classList.add('receiptIndex');
                    receiptIndex.textContent = String(index + 1);
                    refundContainer.append(receiptIndex);

                    const bookingRef = document.createElement('div');
                    bookingRef.classList.add('bookingRef');
                    bookingRef.textContent = bookingReferenceFromReceipt(refund.Receipt_ID, refund.PaymentDate);
                    refundContainer.append(bookingRef);

                    refundsGallery.append(refundContainer);
                });
            }

            function renderRefundDetails(refundId) {
                const refund = refundsCache.find(item => item.Refund_ID === refundId);
                if (!refund) {
                    refundDetailsEmpty.style.display = 'block';
                    refundDetailsContent.style.display = 'none';
                    return;
                }

                refundDetailsEmpty.style.display = 'none';
                refundDetailsContent.style.display = 'flex';

                const bookingRef = bookingReferenceFromReceipt(refund.Receipt_ID, refund.PaymentDate);
                const customerName = `${refund.LastName || "Customer"}, ${refund.FirstName || "Guest"}`;

                refundDetailsContent.innerHTML = `
                    <div class="refundMetaLine">
                        <strong>${bookingRef}</strong>
                        <span>${customerName}</span>
                        <span>PHP ${Number(refund.AmountPaid).toLocaleString()}</span>
                    </div>
                    <div class="refundReasonBox">
                        <div class="refundReasonLabel">Ref. Reason</div>
                        <div>${refund.RefundReason}</div>
                    </div>
                    <div class="refundActions">
                        <button type="button" class="generalAdminButton approveRefundBtn">Accept</button>
                        <button type="button" class="generalAdminButton denyRefundBtn">Deny</button>
                    </div>
                `;

                refundDetailsContent.querySelector('.approveRefundBtn').addEventListener("click", function() {
                    handleRefundDecision(refund.Refund_ID, 'accept');
                });
                refundDetailsContent.querySelector('.denyRefundBtn').addEventListener("click", function() {
                    handleRefundDecision(refund.Refund_ID, 'deny');
                });
            }

            async function handleRefundDecision(refundId, action) {
                try {
                    const message = action === 'accept'
                        ? 'Accept this request? This will delete the linked receipt.'
                        : 'Deny this refund request?';
                    if (!confirm(message)) return;

                    const response = await fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=refund/${refundId}`, {
                        method: 'PUT',
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ action: action })
                    });
                    const data = await response.json();
                    if (!response.ok || data.error) {
                        throw new Error(data.error || `HTTP error! ${response.status}`);
                    }

                    refundsCache = refundsCache.filter(item => item.Refund_ID !== refundId);
                    selectedRefundId = null;
                    renderRefundList();
                } catch (error) {
                    console.error(error);
                    alert("Failed to process refund request.");
                }
            }

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
                    refundsCache = data.data || [];
                    renderRefundList();
                })
                .catch(error => {
                    console.error(error);
                });
            }
        </script>
    </body>
</html>