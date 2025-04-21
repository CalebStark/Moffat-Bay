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
    </head>
    <body>
        <header class="top-banner">
            <div class="logo">
            <a href="index.html"><img class="logo-img" src="images/logo.PNG" alt="Logo if we're feeling fancy!"></a>
            </div>
            <nav class="nav-links">
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="contact.html">Contact Us</a></li>
                    <li><a href="register.html">Register</a></li>
                    <li><a href="login.html">Login</a></li>
                    <li><a href="reserve.php">Reserve a Slip</a></li>
                    <li><a href="waitlist.html">Wait List</a></li>
                    <li><a href="lookup.html">Reservation Lookup</a></li>
                </ul>
            </nav>  
        </header>
        <main>
            <section>
                <h2>Reservation Summary</h2>
                <form>
                    <section class="labelNext">
                        <label for="boatName">Boats Name:</label>
                        <h3 id="boatName"><?=$_SESSION['boatName']?></h3>
                    </section>
                    <section class="labelNext">
                        <label for="boatLength">Boat Length:</label>
                        <h3 id="boatLength"><?=$_SESSION['boatLength']?></h3>
                    </section>
                    <section class="labelNext">
                        <label for="slipId">Slip Number:</label>
                        <h3 id="slipId"><?=$_SESSION['slipId']?></h3>
                    </section>
                </form>
            </section>
        </main>
        <footer>
            <p>© 2025 Moffat Bay Marina</p>
        </footer>
    </body>