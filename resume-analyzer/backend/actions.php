<?php
session_start();
require_once "openai.php";

$resumeText = $_SESSION["resume_text"] ?? "";

if (!$resumeText) {
    echo "Upload resume first";
    exit;
}

$action = $_POST["action"] ?? "";

if ($action === "improve") {
    $prompt = "Improve this resume and rewrite it professionally:\n$resumeText";
}

elseif ($action === "ats") {
    $prompt = "Give ATS score out of 100 and explain:\n$resumeText";
}

elseif ($action === "breakdown") {
    $prompt = "Give section-wise breakdown (skills, projects, experience):\n$resumeText";
}

else {
    echo "Invalid action";
    exit;
}

echo analyzeWithAI($prompt);