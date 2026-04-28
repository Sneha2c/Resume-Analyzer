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

    // Windows (XAMPP)
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

    // ===============================
    // ✅ REMOVE HARD LIMIT (IMPORTANT FIX)
    // ===============================
    // ❌ OLD (REMOVE THIS):
    // $text = substr($text, 0, 6000);

    // ✅ NEW (OPTIONAL SAFE LIMIT – MUCH HIGHER)
    if (strlen($text) > 20000) {
        $text = substr($text, 0, 20000);
    }

    // ===============================
    // ADD TO MESSAGE
    // ===============================
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