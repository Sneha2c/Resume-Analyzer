<?php
declare(strict_types=1);

session_start(); // 🔥 REQUIRED for chat memory

require_once "config.php";
require_once "analyze.php"; // we need extractText()

// ✅ Only allow POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("❌ Invalid request method.");
}

// ✅ Check file upload
if (!isset($_FILES["resume"]) || $_FILES["resume"]["error"] !== UPLOAD_ERR_OK) {
    die("❌ Upload failed. Please try again.");
}

$file = $_FILES["resume"];

$name = $file["name"];
$tmp = $file["tmp_name"];
$mime = $file["type"] ?? "application/octet-stream";
$size = $file["size"] ?? 0;

$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

// ✅ Allowed file types
$allowedTypes = [
    "pdf" => "application/pdf",
    "doc" => "application/msword",
    "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    "txt" => "text/plain",
    "jpg" => "image/jpeg",
    "jpeg" => "image/jpeg",
    "png" => "image/png",
    "gif" => "image/gif"
];

// ❌ Invalid file type
if (!array_key_exists($ext, $allowedTypes)) {
    die("❌ Unsupported file type.");
}

// ❌ File too large
if ($size > 10 * 1024 * 1024) {
    die("❌ File too large (max 10MB).");
}

// 📁 Upload directory
$uploadDir = __DIR__ . "/uploads";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// 🆕 Unique name
$newName = uniqid("resume_", true) . "." . $ext;
$targetPath = $uploadDir . "/" . $newName;

// 🚀 Save file
if (!move_uploaded_file($tmp, $targetPath)) {
    die("❌ Failed to save file.");
}

// 👤 User session (optional)
$userId = $_SESSION["user_id"] ?? null;

// 📦 Relative path
$relativePath = "uploads/" . $newName;

// 💾 Save to DB
$stmt = $conn->prepare(
    "INSERT INTO resumes (user_id, original_name, stored_path, mime_type, file_size)
     VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "isssi",
    $userId,
    $name,
    $relativePath,
    $mime,
    $size
);

if (!$stmt->execute()) {
    die("❌ Database insert failed.");
}

$resumeId = (int)$stmt->insert_id;

/* ===============================
   🔥 NEW: EXTRACT + STORE TEXT
================================ */
$filePath = __DIR__ . "/" . $relativePath;

// Use your existing OCR + extraction
$text = extractText($filePath, $ext);

// store in session for chat
$_SESSION["resume_text"] = $text;


/* ===============================
   🔍 INITIAL ANALYSIS
================================ */
$result = getAnalysisResult($resumeId, $conn, $ext);

// return result
echo $result;

exit;

declare(strict_types=1);

require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Use POST method");
}

if (!isset($_FILES["resume"]) || $_FILES["resume"]["error"] !== 0) {
    die("❌ Upload failed. Please try again.");
}

$file = $_FILES["resume"];
$name = $file["name"];
$tmp = $file["tmp_name"];

$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

if ($ext !== "pdf") {
    die("❌ Only PDF files are allowed. Please upload a PDF resume.");
}

$dir = __DIR__ . "/uploads";

if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$newName = uniqid("resume_", true) . ".pdf";
$path = $dir . "/" . $newName;

if (!move_uploaded_file($tmp, $path)) {
    die("❌ Failed to save file. Please try again.");
}

$userId = $_SESSION["user_id"] ?? null;

$stmt = $conn->prepare(
    "INSERT INTO resumes (user_id, original_name, stored_path, mime_type, file_size)
     VALUES (?, ?, ?, ?, ?)"
);

$relativePath = "uploads/" . $newName;

$stmt->bind_param(
    "isssi",
    $userId,
    $name,
    $relativePath,
    $file["type"],
    $file["size"]
);

$stmt->execute();

$resumeId = $stmt->insert_id;

// Now analyze the resume
require_once "analyze.php";

// Return the analysis result directly instead of redirecting
echo getAnalysisResult($resumeId, $conn);
exit;
?>