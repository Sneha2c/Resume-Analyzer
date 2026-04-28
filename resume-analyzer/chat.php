<?php

require_once "gemini.php";

// ===============================
// GET MESSAGE
// ===============================
$message = $_POST["message"] ?? "";

// ===============================
// FILE HANDLING
// ===============================
if (isset($_FILES["resume"]) && $_FILES["resume"]["error"] === 0) {

    $fileName = $_FILES["resume"]["name"];
    $tmpPath  = $_FILES["resume"]["tmp_name"];

    $uploadDir = __DIR__ . "/uploads/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $path = $uploadDir . time() . "_" . basename($fileName);

    if (!move_uploaded_file($tmpPath, $path)) {
        echo "❌ File upload failed";
        exit;
    }

    // ===============================
    // 🔥 PDF TEXT EXTRACTION (NO COMPOSER)
    // ===============================

    $textFile = $path . ".txt";

    // Windows command (XAMPP compatible)
    $command = "pdftotext \"$path\" \"$textFile\"";
    exec($command);

    if (!file_exists($textFile)) {
        echo "❌ Could not extract text from PDF";
        exit;
    }

    $text = file_get_contents($textFile);

    if (strlen(trim($text)) < 50) {
        echo "❌ Empty or unreadable PDF";
        exit;
    }

    // Limit size
    $text = substr($text, 0, 6000);

    $message .= "\n\nResume Content:\n" . $text;
}


// ===============================
// VALIDATION
// ===============================
if (!$message) {
    echo "❌ No input provided";
    exit;
}


// ===============================
// AI RESPONSE
// ===============================
$response = askAI($message);


// ===============================
// OUTPUT
// ===============================
echo nl2br(htmlspecialchars($response));