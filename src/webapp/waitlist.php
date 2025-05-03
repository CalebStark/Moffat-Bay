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

<html>
<head>
    <title>Wait List - Moffat Bay Marina</title>
    <link rel="stylesheet" href="css/style.css">
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
                <li><a href="waitlist.php">Wait List</a></li>
                <li><a href="lookup.html">Reservation Lookup</a></li>
            </ul>
        </nav>  
    </header>
    <main>
        <section>
            <h2>Current Wait Lists</h2>
            <ul id="waitlistCounts">
                <li>26 ft Slips: <?= $slip26?></li>
                <li>40 ft Slips: <?= $slip40?></li>
                <li>50 ft Slips: <?= $slip50?></li>
            </ul>
        </section>
    </main>
    <footer>
        <p>© 2025 Moffat Bay Marina</p>
    </footer>

</body>
</html>