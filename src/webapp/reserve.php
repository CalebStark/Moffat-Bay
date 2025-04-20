<?php
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reserve a Slip - Moffat Bay Marina</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/reserveStyle.css">

<!-- NOT USED ADVISE ON DELETE-->
	<!-- <script>  Fetches session data created on login
		window.onload = function () {
            fetch('getSession.php')
            .then(res => res.json())
            .then(data => {
                if (data.loggedIn) {
                    console.log("Logged in")
                    document.getElementById('reservationForm').style.display = 'block';
                    document.getElementById('loginMessage').style.display = 'none';

                    // Show debug info for testing
                    const debugBox = document.createElement('div');
                    debugBox.innerHTML = `
                        <h3>Session Info</h3>
                        <ul>
                            <li>Customer ID: ${data.customerId}</li>
                            <li>Boat ID: ${data.boatId}</li>
                            <li>Boat Name: ${data.boatName}</li>
                            <li>Boat Length: ${data.boatLength} ft</li>
                        </ul>
                    `;
                    document.body.appendChild(debugBox);
                }
            });
        };

	</script> -->
</head>
<body>
    <header class="top-banner">
        <div class="logo">
          <a href="index.html"><img class="logo-img" src="images/logo.PNG" alt="Logo if we're feeling fancy!"></a>
        </div>
        <nav class="nav-links">
            <a href="index.html">Home</a>
            <a href="waitlist.html">Wait List</a>
            <a href="lookup.html">Reservation Lookup</a>
            <a href="about.html">About Us</a>
            <a href="contact.html">Contact Us</a>
            <a href="login.html">Login</a>
            <a href="register.html">Sign Up</a>
        </nav>
    </header>
    <main>
        <h1>Reserve a Slip at Moffat Bay Marina</h1>
        <section>
            <h2>Slip Reservation</h2>
            <p id="loginMessage"><a href="login.html">Please login to make a reservation.</a></p>

            <form id="reservationForm" style="display:none;" method="POST" action="reserveSlip.php">
                <label for="boatLength">Boat Length (feet):</label><br>
                <input type="text" id="boatLength" name="boatLength" value="<?= $_SESSION['boatLength']?>" required readonly><br>
                <!-- <select id="boatLength" name="boatLength" required>
                    <option value="26">26 ft</option>
                    <option value="40">40 ft</option>
                    <option value="50">50 ft</option>
                </select><br> -->

                <label for="boatName">Boat Name:</label><br>
                <input type="text" id="boatName" name="boatName" value='<?= $_SESSION["boatName"]?>' required readonly><br>

                <label for="checkInDate">Check-in Date:</label><br>
                <input type="date" id="checkInDate" name="checkInDate" value=''required><br>

                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <input type="hidden" name="slipId" id="slipId" value=""> <!-- Filled dynamically -->

                <button type="submit" class="button">Submit Reservation</button>
            </form>

            <div id="availabilityResult"></div>
            <div id="waitlistResult"></div>
			
			

            <div class="marina">
                <div class="dock" id="colorKey">
                    <h3>Availability</h3>
                    <div class="available colorKey" data-slip="A1" >Available</div>
                    <div class="reserved colorKey" data-slip="A2">Unavailable</div>
                    <div class="confirmed colorKey" data-slip="A3">Selected</div>
                </div>

                <div class="dock" id="dock-a">
                    <h3>Dock A: 26ft</h3>
                    <div class="slip" data-slip="A1" slipNumber="2" slip-size="26">A1</div>
                    <div class="slip" data-slip="A2" slipNumber="4" slip-size="26">A2</div>
                    <div class="slip" data-slip="A3" slipNumber="6" slip-size="26">A3</div>
                </div>

                <div class="dock" id="dock-b">
                    <h3>Dock B: 40ft</h3>
                    <div class="slip" data-slip="B1" slipNumber="3" slip-size="40">B1</div>
                    <div class="slip" data-slip="B2" slipNumber="7" slip-size="40">B2</div>
                    <div class="slip" data-slip="B3" slipNumber="10" slip-size="40">B3</div>
                </div>

                <div class="dock" id="dock-c">
                    <h3>Dock C: 50ft</h3>
                    <div class="slip" data-slip="C1" slipNumber="5" slip-size="50">C1</div>
                    <div class="slip" data-slip="C2" slipNumber="8" slip-size="50">C2</div>
                    <div class="slip" data-slip="C3" slipNumber="9" slip-size="50">C3</div>
                </div>
            </div>
			<!-- Interactive dock for slip reservation -->
			
            <!-- Modals -->
            <div class="modal" id="errorModal">
                <div class="modal-content">
                    <div class="modal-header">Login Error</div>
                    <div class="modal-body" id="modalMessage"></div>
                    <div class="modal-footer">
                        <button onclick="closeModal()">Try Again</button>
                    </div>
                </div>
            </div>

            <div class="modal" id="successModal">
                <div class="modal-content">
                    <div class="modal-header">Reservation Successful</div>
                    <div class="modal-body">Your slip has been reserved!</div>
                    <div class="modal-footer">
                        <button onclick="closeModal()">Close</button>
                    </div>
                </div>
            </div>

        </section>
    </main>
    <footer>
        <p>© 2025 Moffat Bay Marina</p>
    </footer>
	<!-- Modal logic-->
    <script>
    function closeModal() {
        document.getElementById('errorModal').style.display = 'none';
        document.getElementById('successModal').style.display = 'none';
    }

    // Set color to update
    function updateSlipColors() {
        fetch('getSlips.php')
            .then(res => res.json())
            .then(data => {
                document.querySelectorAll('.slip').forEach(slip => {
                    const slipId = slip.getAttribute('slipNumber');
                    const status = data[slipId];

                    slip.classList.remove('available', 'reserved', 'confirmed');
                    if (status == 0) {
                        slip.classList.add('reserved');
                    } else if (status == 1) {
                        slip.classList.add('available');
                    } else {
                        slip.classList.add('available');
                    }
                });
            });
    }

    // Display modals and initialize if logged in
    window.onload = function () {
        const params = new URLSearchParams(window.location.search);
        if (params.has('error')) {
            const msg = decodeURIComponent(params.get('error'));
            document.getElementById('modalMessage').innerText = msg;
            document.getElementById('errorModal').style.display = 'block';
        } else if (params.has('success')) {
            document.getElementById('successModal').style.display = 'block';
            setTimeout(function () {
                window.location.href = 'index.html';
            }, 5000);
        }

        // Fetch session info to determine if logged in
        fetch('getSession.php')
            .then(res => res.json())
            .then(session => {
                if (session.loggedIn) {
                    document.getElementById('reservationForm').style.display = 'block';
                    document.getElementById('loginMessage').style.display = 'none';
                }
            });

        updateSlipColors();
        // setInterval(updateSlipColors, 10000); // Refresh every 10s
    };

     // Handle size filtering
    document.addEventListener('DOMContentLoaded', function () {
        const boatLengthSelect = document.getElementById('boatLength').getAttribute('value');
        document.querySelectorAll('.slip').forEach(slip => {
            const slipSize = slip.getAttribute('slip-size');
            slip.classList.remove('incompatible')
            if (slipSize !== boatLengthSelect){
                slip.classList.add('incompatible');
            }
            
        });

        // Handle slip selection and auto-submit
        document.querySelectorAll('.slip').forEach(slip => {
            slip.addEventListener('click', function () {
                if (
                    this.classList.contains('available') &&
                    !this.classList.contains('incompatible')
                ) {
                    document.querySelectorAll('.slip').forEach(s => s.classList.remove('selected'));
                    this.classList.add('confirmed');

                    const slipId = this.getAttribute('slipNumber')
                    document.getElementById('slipId').value = slipId;

                    //document.getElementById('reservationForm').submit();
                }
            });
        });
    });
</script>


</body>
</html>
