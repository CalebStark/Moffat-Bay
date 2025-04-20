<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
    header("Location: reserve.php?error=" . urlencode("You must be logged in to reserve a slip."));
    exit;
}

// Only accept POST
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['slipId'])) {
    header("Location: reserve.php?error=" . urlencode("Invalid request."));
    exit;
}

// CSRF Protection
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: reserve.php?error=" . urlencode("Invalid security token. Please try again."));
    exit;
}

// Clean input
$slipId = intval($_POST['slipId']);
$customerId = $_SESSION['customerId'];
$boatId = $_SESSION['boatId'];

// Connect to DB
$conn = new mysqli("localhost", "moffatWrite", "password", "moffat_bay_marina");
if ($conn->connect_error) {
    header("Location: reserve.php?error=" . urlencode("Database connection failed."));
    exit;
}

// Check slip availability
$stmt = $conn->prepare("SELECT available FROM slips WHERE slipId = ?");
$stmt->bind_param("i", $slipId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if ($row['available']) {
        // Reserve the slip
        $updateStmt = $conn->prepare("UPDATE slips SET available = 0, customerId = ?, boatId = ? WHERE slipId = ?");
        $updateStmt->bind_param("iii", $customerId, $boatId, $slipId);
        $updateStmt->execute();
        $updateStmt->close();

        // Optional: log reservation
        /*
        $logStmt = $conn->prepare("INSERT INTO reservation_log (customerId, slipId, timestamp) VALUES (?, ?, NOW())");
        $logStmt->bind_param("ii", $customerId, $slipId);
        $logStmt->execute();
        $logStmt->close();
        */

        $conn->close();
        header("Location: reserve.php?success=1");
        exit;
    } else {
        $conn->close();
        header("Location: reserve.php?error=" . urlencode("Sorry, this slip has already been reserved."));
        exit;
    }
} else {
    $conn->close();
    header("Location: reserve.php?error=" . urlencode("Slip not found."));
    exit;
}
?>
