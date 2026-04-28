<?php
require_once "config.php";

$user = $_POST["username"];
$pass = $_POST["password"];

$stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
$stmt->bind_param("s", $user);
$stmt->execute();

$res = $stmt->get_result();
$data = $res->fetch_assoc();

if ($data && password_verify($pass, $data["password_hash"])) {
    $_SESSION["user_id"] = $data["id"];
    header("Location: ../frontend/index.html");
    exit;
} else {
    echo "Login failed";
}
<?php
require_once "config.php";

$user = $_POST["username"];
$pass = $_POST["password"];

$stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
$stmt->bind_param("s", $user);
$stmt->execute();

$res = $stmt->get_result();
$data = $res->fetch_assoc();

if ($data && password_verify($pass, $data["password_hash"])) {
    $_SESSION["user_id"] = $data["id"];
    header("Location: ../frontend/index.html");
    exit;
} else {
    echo "Login failed";
}
?>