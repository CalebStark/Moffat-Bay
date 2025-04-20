<?php
session_start();

$conn = new mysqli("localhost", "moffatWrite", "moffatPass", "moffat_bay_marina");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize and validate input
$checkInDate = $_POST['checkInDate'];
$slipId = strtoupper(trim($_POST['slipId']));
$customerId = $_SESSION['customerId'] ?? null;
$boatId = $_SESSION['boatId'] ?? null;

if (!$checkInDate || !$slipId || !$customerId || !$boatId) {
    header("Location: reserve.php?error=" . urlencode("Missing required reservation information."));
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    // Insert into reservations table
    $stmt = $conn->prepare("
        INSERT INTO reservations (customerId, slipId, checkInDate)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("iis", $customerId, $slipId, $checkInDate);
    $stmt->execute();

    // Update the slip status to confirmed (2)
    $update = $conn->prepare("UPDATE slips SET available = FALSE WHERE slipId = ?");
    $update->bind_param("s", $slipId);
    $update->execute();

    // Commit changes
    $conn->commit();

    header("Location: reserve.php?success=1");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header("Location: reserve.php?error=" . urlencode("Database error: " . $e->getMessage()));
    exit;
}
?>
