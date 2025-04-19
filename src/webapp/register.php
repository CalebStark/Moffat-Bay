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
    $stmt = $conn->prepare("INSERT INTO boats (boatName, boatLength) VALUES (?, ?)");
    $stmt->bind_param("ss", $boatName, $boatLength);
    $stmt->execute();

    $stmt = $conn->prepare("SELECT boatId FROM boats where boatName = ?");
    $stmt->bind_param("s", $boatName);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $boatId = $row['boatId']; // This is your string value
    }

    $stmt = $conn->prepare("INSERT INTO customers (email, firstName, lastName, telephone, boatId, passwordHash) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $email, $firstName, $lastName, $telephone, $boatId, $hashedPassword);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to main page on successful registration
        header("Location: index.html?success=Registration successful!");
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
