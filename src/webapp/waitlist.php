<!DOCTYPE html>

<?php

    $conn = new mysqli("localhost", "moffatRead", "moffatPass", "moffat_bay_marina");

    if ($conn->connect_error) {
        echo json_encode(['error' => 'Connection failed']);
        exit;
    }

    $result = $conn->query("SELECT slipId FROM waitlist");

    $slip26 = 0;
    $slip40 = 0;
    $slip50 = 0;
    while ($row = $result->fetch_assoc()) {
        $getLength = $conn->query("SELECT slipLength from slips WHERE slipId =$row[slipId]");
        $length = $getLength->fetch_assoc();
        if ($length['slipLength'] == 26){
            $slip26++;
        } elseif ($length['slipLength'] == 40){
            $slip40++;
        } elseif ($length['slipLength'] == 50){
            $slip50++;
        }
    }
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wait List - Moffat Bay Marina</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="top-banner">
        <div class="logo">
          <a href="index.html"><img class="logo-img" src="images/logo.PNG" alt="Moffat Bay Marina Logo"></a>
        </div>
        <nav class="nav-links">
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="about.html">About Us</a></li>
                <li><a href="contact.html">Contact Us</a></li>
                <li><a href="register.html">Register</a></li>
                <li><a href="login.html">Login</a></li>
                <li><a href="reserve.php">Reserve a Slip</a></li>
                <li><a href="waitlist.php">Wait List</a></li>
                <li><a href="lookup.html">Reservation Lookup</a></li>
            </ul>
        </nav>  
    </header>

    <main>
        <section class="info-box">
            <h2><i class="fa-solid fa-anchor"></i> Current Wait List Summary</h2>
            <p>The current number of boats waiting for each slip size is shown below. Please check back regularly for availability.</p>
            <ul id="waitlistCounts" style="list-style: none; padding-left: 0; font-size: 1.1em; margin-top: 1em;">
                <li><strong>26 ft Slips:</strong> <?= $slip26 ?></li>
                <li><strong>40 ft Slips:</strong> <?= $slip40 ?></li>
                <li><strong>50 ft Slips:</strong> <?= $slip50 ?></li>
            </ul>
        </section>
    </main>

    <footer>
        <p>© 2025 Moffat Bay Marina</p>
    </footer>

</body>
</html>
