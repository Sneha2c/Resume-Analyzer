<?php

// ===============================
// LOAD .env
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
// API KEY
// ===============================
$API_KEY = $_ENV['GEMINI_API_KEY'] ?? null;

if (!$API_KEY) {
    function askAI($message) {
        return "❌ Gemini API key missing.";
    }
    return;
}


// ===============================
// MAIN FUNCTION (TRY MODELS)
// ===============================
function askAI($message)
{
    $models = [
        "gemini-2.5-pro",
        "gemini-2.5-flash",
        "gemini-2.5-flash-lite"
    ];

    foreach ($models as $model) {

        $response = callGemini($message, $model);

        if (!isError($response)) {
            return $response;
        }

        // Optional debug
        // error_log("Model failed: " . $model);
    }

    return "⚠️ All Gemini models failed. Please try again later.";
}


// ===============================
// GEMINI CALL
// ===============================
function callGemini($message, $model)
{
    global $API_KEY;

    $url = "https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent?key=" . $API_KEY;

    $payload = [
        "contents" => [
            [
                "parts" => [
                    [
                        "text" => "You are a professional resume analyzer.\n\n" . $message
                    ]
                ]
            ]
        ],
        "generationConfig" => [
            "temperature" => 0.7,
            "maxOutputTokens" => 999999
        ]
    ];

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 25
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $err = curl_error($ch);
        curl_close($ch);
        return "❌ Network error: " . $err;
    }

    curl_close($ch);

    $result = json_decode($response, true);

    // ===============================
    // 🔥 DEBUG (USE TEMPORARILY ONLY)
    // ===============================
    /*
    echo "<pre>";
    print_r($result);
    exit;
    */

    // ===============================
    // ❌ API ERROR
    // ===============================
    if (isset($result['error'])) {
        return "❌ " . $result['error']['message'];
    }

    // ===============================
    // ✅ SAFE RESPONSE EXTRACTION
    // ===============================
    $text = "";

    if (isset($result['candidates'][0]['content']['parts'])) {
        foreach ($result['candidates'][0]['content']['parts'] as $part) {
            if (isset($part['text'])) {
                $text .= $part['text'];
            }
        }
    }

    if (!empty($text)) {
        return $text;
    }

    return "❌ No response from AI";
}


// ===============================
// ERROR CHECK
// ===============================
function isError($res)
{
    return strpos($res, "❌") === 0;
}