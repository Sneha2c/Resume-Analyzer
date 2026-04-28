<?php

declare(strict_types=1);

require_once "config.php";
require_once "openai.php";

/* ===============================
   🔍 TEXT EXTRACTION (PDF + IMAGE)
================================ */
function extractText($filePath, $ext)
{
    $text = "";

    if ($ext === "pdf") {

        // 1. Try normal PDF extraction
        $text = shell_exec("\"C:\\Release-25.12.0-0\\poppler-25.12.0\\Library\\bin\\pdftotext.exe\" \"$filePath\" -");

        // 2. If empty → use OCR
        if (strlen(trim($text)) < 50) {

            // Convert PDF → image
            shell_exec("\"C:\\Release-25.12.0-0\\poppler-25.12.0\\Library\\bin\\pdftoppm.exe\" -png \"$filePath\" temp");

            // OCR on first page
            $text = shell_exec("\"C:\\Program Files\\Tesseract-OCR\\tesseract.exe\" temp-1.png stdout");
        }
    }

    elseif (in_array($ext, ["jpg", "jpeg", "png"])) {

        // Direct OCR for images
        $text = shell_exec("\"C:\\Program Files\\Tesseract-OCR\\tesseract.exe\" \"$filePath\" stdout");
    }

    elseif ($ext === "txt") {

        $text = file_get_contents($filePath);
    }

    return $text;
}


/* ===============================
   🧠 MAIN ANALYSIS FUNCTION
================================ */
function getAnalysisResult($resumeId, $conn, $ext)
{
    $id = (int)$resumeId;

    if ($id <= 0) {
        return "❌ Invalid Resume ID";
    }

    $stmt = $conn->prepare("SELECT * FROM resumes WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $res = $stmt->get_result();
    $data = $res->fetch_assoc();

    if (!$data) {
        return "❌ Resume not found";
    }

    // File path
    $filePath = __DIR__ . "/" . $data["stored_path"];

    if (!file_exists($filePath)) {
        return "❌ File missing";
    }

    /* ===============================
       📄 EXTRACT TEXT
    ================================ */
    $text = extractText($filePath, $ext);

    if (strlen(trim($text)) < 50) {
        return "<div class='analysis-result'>
                <p>❌ Unable to extract text from resume.</p>
                <p>Try uploading a clearer PDF or image.</p>
                </div>";
    }

    /* ===============================
       🤖 AI ANALYSIS
    ================================ */
    $aiResult = analyzeWithAI($text);

    /* ===============================
       📊 BASIC KEYWORD SCORE
    ================================ */
    $skills = ["python","java","html","css","javascript","sql","react","node","php","mysql","git","docker","aws","machine learning","ai"];

    $found = [];
    $score = 0;

    foreach ($skills as $skill) {
        if (stripos($text, $skill) !== false) {
            $found[] = $skill;
            $score += 10;
        }
    }

    if ($score > 100) $score = 100;

    /* ===============================
       💾 SAVE RESULT
    ================================ */
    $json = json_encode($found);

    $stmt2 = $conn->prepare(
        "INSERT INTO analysis_results (resume_id, score, skills_found, feedback)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE score=?, skills_found=?, feedback=?"
    );

    $stmt2->bind_param(
        "iississ",
        $id, $score, $json, $aiResult,
        $score, $json, $aiResult
    );

    $stmt2->execute();

    /* ===============================
       🎨 OUTPUT (CHAT STYLE)
    ================================ */
    ob_start();
    ?>

    <div class="analysis-result">

        <div class="score-display">
            <h2>Score: <?= $score ?>/100</h2>
        </div>

        <p><strong>📄 File:</strong> <?= htmlspecialchars($data["original_name"]) ?></p>

        <h3>🎯 Skills Found</h3>
        <ul>
            <?php foreach ($found as $f): ?>
                <li><?= ucfirst($f) ?></li>
            <?php endforeach; ?>
        </ul>

        <div class="feedback-box">
            <h3>🤖 AI Analysis</h3>
            <div class="message-text">
                <?= nl2br(htmlspecialchars($aiResult)) ?>
            </div>
        </div>

    </div>

    <?php
    return ob_get_clean();
}


/* ===============================
   🌐 DIRECT ACCESS (TEST)
================================ */
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["id"]) && isset($_GET["ext"])) {
    echo getAnalysisResult((int)$_GET["id"], $conn, $_GET["ext"]);
}
declare(strict_types=1);

require_once "config.php";

function getAnalysisResult($resumeId, $conn) {
    $id = (int)$resumeId;
    
    if ($id <= 0) {
        return "❌ Invalid resume ID";
    }
    
    $stmt = $conn->prepare("SELECT * FROM resumes WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    $res = $stmt->get_result();
    $data = $res->fetch_assoc();
    
    if (!$data) {
        return "❌ Resume not found";
    }
    
    // Get file path
    $filePath = __DIR__ . "/" . $data["stored_path"];
    
    if (!file_exists($filePath)) {
        return "❌ Resume file not found on server";
    }
    
    // Extract text from PDF
    $text = shell_exec("\"C:\\Release-25.12.0-0\\poppler-25.12.0\\Library\\bin\\pdftotext.exe\" \"$filePath\" -");
    
    $content = strtolower($text ?? "");
    
    // Skills list
    $skills = ["python", "java", "html", "css", "javascript", "sql", "react", "node", "php", "mysql", "git", "docker", "aws", "machine learning", "ai"];
    
    $found = [];
    $score = 0;
    $skillScore = 10;
    
    foreach ($skills as $s) {
        if (strpos($content, $s) !== false) {
            $found[] = $s;
            $score += $skillScore;
        }
    }
    
    // Cap score at 100
    if ($score > 100) $score = 100;
    
    // Generate feedback
    if ($score >= 80) {
        $feedback = "🌟 Excellent resume! You have a strong skill set. Consider adding leadership experience and quantifiable achievements.";
    } elseif ($score >= 50) {
        $feedback = "👍 Good resume! You have a solid foundation. Try adding more technical skills and project details to stand out.";
    } else {
        $feedback = "💡 Your resume needs improvement. Add more technical skills, projects, and work experience to increase your score.";
    }
    
    // Save to DB
    $json = json_encode($found);
    
    $stmt2 = $conn->prepare(
        "INSERT INTO analysis_results (resume_id, score, skills_found, feedback)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE score=?, skills_found=?, feedback=?"
    );
    
    $stmt2->bind_param(
        "iississ",
        $id, $score, $json, $feedback,
        $score, $json, $feedback
    );
    
    $stmt2->execute();
    
    // Return formatted HTML result
    ob_start();
    ?>
    <div class="analysis-result">
        <div class="score-display">
            <span class="score-number"><?= $score ?></span>
            <span class="score-label">Resume Score</span>
        </div>
        
        <p><strong>📄 File:</strong> <?= htmlspecialchars($data["original_name"]) ?></p>
        
        <h3>🎯 Skills Found (<?= count($found) ?>)</h3>
        <div class="skills-grid">
            <?php if (count($found) > 0): ?>
                <?php foreach ($found as $f): ?>
                    <div class="skill-badge"><?= ucfirst($f) ?></div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No major skills detected. Consider adding technical skills to your resume.</p>
            <?php endif; ?>
        </div>
        
        <div class="feedback-box">
            <h4>💬 AI Feedback</h4>
            <p><?= $feedback ?></p>
        </div>
        
        <?php if ($score < 100): ?>
            <div class="feedback-box" style="margin-top: 12px; border-left-color: #ffc107;">
                <h4>📝 Suggestions</h4>
                <p>Consider adding: <?= implode(", ", array_diff($skills, $found)) ?></p>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

// If accessed directly via GET request (old method)
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["id"])) {
    $id = (int)($_GET["id"] ?? 0);
    echo getAnalysisResult($id, $conn);
}
?>