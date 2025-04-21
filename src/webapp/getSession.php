<?php
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
    echo json_encode([
        'loggedIn' => true,
        'customerId' => $_SESSION['customerId'],
        'boatId' => $_SESSION['boatId'],
        'boatName' => $_SESSION['boatName'],
        'boatLength' => $_SESSION['boatLength'],
        'slipId' => $_SESSION['slipId']
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>
