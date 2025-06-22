<?php
$host = 'localhost';
$dbname = 'CONTACT';
$username = 'root';
$password = '';

$message = "";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $subject = htmlspecialchars(trim($_POST["subject"]));
    $message_content = htmlspecialchars(trim($_POST["message"]));

    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message_content);

    if ($stmt->execute()) {
        // Email sending
        $to = "ataho955@gmail.com"; // <-- Replace with your email
        $subject_line = "New Contact Message from $name";
        $email_message = "Name: $name\nEmail: $email\nSubject: $subject\nMessage:\n$message_content";

        if (mail($to, $subject_line, $email_message)) {
            $message = "<div class='success'>Your message has been sent successfully, and we've received it via email too!</div>";
        } else {
            $message = "<div class='error'>Message saved, but email could not be sent.</div>";
        }
    } else {
        $message = "<div class='error'>Error saving message: " . $stmt->error . "</div>";
    }

    $stmt->close();
    $conn->close();
}
?>
