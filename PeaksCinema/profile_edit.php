<?php
// session_start();
// include("peakscinemas_database.php");

// $message = "";


// // ==================== FETCH USER INFO ====================
// $stmt = $conn->prepare("SELECT LastName, FirstName, Email, PhoneNumber, Password FROM customer WHERE Customer_ID = ?");
// $stmt->bind_param("i", $Customer_ID);
// $stmt->execute();
// $result = $stmt->get_result();
// if ($result->num_rows === 0) {
//     echo "User not found.";
//     exit;
// }
// $user = $result->fetch_assoc();

// // ==================== HANDLE PROFILE UPDATE ====================
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['tab'])) {
//     $name = trim($_POST['name']);
//     $email = trim($_POST['email']);
//     $phone = trim($_POST['phone']);
//     $password = trim($_POST['password']);

//     $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $user['Password'];

//     $updateStmt = $conn->prepare("UPDATE customer SET Name = ?, Email = ?, PhoneNumber = ?, Password = ? WHERE Customer_ID = ?");
//     $updateStmt->bind_param("ssssi", $name, $email, $phone, $hashedPassword, $Customer_ID);

//     if ($updateStmt->execute()) {
//         $message = "✅ Your profile has been updated successfully!";
//         $user['Name'] = $name;
//         $user['Email'] = $email;
//         $user['PhoneNumber'] = $phone;
//     } else {
//         $message = "❌ Error updating profile. Please try again.";
//     }
// }

// // ==================== FETCH PURCHASE HISTORY ====================
// $history_stmt = $conn->prepare("SELECT 
//     Purchase_ID, MovieName, MallName, TheaterName, Seats, 
//     TotalPrice, PurchaseDate, Status 
//     FROM purchases 
//     WHERE Customer_ID = ? 
//     ORDER BY PurchaseDate DESC");

// $history_stmt->bind_param("i", $Customer_ID);
// $history_stmt->execute();
// $history_result = $history_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="manifest" href="manifest.json">
    <script src="customer_gate.js"></script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile - PeaksCinemas</title>
<style>
:root {
    --bg-dark: #141414;
    --bg-glass: rgba(255, 255, 255, 0.05);
    --accent: #2dd4bf;
    --accent-soft: rgba(45,212,191,0.2);
    --text-light: #ffffff;
    --text-muted: #9ca3af;
    --border-color: rgba(255,255,255,0.1);
    --success: #00c853;
    --error: #ff4b4b;
}

* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Poppins', sans-serif;
    background: radial-gradient(circle at top, #1f1f1f 0%, #0d0d0d 100%);
    color: var(--text-light);
    min-height: 100vh;
    padding-top: 110px;
}

/* HEADER */
header {
    backdrop-filter: blur(10px);
    background: rgba(0,0,0,0.6);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 50px;
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 10;
    border-bottom: 1px solid var(--border-color);
}
.logo img { height: 48px; cursor: pointer; transition: 0.3s ease; }
.logo img:hover { transform: scale(1.08); }

.header-actions { display: flex; align-items: center; gap: 12px; }
.profile-btn, .logout-btn { /* styles same as before */ }

.profile-btn, .logout-btn {
background:linear-gradient(135deg,#2dd4bf,#14b8a6);
border:none;
padding:8px 20px;
border-radius:30px;
font-weight:bold;
cursor:pointer;
color:#071018;
transition:0.3s;
}

.profile-btn:hover{
transform:scale(1.05);
}

.logout-btn:hover {
    background: #bd3b3b;
}

/* TABS */
.tabs {
    display: flex;
    background: var(--bg-glass);
    border-radius: 12px;
    padding: 6px;
    margin-bottom: 30px;
    border: 1px solid var(--border-color);
}
.tab {
    flex: 1;
    padding: 12px;
    text-align: center;
    border-radius: 10px;
    cursor: pointer;
    transition: 0.3s;
    font-weight: 500;
}
.tab.active {
    background: var(--accent);
    color: #071018;
    font-weight: 600;
}

/* MAIN CARD */
.main-container {
    max-width: 800px;
    margin: auto;
    padding: 40px;
    border-radius: 20px;
    background: var(--bg-glass);
    backdrop-filter: blur(18px);
    border: 1px solid var(--border-color);
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
}

/* Form styles (same as your original) */
label { font-size:14px; color: var(--text-muted); margin-bottom: 6px; display: block; }
input[type="text"], input[type="email"], input[type="tel"], input[type="password"] {
    width: 100%; padding: 12px 14px; margin-bottom: 18px;
    border-radius: 10px; border: 1px solid var(--border-color);
    background: rgba(255,255,255,0.04); color: white;
}
input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft); outline: none; }

input[type="submit"] {
    width: 100%; padding: 14px; border-radius: 12px; border: none;
    background: linear-gradient(135deg, #2dd4bf, #06b3a8);
    font-weight: 600; color: white; cursor: pointer;
}
input[type="submit"]:hover { transform: translateY(-3px) scale(1.02); }

/* History Table */
.history-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
.history-table th, .history-table td {
    padding: 14px 12px;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}
.history-table th {
    background: rgba(45, 212, 191, 0.15);
    color: var(--accent);
}
.history-table tr:hover {
    background: rgba(255,255,255,0.03);
}
.status {
    padding: 6px 14px;
    font-size: 0.85rem;
    font-weight: bold;
}

.refund {
    min-width: 7rem;
}
.status.Paid { background: #2dd4bf; color: #071018; }

.refund-btn {
    margin-left: 10px;
    padding: 6px 12px;
    border-radius: 20px;
    border: none;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    background: #ff4b4b;
    color: white;
    transition: 0.2s;
}
.refund-btn:hover {
    transform: scale(1.05);
}

.message { padding: 12px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-weight: 500; }
.message.success { background: rgba(0,200,83,0.1); border: 1px solid var(--success); color: var(--success); }
.message.error { background: rgba(255,75,75,0.1); border: 1px solid var(--error); color: var(--error); }

.no-history {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-muted);
    font-size: 1.1rem;
}

.refundRequestContainer {
    z-index: 2000;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: rgba(0, 0, 0, 0.5);
}

.refundRequest {
    width: 30%;
    min-width: fit-content;
    height: fit-content;
    display: flex;
    gap: 5px;
    flex-direction: column;
    border: 3px solid #f6e8e0;
    border-radius: 15px;
    padding: 1rem;
    background-color: #0c181a;
}

</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="peakscinemastransparent.png" alt="PeaksCinemas Logo" onclick="window.location.href='home.php'">
    </div>
    <div class="header-actions">
        <button class="logout-btn" onclick="logoutFunc()">Logout</button>
    </div>
</header>

<div class="refundRequestContainer" style="display:none;">
    <div class="refundRequest"></div>
</div>

<div class="main-container">
    <div class="tabs">
        <div class="tab active" onclick="switchTab(0)">Account Settings</div>
        <div class="tab" onclick="switchTab(1)">Booking History</div>
    </div>

    <!-- ==================== ACCOUNT SETTINGS TAB ==================== -->
    <div id="tab0" class="tab-content">
        <div class="profile-header">
            <h2>Account Settings</h2>
            <p>Manage your PeaksCinemas profile information</p>
        </div><br>

        <form id="profileEditForm" class="profileEditForm">
            <input type="hidden" name="tab" value="0">

            <label for="LastName">Last Name</label>
            <input type="text" id="LastName" name="LastName" required value="">
            <div id="lastnamewarning"></div>
            
            <label for="FirstName">First Name </label>
            <input type="text" id="FirstName" name="FirstName" required value="">
            <div id="firstnamewarning"></div>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required value="">
            <div id="emailwarning"></div>

            <label for="phone">Country Code</label>
            <input type="tel" id="countryCode" name="countryCode" value="">

            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" value="">

            <label for="password">New Password (leave blank to keep current)</label>
            <div class="password-container" style="position:relative;">
                <input type="password" id="password" name="password" placeholder="Enter new password">
                <button type="button" id="togglePassword" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#999;cursor:pointer;">Show</button>
            </div>

            <input type="submit" value="Save Changes">
        </form>
    </div>

    <!-- ==================== BOOKING HISTORY TAB ==================== -->
    <div id="tab1" class="tab-content" style="display:none;">
        <h2 class="myBookingHistory" style="margin-bottom:25px; text-align:center;">My Booking History</h2>
        <table class="history-table"></table>
        <div class="no-history" style="display:none">
            <p>You don't have any booking history yet.</p>
            <button onclick="window.location.href='movie.php'" style="margin-top:20px; padding:12px 24px; background:var(--accent); color:#071018; border:none; border-radius:30px; cursor:pointer;">
                Browse Movies Now
            </button>
        </div>
    </div>

</div>

<script>
    // Tab switching
    function switchTab(tabIndex) {
        document.querySelectorAll('.tab').forEach((tab, index) => {
            tab.classList.toggle('active', index === tabIndex);
        });
        
        document.querySelectorAll('.tab-content').forEach((content, index) => {
            content.style.display = (index === tabIndex) ? 'block' : 'none';
        });
    }

    function getCustoms() {
        fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=customer/${Customer_ID}`, {
            method: 'GET'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.log("uh oh");
            }

            customer = data.data[0];

            document.getElementById('LastName').value = customer.LastName;
            document.getElementById('FirstName').value = customer.FirstName;
            document.getElementById('email').value = customer.Email;

            if (customer.CountryCode) {
                document.getElementById('countryCode').value = customer.CountryCode;
            } else {
                document.getElementById('countryCode').placeholder = "No Country Code Inputted Yet."
            }

            if (customer.PhoneNumber) {
                document.getElementById('phone').value = customer.PhoneNumber;
            } else {
                document.getElementById('phone').placeholder = "No Phone Number Inputted Yet."
            }            
        })
    }

    const token = localStorage.getItem('jwt_token');
    const payload = JSON.parse(atob(token.split('.')[1]));
    const Customer_ID = payload.id;

    // Password toggle (for Account Settings tab)
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', () => {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleBtn.textContent = 'Hide';
                } else {
                    passwordInput.type = 'password';
                    toggleBtn.textContent = 'Show';
                }
            });
        }
        
        getCustoms();
        getReceipts();
    });

    const profileEditForm = document.getElementById('profileEditForm');
    profileEditForm.addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(profileEditForm);
        const payload = {
            LastName: formData.get('LastName'),
            FirstName: formData.get('FirstName'),
            Email: formData.get('email'),
            CountryCode: formData.get('countryCode'),
            PhoneNumber: formData.get('phone'),
            Password: formData.get('password')
        }

        fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=customer/${Customer_ID}`, {
            method: 'PUT',
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(payload)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status) {
                console.log(data.status);
                profileEditForm.reset();
                getCustoms();
            }
        })
    })

    function logoutFunc() {
        localStorage.clear();
        sessionStorage.clear();
        window.location.href = 'personal_info_form.php';
    }

    function refundFunc(Receipt_ID, PaymentDate) {
        const refundRequest = document.querySelector('.refundRequest');
        const paymentYear = new Date(PaymentDate).getFullYear();
        refundRequest.innerHTML = "";

        refundRequest.innerHTML = `<form id="refundRequestForm">
                                    <button type="button" onclick="refundMenuOpenClose()">Back</button>
                                    <div>Booking Reference PC${Receipt_ID}${paymentYear}</div>
                                    <label for="refundReason">Why do you want to refund? </label>
                                    <input type="text id="refundReason" name="refundReason">

                                    <button type="submit">Submit</button>
                                   </form>`;
        refundMenuOpenClose();

        refundRequest.addEventListener("submit", function(e) {
            e.preventDefault();

            const refundRequestForm = document.getElementById('refundRequestForm');
            const formData = new FormData(refundRequestForm);
            const payload = {
                Customer_ID: Customer_ID,
                Receipt_ID: Receipt_ID,
                RefundReason: formData.get('refundReason')
            }
            fetch('http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=refund', {
                method: 'POST',
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(payload)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    console.log("something went wrong.");
                    return;
                }
                if (data.status) {
                    console.log(data.status);
                    refundMenuOpenClose();
                    getReceipts();
                }
            })
        })
    }

    const refundRequestContainer = document.querySelector('.refundRequestContainer');
    let isRefundMenuOpen = false;
    function refundMenuOpenClose() {
        if (isRefundMenuOpen) {
            refundRequestContainer.style.display = "none";
        } else {
            refundRequestContainer.style.display = "flex";
        }
        isRefundMenuOpen = !isRefundMenuOpen;
    }

    function getReceipts() {
        fetch(`http://localhost/Peak-Redux-Repo/PeaksCinema/pc_api.php?request=receipt/customer/${Customer_ID}`, {
            method: 'GET'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                document.querySelector(".no-history").style.display = "flex";
                return;
            }
            const receipts = data.data;
            document.querySelector('.history-table').innerHTML = `<thead>
                                                                    <tr>
                                                                        <th>Booking Ref#</th>
                                                                        <th>Date</th>
                                                                        <th>Movie</th>
                                                                        <th>Theater</th>
                                                                        <th>Seats</th>
                                                                        <th>Total</th>
                                                                        <th>Status</th>
                                                                        <th></th>
                                                                    </tr>
                                                                </thead>`;

            const tbody = document.createElement("tbody");
            receipts.forEach(purchase => {
                const tr = document.createElement("tr");
                tr.setAttribute('data-id', purchase.Receipt_ID);
                
                const paymentYear = new Date(purchase.PaymentDate).getFullYear();

                if (purchase.Status === "Pending Refund") {
                    tr.innerHTML = `
                                    <td class="BookingRef">${purchase.Receipt_ID}${paymentYear}</td>
                                    <td class="PurchaseDate">${purchase.PaymentDate}</td>
                                    <td class="MovieName">${purchase.MovieName}</td>
                                    <td class="TheaterName">${purchase.TheaterName}</td>
                                    <td class="seats"></td>
                                    <td class="TotalPrice">₱${purchase.AmountPaid}</td>
                                    <td class="status">${purchase.Status}</td>
                                    <td class="refund">
                                    </td>
                                `;
                } else {
                    tr.innerHTML = `
                                    <td class="BookingRef">${purchase.Receipt_ID}${paymentYear}</td>
                                    <td class="PurchaseDate">${purchase.PaymentDate}</td>
                                    <td class="MovieName">${purchase.MovieName}</td>
                                    <td class="TheaterName">${purchase.TheaterName}</td>
                                    <td class="seats"></td>
                                    <td class="TotalPrice">₱${purchase.AmountPaid}</td>
                                    <td class="status">${purchase.Status}</td>
                                    <td class="refund">                                    
                                        <button class="refund-btn" onclick="refundFunc(${purchase.Receipt_ID}, '${purchase.PaymentDate}')">
                                        Refund
                                        </button>
                                    </td>
                                `;
                }
                
                tbody.appendChild(tr);
            })
            document.querySelector('.history-table').append(tbody);         
        })
    }
</script>

</body>
</html>