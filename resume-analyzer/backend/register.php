<?php
require_once "config.php";

$user = $_POST["username"];
$pass = password_hash($_POST["password"], PASSWORD_DEFAULT);

// check if exists
$check = $conn->prepare("SELECT id FROM users WHERE username=?");
$check->bind_param("s", $user);
$check->execute();

$res = $check->get_result();

if ($res->num_rows > 0) {
    echo "Username already exists";
    exit;
}

$stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?,?)");
$stmt->bind_param("ss", $user, $pass);

if ($stmt->execute()) {
    header("Location: ../frontend/login.html");
    exit;
} else {
    echo "Registration failed";
}
<?php
require_once "config.php";

$user = $_POST["username"];
$pass = password_hash($_POST["password"], PASSWORD_DEFAULT);

// check if exists
$check = $conn->prepare("SELECT id FROM users WHERE username=?");
$check->bind_param("s", $user);
$check->execute();

$res = $check->get_result();

if ($res->num_rows > 0) {
    echo "Username already exists";
    exit;
}

$stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?,?)");
$stmt->bind_param("ss", $user, $pass);

if ($stmt->execute()) {
    header("Location: ../frontend/login.html");
    exit;
} else {
    echo "Registration failed";
}
?>