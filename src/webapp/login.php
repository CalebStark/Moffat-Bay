<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email || empty($password)) {
        redirectWithError("Please enter a valid email and password.");
    } else {
        try {
			if (checkLogin($email, $password))	{
				header("Location: login.html?success=true");
				exit();
			}
			else {
                redirectWithError("Invalid login credentials.");
            }
        } catch (Exception $e) {
            redirectWithError("An error occurred. Please try again later.");
        }
    }
}

function checkLogin($email, $pass) {
    $conn = new mysqli("localhost", "moffatRead", "moffatPass", "moffat_bay_marina");
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT email, passwordHash FROM customers WHERE email = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['passwordHash'])) {
            $_SESSION['email'] = $row['email'];
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
