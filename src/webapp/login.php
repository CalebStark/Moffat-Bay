<?php
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email || empty($password)) {
        redirectWithError("Please enter a valid email and password.");
    } else {
        try {
            if (checkLogin($email, $password)) {
                header("Location: login.html?success=true");
                exit();
            } else {
                redirectWithError("Invalid login credentials.");
            }
        } catch (Exception $e) {
            redirectWithError("An error occurred. Please try again later.");
            // For debugging only (don't leave this in production):
            // die("Error: " . $e->getMessage());
        }
    }
}

function checkLogin($email, $pass) {
    $conn = new mysqli("localhost", "moffatRead", "moffatPass", "moffat_bay_marina");
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Join to get customer and boat info
    $stmt = $conn->prepare("
        SELECT c.customerId, c.boatId, c.passwordHash, b.boatName, b.boatLength, b.slipId
        FROM customers c
        JOIN boats b ON c.boatId = b.boatId
        WHERE c.email = ?
    ");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['passwordHash'])) {
            // Set session variables
            $_SESSION['customerId'] = $row['customerId'];
            $_SESSION['boatId'] = $row['boatId'];
            $_SESSION['boatName'] = $row['boatName'];
            $_SESSION['boatLength'] = $row['boatLength'];
            $_SESSION['slipId'] = $row['slipId'];
            $_SESSION['loggedIn'] = true;

            $stmt->close();
            $conn->close();
            return true;
        }
    }

    $stmt->close();
    $conn->close();
    return false;
}

function redirectWithError($msg) {
    $msg = urlencode($msg);
    header("Location: login.html?error=$msg");
    exit();
}
?>
