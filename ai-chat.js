const GEMINI_API_KEY = "AIzaSyAYoOAIrd7-WYQZzdYbsAjAatGEkKyB6oA";

async function askGeminiAI(userMessage) {
  const url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" + GEMINI_API_KEY;
  const body = {
    contents: [
      {
        role: "user",
        parts: [{ text: userMessage }]
      }
    ]
  };
  const res = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(body)
  });
  const data = await res.json();
  return data.candidates?.[0]?.content?.parts?.[0]?.text || "Sorry, I couldn't understand.";
}

document.getElementById('ai-chat-input-area').onsubmit = async function(e) {
  e.preventDefault();
  const input = document.getElementById('ai-chat-input');
  const userMsg = input.value.trim();
  if (!userMsg) return;

  // Show user message
  const msgDiv = document.getElementById('ai-chat-messages');
  msgDiv.innerHTML += `<div style="text-align:right;margin:5px 0;"><span style="background:#e0f7fa;padding:6px 12px;border-radius:12px;display:inline-block;">${userMsg}</span></div>`;
  msgDiv.scrollTop = msgDiv.scrollHeight;

  // Show loading
  msgDiv.innerHTML += `<div id="ai-typing" style="margin:5px 0;"><span style="background:#f0f0f0;padding:6px 12px;border-radius:12px;display:inline-block;">Thinking...</span></div>`;
  msgDiv.scrollTop = msgDiv.scrollHeight;

  input.value = '';
  input.disabled = true;

  // Get AI response
  const aiReply = await askGeminiAI(userMsg);

  // Remove loading
  document.getElementById('ai-typing').remove();

  // Show AI message
  msgDiv.innerHTML += `<div style="text-align:left;margin:5px 0;"><span style="background:#f3e5f5;padding:6px 12px;border-radius:12px;display:inline-block;">${aiReply}</span></div>`;
  msgDiv.scrollTop = msgDiv.scrollHeight;

  input.disabled = false;
  input.focus();
};