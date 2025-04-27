<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Capture form data
        $name = $_POST['name'];
        $email = $_POST['email'];
        $message = $_POST['message'];

        // Simple validation (you can extend this)
        if (empty($email) || empty($name) || empty($name)) {
            // Redirect back with an error message
            header("Location: contact.html?error=All fields are required.");
            exit();
        }

        $messagesFile = fopen("contactMessages.txt", "a") or die("Unable to open file!");
        $txt = "$name\n";
        fwrite($messagesFile, $txt);
        $txt = "$email\n";
        fwrite($messagesFile, $txt);
        $txt = "$message\n";
        fwrite($messagesFile, $txt);
        $txt = "\n\n";
        fwrite($messagesFile, $txt);
        fclose($messagesFile);

        header("Location: contact.html?success=Registration successful!");
    }
?>