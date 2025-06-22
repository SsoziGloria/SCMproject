<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'SCM_PROJECT';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
echo "✅ Connected to MySQL and $dbname!";
$conn->close();
?>
