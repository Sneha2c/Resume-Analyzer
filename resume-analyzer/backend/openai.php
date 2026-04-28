<?php

// ===============================
// LOAD .env FILE (MANUAL SIMPLE LOADER)
// ===============================
$envPath = __DIR__ . "/.env";

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;

        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// ===============================
// GET API KEY
// ===============================
$OPENAI_API_KEY = $_ENV['OPENAI_API_KEY'] ?? null;

if (!$OPENAI_API_KEY) {
    error_log("❌ OPENAI_API_KEY not found in .env");

    function askOpenAI($message) {
        return "❌ API key not configured.";
    }

    return;
}


// ===============================
// OPENAI CALL FUNCTION
// ===============================
function callOpenAI($messages)
{
    global $OPENAI_API_KEY;

    $payload = [
        "model" => "gpt-4o-mini",
        "messages" => $messages,
        "temperature" => 0.7
    ];

    $ch = curl_init("https://api.openai.com/v1/chat/completions");

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . $OPENAI_API_KEY,
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 20
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return "❌ cURL Error: " . curl_error($ch);
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode !== 200) {
        return "❌ API Error: " . ($result['error']['message'] ?? $response);
    }

    return $result["choices"][0]["message"]["content"] ?? "❌ No response from AI";
}


// ===============================
// CHAT FUNCTION
// ===============================
function askOpenAI($message)
{
    return callOpenAI([
        ["role" => "system", "content" => "You are a helpful assistant."],
        ["role" => "user", "content" => $message]
    ]);
}