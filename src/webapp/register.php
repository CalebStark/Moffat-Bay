<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $telephone = $_POST['telephone'];
    $boatName = $_POST['boatName'];
    $boatLength = $_POST['boatLength'];
    $password = $_POST['password'];

    // Simple validation (you can extend this)
    if (empty($email) || empty($firstName) || empty($lastName) || empty($telephone) || empty($boatName) || empty($boatLength) || empty($password)) {
        // Redirect back with an error message
        header("Location: register.html?error=All fields are required.");
        exit();
    }

    // Hash the password using bcrypt
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Database connection (adjust these variables accordingly)
    $servername = "localhost";
    $username = "moffatWrite";
    $dbPassword = "moffatWrite";
    $dbname = "moffat_bay_marina";

    // Create connection
    $conn = new mysqli($servername, $username, $dbPassword, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to insert user data
    $stmt = $conn->prepare("INSERT INTO customers (email, firstName, lastName, telephone, boatName, boatLength, passwordHash) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $email, $firstName, $lastName, $telephone, $boatName, $boatLength, $hashedPassword);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to login page on successful registration
        header("Location: login.html?success=Registration successful!");
        exit();
    } else {
        // Redirect back with an error message
        header("Location: register.html?error=Registration failed. Please try again.");
        exit();
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
