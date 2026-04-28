// ===============================
// INIT
// ===============================
document.addEventListener("DOMContentLoaded", () => {

  const form = document.getElementById("uploadForm");
  const input = document.getElementById("userInput");
  const fileInput = document.getElementById("fileUpload");

  if (!form || !input || !fileInput) {
    console.error("❌ Missing required elements");
    return;
  }

  // ===============================
  // FILE SELECT
  // ===============================
  fileInput.addEventListener("change", () => {
    const file = fileInput.files[0];

    if (file) {
      addMessage("user", `📎 <b>${file.name}</b>`, false);
    }
  });

  // ===============================
  // FORM SUBMIT
  // ===============================
  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const message = input.value.trim();
    const file = fileInput.files[0];

    if (!message && !file) return;

    if (message) addMessage("user", message, false);

    const formData = new FormData();
    if (message) formData.append("message", message);
    if (file) formData.append("resume", file);

    input.value = "";
    fileInput.value = "";

    addLoadingMessage();

    fetch("../backend/chat.php", {
      method: "POST",
      body: formData
    })
    .then(res => {
      if (!res.ok) throw new Error("Server error");
      return res.text();
    })
    .then(data => {
      removeLoadingMessage();
      addMessage("bot", data, true); // 🔥 formatted
    })
    .catch(() => {
      removeLoadingMessage();
      addMessage("bot", "❌ Error connecting to server.", false);
    });
  });

  // ===============================
  // ENTER KEY
  // ===============================
  input.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      form.requestSubmit();
    }
  });

});


// ===============================
// MESSAGE UI
// ===============================
function addMessage(type, content, isFormatted = false) {
  const chat = document.getElementById("chatMessages");

  const div = document.createElement("div");
  div.className = `message ${type}`;

  const finalContent = isFormatted ? formatResponse(content) : content;

  div.innerHTML = `
    <div class="message-content">
      ${type === "bot" ? '<div class="message-icon">🤖</div>' : ''}
      <div class="message-text">${finalContent}</div>
    </div>
  `;

  chat.appendChild(div);
  chat.scrollTop = chat.scrollHeight;
}


// ===============================
// 🔥 CLEAN FORMATTER (FINAL FIX)
// ===============================
function formatResponse(text) {

  // Headings (##)
  text = text.replace(/^##\s*(.*)$/gm, "<h3>$1</h3>");

  // Bold (**text**)
  text = text.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");

  // Convert bullet lines into array
  let lines = text.split("\n");
  let formatted = "";
  let inList = false;

  lines.forEach(line => {
    line = line.trim();

    // Bullet
    if (line.startsWith("* ")) {
      if (!inList) {
        formatted += "<ul>";
        inList = true;
      }
      formatted += `<li>${line.substring(2)}</li>`;
    } 
    else {
      if (inList) {
        formatted += "</ul>";
        inList = false;
      }

      if (line !== "") {
        formatted += `<p>${line}</p>`;
      }
    }
  });

  if (inList) formatted += "</ul>";

  return formatted;
}


// ===============================
// LOADING UI
// ===============================
function addLoadingMessage() {
  const chat = document.getElementById("chatMessages");

  const div = document.createElement("div");
  div.id = "loadingMsg";
  div.className = "message bot";

  div.innerHTML = `
    <div class="message-content">
      <div class="message-icon">🤖</div>
      <div class="message-text">Thinking...</div>
    </div>
  `;

  chat.appendChild(div);
}

function removeLoadingMessage() {
  const el = document.getElementById("loadingMsg");
  if (el) el.remove();
}