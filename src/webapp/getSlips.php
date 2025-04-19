<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "moffatRead", "moffatPass", "moffat_bay_marina");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed']);
    exit;
}

$result = $conn->query("SELECT slipId, status FROM slips"); // or use `available` if that's the column

$slips = [];
while ($row = $result->fetch_assoc()) {
    $slips[$row['slipId']] = $row['status'];
}

echo json_encode($slips);
$conn->close();
?>
