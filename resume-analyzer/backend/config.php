<?php
declare(strict_types=1);

session_start();

$conn = new mysqli("127.0.0.1", "root", "", "resume_analyzer");

if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
<?php
declare(strict_types=1);

session_start();

$conn = new mysqli("127.0.0.1", "root", "", "resume_analyzer");

if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>